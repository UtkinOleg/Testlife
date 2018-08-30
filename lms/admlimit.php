<?php
if(defined("IN_ADMIN")) {  

  include "config.php";
  include "func.php";
  
  $title=$titlepage="Список ограниченных супервизоров";
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
      $( "#addlimit" ).button();
			$('.fancybox').fancybox();
    	$("#addlimit").click(function() {
				$.fancybox.open({
					href : 'editlimit&mode=add',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 550,
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

  echo"<p align='center'><a class=link id='addlimit' href='javascript:;'><i class='fa fa-cloud fa-lg'></i>&nbsp;Добавить ограниченного супервизора
  </a></p><p align='center'>";

  
  $gst = mysqli_query($mysqli, "SELECT * FROM limitsupervisor ORDER BY id DESC;");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable align="center" width="90%" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td witdh='50'></td>
              <td><p class=help>Имя</p></td>
              <td><p class=help>Модель</p></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysqli_fetch_array($gst))
  {

    ?>
<script type="text/javascript">

	$(document).ready(function() {
    	$("#editlimit<? echo $member['id'] ?>").click(function() {
				$.fancybox.open({
					href : 'editlimit&mode=edit&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 500,
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
    if ($member['userid']!=0)
    {
     echo "<td>";
     $gst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."'");
     $user = mysqli_fetch_array($gst2);
     if (!empty($user['photoname'])) 
        {
          if (stristr($user['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$user['id'].$user['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$user['photoname']."' height='40'><div>"; 
        }  
     echo "</td>";
     echo "<td><p>".$user['usertype']."&nbsp;<a href=viewuser&utype=user&id=".$user['id'].">".$user['userfio']."</a> (".$user['email'].")"; 
     mysqli_free_result($qst2);
    } else
    echo "<td></td><td></td>";
    
    echo "<td><p>";
    $pa = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM projectarray WHERE id='".$member['proarrid']."' LIMIT 1;");
    $paname = mysqli_fetch_array($pa);
    echo $paname['name']." <a id='editlimit".$member['id']."' href='javascript:;'><i class='fa fa-pencil fa-lg'></i></a>";
    mysqli_free_result($pa);
    ?>
    &nbsp;<a href="#" onClick="DelWindow(<? echo $member['id'];?> ,0 ,'dellimit')" title="Удалить"><i class='fa fa-trash fa-lg'></i></a></p>
    <?
    echo "</td></tr>";
  }
  mysql_free_result($gst);
  echo "</table></div></p>";
  include "bottomadmin.php";
} else die;  
?>