<?php
 if(!defined("IN_ADMIN")) die;  
 
 include "config.php";
 include "func.php";

 $title = "Отправить всем сообщение";
 $titlepage=$title;  

 include "topadmin.php";

 $action = $_POST["action"];

if (!empty($action)) 
{

  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["title"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не ввели заголовок.\n";
  }

  if (empty($_POST["body"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не ввели сообщение.\n";
  }

  require_once('lib/unicode.inc');

  $tom = $_POST["to"];
  $gst = mysql_query("SELECT * FROM users WHERE usertype='".$tom."' ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  while($member = mysql_fetch_array($gst))
  {

  $toid = $member["id"];
  $toemail = $member["email"];
  $title = $_POST["title"];
  $body = $_POST["body"];
  $fromid = USER_ID;


  $mimeheaders = array();
  $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
  $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
  $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
  $mimeheaders[] = 'From: '. mime_header_encode(USER_FIO.' <'.USER_EMAIL.'>');

 if (!empty($toemail))
 {
  if (!mail(
      $toemail,
      mime_header_encode($title),
      str_replace("\r", '', $body),
      join("\n", $mimeheaders)
    )) {

  puterror("Ошибка при отправке сообщения.");
 } else {

     $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        $fromid,
                                        '$title',
                                        '$body',0,NOW());";
     if(!mysql_query($query))
      puterror("Ошибка при обращении к базе данных.");
    }
   
  }
 }

      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=index.php?op=welcome'>\n";
      print "</HEAD></HTML>\n";
 
}

if (empty($action)) 
{
  $helppage='';
  $tom = $_GET['to'];
  
?>


<form action=msgs method=post>
<input type=hidden name=action value=post>
<p align='center'><table class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td width="400"><p class=ptd>Кому:</p></td>
        <? if ($tom=='user') echo('<td>Всем участникам</td>'); else echo('<td>Всем экспертам</td>'); ?>
    </tr>
    <tr>
        <td><p class=ptd>Заголовок</p></td>
        <td><input type=text name=title size=25></td>
    </tr>
    <tr>
        <td><p class=ptd>Сообщение</p></td>
        <td><textarea name="body" cols="30" rows="5"></textarea></td>
    </tr>
    <tr>
        <td colspan="3">
            <input type="submit" value="Отправить сообщение">&nbsp;
            <input type="button" name="close" value="Назад" onclick="history.back()"> 
        </td>
    </tr>           
</table></p>
<input type=hidden name='to' value=<?php echo $tom; ?>>
</form>

<?
include "bottomadmin.php";
}
?>