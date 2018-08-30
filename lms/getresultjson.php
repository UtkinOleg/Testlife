<?php
if(!defined("USER_REGISTERED")) die;

  include "config.php";
  require_once "resultblocker.php";
  
  $testid = $_POST["tid"];
  $begindate = $_POST["bdate"];
  $enddate = $_POST["edate"];
  $groupid = $_POST["grid"];
  $folderid = $_POST["frid"];
  $folder_parent_id = $_POST["frpid"];

  $begindate1 = $_POST["bdate"];
  $enddate1 = $_POST["edate"];

  if(defined("IN_SUPERVISOR"))
   $sum0 = getTestCount($mysqli);

  if (!empty($testid))
  {
   $testsignature = $testid;
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE signature = '".$testid."' LIMIT 1;");
   $test = mysqli_fetch_array($sql);
   $testtype = $test['testtype'];
   $testid = $test['id'];
   $testname = $test['name']; 
   $scaleid = $test['scale'];
   mysqli_free_result($sql);

     if ($scaleid>0)
     {
      $kk=0;
      $sqlsc2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scaleparams WHERE scaleid='".$scaleid."' ORDER BY id;");
      while($scpar = mysqli_fetch_array($sqlsc2))
      {
       $scpara[] = array ($scpar['name'], $scpar['top'], $scpar['end'], $kk);
       $pcounter[] = 0;
       $kk++;
      }    
      mysqli_free_result($sqlsc2);
     }

  }

  if (!empty($groupid)) { 
   if(defined("IN_ADMIN"))
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$groupid."' LIMIT 1;");
   else
   if(defined("IN_SUPERVISOR"))
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$groupid."' AND userid='".USER_ID."' LIMIT 1;");
   if (!$sql) { die; }
   $data = mysqli_fetch_array($sql);
   $gname = $data['name']; 
   mysqli_free_result($sql);
  }  

  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid)) { 
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM folders WHERE id='".$folderid."' LIMIT 1;");
    if (!$sql) { die; }
    $data = mysqli_fetch_array($sql);
    $fname = $data['name']; 
    mysqli_free_result($sql);
   }
   else
   if (!empty($folder_parent_id)) { 
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM folders WHERE id='".$folder_parent_id."' LIMIT 1;");
    if (!$sql) { die; }
    $data = mysqli_fetch_array($sql);
    $fname = $data['name']; 
    mysqli_free_result($sql);
   }
  }  
  
  if (!empty($begindate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $begindate.' 00:00:00'); // Начало суток
   $begindate = $DateTime1->format('Y-m-d H:i:s');
  }
  
  if (!empty($enddate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $enddate.' 23:59:59');  // Конец суток
   $enddate = $DateTime1->format('Y-m-d H:i:s');
  }

  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
  {
   $userid = $_POST["uid"];
  }
  else
  {
   if (defined("IN_USER"))
   {
    $userid = USER_ID;
   }
   else
   {
    die;
   }
  }
   
  $selector = "";
                                                                              
  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid) and empty($groupid) and empty($folder_parent_id))
   {
     $selector = ", usergroups as g, useremails as e, users as u WHERE g.folderid='".$folderid."' AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   } 
   else
   if (empty($folderid) and empty($groupid) and !empty($folder_parent_id))
   {
     $selector = ", folders as f, usergroups as g, useremails as e, users as u WHERE f.parentid='".$folder_parent_id."' AND g.folderid=f.id AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   }
  } 
  
  if (!empty($groupid) and empty($folderid) and empty($folder_parent_id))
  {
     $selector = ", useremails as e, users as u WHERE e.usergroupid='".$groupid."' AND e.email=u.email AND s.userid=u.id";
  }
  
  if (!empty($testid))
  {
    if (strlen($selector)>0)
      $selector .= " AND s.testid='".$testid."'";
    else
      $selector = " WHERE s.testid='".$testid."'";
  }
  
  if (!empty($begindate) and !empty($enddate))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
    else
     $selector = " WHERE s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
   } 
  
  if (!empty($userid))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.userid='".$userid."'";
    else
     $selector = " WHERE s.userid='".$userid."'";
   } 
  
  $order1 = "ORDER BY s.id DESC;";
  
  if(defined("IN_ADMIN"))
   $q = "SELECT s.* FROM singleresult as s".$selector." ".$order1; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector == "")
    $q = "SELECT s.* FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
   else
    $q = "SELECT s.* FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
  }
  else
  if(defined("IN_USER"))
  {
   if ($selector == "")
    $q = "SELECT s.* FROM singleresult as s WHERE s.userid=".USER_ID." ".$order1; 
   else
    $q = "SELECT s.* FROM singleresult as s".$selector." AND s.userid=".USER_ID." ".$order1; 
  }
  
  if(defined("IN_ADMIN"))
   $q1 = "SELECT count(*) FROM singleresult as s".$selector." LIMIT 1;"; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector == "")
    $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." LIMIT 1;"; 
   else
    $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID." LIMIT 1;"; 
  }
  else
  if(defined("IN_USER"))
  {
   if ($selector == "")
    $q1 = "SELECT count(*) FROM singleresult as s WHERE s.userid=".USER_ID." LIMIT 1;"; 
   else
    $q1 = "SELECT count(*) FROM singleresult as s".$selector." AND s.userid=".USER_ID." LIMIT 1;"; 
  }

  $rows = array();
  $tot = mysqli_query($mysqli,$q1);
  $total = mysqli_fetch_array($tot);
  $counter = $total['count(*)'];
  mysqli_free_result($tot);
  if ($counter>0)
  {
  $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);
 
  $i=0;
  $allrightball = 0;
  $allball = 0;
  
  $count2 = 0;
  $count3 = 0;
  $count4 = 0;
  $count5 = 0;
  
  while($member = mysqli_fetch_array($res))
  {
    $row = array();
    $row[] = ++$i;

    if (empty($testid))
    {
     $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
     $testdata = mysqli_fetch_array($test);
     $testtype = $testdata['testtype'];
     $testsignature = $testdata['signature'];
     $testname = $testdata['name'];
     $scaleid = $testdata['scale'];
     mysqli_free_result($test);
     if ($scaleid>0)
     {
      unset($scpara);
      unset($pcounter);
      $kk=0;
      $sqlsc2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scaleparams WHERE scaleid='".$scaleid."' ORDER BY id;");
      while($scpar = mysqli_fetch_array($sqlsc2))
      {
       $scpara[] = array ($scpar['name'], $scpar['top'], $scpar['end'], 0);
       $pcounter[] = 0;
       $kk++;
      }    
      mysqli_free_result($sqlsc2);
     }
    }
    
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
    {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     $img = '';
     if (!empty($fromuser['photoname'])) 
        {
          if (stristr($fromuser['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb&h=24&a=".$fromuser['token']."' height='24'>"; 
          else
           $img = "<img class='img-circle' src='".$fromuser['photoname']."' height='24'>"; 
        }  

     $fromuseremail = $fromuser['email'];
     if (empty($img))
      $img = $fromuser['userfio'];
     else 
      $img .= " ".$fromuser['userfio'];
     $fromuserid = $fromuser['id'];
     mysqli_free_result($from);

     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT g.id, g.name FROM useremails as e, usergroups as g WHERE g.id=e.usergroupid AND e.email='".$fromuseremail."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     $fromgroupid = $fromuser['id'];
     $fromgroupname = $fromuser['name'];
     mysqli_free_result($from);
    
     if (!empty($userid))
      $row[] = $img;
     else
     { 
      $href = "vr";
      if (empty($userid))
       $href .= "&uid=".$fromuserid;
      if (!empty($testid))
       $href .= "&tid=".$testsignature;
      if (!empty($groupid))
       $href .= "&grid=".$fromgroupid;
      if (!empty($begindate1))
       $href .= "&bdate=".$begindate1."&edate=".$enddate1;
      $row[] = "<a href='".$href."'>".$img."</a>". " (". $fromuseremail .")";
     }
     
     $fromfoldername = "";
     
     if (defined("IN_ADMIN")) 
     {
        $sql1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE id='".$fromgroupid."' LIMIT 1;");
        $grps = mysqli_fetch_array($sql1);
        $user_grp_name = $grps['name'];
        $user_grp_folderid = $grps['folderid'];
        if ($user_grp_folderid > 0)
        {
         $href = "vr";
         if (!empty($userid))
          $href .= "&uid=".$fromuserid;
         if (!empty($testid))
          $href .= "&tid=".$testsignature;
         if (!empty($begindate1))
          $href .= "&bdate=".$begindate1."&edate=".$enddate1;
         $fromfoldername = GetFolderName($mysqli, $user_grp_folderid, "", $href) . " / ";
        }
        mysqli_free_result($sql1); 
     }       
     
     if (!empty($groupid))
     {
      $row[] = $fromgroupname;
     }
     else
     {
      $href = "vr";
      if (!empty($userid))
       $href .= "&uid=".$fromuserid;
      if (!empty($testid))
       $href .= "&tid=".$testsignature;
      if (empty($groupid))
       $href .= "&grid=".$fromgroupid;
      if (!empty($begindate1))
       $href .= "&bdate=".$begindate1."&edate=".$enddate1;
      $row[] = $fromfoldername."<a href='".$href."'>".$fromgroupname."</a>";
     }
    
    }
    
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
    {
     if ($testtype=='pass') { 
      $row[] = "<i class='fa fa-check-square-o fa-lg' title='Зачетный тест'></i>"; 
     } else 
     if ($testtype=='check') {
      $row[] = "<i class='fa fa-share-square-o fa-lg' title='Проверочный тест'></i>"; 
     }
    }
    
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
    {
     if (!empty($testid))
      $row[] = $testname;
     else
     {
      $href = "vr";
      if (!empty($userid))
       $href .= "&uid=".$fromuserid;
      if (empty($testid))
       $href .= "&tid=".$testsignature;
      if (!empty($groupid))
       $href .= "&grid=".$fromgroupid;
      if (!empty($begindate1))
       $href .= "&bdate=".$begindate1."&edate=".$enddate1;
      if (($user_grp_folderid > 0) and empty($groupid))
       $href .= "&frid=".$user_grp_folderid;
      $row[] = "<a href='".$href."'>".$testname."</a>";
     }
    }
    else
    {
     $href = "vr";
     if (!empty($begindate1))
      $href .= "&bdate=".$begindate1."&edate=".$enddate1;
     if (empty($testid))
      $row[] = "<a href='".$href."&tid=".$testsignature."'>".$testname."</a>";
     else
      $row[] = $testname;
    }
    
    $row[] = $member['allq'];

    $blocked = false;
    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
      $blocked = isBlocked($mysqli, $sum0, $member['id']);

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
      {
          $row[] = 0;
      }
      else  
      {
          $row[] = $member['rightq'];
      }    
    }
    else
    {
        $row[] = $member['rightq'];
    }

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
        $row[] = '-';
      else  
      {    
        $row[] = $member['rightball']." из ".$member['allball'];
        $allrightball += $member['rightball'];
        $allball += $member['allball'];
      }
        
    }
    else
    {
        $row[] = $member['rightball']." из ".$member['allball'];
        $allrightball += $member['rightball'];
        $allball += $member['allball'];
    }

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
        $percent = 0;
      else  
        $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    }
    else
     $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    
    $row[] = $percent."%";
    
    if ($scaleid==0)
    {
     if ($percent<45) $ocenka = '<span class="badge" style="background-color:#F20909;">2</span>';
     else
     if ($percent>=45 and $percent<=69) $ocenka = '<span class="badge" style="background-color:#ED9C09;">3</span>';
     else
     if ($percent>=70 and $percent<=85) $ocenka = '<span class="badge" style="background-color:#153FE4;">4</span>';
     else
      $ocenka = '<span class="badge" style="background-color:#29B004;">5</span>';

     if ($percent<45) 
      $count2++;
     else
     if ($percent>=45 and $percent<=69) 
      $count3++;
     else
     if ($percent>=70 and $percent<=85) 
      $count4++;
     else
      $count5++;

    }
    else
    {
     foreach ($scpara as list($scpar_name, $scpar_top, $scpar_end, $scpar_count)) {
      if ($percent>=$scpar_top and $percent<=$scpar_end)
       {
        $ocenka = '<span class="badge">'.$scpar_name.'</span>';
        $pcounter[$scpar_count]++;
       } 
     }
    } 
    
    $row[] = $ocenka;
     
    $row[] = data_convert ($member['resdate'], 1, 1, 0);

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
        $row[] = "<i class='fa fa-line-chart fa-lg'></i>";
      else  
        $row[] = "<a title='Результаты тестирования' onclick='dialogOpen(\"testresults&id=".$member['signature']."\",0,0)' href='javascript:;'><i class='fa fa-line-chart fa-lg'></i></a>";
    }
    else
     $row[] = "<a title='Результаты тестирования' onclick='dialogOpen(\"testresults&id=".$member['signature']."\",0,0)' href='javascript:;'><i class='fa fa-line-chart fa-lg'></i></a>";

   if(defined("IN_ADMIN"))
    $row[] = "<a href='javascript:;' onclick='$(\"#DelResulthiddenInfoId\").val(\"".$member['signature']."\");$(\"#DelResult\").modal(\"show\");' title='Удалить результат'><i class='fa fa-trash fa-lg'></i></a>";

   $rows[] = $row;    
  }                                 
 
if ($scaleid==0)
{

  $percent = (int) floor($allrightball / $allball * 100);
  $row = array();
  $row[] = ++$i;
  $i--;
  $zn = (int) floor(($count4 + $count5) / $i * 100);
  $us = (int) floor(($count3 + $count4 + $count5) / $i * 100);
  if ($percent<45) $srocenka = '<span class="badge" style="background-color:#F20909;">2</span>';
  else
  if ($percent>=45 and $percent<=69) $srocenka = '<span class="badge" style="background-color:#ED9C09;">3</span>';
  else
  if ($percent>=70 and $percent<=85) $srocenka = '<span class="badge" style="background-color:#153FE4;">4</span>';
  else
   $srocenka = '<span class="badge" style="background-color:#29B004;">5</span>';
  if (empty($testid))
   $testname = '';
   
  $ocenka = '
<button type="button" class="btn btn-outline btn-primary" onclick="$(\'#myResultMsg\').modal(\'show\');"><i class="fa fa-file-text-o fa-fw"></i> Итоговые результаты</button>
<div class="modal fade" id="myResultMsg" tabindex="-1" role="dialog" aria-labelledby="myResultLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myResultLabel">Итоговые результаты: '.$testname.' '.$gname.' '.$fname.'</h4>
      </div>
      <div id="myResultMsgContent" class="modal-body">
  
  <p><table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Параметр</th>
                                            <th>Значение</th>
                                        </tr>    
                                    </thrad><tbody>
<tr><td>Набрано баллов</td><td>'.$allrightball." из ".$allball.'</td></tr>
<tr><td>Средняя оценка по шкале</td><td>'.$srocenka.'</td></tr>
<tr><td>Количество <span class="badge" style="background-color:#F20909;">2</span></td><td>'.$count2.'</td></tr>
<tr><td>Количество <span class="badge" style="background-color:#ED9C09;">3</span></td><td>'.$count3.'</td></tr>
<tr><td>Количество <span class="badge" style="background-color:#153FE4;">4</span></td><td>'.$count4.'</td></tr>
<tr><td>Количество <span class="badge" style="background-color:#29B004;">5</span></td><td>'.$count5.'</td></tr>
<tr><td>Уровень освоения (процент набранных баллов)</td><td>'.$percent.'%</td></tr>
<tr><td>Уровень знаний (кол-во "5" + кол-во "4" / кол-во результатов)</td><td>'.$zn.'%</td></tr>
<tr><td>Уровень успеваемости (кол-во "5" + кол-во "4" + кол-во "3"/ кол-во результатов)</td><td>'.$us.'%</td></tr>
<tr><td>Всего результатов</td><td>'.$i.'</td></tr>
</tbody></table>

      </div>
    </div>
  </div>
</div>
';  
}
else
{

  $percent = (int) floor($allrightball / $allball * 100);
  foreach ($scpara as list($scpar_name, $scpar_top, $scpar_end, $scpar_count)) {
      if ($percent>=$scpar_top and $percent<=$scpar_end)
       {
        $srocenka = '<span class="badge">'.$scpar_name.'</span>';
       } 
  }

  $row = array();
  $row[] = ++$i;
  $i--;
  if (empty($testid))
   $testname = '';
   
  $ocenka = '
<button type="button" class="btn btn-outline btn-primary" onclick="$(\'#myResultMsg\').modal(\'show\');"><i class="fa fa-file-text-o fa-fw"></i> Итоговые результаты</button>
<div class="modal fade" id="myResultMsg" tabindex="-1" role="dialog" aria-labelledby="myResultLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myResultLabel">Итоговые результаты: '.$testname.' '.$gname.' '.$fname.'</h4>
      </div>
      <div id="myResultMsgContent" class="modal-body">
  
  <p><table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Параметр</th>
                                            <th>Значение</th>
                                        </tr>    
                                    </thrad><tbody>
<tr><td>Набрано баллов</td><td>'.$allrightball." из ".$allball.'</td></tr>
<tr><td>Средняя оценка по шкале</td><td>'.$srocenka.'</td></tr>';
foreach ($scpara as list($scpar_name, $scpar_top, $scpar_end, $scpar_count)) {
 $ocenka .= '<tr><td>Количество <span class="badge">'.$scpar_name.'</span></td><td>'.$pcounter[$scpar_count].'</td></tr>';
}
$ocenka .='<tr><td>Уровень освоения (процент набранных баллов)</td><td>'.$percent.'%</td></tr>
<tr><td>Всего результатов</td><td>'.$i.'</td></tr>
</tbody></table>

      </div>
    </div>
  </div>
</div>
';  
 
}
$row[] = $ocenka;
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$rows[] = $row;    
 
mysqli_free_result($res);
}

$json['data'] = $rows;
echo json_encode($json); 
