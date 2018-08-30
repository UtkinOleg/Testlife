<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";

$error = "";
$action = "";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
    $paid = $_POST["paid"];
    require_once ('lib/transliteration.inc');
    // Проверяем правильность ввода информации в поля формы
    if (empty($_FILES["xmlfile"]["name"])) 
    {
     $action = ""; 
     $error = $error." Файл не найден.";
    }

    if($_FILES["xmlfile"]["name"]!=""){ 
     $filedata = $_FILES["xmlfile"]["name"]; 
     $realfiledata = transliteration_clean_filename($_FILES["xmlfile"]["name"],"ru");
     $filesize = $_FILES["xmlfile"]["size"]; 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if($_FILES["xmlfile"]["name"]!=""){ 
          $origfilename = $_FILES["xmlfile"]["name"]; 
          $filename = explode(".", $origfilename); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          if($filenameext=='xml') 
              $file_ext_allow = TRUE; 

          if($file_ext_allow){ 
            if($_FILES["xmlfile"]["size"]<$max_file_size){ 
              if(move_uploaded_file($_FILES["xmlfile"]["tmp_name"], $xmlupload_dir.$_POST["qgid"].$realfiledata)){ 
                echo("Файл успешно загружен. - <a href='".$xmlupload_dir.$_POST["qgid"].$realfiledata."' target='_blank'>".$filedata."</a><br />"); 
              }else{ 
                $error = $error." ".$origfilename." не был загружен в каталог сервера."; 
              } 
            }else{ 
              $error=$error." ".$origfilename." превышает установленный размер."; 
            } 
          }else{ 
            $error=$error." ".$origfilename." не поддерживается."; 
          } 
    }   
   
   if (!file_exists($xmlupload_dir.$_POST["qgid"].$realfiledata)) {
     $error=$error.' Не удалось открыть файл '.$xmlupload_dir.$_POST["qgid"].$realfiledata;
   }
   
  if (!empty($error)) 
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancybox();';
   echo '</script>';
   exit();
  } 
   
   if (file_exists($xmlupload_dir.$_POST["qgid"].$realfiledata)) 
   {
    $xml = simplexml_load_file($xmlupload_dir.$_POST["qgid"].$realfiledata);
 
    $i=0;
    $multiid = $_POST['multiid'];
    
   
    foreach ($xml->xpath('//param') as $param) 
    {
     
     // Запишем вопрос в базу
     if (!empty($param->name))
     {
      $name = $param->name;
      $doptext = $param->doptext;

      $typecontent = $param->type;

      switch ($typecontent) 
      { 
        case 'str': {
          $content = 'yes';
          $files = 'no';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'file';
          $fileformat = 'simple';
          $typetext = 'text';
          break; }
        case 'text': {
          $content = 'yes';
          $files = 'no';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'file';
          $fileformat = 'simple';
          $typetext = 'textarea';
          break; }
        case 'link': {
          $content = 'yes';
          $files = 'no';
          $youtube = 'no';
          $link = 'yes';
          $filetype = 'file';
          $fileformat = 'simple';
          $typetext = 'text';
          break; }
        case 'youtube': {
          $content = 'yes';
          $files = 'no';
          $youtube = 'yes';
          $link = 'no';
          $filetype = 'file';
          $fileformat = 'simple';
          $typetext = 'text';
          break; }
        case 'file': {
          $content = 'no';
          $files = 'yes';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'file';
          $fileformat = 'simple';
          $typetext = 'text';
          break; }
        case 'photo': {
          $content = 'no';
          $files = 'yes';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'foto';
          $fileformat = 'simple';
          $typetext = 'text';
          break; }
        case 'files': {
          $content = 'no';
          $files = 'yes';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'file';
          $fileformat = 'ajax';
          $typetext = 'text';
          break; }
        case 'photos': {
          $content = 'no';
          $files = 'yes';
          $youtube = 'no';
          $link = 'no';
          $filetype = 'foto';
          $fileformat = 'ajax';
          $typetext = 'text';
          break; }
    }      


    mysql_query("LOCK TABLES poptions WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    $query = "INSERT INTO poptions VALUES (0,
                                        '$name',
                                        '$content',
                                        '$files',
                                        $paid,
                                        '$typetext',
                                        '$youtube',
                                        '$filetype',
                                        '$fileformat',
                                        '$doptext',
                                        '$link',
                                        $multiid);";
  
      if(!mysql_query($query)) {
      }
            
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");      
      
     }   
    }
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");   

    
    } 

    echo "<script>parent.closeFancyboxAndRedirectToUrl('http://expert03.ru/poptions&paid=".$paid."&tab=1');</script>"; 
    exit();


}
else
if (empty($action)) 
{
   if (defined("IN_SUPERVISOR") or defined("IN_ADMIN")) 
   {

  $paid = $_GET["paid"];
  

require_once "header.php"; 
?>
<script type="text/javascript"> 
 function startStatus(total) {
 var i=1;
	for ( ; i < total+1; i++ ) {
    $("#form_upload"+i).fadeOut(); 
  }
  $("#progress_bar").fadeIn(); 
 }  
 $(function() {
   $( "#accordion" ).accordion({
    heightStyle: "content",collapsible: true, active: false
   });
   $( document ).tooltip({
      show: {
        effect: "slideDown",
        delay: 250
      },
      position: {
        my: "center top+20",
        at: "center bottom"
      }
   }); 
   $( "#multiid" ).selectmenu();
   $( "#ok" ).button();
 });
</script> 
<style type="text/css"> 
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
#progress_bar{ 
    position:relative; 
    width:300px; 
    display: none; 
    margin:15px 0 0 0px; 
} 
#bg{ 
    width:300px; 
    border:1px solid black; 
    height:10px; 
    display:block; 
    background-image:url(/img/progress.gif); 
    background-repeat: repeat-x; 
} 
</style>  		
</head><body>
<form action="addpoptionfromfile" method="post" enctype="multipart/form-data" onsubmit="startStatus(1);">
<input type="hidden" name="action" value="post">
<input type="hidden" name="paid" value="<? echo $paid; ?>">
<p align='center'>
<table class=bodytable width="100%" border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Добавить параметры из файла XML</p></h3>
    </td></tr>
    <tr>
        <td>
          <p>Раздел мультишаблона *:</p>
        </td>
    </tr>
    <tr>
        <td><select id='multiid' name='multiid'>
        <? 
          echo "<option value='0'>По умолчанию</option>";
          $know = mysql_query("SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
          while($knowmember = mysql_fetch_array($know))
          {
            echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
          }
        ?>
        </select>&nbsp;<img src='img/b_docs.png' title="Выбор раздела необходим в случае использования мультишаблона. Если мультишаблон не используется - раздел будет по умолчанию."></td>
    </tr>    

    <tr><td>
        <p class=ptd><b>Загрузить параметры шаблона из файла XML</b> 
        Размер файла не должен превышать 1Мб.</p>
        <input type='file' name='xmlfile'/>
    </td></tr>


    <tr><td>
    
<div id='accordion'><h3 style='font-size:12px; color: #fff;'><b>Пример файла XML</b></h3><div>
<xmp style="font-size: 12px;">
<?echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";?>
<shablonset>
  <param> 
    <name>Строковый параметр</name> 
    <doptext>Это строковый параметр</doptext>
    <type>str</type>
  </param>
  <param> 
    <name>Текстовый блок</name> 
    <doptext>Это несколько строк</doptext>
    <type>text</type>
  </param>
  <param> 
    <name>Ссылка</name> 
    <doptext>Это ссылка</doptext>
    <type>link</type>
  </param>
  <param> 
    <name>Видеоклип</name> 
    <doptext>Это ссылка на видеоклип</doptext>
    <type>youtube</type>
  </param>
  <param> 
    <name>Файл</name> 
    <doptext>Это файловый параметр</doptext>
    <type>file</type>
  </param>
  <param> 
    <name>Картинка</name> 
    <doptext>Это картинка</doptext>
    <type>photo</type>
  </param>
</shablonset>
</xmp>
</div></div>
    
    </td></tr>



    <tr align="center">
        <td>
            <input id='ok' type="submit" value="Загрузить XML файл">
        </td>
    </tr>           
</table>
</form>
</body></html>
<?
}
}} else die;
?>
