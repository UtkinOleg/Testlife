<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$error = "";
$action = "";

$mode = $_GET["mode"];

if ($mode=='news') 
 $title=$titlepage="Новая новость";
else 
 $title=$titlepage="Новая страница";

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
    $pagetype = $_POST["pagetype"];
    $picurl = $_POST["picurl"];
    $userid = USER_ID;
    $docurl = $_POST["docurl"];
    $docname = $_POST["docname"];
    if(defined("IN_ADMIN"))
     $published=1;
    else 
    {
      $published=0;

      $toemail = $valmail;
      $fio=USER_FIO;
      $title = "Содана новость или страница";
      $body = "Создана новость (страница): ".$name."\n
      пользователем ".$fio;

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode(admin.' <'.$valmail2.'>');

      if (!empty($toemail))
      {
       if (!mail(
        $toemail,
        mime_header_encode($title),
        str_replace("\r", '', $body),
        join("\n", $mimeheaders)
       )) {
           puterror("Ошибка при отправке сообщения.");
          }   
      }  
     
    } 
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO news VALUES (0,
                                        '$name',
                                        '$content',
                                        $userid,
                                        NOW(),
                                        '$pagetype',
                                        '$picurl',
                                        '$docurl',
                                        '$docname',
                                        $published,
                                        '$content2');";
    if(mysqli_query($mysqli,$query))
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
?>
<style>
#feedback { font-size: 1em; }
#selectable .ui-selecting { background: #9CBED4; }
#selectable .ui-selected { background: #9CBED4; }
#selectable { list-style-type: none; margin: 1; padding: 1; width: 90%; }
#selectable li { margin: 3px; padding: 1px; float: left; width: 80px; height: 80px; font-size: 4em; text-align: center; }
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script>
  $(function() {
    $( "#selectable" ).selectable({
        selecting: function(event, ui){
            if( $(".ui-selected, .ui-selecting").length > 1){
                  $(ui.selecting).removeClass("ui-selecting");
            }
        },
        stop: function() {
        $( ".ui-selected", this ).each(function() {
          var index = $(this).attr('id');
          $( "#picurl" ).val( index );
        });   
      }
    });
  });
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
        var projectVal = $("#picurl").val();
        if(projectVal == '') {
            $("#selectable").after('<span class="iferror" style="text-align:center;">Необходимо выбрать картинку!</span>');
            hasError = true;
        }
        if(hasError == true) { return false; }
    });
});
</script>
<form action='addnews' method='post'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='pagetype' value='<? echo $mode; ?>'>
<div id="menu_glide" class="menu_glide">
<table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Заголовок:</em></b></td>
    </tr>
    <tr>
        <td><input type='text' id='name' name='name' size=90 style='width:100%'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Картинка:</em></b></td>
    </tr>
    <input type="hidden" name="picurl" id="picurl" value="">
    <tr><td><ol id="selectable">
        <? 
          echo "<li class='ui-widget-content' id='img/logoexpertbig.jpg'><img src='img/logoexpertbig.jpg' width='80' height='80'></li>";
          $know = mysqli_query($mysqli,"SELECT id,photoname FROM projectarray WHERE ownerid='".USER_ID."' ORDER BY name;");
          while($knowmember = mysqli_fetch_array($know))
            {
             $photoname = $knowmember['photoname'];
             if (!empty($photoname))
             {      
              if (stristr($photoname,'http') === FALSE)    
               echo "<li class='ui-widget-content' id='uploads/pavatars/".$knowmember['id'].$photoname."'><img src='uploads/pavatars/".$knowmember['id'].$photoname."' width='80' height='80'></li>";
              else
               echo "<li class='ui-widget-content' id='".$photoname."'><img src='".$photoname."' width='80' height='80'></li>";
             }
            }
          mysqli_free_result($know);  
        ?>
        </ol></td>
    </tr>    
    <tr>
        <td><p class=ptd><b><em class=em>Ссылка на информационный файл DOC, DOCX, PDF (например - http://yousite.com/doc1.doc):</em></b></td>
    </tr>
    <tr>
        <td><input type='text' name='docurl' size=90 style='width:100%'></td>
    </tr>
    <tr>
        <td><p class=ptd><b><em class=em>Подпись информационного файла:</em></b></td>
    </tr>
    <tr>
        <td><input type='text' name='docname' size=90 style='width:100%'></td>
    </tr>

<tr><td>
    <? if ($mode=='news') {?>
    <p class=ptd>Анонс новости:</p></td></tr>
    <?} else {?>
    <p class=ptd>Содержание страницы:</p></td></tr>
    <?}?>
    <tr><td><textarea name='content' style='width:100%' rows='20'></textarea></td>
    </tr>

    <? if ($mode=='news') { ?>
<tr><td>
    <p class=ptd>Содержание новости:</p>
</td></tr>
<tr><td>
     <textarea name='content2' style='width:100%' rows='20'></textarea>
</td></tr>
    <? } ?>
    
    <tr>
        <td align='center'>
          <? 
           if(defined("IN_ADMIN"))
           { ?>
            <input id="submit1" type="submit" value="Опубликовать">&nbsp;&nbsp;
          <? } else { ?>  
            <input id="submit1" type="submit" value="Сохранить">&nbsp;&nbsp;
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
