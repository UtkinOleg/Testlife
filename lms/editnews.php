<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 
// Устанавливаем соединение с базой данных
include "config.php";

$error = "";
$action = "";
$title=$titlepage="Изменить новость или страницу";

include "topadmin2.php";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  
    $name = $_POST["name"];
    $content = htmlspecialchars($_POST["content"], ENT_QUOTES);
    if (!empty($_POST["content2"])) 
     $content2 = htmlspecialchars($_POST["content2"], ENT_QUOTES);
    else
     $content2 =''; 
    $picurl = $_POST["picurl"];
    $userid = USER_ID;
    $docurl = $_POST["docurl"];
    $docname = $_POST["docname"];
    if(defined("IN_ADMIN"))
     $published = 1;
    else 
     $published = 0;

    $query = "UPDATE news SET name='".$name."',
            content='".$content."', 
            picurl='".$picurl."', 
            docurl='".$docurl."', 
            docname='".$docname."',
            published=".$published.", 
            content2='".$content2."' WHERE id=".$_POST["id"];
            
    if(mysql_query($query))
    {


      // Возвращаемся на главную страницу если всё прошло удачно
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=newses'>\n";
      print "</HEAD></HTML>\n";
      exit();
    }
    else
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo "<a href='newses'>Вернуться</a>";
      echo("<P> Ошибка при добавлении участника</P>");
      echo("<P> $query</P>");
      exit();
    }
    
  
}

if (empty($action)) 
{
 $id = $_GET['id'];
 $query = "SELECT * FROM news WHERE id='".$id."' LIMIT 1;";
 $gst = mysql_query($query);
  if ($gst)
    $member = mysql_fetch_array($gst);
  else 
    puterror("Ошибка при обращении к базе данных");
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script>
$(document).ready(function() {

    $( "#submit1" ).button();
    $( "#back" ).button();
 
    $('#submit1').click(function() { 
        $(".iferror").hide();
        var hasError = false;

        if($("#name").val() == '') {
            $("#name").after('<span class="iferror" style="text-align:center;">Необходимо ввести наименование новости или страницы!</span>');
            hasError = true;
        }
        if(hasError == true) { return false; }
    });
});
</script>
<form action=editnews method=post>
<input type=hidden name=id value=<?php echo $id; ?>>
<input type=hidden name=action value=post>
<div id="menu_glide" class="menu_glide">
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Заголовок:</em></b></td>
    </tr>
    <tr>
        <td><input id="name" type='text' name='name' size='90' style='width:100%' value='<? echo $member['name']; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Картинка:</em></b></td>
    </tr>
    <tr>
        <td><img src="<? echo $member['picurl']; ?>" height="40"><input type='hidden' name='picurl' size='90' value='<? echo $member['picurl']; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Ссылка на информационный файл DOC, DOCX, PDF (например - http://yousite.com/doc1.doc):</em></b></td>
    </tr>
    <tr>
        <td><input type='text' name='docurl' size='90' style='width:100%' value='<? echo $member['docurl']; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Подпись информационного файла:</em></b></td>
    </tr>
    <tr>
        <td><input type='text' name='docname' size='90' style='width:100%' value='<? echo $member['docname']; ?>'></td>
    </tr>

<tr><td>
    <? if ($member['pagetype']=='news') {?>
     <p class=ptd>Анонс новости:</p></td></tr>
    <?} else {?>
     <p class=ptd>Содержание страницы:</p></td></tr>
    <?}?>
    <tr><td><textarea name='content' style='width:100%' rows='20'><? echo $member['content'] ?></textarea></td>
    </tr>
<? if ($member['pagetype']=='news') { ?>
<tr><td>
     <p class=ptd>Содержание новости:</p></td></tr>
    <tr><td><textarea name='content2' style='width:100%' rows='20'><? echo $member['content2'] ?></textarea></td>
    </tr>
<? } ?>
    <tr>
        <td align='center'>
          <? if (defined("IN_ADMIN")) {?>
            <input id="submit1" type="submit" value="Изменить">&nbsp;&nbsp;
          <?} else if (!$member['published']) {?>
            <input id="submit1" type="submit" value="Изменить">&nbsp;&nbsp;
          <? } ?>
            <input id="back" type="button" name="close" value="Назад" onclick="history.back()"> 
        </td>
    </tr>           
</table>
</div>
</form>
<?php
include "bottomadmin.php";
}
} else die;
?>
