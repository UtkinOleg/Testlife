<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Настройка вычисляемых показателей шаблона проекта";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);
  

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

?>

<script type="text/javascript">

		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
  
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    window.location = url;
    window.location.reload();
   }    
		  
		$(document).ready(function() {
    	$("#addindicator").click(function() {
				$.fancybox.open({
					href : 'addindicator&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 850,
          height : 350,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    }); 
</script>    
<?

   $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid."' LIMIT 1;");
   $tot2cnt = mysql_fetch_array($tot2);
   $countpr = $tot2cnt['count(*)'];  
   mysql_free_result($tot2);

   maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);

  if ($countpr>0){  

?>

            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Редактирование показателей запрещено! В системе созданы проекты.</p>
            	</div>
            </div><p></p> 
 <? } ?>  
 
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Вычисляемые показатели позволяют на этапе экспертизы подключить к проекту дополнительные параметры на основе параметров указанных в шаблоне проекта. Например, в проекте указывается количество компьютеров в школе и количество учеников, тогда вычисляемым показателем может быть отношение количества компьютеров к количеству учеников в школе. Вычисляемые показатели могут быть на основе четырех арифметических операций.</p>
	</div>
</div>
  <?


  $tot2 = mysql_query("SELECT count(*) FROM pindicator WHERE proarrid='".$paid."'");
  $tot2cnt = mysql_fetch_array($tot2);
  $counti = $tot2cnt['count(*)'];  
  mysql_free_result($tot2);                         

  if ($countpr==0)
  {
   if (LOWSUPERVISOR)
   {
     if ($counti==0)
       echo"<p align='center'><a id='addindicator' href='javascript:;'><i class='fa fa-cube fa-lg'></i>&nbsp;Добавить показатель</a></p>";
   }
   else
    echo"<p align='center'><a id='addindicator' href='javascript:;'><i class='fa fa-cube fa-lg'></i>&nbsp;Добавить показатель</a></p>";
  
  
  }
  echo "<p align='center'>";

  if ($counti>0)
  {
  $gst = mysql_query("SELECT * FROM pindicator WHERE proarrid='".$paid."' ORDER BY id;");
  if (!$gst) puterror("Ошибка при обращении к базе данных");

  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table align="center" width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {
    
?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editind<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'addindicator&mode=edit&paid=<? echo $paid ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 850,
          height : 350,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?      
    $i++;
    echo "<tr><td witdh='50'><p>".$i."</p></td>";
    
    if ($countpr==0)
    {
     echo "<td><p><a id='editind".$member['id']."' href='javascript:;'>".$member['name']." <i class='fa fa-pencil fa-lg'></i></a>";
     ?> <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delindicator','pindicator','показатель')" title="Удалить показатель шаблона проекта"><i class='fa fa-trash fa-lg'></i></a></p>         
     <?
     echo "</td>"; 
    } else
    {
     echo "<td><p>".$member['name']."</p></td>"; 
    }
    if ($member['operation']=='mul') $op=' УМНОЖИТЬ С ';
    else
    if ($member['operation']=='div') $op=' РАЗДЕЛИТЬ НА ';
    else
    if ($member['operation']=='sum') $op=' ПЛЮС ';
    else
    if ($member['operation']=='sub') $op=' МИНУС ';
    
    echo "<td><p>'".$member['indicator1name']."' <b>".$op."</b> '".$member['indicator2name']."'</p></td></tr>";
  }
  echo "</table></div></p>";
  }
  include "bottomadmin.php";
}} else die;  
?>