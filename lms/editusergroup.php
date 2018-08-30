<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

include "config.php";

$action = $_POST["action"];
if (!empty($action)) 
{
  $id = $_POST["id"];
  $folderid = $_POST["p"];
  $mode = $_POST["m"];
  $users = $_POST["cntusers"];
  $usergrouptype = $_POST["usergrouptype"];
  $name = $_POST["name"];
  $name = str_replace('"','',$name);
  $name = str_replace("'",'',$name);
   
  if ($mode=='a')
  {
   mysqli_query($mysqli,"START TRANSACTION;");
   $query = "INSERT INTO usergroups VALUES (0,
      '$name',
      NOW(),
      ".USER_ID.",
      ".$folderid.",
      ".$usergrouptype.")";
  
   if(mysqli_query($mysqli,$query)) {
    $id = mysqli_insert_id($mysqli);
    // Запишем email
    if ($id>0)
    {
     for ($i = 1; $i <= $users; $i++) {
      $email = htmlspecialchars($_POST["email".$i], ENT_QUOTES);
      if (!empty($email))
      {
         $query = "INSERT INTO useremails VALUES (0,
         '$email',
         $id,0)";
         mysqli_query($mysqli,$query);
      }
    }
   }
  }
   
     mysqli_query($mysqli,"COMMIT");
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$folderid.');';
     echo '</script>';
     exit();
   }
   else
   if ($mode=='e')
   {
     // Сначала удалим старые 
     mysqli_query($mysqli,"START TRANSACTION;");
     mysqli_query($mysqli,"DELETE FROM useremails WHERE usergroupid=".$id);
     mysqli_query($mysqli,"COMMIT");

     mysqli_query($mysqli,"START TRANSACTION;");
     // Добавим ответы 
     for ($i = 1; $i <= $users; $i++) 
     {
      $email = htmlspecialchars($_POST["email".$i], ENT_QUOTES);
      if (!empty($email))
      {
         $query = "INSERT INTO useremails VALUES (0,
         '$email',
         $id,0)";
         mysqli_query($mysqli,$query);
      }
     }
     
     // Обновим 
     $query = "UPDATE usergroups SET name = '".$name."', usergrouptype = ".$usergrouptype." WHERE id=".$id;
     mysqli_query($mysqli,$query);
     mysqli_query($mysqli,"COMMIT");
     
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$folderid.');';
     echo '</script>';
     exit();
   } 
    
}
else
if (empty($action)) 
{
  $mode = $_GET["m"];
  $id = $_GET["id"];
  $folderid = $_GET["p"];
  
  if ($mode=='e')
  {
   $modename = '<i class="fa fa-users fa-fw"></i> Изменить группу участников';
   if (defined("IN_ADMIN")) 
   {
       $query = "SELECT * FROM usergroups WHERE id='".$id."' LIMIT 1;";
   }
   else
   { 
       $query = "SELECT * FROM usergroups WHERE id='".$id."' AND userid='".USER_ID."' LIMIT 1;";
   }
   
   $sql = mysqli_query($mysqli,$query);
   $usergroup = mysqli_fetch_array($sql);
   $usergroupname = $usergroup['name'];
   $usergrouptype = $usergroup['usergrouptype'];
   mysqli_free_result($sql);
  }
  else
  if ($mode=='a')
  {
    $usergroupname = '';
    $usergrouptype=0;
    $modename = '<i class="fa fa-users fa-fw"></i> Новая группа участников';
  }
  
  require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script type="text/javascript">

 $(function(){
   $("#spinner").fadeOut("slow");
   $("button").button();
   $( "#usergrouptype" ).selectmenu({ width: 400 });
    <? if ($mode=='e') { 
   
   $sql2 = mysqli_query($mysqli,"SELECT * FROM useremails WHERE usergroupid='".$id."' ORDER BY id;");
   while($email = mysqli_fetch_array($sql2))
   {
   
    $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM users as u, singleresult as s WHERE s.userid=u.id AND u.email='".$email['email']."' LIMIT 1;");
    $cntusers = mysqli_fetch_array($countu);
    $count_res2 = $cntusers['count(*)'];
    mysqli_free_result($countu); 
   
   ?>
    email = "<?=trim(rtrim($email['email'], '\n\r'))?>";
    count = "<?=$count_res2?>";
    addusermanual(email, count);
   <?}    
   mysqli_free_result($sql2);
   } ?>
  });       

 function checkRegexp(n, t) {
        return t.test(n);
 }

 function deluser(cnt)
 {
   $("#email"+cnt).val('');
   $('#duser'+cnt).empty();
 }

 function adduser()
 {
     var hasError = false; 
     var cnt = $("#cntusers").val();
     var email = $("#email");
     
     if(email.val()==='') {
            $("#info2").html('Введите адрес электронной почты участника');
            hasError = true;
     }

     var emails = email.val().split('\n');

     if(hasError === false) {     
      for	(index = 0; index < emails.length; index++) {
       if (checkRegexp(emails[index], /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i)==0)
       {
            $("#info2").html('Адрес электронной почты '+emails[index]+' указан неправильно.');
            hasError = true;
            break;
       }
      }
     }  
     
     
     if(hasError === false) {     
      for (i = 1; i <= cnt; i++) { 
        for	(index = 0; index < emails.length; index++) {
         if ($("#email"+i).val() === emails[index])
         {
            $("#info2").html('Адрес '+emails[index]+' уже существует');
            hasError = true;
            break;
         }
        }
        if(hasError === true)
         break; 
      }
     }
      
     if(hasError === true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#email").focus();
     }
     else
     {

      for	(index = 0; index < emails.length; index++) {
       cnt++;
       $('#hiddenusers').append('<input type="hidden" id="email'+cnt+'" name="email'+cnt+'" value="' + emails[index] + '">'); 
       $('#showusers').append('<div id="duser'+cnt+'"><p>' + emails[index] + '&nbsp;<button title="Удалить участника" id="delb'+cnt+'" onclick="deluser('+cnt+');"><i class="fa fa-minus-circle fa-lg"></i></button></p></div>');        
       $("#delb"+cnt).button();
      }
      $("#cntusers").val(cnt);
      
      $("#email").val('');
      $("#email").focus();
     }
     
 }

 function addusermanual(email, count)
 {
      var cnt = $("#cntusers").val();
      cnt++;
      $("#cntusers").val(cnt);

      $('#hiddenusers').append('<input type="hidden" id="email'+cnt+'" name="email'+cnt+'" value="' + email + '">'); 
      var s = '<div id="duser'+cnt+'"><p>' + email;
      if (count==0)
       s+='&nbsp;<button title="Удалить участника" id="delb'+cnt+'" onclick="deluser('+cnt+');"><i class="fa fa-minus-circle fa-lg"></i></button></p></div>';        
      $('#showusers').append(s);
      if (count==0)
       $("#delb"+cnt).button();
      $("#email").val('');
      $("#email").focus();
     
 }
 
 jQuery(document).ready(function() {
    $('#save').submit(function()
    {
     if ($("#name").val() === '')
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Введите наименование группы участников');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#name").focus();
      return false;
     }   

     var cnt = $("#cntusers").val();
     if (cnt>0)
     {
      $('#ok', $(this)).attr('disabled', 'disabled');
      $("#spinner").fadeIn("slow");
      return true;
     } 
     else 
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Укажите электронные адреса участников тестирования');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#email").focus();
      return false;
     }   
    });   
  });   

</script>
<style>
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.8em;
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
 <form id="save" action="edusergroup" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="id" value="<?=$id?>">
  <input type="hidden" name="m" value="<?=$mode?>">
  <input type="hidden" name="p" value="<?=$folderid?>">
  <input type='hidden' id='cntusers' name='cntusers' value='0'>
   <table border="0" width='90%' cellpadding=0 cellspacing=0>
    <tr>
        <td width="30%"><p>Наименование группы *:</p></td>
        <td><input type='text' id='name' name='name' style='width:100%' value='<?=$usergroupname?>'></td>
    </tr>
    <tr>
        <td width="30%">
         <p>Тип группы участников:</p>
        </td>
        <td>
               <select id="usergrouptype" name="usergrouptype">     
                <option value="0" <?if($usergrouptype==0) echo 'selected'?>>Участники тестирования</option>   
                <option value="1" <?if($usergrouptype==1) echo 'selected'?>>Эксперты проверки КИМ</option>   
               <? if (USER_EXTMODE) {?> 
                <option value="2" <?if($usergrouptype==2) echo 'selected'?>>Эксперты выполнения ФГОС</option>   
               <?}?>
               </select> 
        </td>              
    </tr>
    <tr>
        <td width="30%">
         <p>Электронные адреса (возможен ввод нескольких строк):</p>
        </td>
        <td>
         <p><textarea id='email' name='email' style='width:100%' rows='4'></textarea></p>
        </td>
    </tr>
    <tr>    
     <td>
        <div id="hiddenusers"></div>
     </td>
     <td>
       <div id="showusers"></div>
     </td>
    </tr>
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id="add" onclick="adduser();"><i class="fa fa-user fa-lg"></i> Добавить</button>
            <button id='ok' onclick="$('#save').submit();" >Сохранить группу</button>
            <button id="close" onclick="parent.closeFancybox();">Отмена</button> 
            <button id="help" onclick="window.open('h&id=7');"><i class="fa fa-question fa-lg"></i> Помощь</button>
 </div>
</body></html>
<?
} 
} else die; 
