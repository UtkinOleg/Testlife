<?php
if(defined("IN_ADMIN")) {

include "config.php";
//include "func.php";

$action = "";
$action = $_POST["action"];
if (!empty($action)) 
{

//ini_set('display_errors', 1);
//error_reporting(E_ALL); // E_ALL
 

   require_once('emailmsg.php');
   require_once('lib/unicode.inc');
     

     $userid = $_POST["id"];
     $num = $_POST["num"]*10;
     $query = "INSERT INTO money VALUES (0,0,$userid,$num,NOW());";
     mysqli_query($mysqli,$query);

         // Отправим сообщение
         $to = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, userfio, email FROM users WHERE id='".$userid."' LIMIT 1;");
         $touser = mysqli_fetch_array($to);

         if (!empty($touser))
         {
          $title = "Вам добавлено ".$_POST["num"]." сеансов тестирования на сайте testlife.org";
          $body = msghead($touser['userfio'], $site);
          $body .= "<p>Вам добавлено ".$_POST["num"]." дополнительных сеансов тестирования на сайте <strong>testlife.org</strong></p>";
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


     echo '<script language="javascript">';
     echo 'parent.closeFancybox();';
     echo '</script>';
     exit();
}
else
if (empty($action)) 
{
 $modename = "Добавить сеансы тестирования";
 $id = $_GET['id'];
 
require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
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
</style>
<script>
 $(document).ready(function(){
    $("button").button();
    $('#addk').submit(function()
    {
     var hasError = false; 
     var name = $("#name");
     if(name.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите количество сеансов!');
            name.focus();
            hasError = true;
     }
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       return false; 
     }
     else
     {
       $('#ok', $(this)).attr('disabled', 'disabled');
       return true; 
     }
    });   
  });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align="center">
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2"><?=$modename?></div>    
        </p>            	   
      </div>
    </div>
<p></p>
<form id='addk' action='addmoney' method='post'>
<input type='hidden' name='id' value='<?=$id?>'>
<input type='hidden' name='action' value='post'>
<table width="99%" align="center" border="0" cellpadding=3 cellspacing=3>
    <tr>
        <td><p>Количество сеансов *:</p></td>
    </tr>
    <tr>
        <td><input type='text' id='num' name='num' style='width:100%' value='0'></td>
    </tr>
</table></div>
</form>
<p></p>
<table width="99%" align="center" border="0" cellpadding=3 cellspacing=3>
    <tr>
        <td align="center">
            <button id="ok" onclick="$('#addk').submit();"><?=$modename ?></button> 
            <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
        </td>
    </tr>           
</table>
</td></tr></table>
</body></html>
<?php
}} else die;
?>
