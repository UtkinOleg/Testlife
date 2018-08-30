<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $paid = $_GET["paid"];
  $id = $_GET["id"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

require_once "header.php"; 
?>
<link rel="stylesheet" href="scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
<link rel="stylesheet" type="text/css" href="scripts/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>
<style>
@import url("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить параметры составного критерия</p></h3>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p>
      <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
     	<div id="content">Составной критерий позволяет разделить критерий на несколько составных и назначать на каждый отдельный балл увеличения или уменьшения оценки. Критерий состоит из нескольких параметров, связанных между собой правилами изменения начисления баллов.
      </div>
    </p>
	</div>
<center>

<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
   function closeFancybox(){
    $.fancybox.close();
   }    
	$(document).ready(function() {
    	$("#addcomp<?php echo $id; ?>").click(function() {
				$.fancybox.open({
					href : 'addcomp&paid=<? echo $paid ?>&id=<? echo $id; ?>',
					type : 'iframe',
          width : 600,
          height : 420,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      });
</script>

 <?

  echo "<p align='center'>
  <a href='javascript:;' id='addcomp".$id."'><i class='fa fa-expand fa-lg'></i>&nbsp;Добавить новый параметр</a></p>
  <p align='center'>";

  $gst = mysql_query("SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$id."' ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <p align="center">
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {
    ?>
<script type="text/javascript">
		$(document).ready(function() {
    	$("#editcomp<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editcomp&id=<? echo $member["id"]; ?>&paid=<? echo $paid ?>&shid=<? echo $id; ?>',
					type : 'iframe',
          width : 600,
          height : 420,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      });
</script>
    <?
    echo "<tr><td witdh='20' align='center'><p>".++$i."</p></td>";
    echo "<td><a href='javascript:;' id='editcomp".$member['id']."' title='Редактировать параметр'>".$member['name']." <i class='fa fa-pencil fa-lg'></i></a> ";
    ?>
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?>,<? echo $paid;?>,'delcomp&shid=<? echo $id;?>')" 
     title="Удалить составной критерий"><i class='fa fa-trash fa-lg'></i></a>
     </td>
     <td align="center"><p>
     <?
        $gst2 = mysql_query("SELECT * FROM shabloncparams WHERE shabloncid='".$member['id']."' ORDER BY id");
        if (!$gst2) puterror("Ошибка при обращении к базе данных");
        while($member2 = mysql_fetch_array($gst2))
        { 
         if ($member2['type'] == 1)
          echo "<i class='fa fa-plus-square fa-lg'></i> <b>".$member2['value']."</b>";
         if ($member2['type'] == -1)
          echo " <i class='fa fa-minus-square fa-lg'></i> <b>".$member2['value']."</b>";
        }
        mysql_free_result($gst2);
     ?>
     </p></td></tr>
    <?
  }
    ?>
    </table></div></p></td></tr></table></body></html>
    <?    
}
} else die;  
?>