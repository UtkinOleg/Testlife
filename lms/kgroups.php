<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Список групп критериев";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {

  
  $tot2 = mysql_query("SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paid."'");
  $tot2ee = mysql_fetch_array($tot2);
  $countpr = $tot2ee['count(*)'];
  
  if ($countpr==0){  
    echo"<p align='center'>
    <a class=link href='index.php?op=addkgroup&paid=".$paid."'><img src='img/b_newtbl.png'>
    &nbsp;Добавить новую группу критериев</a></p><p align='center'>";
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
      <table class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='100'><p class=help>№</p></td>
              <td witdh='300'><p class=help>Наименование</p></td>
              <td witdh='300'><p class=help>Максимальный балл</p></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {
    $i=$i+1;
    echo "<tr><td witdh='100'><p class=help>".$i."</p></td>";
    if ($countpr==0){  
     echo "<td width='300'><a class='menu' href=index.php?op=editkgroup&paid=".$paid."&id=".$member['id']."&start=$start title='Редактировать'><p class=zag2>".$member['name']."</a>";
     ?>
     &nbsp;
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delkgroup','kgroups','группу критериев')" title="Удалить группу критериев"><img src="img/b_drop.png" width="16" height="16"></a></p>
     <?
     echo "</td><td width='300'><a class='menu' href=editkgroup&paid=".$paid."&id=".$member['id']."&start=$start title='Редактировать'><p class=zag2>".$member['maxball']."</a>";
    }
    else
    {
     echo "<td width='300'><p>".$member['name']."</p></td><td>".$member['maxball']."</td>";
    }
  }
    echo "</table></div>";
  }  
    echo "</p>";
    ?>
    <p align="center"><input type="button" name="close" value="Вернуться к шаблонам" onclick="document.location='<? echo $site; ?>/parray'"></p>
    <?    
  include "bottomadmin.php";
}} else die;  
  
?>