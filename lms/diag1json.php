<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";
  require_once "resultblocker.php";
  
  $x = $_POST["x"];
  $y = $_POST["y"];

  $testid = $_POST["tid"];
  $begindate = $_POST["bdate"];
  $enddate = $_POST["edate"];
  $groupid = $_POST["grid"];
  $folderid = $_POST["frid"];
  $folder_parent_id = $_POST["frpid"];

  if(defined("IN_SUPERVISOR"))
   $sum0 = getTestCount($mysqli);

  if (!empty($testid))
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM testgroups WHERE signature = '".$testid."' LIMIT 1;");
   $test = mysqli_fetch_array($sql);
   $testid = $test['id'];
   mysqli_free_result($sql);
  }

  $begindate1 = $_POST["bdate"];
  $enddate1 = $_POST["edate"];
  
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
  
  $userid = $_POST["uid"];

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
  
  $order1 = "ORDER BY s.rightball ASC;";
  
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

  if(defined("IN_ADMIN"))
   $q1 = "SELECT count(*) FROM singleresult as s".$selector; 
  else
  if(defined("IN_SUPERVISOR"))
  {
  if ($selector == "")
   $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID; 
  else
   $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID; 
  }

  $rows = array();
  $tot = mysqli_query($mysqli,$q1);
  $total = mysqli_fetch_array($tot);
  $counter = $total['count(*)'];
  mysqli_free_result($tot);
  if ($counter>0)
  {
   $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);
 
   while($member = mysqli_fetch_array($res))
   {

    $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."' LIMIT 1;");
    $fromuser = mysqli_fetch_array($from);
    $name = $fromuser['userfio'];
    $email = $fromuser['email'];
    mysqli_free_result($from);

    $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
    $testname = mysqli_fetch_array($test);
    $tname = $testname['name'];
    mysqli_free_result($test);

    $allq = $member['allq'];
    $rightq = $member['rightq'];

    $rightball = $member['rightball'];
    $allball = $member['allball'];
    $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    $resdate = data_convert ($member['resdate'], 1, 1, 0);

    switch ($x) 
    { 
      case 0 : $xdata = $name; break;
      case 1 : $xdata = $email; break;
      case 2 : $xdata = $tname; break;
    }

    switch ($y) 
    { 
      case 3 : $ydata = $rightq; break;
      case 4 : $ydata = $rightball; break;
      case 5 : $ydata = $percent; break;
    }

    $blocked = false;
    
    if(defined("IN_ADMIN"))
    {
     if ($y==6)
      $rows[]=array('x'=>$xdata,'y'=>$rightq,'a'=>$rightball,'c'=>$percent);
     else
      $rows[]=array('x'=>$xdata,'y'=>$ydata);
    }
    else
    {
        
    if(defined("IN_SUPERVISOR"))
      $blocked = isBlocked($mysqli, $sum0, $member['id']);
    
    if(defined("IN_SUPERVISOR"))
    {
      if ($blocked)
        $row[] = array('x'=>0,'y'=>0,'a'=>0,'c'=>0);
      else  
      {    
       if ($y==6)
        $rows[]=array('x'=>$xdata,'y'=>$rightq,'a'=>$rightball,'c'=>$percent);
       else
        $rows[]=array('x'=>$xdata,'y'=>$ydata);
      }
    }
    else
    {    
     if ($y==6)
      $rows[]=array('x'=>$xdata,'y'=>$rightq,'a'=>$rightball,'c'=>$percent);
     else
      $rows[]=array('x'=>$xdata,'y'=>$ydata);
    }
    
    }
   }                                 
  
   mysqli_free_result($res);
  }

//  asort($rows);
  echo json_encode($rows); 


} else die;

?>