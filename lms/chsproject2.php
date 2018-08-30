<?php
 if(defined("IN_USER") or defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
 
 include "config.php";
 include "func.php";


 $title = "Изменение статуса проекта";
 $titlepage=$title;  


 $action = $_POST["action"];

if (!empty($action)) 
{

   $paid = $_POST["paid"];

   // Сразу обновим статус проекта
   if(mysqli_query($mysqli, "UPDATE projects SET status = '".$_POST["status"]."' WHERE id='".$_POST["id"]."';"))
   {
      
      writelog("Изменен статус проекта №".$_POST['id']." - ".$_POST["status"].".");
      
      require_once('lib/unicode.inc');
      
      // Отправка на экспертизу - модератору шаблона на проверку
      if ($_POST["status"]=="accepted") {


       $gst2 = mysqli_query($mysqli, "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
       if (!$gst2) puterror("Ошибка при обращении к базе данных");
       $owner = mysqli_fetch_array($gst2);

       $gst4 = mysqli_query($mysqli, "SELECT * FROM users WHERE id='".$owner['ownerid']."' LIMIT 1");
       $admin = mysqli_fetch_array($gst4);
      
       $toid = $admin['id'];
       $toemail = $admin['email'];

       $title = "Проект №".$_POST["id"]." подготовлен к проверке.";

       $body = msghead($owner['name'], $site);
       $body .= '<p>Проект №'.$_POST["id"].' подготовлен к проверке на корректность перед экспертизой.</p>
       <p>После проверки проекта №'.$_POST["id"].' необходимо у проекта изменить статус - проходит экспертизу (все эксперты будут оповещены о начале экспертизы актоматически).</p>';

       $body2 = '<p>Проект №'.$_POST["id"].' подготовлен к проверке на корректность перед экспертизой.</p>
       <p>После проверки проекта №'.$_POST["id"].' необходимо у проекта изменить статус - проходит экспертизу (все эксперты будут оповещены о начале экспертизы актоматически).</p>';
       
       $body .= msgtail($site);

       $fromid = USER_ID;
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
       )) puterror("Ошибка при отправке сообщения.");
       
       }
      
       $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        $fromid,
                                        '$title',
                                        '$body2',0,NOW());";
       if(!mysqli_query($mysqli,$query))
        puterror("Ошибка при обращении к базе данных.");

       mysqli_free_result($gst2);

      }
      else
      // Проект проверен - отправим сообщение экспертам
      if ($_POST["status"]=="inprocess") {

      $proj = mysqli_query($mysqli, "SELECT info FROM projects WHERE id='".$_POST['id']."' LIMIT 1;");
      if (!$proj) puterror("Ошибка при обращении к базе данных");
      $pr = mysqli_fetch_array($proj);
      $projinfo = $pr['info'];
      mysqli_free_result($proj);

      $gst = mysqli_query($mysqli, "SELECT * FROM proexperts WHERE proarrid='".$paid."' ORDER BY id");
      if (!$gst) puterror("Ошибка при обращении к базе данных");
      
      while($member = mysqli_fetch_array($gst))
      {

      $gst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['expertid']."' LIMIT 1");
      $user = mysqli_fetch_array($gst2);

      $toid = $user["id"];
      $toemail = $user["email"];
      $title = "Проект №".$_POST["id"]." подготовлен к экспертизе.";

      $body = msghead($user["userfio"], $site);
      $body .= '<p>Проект №'.$_POST["id"].' <strong>'.$projinfo.'</strong> подготовлен к экспертизе. Требуется экспертная оценка проекта.</p>
      <p>Помните, что экспертный лист можно сохранить только один раз!</p>';
      $body .='<p><a href="'.$site.'/page&id=60" title="Инструкция - экспертиза проектов" target="_blank">Подробнее о процедуре экспертизы</a>.</p><p>В зависимости от условий, перед началом экспертизы, для подтверждения статуса <strong>эксперта</strong>, Вам может быть предложено также пройти онлайн тестирование.</p>';
      $body .= msgtail($site);

      $body2 = '<p>Проект №'.$_POST["id"].' '.$projinfo.' подготовлен к экспертизе. Требуется экспертная оценка проекта.</p>
      <p>Помните, что экспертный лист можно сохранить только один раз!</p>';
      $body2 .='<p><a href="'.$site.'/page&id=60" title="Инструкция - экспертиза проектов" target="_blank">Подробнее о процедуре экспертизы</a>.</p><p>В зависимости от условий, перед началом экспертизы, для подтверждения статуса <strong>эксперта</strong>, Вам может быть предложено также пройти онлайн тестирование.</p>';

      $fromid = USER_ID;
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
          )) puterror("Ошибка при отправке сообщения.");

          $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        $fromid,
                                        '$title',
                                        '$body2',0,NOW());";
          if(!mysqli_query($mysqli, $query))
            puterror("Ошибка при обращении к базе данных.");
   
       }
  
      }
      }
      echo "<script>parent.closeFancyboxAndRedirectToUrl('".$site."/projects');</script>"; 
   } 
   else  
   {
    echo '<script language="javascript">';
    echo 'alert("Ошибка при обращении к базе данных");
    parent.closeFancyboxAndRedirectToUrl("'.$site.'/projects");';
    echo '</script>';
   }
   
}
else
if (empty($action)) 
{
  
  $topage = $_GET['to'];
  $id = $_GET['id'];

  // Получим ИД шаблона

  $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE id='".$id."' LIMIT 1");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  
  $tableheader = "class=tableheader";
  $showhide = "";
  $tableheader = "class=tableheaderhide";
  $member = mysqli_fetch_array($gst);
  $paid = $member['proarrid'];
  
  require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script>
      $(function() {
          $( "#ok" ).button();
          $( "#status" ).selectmenu({ width: 300});
      });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменение статуса проекта</p></h3>
 <?  if ($member['status']=='created') {?>
    <div class="ui-widget">	
      <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">		
        <p>
          <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;">
          </span>		Внимание! Изменение статуса проекта приведет к невозможности его изменения и удаления!
        </p>	
      </div>
    </div>
   <? } ?> 
<p>
<div id="menu_glide" class="menu_glide">
<table align="center" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<form action=chsproject2 method=post>
<input type=hidden name=action value=post>
<input type=hidden name=id value=<?php echo $id; ?>>
<input type=hidden name=paid value=<?php echo $paid; ?>>
<?   if (!empty($topage)) {
?>
 <input type=hidden name="topage" value="<? echo $topage; ?>">
 <input type=hidden name="paname" value="<? echo $paname; ?>">
<? } ?> 
    <tr>
        <td><p class=ptd><b>Наименование проекта:</b></p></td><td><p class=ptd><? echo $member['info']; ?></p></td>
    </tr>
    <tr>
        <td><p class=ptd><b>Изменить статус проекта *:</b></p></td>
        
        <?
        echo"<td><select id='status' name='status'>";

        if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
        {
          echo"<option value='created'>Создание проекта</option>";
        }
        
        if ($member['status']=='created') 
        {
         // В зависимости от настроек шаблона - либо отправляем на проверку модератору, либо сразу экспертам
         $gst2 = mysqli_query($mysqli,"SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
         if (!$gst2) puterror("Ошибка при обращении к базе данных");
         $owner = mysqli_fetch_array($gst2);
         if ($owner['moderatorverify']==1) {
          echo"<option value='accepted'>Подготовлен к экспертизе</option>";
         } 
         else {
          echo"<option value='inprocess'>Подготовлен к экспертизе</option>";
         }
        } 

        if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
        {
         if ($member['status']=='accepted')
          echo"<option value='inprocess'>Проходит экспертизу</option>";
        }
        
        if ($member['status']=='finalized') {
         echo"<option value='published'>Публикация проекта</option>";
         if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
          echo"<option value='inprocess'>Возврат к прохождению экспертизы</option>";
        }
        
        if ($member['status']=='published') {
          echo"<option value='finalized'>Отменить публикацию проекта</option>";
        }
        
        echo"</select></td>";   
        ?>
        
    </tr><tr align="center">
        <td colspan="3">
            <input id='ok' type="submit" value="Изменить статус">&nbsp;
        </td>
    </tr>           

</form></table></div></p>

<?
 echo "</td></tr></table>";
 echo "</body></html>";
}
}
else die;  
?>