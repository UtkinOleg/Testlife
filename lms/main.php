<?php
  if(!defined("IN_ADMIN")) die;  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Список участников";
  include "topadmin.php";
  
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;  

  $userid = $_POST["userid"];
  

  if (empty($userid))
  {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM users WHERE usertype='user';");
   $gst = mysqli_query($mysqli,"SELECT * FROM users WHERE usertype='user' ORDER BY regdata DESC LIMIT $start, $pnumber;");
  }
  else
  {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM users WHERE id=".$userid." LIMIT 1;");
   $gst = mysqli_query($mysqli,"SELECT * FROM users WHERE id=".$userid." LIMIT 1;");
  }
  
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table align="center" width="100%" class=bodytable border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td witdh='50'></td>
              <td witdh='300'><p class=help>Ф.И.О. участника</p></td>
              <td witdh='300'><p class=help>Место работы</p></td>
              <td witdh='100'><p class=help>Количество проектов</p></td>
              <td witdh='100'><p class=help>Дата регистрации</p></td>
              <td witdh='200'><p class=help>E-mail</p></td>
          </tr>   
     <?         

  $i=$start;
  while($member = mysqli_fetch_array($gst))
  {
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE userid='".$member['id']."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];
    mysqli_free_result($tot2);
    if ($count2==0)
     echo "<tr style='background-color:#FCC0C0;'>";
    else
     echo "<tr>";
    echo "<td witdh='50' align='center'><p>".++$i." [".$member['id']."]</p></td>";
    echo "<td width='50'>";
    
        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$member['id'].$member['photoname']."' height='40'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='40'><div>"; 
        }  
    echo "</td>";
    echo "<td width='300'><a class='menu' href='edituser&utype=user&id=".$member['id']."&start=".$start."' title='Редактировать участника'><p class=zag2>".$member['userfio']."</a>";
    ?>
    &nbsp;
    <a href="#" onClick="DelWindow(<? echo $member['id'];?> ,<? echo $start;?>,'delmember','members','')" title="Удалить участника"><i class='fa fa-trash fa-lg'></i></a>
    <?
    echo "</td>";
    echo "<td width='300'><p>".$member['job']."</p></td>";

    echo "<td width='100' align='center'><a class='menu' href='projects&id=".$member['id']."&start=".$start."' title='Проекты участника'><p>".$count2."</a>";
    echo "<td witdh='100' align='center'><p>".data_convert ($member['puttime'], 1, 0, 0)."</p></td>";
    echo "<td witdh='200' align='center'><p>".$member['email']."</p></td></tr>";
  }
  echo "</table><div></p>";

  $total = mysqli_fetch_array($tot);
  $count = $total['count(*)'];
  $cc = $count;
  echo "<p align=center>";
  $i=1;
  $start2 = 0;
  if ($count>$pnumber)
  while ($count > 0)
  {
    if ($start==$start2)
     echo $i."&nbsp;";
    else {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='members&start=<? echo $start2; ?>'">&nbsp;
     <?
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }
  echo "</p>";
    
  echo"<p align='center'>Всего участников - ".$cc."</p>";
?>
<br><br>
<?
  include "bottomadmin.php";
?>