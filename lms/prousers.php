<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Cписок участников";
  
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
    	$("#addprouser").click(function() {
				$.fancybox.open({
					href : 'addprouser&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 650,
          height : 350,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
	});
  
  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
  function closeFancybox(){
    $.fancybox.close();
   }    
</script>    
<?
      

   echo"<p align='center'>";
   maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);
    
//  $tot2 = mysql_query("SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paid."'");
//  $tot2ee = mysql_fetch_array($tot2);
//  $countpr = $tot2ee['count(*)'];
//  if ($countpr==0){
   
?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Участники идентифицируются по адресу электронной почты. Определите список участников и отправьте им приглашения. Как только участник зарегестрируется в системе и укажет свой адрес электронной почты, для него откроется возможность создать проект или пройти тестирование (в зависимости от настроек модели).</p>
	</div>
</div>
  <?

  $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM prousers WHERE proarrid='".$paid."'");
  $tot2cnt = mysqli_fetch_array($tot2);
  $counti = $tot2cnt['count(*)'];  
  mysqli_free_result($tot2);                         


   if (LOWSUPERVISOR) // Для бесплатного супервизора - три участника
   {
     if ($counti < 3) 
     {
      echo"<p align='center'><a id='addprouser' href='javascript:;'><i class='fa fa-user fa-lg'></i>&nbsp;Добавить участников</a></p>";
     } 
   }
   else
   {
    // Проверим есть ли супервизор в списке ограниченных
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM limitsupervisor WHERE proarrid='".$paid."' AND userid='".USER_ID."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $countlim = $tot2cnt['count(*)'];  
    mysqli_free_result($tot2);                         
    if ($countlim > 0)
    {
     if ($counti < 20) // ограниченный супервизор - двадцать участников
      echo"<p align='center'><a id='addprouser' href='javascript:;'><i class='fa fa-user fa-lg'></i>&nbsp;Добавить участников</a></p>";
    }
    else
     echo"<p align='center'><a id='addprouser' href='javascript:;'><i class='fa fa-user fa-lg'></i>&nbsp;Добавить участников</a></p>";
   }


  if ($counti>0)
  {
  $gst = mysql_query("SELECT * FROM prousers WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     </p><p align='center'><div id='menu_glide' class='menu_glide'>
      <table width="100%" align='center' border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {
    
    echo "<tr><td align='center'><p>".++$i."</p></td>";
    
    echo "<td width='100'>";
    if ($member['userid']!=0)
    {
     $gst2 = mysql_query("SELECT * FROM users WHERE id='".$member['userid']."'");
     $user = mysql_fetch_array($gst2);
     if (!empty($user['photoname'])) 
        {
          if (stristr($user['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$user['id'].$user['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$user['photoname']."' height='40'><div>"; 
        }  
     echo "</td>";
     echo "<td><p>".$user['usertype']."&nbsp;<a href=viewuser&utype=user&id=".$user['id'].">".$user['userfio']."</a> (".$user['email'].")"; 
     mysql_free_result($qst2);
    }
    else
    {
     echo "<td><p>".$member['email']." <a href='javascript:;' onclick='' title='Отправить уведомление повторно'><i class='fa fa-mail-forward fa-lg'></i></a>"; 
    }
    $totl = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid."' AND userid='".$member['userid']."'");
    $totall = mysql_fetch_array($totl);
    $countp = $totall['count(*)'];  // Количество проектов
    mysql_free_result($totl);
    if ($countp==0){
    ?>
    &nbsp;
    <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delprouser','prousers','участника из списка')" title="Удалить участника из списка"><i class='fa fa-trash fa-lg'></i></a></p>
    <?
    }
    echo "</p></td></tr>"; 
    mysql_free_result($gst2);
  }
  echo "</table></div></p>";
  }
  include "bottomadmin.php";
 } 
} else die;  
?>