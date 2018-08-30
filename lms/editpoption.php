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

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
{
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не заполнили наименование параметра.\n";
  }

  if (!empty($action)) 
  {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $doptext = $_POST["doptext"];
    $multiid = $_POST["multiid"];

    mysql_query("LOCK TABLES poptions WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    
    $query = "UPDATE poptions SET name = '".$name."'
    , doptext = '".$doptext."'
    , multiid = ".$multiid."
     WHERE id='".$id."'";

    if(!mysql_query($query))
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      echo '<script language="javascript">';
      echo 'alert("Ошибка при выполнении запроса.");
      parent.closeFancyboxAndRedirectToUrl("'.$site.'/poptions&paid='.$paid.'&tab=1");';
      echo '</script>';
      exit();
    }
   
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      echo "<script>parent.closeFancyboxAndRedirectToUrl('".$site."/poptions&paid=".$paid."&tab=1');</script>"; 
    
  } 
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancyboxAndRedirectToUrl("'.$site.'/poptions&paid='.$paid.'&tab=1");';
   echo '</script>';
  } 
   
  
}
}

if (empty($action)) 
{

  $paid = $_GET["paid"];
  $id = $_GET["id"];
  
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $po = mysql_query("SELECT * FROM poptions WHERE id='".$id."' LIMIT 1");
  if (!$po) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);
  $member = mysql_fetch_array($po);

if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
{
  
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
.iferror {
	margin:0;
  color: #FF4565; 
  font-size: 0.7em;
  font-family: Verdana,Arial,sans-serif;
}
.error .iferror {
	display:block;
}
</style>
<script>
$(function() {
    $( "#ok" ).button();
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
    $( "#typecontent" ).selectmenu({
          change: function( event, data ) {
        
    var str = "";
    var typecontent = data.item.value;
    if ( typecontent === "str" ) {
       str += "Ввод одной строки.";
      } else if ( typecontent === "text" ) {
       str += "Ввод нескольких строк.";
      } else if ( typecontent === "link" ) {
       str += "Ввод ссылки в формате ссылки на сайт или страницу (например www.yousite.com/youpage.html).";
      } else if ( typecontent === "youtube" ) {
       str += "Идентификатор ролика youtube.com или ссылка на youtube.com (например http://www.youtube.com/watch?v=QKT0or9fvNU).";
      } else if ( typecontent === "file" ) {
       str += "Загрузка файла - позволяет загрузить (прикрепить к проекту) файл. Максимальный размер - 3 Мб.";
      } else if ( typecontent === "photo" ) {
       str += "Загрузка фотографии (картинки) - позволяет загрузить (прикрепить к проекту) фотографию или картинку. Одновременно будет создана пользовательская фотогалерея. Максимальный размер файла - 1 Мб.";
      } else if ( typecontent === "files" ) {
       str += "Одновременная загрузка до пяти файлов - позволяет загрузить (прикрепить к проекту) несколько файлов. Максимальный размер каждого файла - 3 Мб.";
      } else if ( typecontent === "photos" ) {
       str += "Одновременная загрузка до пяти фотографий (картинок) - позволяет загрузить (прикрепить к проекту) несколько фотографий. Максимальный размер каждого файла - 1 Мб.";
      }
    $( "#content" ).text( str );
        }
        });
    $( "#multiid" ).selectmenu();
});
 $(document).ready(function(){
    $('form').submit(function()
    {
     var hasError = false; 
     $(".iferror").hide();
     var name = $("#name");
     if(name.val()=='') {
            name.after('<span class="iferror"><strong>Введите наименование!</strong></span>');
            name.focus();
            hasError = true;
     }
     if(hasError == true) {
       return false; 
     }
     else
     {
       $('input[type=submit]', $(this)).attr('disabled', 'disabled');
       return true; 
     }
    });   
  });

</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить параметр шаблона</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p>
      <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
      <?
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Ввод одной строки.</div>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'textarea')
         echo "<div id=\"content\">Ввод нескольких строк.</div>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'yes' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Ввод ссылки в формате ссылки на сайт или страницу (например www.yousite.com/youpage.html).</div>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'yes' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Идентификатор ролика youtube.com или ссылка на youtube.com (например http://www.youtube.com/watch?v=QKT0or9fvNU).</div>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Загрузка файла - позволяет загрузить (прикрепить к проекту) файл. Максимальный размер - 3 Мб.</div>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Загрузка фотографии (картинки) - позволяет загрузить (прикрепить к проекту) фотографию или картинку. Одновременно будет создана пользовательская фотогалерея. Максимальный размер файла - 1 Мб.</div>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Одновременная загрузка до пяти файлов - позволяет загрузить (прикрепить к проекту) несколько файлов. Максимальный размер каждого файла - 3 Мб.</div>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<div id=\"content\">Одновременная загрузка до пяти фотографий (картинок) - позволяет загрузить (прикрепить к проекту) несколько фотографий. Максимальный размер каждого файла - 1 Мб.</div>";
      ?>
    </p>
	</div>
</div> 
<p></p>
<div id="menu_glide" class="menu_glide">
<form action=editpoption method=post>
<input type=hidden name=action value=post>
<input type=hidden name=paid value=<? echo $paid; ?>>
<input type=hidden name=id value=<? echo $id; ?>>
<table width="100%" align='center' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td witdh='400'><p class=ptd><b><em class=em>Наименование параметра *:</em></b></td>
    </tr>
    <tr>
        <td><input type=text id='name' name='name' style='width:100%' value='<? echo $member['name'] ?>'></td>
    </tr>
    <tr>
        <td witdh='400'><p class=ptd><em class=em>Дополнительное пояснение:</em></td>
    </tr>
    <tr>
        <td><input type=text name='doptext' style='width:100%' value='<? echo $member['doptext'] ?>'></td>
    </tr>
    <tr>
        <td>
          <p>Раздел мультишаблона *:</p>
        </td>
    </tr>
    <tr>
        <td><select id='multiid' name='multiid' title="Выбор раздела необходим в случае использования мультишаблона. Если мультишаблон не используется - раздел будет по умолчанию.">
        <? 
          echo "<option value='0'>По умолчанию</option>";
          $know = mysql_query("SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
          while($knowmember = mysql_fetch_array($know))
          {
            if ($member['multiid']==$knowmember['id'])
             echo "<option value='".$knowmember['id']."' selected>".$knowmember['name']."</option>";
            else
             echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
          }
        ?>
        </select></td>
    </tr>    
</table></div>
<p></p>
<table align='center' width='100%' border="0" cellpadding=3 cellspacing=3>
  <tr align="center"><td>
     <input id='ok' type="submit" value="Изменить параметр">
   </td></tr>    
</table>
</form>
</td></tr></table>
</body></html>

<?
}
}
} else die;
?>
