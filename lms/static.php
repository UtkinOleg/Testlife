<?
  if(defined("IN_ADMIN")) 
  {
  include "config.php";
  include "func.php";

  $title = "Статистика по проектам";
  $titlepage=$title;  
  include "topadmin.php";

  $tot = mysql_query("SELECT count(*) FROM users WHERE usertype='user'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countusers = $total['count(*)'];
  
  $tot = mysql_query("SELECT count(*) FROM users WHERE usertype='expert'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countexperts = $total['count(*)'];
  
  $tot = mysql_query("SELECT count(*) FROM users WHERE usertype='supervisor'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countsuper = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM projectarray");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countpa = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM projectarray WHERE openproject=1");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countpaopen = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM projects WHERE status='inprocess'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countinprocess = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM projects WHERE status='finalized'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countfinalized = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM projects WHERE status='published'");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countpublished = $total['count(*)'];

  $tot = mysql_query("SELECT count(*) FROM shablondb");
  if (!$tot) puterror("Ошибка при обращении к базе данных");
  $total = mysql_fetch_array($tot);
  $countexp = $total['count(*)'];
    
  echo"<p align='center'>";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table class="bodytable" border="0" cellpadding="3" cellspacing="3" bordercolorlight="gray" bordercolordark="white">
    <?

  echo "<tr><td><p>Зарегистрированных пользователей (<a href='?op=page&id=6'>участников</a>):</p></td>";
  echo "<td><p>".$countusers."</p></td></tr>";
  echo "<tr><td><p><a href='?op=page&id=7'>Экспертов</a>:</p></td>";
  echo "<td><p>".$countexperts."</p></td></tr>";
  echo "<tr><td><p>Создателей проектов (<a href='?op=page&id=8'>модераторов</a>):</p></td>";
  echo "<td><p>".$countsuper."</p></td></tr>";
  echo "<tr><td><p>Общее количество тем (шаблонов проектов) и экспертных сообществ:</p></td>";
  echo "<td><p>".$countpa."</p></td></tr>";
  echo "<tr><td><p>В том числе с <a href='?op=tops'>открытым рейтингом</a>:</p></td>";
  echo "<td><p>".$countpaopen."</p></td></tr>";
  echo "<tr><td><p>Количество проектов, проходящих экспертизу:</p></td>";
  echo "<td><p>".$countinprocess."</p></td></tr>";
  echo "<tr><td><p>Количество проектов, экспертиза которых завершена:</p></td>";
  echo "<td><p>".$countfinalized."</p></td></tr>";
  echo "<tr><td><p>Количество <a href='?op=public'>опубликованных проектов</a>:</p></td>";
  echo "<td><p>".$countpublished."</p></td></tr>";
  echo "<tr><td><p>Количество проведенных экспертиз:</p></td>";
  echo "<td><p>".$countexp."</p></td></tr>";

  echo "</table></div></p>";

  include "bottomadmin.php";
} else die;  
?>