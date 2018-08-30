<?php

if(defined("IN_ADMIN")) {  

include "config.php";
require_once('emailmsg.php');

$action = $_POST["action"];

if (!empty($action)) 
{
  $id = $_POST["id"];
  $mode = $_POST["m"];
  $news = $_POST["news"];
  $active = $_POST["active"];
  $content = mysqli_real_escape_string($mysqli,$_POST["content"]);
  $name = $_POST["name"];
   
  mysqli_query($mysqli,"START TRANSACTION;");
  if ($mode=='a')
  {
   $query = "INSERT INTO helppages VALUES (0,
      '$name',
      '$content',
      NOW(), $news)";
     mysqli_query($mysqli,$query);
  }
  else
  if ($mode=='e')
  {
     $query = "UPDATE helppages SET name = '".$name."'
            , content = '".$content."' 
            WHERE id=".$id;
     mysqli_query($mysqli,$query);
  }  
  mysqli_query($mysqli,"COMMIT;");

  if ($mode=='e' and $active)
  {
        $to = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE usertype='supervisor'");
        while ($touser = mysqli_fetch_array($to))
        //if ($touser['email']=='utkinolegv@gmail.com')
        {

         $toid = $touser['id'];
         $signature = md5(time().$toid."news");  // Уникальная сигнатура сообщения
         $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        ".USER_ID.",
                                        'Новости сервиса: ".$name."',
                                        '".$content."',
                                        0,
                                        NOW(),
                                        '".$signature."');";
         mysqli_query($mysqli,$query);
         // Отправим сообщение
         require_once('lib/unicode.inc');
      
         $title = "Новости сервиса TestLife: ".$name;
         $body = msghead($touser['userfio'], $site);
         $body .= "<p><strong>".$name."</strong></p>";
         $body .= "<p>".$content."</p>";
         $body .= "<p>Все подробности в личном кабинете в разделе <strong>Помощь.</strong></p>";
         $body .= msgtail($site);
         $mimeheaders = array();                                                                                                                                                           
         $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
         $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
         $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
         $mimeheaders[] = 'From: '. mime_header_encode('TestLife <'.$valmail.'>');

         mail(
           $touser['email'],
           mime_header_encode($title),
           str_replace("\r", '', $body),
           join("\n", $mimeheaders)
          );
         }
         mysqli_free_result($to);
  
  } 
  
  echo '<script language="javascript">';
  echo 'parent.closeFancybox();';
  echo '</script>';
  exit();
}
else
if (empty($action)) 
{
  $mode = $_GET["m"];
  $id = $_GET["id"];
  $news = $_GET["t"];
  if ($news=='n')
   $news=1;
  else  
   $news=0;
  
  if ($mode=='e')
  {
   $modename = "Изменить страницу";
   $query = "SELECT * FROM helppages WHERE id='".$id."' LIMIT 1;";
   $sql = mysqli_query($mysqli,$query);
   $page = mysqli_fetch_array($sql);
   
  }
  else
  if ($mode=='a')
  {
    $modename = 'Новая страница';
  }
  
  require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script src="lms/scripts/wysiwyg.js"></script>
<script type="text/javascript">
 jQuery(document).ready(function() {
    $("#spinner").fadeOut("slow");
    $("button").button();
    $("#active").buttonset();
    $('#content').juirte({ height: "400px" });
    $('#adds').submit(function()
    {
     if ($("#name").val() == '')
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Введите наименование страницы');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#name").focus();
      return false;
     }   
     if ($("#content").val() == '')
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Введите содержание вопроса');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#content-editor").focus();
      return false;
     }   
    });   
  });   
</script>
<style>
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.9em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
p { font: 14px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
.ui-wysiwyg .ui-button{font-size:90%;height:24px;min-width:30px}
.ui-wysiwyg .ui-button span{font-size:80%}
.ui-wysiwyg sub{font-size:6px;font-weight:700;line-height:1px}
.ui-wysiwyg sup{font-size:6px;font-weight:700}
.ui-wysiwyg-btn-forecolor span{text-decoration:underline}
.ui-wysiwyg-btn-italic span{font-weight:400}
.ui-wysiwyg-btn-strikeThrough span{font-weight:400;text-decoration:line-through}
.ui-wysiwyg-btn-underline span{font-weight:400;text-decoration:underline}
.ui-wysiwyg-colorinput{width:60px}
.ui-wysiwyg-dropdown{display:none;margin-left:2px;position:absolute;z-index:2229}
.ui-wysiwyg-dropdown ul{list-style:none;margin:0;padding:10px}
.ui-wysiwyg-dropdown ul li{cursor:pointer;padding:2px;text-decoration:underline}
.ui-wysiwyg-dropdown ul li h1,.ui-wysiwyg-dropdown ul li h2,.ui-wysiwyg-dropdown ul li h3,.ui-wysiwyg-dropdown ul li h4,.ui-wysiwyg-dropdown ul li h5,.ui-wysiwyg-dropdown ul li h6{margin:0;padding:0}
.ui-wysiwyg-justify-wrap{line-height:2px;margin-top:-4px;text-align:left}
.ui-wysiwyg-justify-center{text-align:center}
.ui-wysiwyg-justify-right{text-align:right}
.ui-wysiwyg-left{float:left;margin-bottom:4px}
.ui-wysiwyg-list-wrap{font-size:7px;line-height:6px;overflow:hidden;text-align:left}
.ui-wysiwyg-menu {margin:-4px 0 0 -1px;padding:0}
.ui-wysiwyg-menu-wrap {margin:4px 4px 0}
.ui-wysiwyg-row{clear:left;float:left}
.ui-wysiwyg-swatch{border:1px solid #000;display:table;height:12px;text-decoration:none;width:100%}
.ui-wysiwyg-fontbgcdropdown,.ui-wysiwyg-fontcldropdown{padding:4px; width: 80px}
.ui-wysiwyg-fontdropdown,.ui-wysiwyg-fontbgcdropdown,.ui-wysiwyg-fontcldropdown{ overflow: auto; height: 120px;}
.ui-wysiwyg-fontdropdown{ width: 200px}
.ui-wysiwyg-container{display:table-cell}
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2"><?=$modename?></div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<div id="buttonsetm">
 <form id="adds" action="edhelppage" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="id" value="<?=$id?>">
  <input type="hidden" name="m" value="<?=$mode?>">
  <input type="hidden" name="news" value="<?=$news?>">
   <table border="0" width='99%' height="100%" cellpadding=0 cellspacing=0 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width="30%"><p>Наименование страницы *:</p></td>
    </tr>
    <tr>
        <td><input type='text' id='name' name='name' style='width:100%' value='<?=$page['name']?>'></td>
    </tr>
    <tr>
     <td>
        <p></p>
        <textarea id="content" name='content' style='width:100%' rows='27'><? if ($mode=='e') echo $page['content']; ?></textarea>
     </td>
    </tr>
      <? if ($mode=='e' and $page['news']) { ?>          
                <tr>        
                  <td>         
                    <p>Отправить оповещение создателям:</p>        
                  </td>        
    </tr>
    <tr>
                  <td>
        <div id="active">
          <input type="radio" value='1' id="active1" name="active"><label for="active1">Да</label>       
          <input type="radio" value='0' id="active2" name="active" checked="checked"><label for="active2">Нет</label>       
        </div>
                  </td>    
                </tr>
     <?}?>           
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id='ok' onclick="$('#adds').submit();" ><i class='fa fa-check fa-lg'></i> Сохранить страницу</button>
            <button id="close" onclick="parent.closeFancybox();"><i class='fa fa-times fa-lg'></i> Закрыть</button> 
 </div>
</body></html>
<?
} 
} else die; 

?>