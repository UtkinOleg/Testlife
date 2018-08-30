<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Настройки шаблона - оплата проектов";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

  if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {

  echo"<p align='center'>";

    
    maintab($paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment'], 1);
    
//  $tot2 = mysql_query("SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paid."'");
//  $tot2ee = mysql_fetch_array($tot2);
//  $countpr = $tot2ee['count(*)'];
//  if ($countpr==0){
    
?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> В случае, если размещение проекта в системе является платной услугой с определенной фиксированной суммой, будет автоматически формироваться список пользователей, оплативших услугу с указанием внесенной суммы. Если стоимость оплаты равна стоимости услуги, пользователю будет предоставлен доступ к размещению проекта. В случае возникновения форс-мажорной ситуации (например участник оплатил размещение проекта, но в системе оплата не прошла), оплату можно добавить вручную через ссылку - <strong><a id='addoplata' href='javascript:;'>добавить оплату</a></strong>. Тариф использования услуги экспертной системы - <strong>10% от суммы каждого платежа</strong>.</p>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {
			$('.fancybox').fancybox();
    	$("#addoplata").click(function() {
				$.fancybox.open({
					href : 'addoplata&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 700,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    	$("#addoplata2").click(function() {
				$.fancybox.open({
					href : 'addoplata&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 700,
          height : 300,
          fitToView : false,
          autoSize : false,          
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
    
    if (defined("IN_ADMIN"))
     echo"<p align='center'><a id='addoplata2' href='javascript:;'><i class='fa fa-dollar fa-lg'></i>&nbsp;Добавить оплату вручную</a></p>";
//  }
  
  $gst = mysql_query("SELECT * FROM money WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     </p><p align='center'><div id='menu_glide' class='menu_glide'>
      <table width="100%" align='center' border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td witdh='50'></td>
              <td><p class=help>Плательщик</p></td>
              <td witdh='50'><p class=help>Сумма (руб.)</p></td>
              <td witdh='50'><p class=help>Дата и время</p></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {

    $order1 = mysql_query("SELECT * FROM orders WHERE id='".$member['orderid']."' LIMIT 1");
    if (!$order1) puterror("Ошибка при обращении к базе данных");
    $o1 = mysql_fetch_array($order1);
    
    $gst2 = mysql_query("SELECT * FROM users WHERE id='".$o1['userid']."' LIMIT 1");
    $user = mysql_fetch_array($gst2);
    
    echo "<tr><td><p>".++$i." (Заказ №".$o1['id'].")</p></td>";

    echo "<td>";
    if (!empty($user['photoname'])) 
        {
          if (stristr($user['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$user['id'].$user['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$user['photoname']."' height='40'><div>"; 
        }  
    echo "</td>";
    
    echo "<td><p>".$user['usertype']."&nbsp;<a href=viewuser&id=".$user['id'].">".$user['userfio']."</a> (".$user['email'].")"; 
    ?>
    &nbsp;
    <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'deloplata','oplata','оплату из списка')" title="Удалить плательщика из списка"><i class='fa fa-trash fa-lg'></i></a></p>
    <?
    echo "</p></td>"; 
    echo "<td align='center'><p>".$member['summa']."</p></td>"; 
    echo "<td align='center'><p>".$member['paydate']."</p></td>"; 
    mysql_free_result($gst2);
    mysql_free_result($order1);
  }
  mysql_free_result($gst);
  echo "</table></div></p>";
  include "bottomadmin.php";
 } 
} else die;  
?>