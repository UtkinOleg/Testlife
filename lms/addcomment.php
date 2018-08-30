<?php
if(defined("IN_ADMIN") or defined("IN_EXPERT") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";


  $error = "";
  $action = "";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  // Проверяем совпадает ли идентификатор сессии с
  // переданным в форме - защита а авто-постинга
  $id = $_POST["id"];
  $paid = $_POST["paid"];
  $expertid = USER_ID;
  $content = $_POST["content"];

  // Проверяем правильность ввода информации в поля формы
  if (empty($content)) 
  {
    $action = ""; 
    $error = $error." Вы не заполнили комментарий.";
  }
   
  if (!empty($action)) 
  {
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO comments VALUES (0, $id, $expertid, NOW(), '$content', 0);";
    if(mysql_query($query))
    {

    // Отправить сообщение участнику - добавлен комментарий к проекту
    $res1=mysql_query("SELECT * FROM users WHERE id='".$expertid."'");
    if(!$res1) puterror("Ошибка 3 при изменении данных.");
    $expert = mysql_fetch_array($res1);

    $res2=mysql_query("SELECT userid FROM projects WHERE id='".$id."'");
    if(!$res2) puterror("Ошибка 3 при изменении данных.");
    $project = mysql_fetch_array($res2);

    $res3=mysql_query("SELECT * FROM users WHERE id='".$project['userid']."'");
    if(!$res3) puterror("Ошибка 3 при изменении данных.");
    $user = mysql_fetch_array($res3);

    $toemail = $user['email'];
    $title = "Добавлен комментарий к проекту";

    $body = msghead($user['userfio'], $site);
    $body.='<p>К проекту <strong>'.$project['info'].'</strong> добавлен новый комментарий от эксперта '.$expert['userfio'].':</p>';
    $body.='<p>'.$content.'</p>';
    $body .= msgtail($site);


    require_once('lib/unicode.inc');

    $mimeheaders = array();
    $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
    $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
    $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
    $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

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
      
      // Возвращаемся на главную страницу если всё прошло удачно
      echo "<script>parent.closeFancyboxAndRedirectToUrl('".$site."/projects&paid=".$paid."');</script>"; 
      exit();
    }
    else
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибка при выполнении запроса.");
      parent.closeFancybox();';
      echo '</script>';
      exit();
    }
  }
  else
  {
   echo '<script language="javascript">';
   echo 'alert("Ошибки: '.$error.'");
   parent.closeFancybox();';
   echo '</script>';
   exit();
  }  
  
}
else
{

  $pid = $_GET["id"];
  $paid = $_GET["paid"];


require_once "header.php"; 
?>
<script type="text/javascript">
 $(function(){
   $("#ok").button();
 });
</script> 
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
</style>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Добавить комментарий</p></h3>
<center>
<div id="menu_glide" class="menu_glide">
<table width='100%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<?

  if(defined("IN_ADMIN") or defined("IN_EXPERT") or defined("IN_SUPERVISOR"))
   $gst = mysql_query("SELECT * FROM projects WHERE id='".$pid."'");
  else
   $gst = mysql_query("SELECT * FROM projects WHERE id='".$pid."' AND userid='".USER_ID."'");

  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $member = mysql_fetch_array($gst);

  if ($member['status']!='finalized' or $member['status']!='published') 
  {
   // Добавим поле комментария для эксперта
   if ($member['userid']!=USER_ID) 
   {
   $res4=mysql_query("SELECT * FROM projectarray WHERE id='".$member['proarrid']."'");
   if(!$res4) puterror("Ошибка 3 при изменении данных.");
   $projectarray = mysql_fetch_array($res4);
   if ($projectarray['addcomment']==1) {
    ?>

<form action="addcomment" method="post" enctype="multipart/form-data">
<input type=hidden name="action" value="post">
<input type=hidden name="paid" value="<? echo $paid ?>">
<input type=hidden name="id" value="<? echo $pid ?>">
<tr><td width='100%'><textarea name='content' style='width:100%' rows='10'></textarea></td></tr>
     <tr align='center'><td>
       <input id='ok' type='submit' value='Добавить'>
     </td></tr></form>
</table>
</form>
</table></div>
</center></td></tr></table>
</body></html>
   <?
     
    }
   }
  }

}
}
else die;

?>
