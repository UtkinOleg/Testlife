<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Экспертные листы и настройка критериев модели проекта";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
    maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);

    ?>
    
<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
  
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    

	 $(document).ready(function() {

    	$("#addshablon").click(function() {
				$.fancybox.open({
					href : 'addshablon&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 800,
          height : 600,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#viewshablon").click(function() {
				$.fancybox.open({
					href : 'viewshablon&paid=<? echo $paid ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});

    	$("#addkgroup").click(function() {
				$.fancybox.open({
					href : 'addkgroup&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 500,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#addkgroup2").click(function() {
				$.fancybox.open({
					href : 'addkgroup&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 500,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#addexgroup").click(function() {
				$.fancybox.open({
					href : 'addexgroup&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 500,
          height : 150,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

			});
      
 $(function() {
  $( "#tabs" ).tabs({
   active: <? if (empty($_GET['tab'])) echo '0'; else echo $_GET['tab']; ?>
  });
 });
      
</script>

<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}
</style>
      
<?

  $tot2 = mysql_query("SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paid."'");
  $tot2ee = mysql_fetch_array($tot2);
  $countpr = $tot2ee['count(*)'];

if ($countpr>0){  

?>

            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Редактирование настроек экспетрных листов запрещено! Уже проведены экспертизы проектов.</p>
            	</div>
            </div><p></p> 
 <? } ?>        

<div id="tabs">
  <ul>
    <li><a href="#Tab1">Экспертные листы</a></li>
    <li><a href="#Tab2">Группы критериев</a></li>
    <li><a href="#Tab3">Критерии</a></li>
  </ul>
<div id="Tab1">

<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Настройка экспертных листов для проведения экспертизы проекта построена по трехуровневой модели. Первый уровень - необходимо определиться с количеством экспертных листов в модели проекта. Разделение критериев на экспертные листы может понадобиться, например, при проведении экспертизы проектов в разные дни. Если экспертный лист не указывается - тогда система предполагает, что экспертный лист всего один.</p>
	</div>
</div>
  <?
  
    echo "<p align='center'>";

if ($countpr==0){ 
      ?>
       <a title="Добавить экспертный лист" id="addexgroup" href="javascript:;"><i class='fa fa-thumbs-o-up fa-lg'></i> Добавить экспертный лист</a></p>
      <?
}

  $tot = mysql_query("SELECT count(*) FROM expertcontentnames WHERE proarrid='".$paid."'");
  $gst = mysql_query("SELECT * FROM expertcontentnames WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");

  $tot2cnt = mysql_fetch_array($tot);
  $countkg = $tot2cnt['count(*)'];
  
  if ($countkg>0) {
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" align="center" border="0" cellpadding=0 cellspacing=3 bordercolorlight=gray bordercolordark=white>
          <tr>
              <td witdh='100'><p>1</p></td>
              <td><p>Экспертный лист по умолчанию</p></td>
          </tr>   
     <?         

  $i=1;
  while($member = mysql_fetch_array($gst))
  {

?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editexgroup<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editexgroup&paid=<? echo $paid ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 500,
          height : 150,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?  

    echo "<tr><td witdh='100'><p>".++$i."</p></td>";
    if ($countpr==0){  
     ?>
     <td><p><a title="Редактировать экспертный лист" id="editexgroup<? echo $member['id'] ?>" href="javascript:;"><? echo $member['name']; ?> <i class='fa fa-pencil fa-lg'></i></a>
     <?
     $totkrit = mysql_query("SELECT count(*) FROM shablongroups WHERE proarrid='".$paid."' AND exlistid='".$member['id']."'");
     if (!$totkrit) puterror("Ошибка при обращении к базе данных");
     $totkr = mysql_fetch_array($totkrit);
     $countkrit = $totkr['count(*)'];
     mysql_free_result($totkrit);
     if ($countkrit==0) {  
     ?>
     &nbsp;
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delexgroup','shablons','экспертный лист')" title="Удалить экспертный лист"><i class='fa fa-trash fa-lg'></i></a>
     <?
     }
     echo "</p></td>";
    }
    else
    {
     echo "<td><p>".$member['name']."</p></td>";
    }
  }
    echo "</table></div>";
  }  
    echo "</p></div>";

  // Группы Критериев ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

  echo "<div id='Tab2'>";
  ?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Второй уровень - это разделение критериев на группы. В каждой группе критериев определяется максимальный балл. Определяется также принадлежность группы к экспертному листу. В отличие от экспертного листа, необходимо присутствие хотябы одной группы критериев в модели проекта.</p>
	</div>
</div>
  <?
  
  echo "<p align='center'>";
  
  if ($countpr==0){  

      ?>
       <a title="Добавить новую группу критериев" id="addkgroup" href="javascript:;"><i class='fa fa-navicon fa-lg'></i> Добавить новую группу критериев</a></p>
      <?

//    echo"<p align='center'>
//    <a class=link href='addkgroup&paid=".$paid."'><img src='img/b_newtbl.png'>
//    &nbsp;Добавить новую группу критериев</a></p><p align='center'>";
  }
  
  $tot = mysql_query("SELECT count(*) FROM shablongroups WHERE proarrid='".$paid."'");
  $gst = mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");

  $tot2cnt = mysql_fetch_array($tot);
  $countkg = $tot2cnt['count(*)'];
  
  if ($countkg>0) {
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" align="center" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {

?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editkgroup<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editkgroup&paid=<? echo $paid ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 500,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?  

    echo "<tr><td witdh='50'><p align='center'>".++$i."</p></td>";
    if ($countpr==0){  
     ?>
     <td><a title="Редактировать группу критериев" id="editkgroup<? echo $member['id'] ?>" href="javascript:;"><? echo $member['name']; ?> <i class='fa fa-pencil fa-lg'></i></a>
     <?
     $totkrit = mysql_query("SELECT count(*) FROM shablon WHERE proarrid='".$paid."' AND groupid='".$member['id']."'");
     if (!$totkrit) puterror("Ошибка при обращении к базе данных");
     $totkr = mysql_fetch_array($totkrit);
     $countkrit = $totkr['count(*)'];
     mysql_free_result($totkrit);
     if ($countkrit==0) {  
     ?>
     &nbsp;
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delkgroup','shablons','группу критериев')" title="Удалить группу критериев"><i class='fa fa-trash fa-lg'></i></a>
     <?
     }
     echo "</p></td><td align='left'><p>Максимальный балл: ".$member['maxball']."</p></td>";
    }
    else
     echo "<td><p>".$member['name']."</p></td><td align='left'>Максимальный балл: ".$member['maxball']."</td>";

    if ($member['exlistid']>0)
    {
    $gstex = mysql_query("SELECT name FROM expertcontentnames WHERE id='".$member['exlistid']."' LIMIT 1;");
    $memberex = mysql_fetch_array($gstex);
    echo "<td align='left'><p>Экспертный лист ".$memberex['name']."</p></td>";
    mysql_free_result($gstex);
    }
    else echo "<td align='left'><p>Экспертный лист по умолчанию</p></td>";
  }
    echo "</table></div>";
  }  
    echo "</p></div>";


  // Критерии ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  
  echo"<div id='Tab3'>";
  ?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Третий уровень - это настройка самих критериев. Критерии могут быть трех типов. Первый - выбор балла - оценка критерия устанавливается через слайдер. Цифровой критерий - оценка производится путем ввода числа. Составной критерий позволяет разделить критерий на несколько составных и назначать на каждый отдельный балл увеличения или уменьшения оценки. Критерии привязаны к группам. Суммарный балл за каждый критерий равен максимальному баллу в группе.</p>
	</div>
</div>
  <?
  
  echo "<p align='center'>";

  $tot22 = mysql_query("SELECT count(*) FROM shablon WHERE proarrid='".$paid."'");
  if (!$tot22) puterror("Ошибка при обращении к базе данных");
  $tot22sh = mysql_fetch_array($tot22);
  $countsh = $tot22sh['count(*)'];
  mysql_free_result($tot22);                         
  
  if ($countkg>0) {
   if ($countpr==0){

   if (LOWSUPERVISOR) // Для бесплатного супервизора - два критерия
   {
     if ($countsh < 2) {
      ?>
       <a title="Добавить новый критерий" id="addshablon" href="javascript:;"><i class='fa fa-star fa-lg'></i> Добавить новый критерий</a>
      <?
     } 
     if ($countsh>0)
     {
     ?>
     &nbsp;|&nbsp;<a id="viewshablon" href="javascript:;"><i class='fa fa-search fa-lg'></i> Просмотр листа</a>
     &nbsp;|&nbsp;<a href="#" onClick="DelWindow(<? echo $paid;?>,0,'delallshablon','','')"><i class='fa fa-trash fa-lg'></i> Удалить все</a>
     <?
     }
   }
   else
   {
    ?>
       <a title="Добавить новый критерий" id="addshablon" href="javascript:;"><i class='fa fa-star fa-lg'></i> Добавить новый критерий</a>
    <?
    if ($countsh>0)
    {
    ?>
     &nbsp;|&nbsp;<a id="viewshablon" href="javascript:;"><i class='fa fa-search fa-lg'></i> Просмотр листа</a>
     &nbsp;|&nbsp;<a href="#" onClick="DelWindow(<? echo $paid;?>,0,'delallshablon','','')"><i class='fa fa-trash fa-lg'></i> Удалить все</a>
    <?
    }
   }
  }
  echo "</p>";
  
  $gstk = mysql_query("SELECT * FROM shablon WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gstk) puterror("Ошибка при обращении к базе данных");

  if ($countsh>0) {

  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" align="center" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
     <?         


  $i=0;
  while($memberk = mysql_fetch_array($gstk))
  {
?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editshablon<?php echo $memberk['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editshablon&paid=<? echo $paid ?>&id=<? echo $memberk['id'] ?>',
					type : 'iframe',
          width : 800,
          height : 600,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    	$("#edcomplex<?php echo $memberk['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'edcomplex&paid=<? echo $paid ?>&id=<? echo $memberk['id'] ?>',
					type : 'iframe',
          width : 800,
          height : 600,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?  
  
    echo "<tr><td witdh='50'><p align='center'>".++$i."</p></td>";
    if ($countpr==0){

      ?>
       <td><a title="Редактировать критерий" id="editshablon<? echo $memberk['id'] ?>" href="javascript:;"><? echo $memberk['name']; ?> <i class='fa fa-pencil fa-lg'></i></a>
      <?
    
//    echo "<td width='300'><a class='menu' href='editshablon&paid=".$paid."&id=".$member['id']."' title='Редактировать'><p class=zag2>".$member['name']."</a></td><td>";
    
    if ($memberk['complex']==1) { 
     echo " <a href='javascript:;' id='edcomplex".$memberk['id']."' title='Изменить параметры составного критерия'><i class='fa fa-cogs fa-lg'></i></a>";
    }
    
    ?>
     &nbsp;<a href="#" onClick="DelWindowPaid(<? echo $memberk['id'];?> ,<? echo $paid;?>,'delshablon','shablons','критерий')" 
     title="Удалить критерий"><i class='fa fa-trash fa-lg'></i></a>
     </td>
    <?
    }
    else
    {
     echo "<td><p>".$memberk['name']."</p></td>";
    }
    $gst2 = mysql_query("SELECT * FROM shablongroups WHERE id='".$memberk['groupid']."'");
    $member2 = mysql_fetch_array($gst2);
    echo "<td align='left'><p>Группа: ".$member2['name']."</p></td>";
    echo "<td align='left'><p>Максимальный балл: ".$memberk['maxball']."</p></td></tr>";
    mysql_free_result($gst2);
  }
  echo "</table></div>";
  }
  }
  else
  {
  if ($countpr==0){
  ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Не установлены группы критериев. Перед установкой критериев необходимо определить группу. <a title="Добавить новую группу критериев" id="addkgroup2" href="javascript:;">Добавить новую группу критериев</a></p>
            	</div>
           </div>    
  <?
  }
  }
  echo "</p></div>";
}
  include "bottomadmin.php";
} else die;  
?>