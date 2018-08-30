<?php
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Список новостей и страниц";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  ?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Новости публикуются на главной странице сервиса. Новости в системе - это анонсы мероприятий, дополнительная информация о конкурсах и тестовых испытаниях, публикация открытых рейтингов и т.д. На страницах можно размещать дополнительную информацию, а затем делать ссылки на новостных страницах. Все новости и страницы проходят внутреннюю модерацию перед публикацией.</p>
  </div>
</div>  <?

  echo"<p align='center'><a class=link href='addnews&mode=news'><i class='fa fa-newspaper-o fa-lg'></i> Добавить новость
  </a>";
  echo" <a class=link href='addnews&mode=page'><i class='fa fa-newspaper-o fa-lg'></i> Добавить страницу
  </a></p><p align='center'>";

  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  if ($start < 0) $start = 0;

  if(defined("IN_ADMIN"))
   $gst = mysql_query("SELECT * FROM news ORDER BY ndate DESC");
  else
   $gst = mysql_query("SELECT * FROM news WHERE userid='".USER_ID."' ORDER BY ndate DESC");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
?>
<script>

  function gomodarator(id,name){
   $("#spinner").fadeIn("slow");
   name = ''+name;
   $.post('moderatornewsjson',{newsid:id, newsname:name},  
    function(data){  
      eval('var obj='+data);         
      $("#spinner").fadeOut("slow");
      if(obj.ok=='1') 
       alert("Новость отправлена на модерацию.");
      else 
       alert("Ошибка при отправке сообщения о модерации.");
    }); 
  } 

  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
      collapsible: true
    });
  });
</script>
  
  <div id="accordion">
  <?         
  
  $pt = array ('Новость'=>'news','Страница'=>'page'); 
  
  while($member = mysql_fetch_array($gst))
  {
    echo "<h3>".array_search($member['pagetype'], $pt)." от ".data_convert ($member['ndate'], 1, 0, 0)." - ".$member['name'];
    if (!$member['published']) 
     echo "<font color='#FF0000'> - Не опубликована</font> <a href='javascript:;' onclick=\"gomodarator(".$member['id'].",'".$member['name']."');\"><i class='fa fa-external-link-square fa-lg' title='Отправить на модерацию'></i></a>";
    echo "</h3>";
    echo "<div>";
    if (!empty($member['picurl']))
     echo "<img src='".$member['picurl']."' height='35'>";
    echo " <a class='menu' href=editnews&id=".$member['id']." title='Редактировать'><p class=zag2>".$member['name']." <i class='fa fa-pencil fa-lg'></i></a>";
    if (defined("IN_ADMIN"))
    {
      ?>&nbsp;<a href="#" onClick="DelWindow(<? echo $member['id'];?> ,<? echo $start;?>,'delnews','newses','новость или страницу')" title="Удалить"><i class='fa fa-trash fa-lg'></i></a></p>
      <?
    }
    else
    {
     if (!$member['published']) 
     {
      ?>&nbsp;<a href="#" onClick="DelWindow(<? echo $member['id'];?> ,<? echo $start;?>,'delnews','newses','новость или страницу')" title="Удалить"><i class='fa fa-trash fa-lg'></i></a></p>
      <?
     }
    }
    if ($member['pagetype']=='page' and $member['published'])
     echo "<p><font size=-2><a href='page&id=".$member['id']."' target='_blank' title='Ссылка на страницу'>page&id=".$member['id']."</a></font></p>";
    if(defined("IN_ADMIN") && $member['userid']!=USER_ID)
     echo "<p><a class='menu' href=edituser&utype=user&id=".$member['userid']."><p class=zag2>Пользователь №".$member['userid']."</a></p>";
    echo "</div>";
  }
    echo "</div></p>";
  ?>
    <div id="spinner"></div>
  <?  
  include "bottomadmin.php";
  } else die;
?>