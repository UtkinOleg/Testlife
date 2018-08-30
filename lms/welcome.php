<?php
  if(!defined("USER_REGISTERED")) die;  

  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $title=$titlepage= USER_FIO." (".USER_STATUS.")";

  include "topadmin.php";

  if (defined("IN_SUPERVISOR"))
  {
   if (LOWSUPERVISOR) // Для бесплатного супервизора - три участника
   {
  ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Тариф бесплатный</strong> Можно создать только одну модель с ограниченными функциями: максимум два параметра в шаблоне проекта, один вычисляемый показатель, максимум два критерия в экспертном листе, один эксперт в системе, один тест и максимум три участника (тестируемых). Также закрыт доступ к расширенной аналитике по экспертизе проектов и тестированию. Сменить на тариф <a title="Сменить на тариф Ограниченный" id="limit" href="javascript:;"><strong>Ограниченный</strong></a> или сменить на тариф <a title="Сменить на тариф Базовый" id="base" href="javascript:;"><strong>Базовый</strong></a></p>
            	</div>
           </div><p></p>    
  <?
   }
   else
   {
    // Проверим есть ли супервизор в списке ограниченных
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM limitsupervisor WHERE userid='".USER_ID."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $countlim = $tot2cnt['count(*)'];  
    mysqli_free_result($tot2);                         
    if ($countlim > 0)
    {
     // ограниченный супервизор - двадцать участников
  ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Тариф ограниченный для моделей: <? echo $countlim; ?>.</strong> Максимум пять экспертов и двадцать участников. Отключено голосование, открытая экспертиза и платный конкурс. Сменить на тариф <a title="Сменить на тариф Базовый" id="base" href="javascript:;"><strong>Базовый</strong></a></p>
            	</div>
           </div><p></p>    
  <?
    }
   }
  }


  $iwant = $_GET["iwant"];
  if (empty($iwant)) $iwant=0;


     ?>
<script type="text/javascript">

	$(document).ready(function() {
			
      var iwant=<? echo $iwant; ?>;
      if (iwant==1)
      {
				$.fancybox.open({
					href : 'addproarr',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
      }
      else
      if (iwant==2)
      {
				$.fancybox.open({
					href : 'iwant',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
      }
      
      $("#iwant").click(function() {
				$.fancybox.open({
					href : 'iwant',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
   
   }); 

   function createModel(){
        $.fancybox.close();
        location.replace("<? echo $site ?>/welcome&iwant=1");
   }

   function closeFancybox(){
     $.fancybox.close();
   }    
</script>     
     <?


  if (defined("IN_USER"))
  {
    echo "<table border='0' cellpadding='10' cellspacing='10' align='center'>";
    echo "<tr valign='top' align='center'><td valign='top'>";
    echo "<div id='menu_glide' class='menu_glide_shadow'>
    <table border='0' cellpadding='5' cellspacing='5' width='230'>
    <tr valign='middle' align='center'><td valign='center' width='230'>";
    echo "<a href='edituser' class=link>";
   
    $tot2 = mysql_query("SELECT email,photoname,id FROM users WHERE id='".USER_ID."'");
    if (!$tot2) puterror("Ошибка при обращении к базе данных");
    $total2 = mysql_fetch_array($tot2);
    $email = $total2['email'];

        if (!empty($total2['photoname'])) 
        {
          if (stristr($total2['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$total2['id'].$total2['photoname']."' height='50'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$total2['photoname']."' height='50'></div>"; 
        } 
        else  
         echo "<i class='fa fa-user fa-3x'></i>";
    mysql_free_result($tot2);
           
    echo"<p>Мой профиль</a></p>";
    echo"<p><a href='chpass' class=link>Изменить пароль</a></p>";

    echo "</tr></td></table></div>";
    echo "</td><td valign='top'>";
    echo "<div id='menu_glide' class='menu_glide_shadow'>
    <table border='0' cellpadding='10' cellspacing='10' width='230'>
    <tr valign='middle' align='center'><td valign='center' width='230'>";
    echo "<a href='projects' class=link><i class='fa fa-book fa-3x'></i>";

    $tot2 = mysql_query("SELECT count(*) FROM projects WHERE userid='".USER_ID."'");
    if (!$tot2) puterror("Ошибка при обращении к базе данных");
    $total2 = mysql_fetch_array($tot2);
    $count2 = $total2['count(*)'];
    if ($count2==0)
     echo"<p>Мои проекты</a></p>";
    else 
     echo"<p>Мои проекты (".$count2.")</a></p>";
    mysql_free_result($tot2);

    echo "</tr></td></table></div>";

    echo "</td></tr>";
    echo "<tr valign='top' align='center'><td valign='top'>";
    echo "<div id='menu_glide' class='menu_glide_shadow'>
    <table border='0' cellpadding='10' cellspacing='10' width='230'>
    <tr valign='middle' align='center'><td valign='center' width='230'>";
    echo "<a href='usermsgs' class=link><i class='fa fa-envelope-o fa-3x'></i>";
    echo"<p>Мои сообщения</a></p>";

  $tot2 = mysql_query("SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed='0'");
  if (!$tot2) puterror("Ошибка при обращении к базе данных");
  $total2 = mysql_fetch_array($tot2);
  $count2 = $total2['count(*)'];
  mysql_free_result($tot2);
  
  if ($count2>0) {
   echo"<p><font face='Tahoma,Arial' color='#FF0F0'>Вам пришли новые сообщения (".$count2.").</font></p>";
  }
  
    echo "</tr></td></table></div>";
    echo "</td><td valign='top'>";
    echo "<div id='menu_glide' class='menu_glide_shadow'>
    <table border='0' cellpadding='10' cellspacing='10' width='230'>
    <tr valign='middle' align='center'><td valign='center' width='230'>";
    echo "<a href='newprojects' class=link><i class='fa fa-pencil fa-3x'></i>";
    echo"<p>Создать проект</a></p>";
    echo "</tr></td></table></div>";
    echo "</td></tr></table>";
    
    if (!defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
    
     echo "<table border='0' cellpadding='20' cellspacing='20' align='center'>";
     echo "<tr valign='top' align='center'><td valign='top'>";
     echo "<div id='menu_glide' class='menu_glide_shadow'>
     <table border='0' cellpadding='10' cellspacing='10' width='330'>
     <tr valign='middle' align='center'><td valign='center' width='330'>";
     echo "<a id='iwant' href='javascript:;' title='Обладая правами супервизора, Вы можете самостоятельно организовать новый онлайн конкурс, внешнюю экспертизу различных проектов или онлайн тестирование.'><i class='fa fa-sun-o fa-3x'></i></a>";
     echo"<p>Cтать супервизором бесплатно</p>";
     echo "</tr></td></table></div>";
     echo "</td></tr></table>";
    }
  }
   
  include "bottomadmin.php";
?>