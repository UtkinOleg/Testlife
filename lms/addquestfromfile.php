<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "importer.php";

$error = "";
$action = $_POST["action"];

if ($action=='file') 
{
    $groupid = $_POST["qgid"];
    $kid = $_POST["kid"];
    if (!empty($_FILES["textfile"]))
    {
     $origfilename = $_FILES["textfile"]["name"]; 
     $filename = explode(".", $origfilename); 
     $filenameext = strtolower($filename[count($filename)-1]); 
     if ($filenameext=='xml')
      $error = ImportXML($mysqli, $_FILES["textfile"], $groupid, $xmlupload_dir);
     else 
     if ($filenameext=='txt')
      $error = ImportTXT($mysqli, $_FILES["textfile"], $groupid, $xmlupload_dir);
     else
      $error = " Расширение файла не поддерживается."; 
    }
    if (!empty($error))
     $groupid = 0;

    if (!empty($error)) 
    {
      echo '<script language="javascript">';
      echo 'alert("Ошибки:'.$error.'");
      parent.closeFancybox();';
      echo '</script>';
      exit();
    }
    else   
    {
      echo '<script language="javascript">';
      echo 'parent.closeFancyboxAndRedirectToUrl('.$kid.',"q");';
      echo '</script>';
      exit();
    }  
}
else
if (empty($action)) 
{
   if (defined("IN_SUPERVISOR") or defined("IN_ADMIN")) 
   {

  $qgid = $_GET["id"];
  $kid = $_GET["kid"];
  
require_once "header.php"; 
  
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script>
 $(document).ready(function(){
    $( "button" ).button();
    $('#import').submit(function()
    {
     var hasError = false;
     if($("#textfile").val()=='') {
            $("#info2").empty();
            $("#info2").append('Необходимо выбрать XML или TXT файл для импорта.');
            hasError = true;
     }  
     if (hasError == false)
     {
     var f2 = $("#textfile").val().search(/^.*\.(?:xml|txt)\s*$/ig);
     if(f2!=0){
            $("#info2").empty();
            $("#info2").append(' Поддерживаются файлы только с расширением XML или TXT.');
            hasError = true;
     }
     }
     if(hasError == true) {     
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#textfile").focus();
       return false; 
     }
     $('#ok', $(this)).attr('disabled', 'disabled');
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
<style type="text/css"> 
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
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
</style>  		
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align="center">
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2">Добавить вопросы из файла.</div>    
        </p>            	   
      </div>
    </div>
</td></tr>
<tr><td align="center">
<table width='98%' border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<form id="import" action="addquestfromfile" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="file">
<input type="hidden" name="qgid" value="<? echo $qgid; ?>">
<input type="hidden" name="kid" value="<? echo $kid; ?>">
    <tr><td>
        <p class=ptd><b>Загрузить вопросы из файла XML (LMS Moodle) или TXT</b> 
        Размер файла не должен превышать 100кб.</p>
        <input type='file' id='textfile' name='textfile'/>
    </td></tr>
</form>
</table>
</td></tr>
<tr><td align="center">
 <p></p>
</td></tr>
<tr><td align="center">
 <p></p>
</td></tr>
<tr><td align="center">
 <p></p>
</td></tr>
<tr><td align="center">
  <button id="ok" onclick="$('#import').submit();">Загрузить файл</button> 
  <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
  <button id="help" onclick="window.open('h&id=3');"><i class="fa fa-question fa-lg"></i> Помощь</button>
</td></tr></table>
</body></html>
<?
}}} else die;
?>
