<?php
if(defined("IN_ADMIN")) {  

  include "config.php";
  include "func.php";
  
  $pmode = $_GET["mode"];
  
  if ($pmode == 'project')
   $title=$titlepage="Список шаблонов проектов";
  else
   $title=$titlepage="Список шаблонов критериев";
  
  include "topadmin.php";

    ?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 1em;
}
</style>
<script type="text/javascript">

	$(document).ready(function() {
      $( "#addshab" ).button();
			$('.fancybox').fancybox();
    	$("#addshab").click(function() {
				$.fancybox.open({
					href : 'editshab&mode=add&pmode=<? echo $pmode; ?>',
					type : 'iframe',
          width : 800,
          height : 560,
          fitToView : true,
          autoSize : false,
					padding : 5
				});
			});
	});
  
  function closeFancybox(){
    $.fancybox.close();
   }    
</script>    
<?

  echo"<p align='center'><a class=link id='addshab' href='javascript:;'><i class='fa fa-cloud fa-lg'></i>&nbsp;Добавить шаблон
  </a></p><p align='center'>";

  
  $gst = mysql_query("SELECT * FROM adminshab WHERE type='".$pmode."' ORDER BY id;");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable align="center" width="90%" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td><p class=help>Наименование</p></td>
              <td><p class=help>Пояснение</p></td>
              <td><p class=help>Модель</p></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {

    ?>
<script type="text/javascript">

	$(document).ready(function() {
    	$("#editshab<? echo $member['id'] ?>").click(function() {
				$.fancybox.open({
					href : 'editshab&pmode=<? echo $pmode; ?>&mode=edit&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 800,
          height : 560,
          fitToView : true,
          autoSize : false,
					padding : 5
				});
			});
	});
  
</script>    
<?

    echo "<tr>
    <td align='center'><p>".++$i."</p></td>";
    echo "<td><p>".$member['name']." <a id='editshab".$member['id']."' href='javascript:;'><i class='fa fa-pencil fa-lg'></i></a>";
    $pa = mysql_query("SELECT name FROM projectarray WHERE id='".$member['paid']."' LIMIT 1;");
    $paname = mysql_fetch_array($pa);
    $paname1 = $paname['name'];
    mysql_free_result($pa);
    if ($cnt==0){
    ?>
    <a href="#" onClick="DelWindow(<? echo $member['id'];?> ,0 ,'delshab&mode=<? echo $pmode; ?>')" title="Удалить"><i class='fa fa-trash fa-lg'></i></a></p>
    <?
    }
    echo "<td><p>".$member['content']."</p></td>";
    echo "<td><p>".$paname1."</p></td></tr>";
  }
  mysql_free_result($gst);
  echo "</table></div></p>";
  include "bottomadmin.php";
} else die;  
?>