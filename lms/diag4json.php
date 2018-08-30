<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

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
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM testgroups WHERE signature = '".$testid."' LIMIT 1;");
   $test = mysqli_fetch_array($sql);
   $testid = $test['id'];
   mysqli_free_result($sql);
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
   $userid = $_POST["uid"];
  else
  if (defined("IN_USER"))
   $userid = USER_ID;
  else
   die;
   
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

//    $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."' LIMIT 1;");
//    $fromuser = mysqli_fetch_array($from);
//    $name = $fromuser['userfio'];
//    $email = $fromuser['email'];
//    mysqli_free_result($from);

//    $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
//    $testname = mysqli_fetch_array($test);
//    $tname = $testname['name'];
//    mysqli_free_result($test);

    $allq = $member['allq'];
    $rightq = $member['rightq'];

    $rightball = $member['rightball'];
    $allball = $member['allball'];
    $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    $resdate = data_convert ($member['resdate'], 1, 1, 0);


    $blocked = false;
    if(defined("IN_ADMIN"))
    {
     $rows[] = $percent;
    }
    else
    {
        
    if(defined("IN_SUPERVISOR"))
     $blocked = isBlocked($mysqli, $sum0, $member['id']);

    if(defined("IN_SUPERVISOR"))
    {
      if (!$blocked)
       $rows[] = $percent;
    }
    else
     $rows[] = $percent;

    }
   }                                 
  
   mysqli_free_result($res);
  }

//  asort($rows);

   $value=0;
   foreach($rows as $val) 
    if ($val>=0 and $val<=4) $value++;
   if ($value>0) $rows2[]=array('label'=>'0-4%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=5 and $val<=9) $value++;
   if ($value>0) $rows2[]=array('label'=>'5-9%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=10 and $val<=14) $value++;
   if ($value>0) $rows2[]=array('label'=>'10-14%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=15 and $val<=19) $value++;
   if ($value>0) $rows2[]=array('label'=>'15-19%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=20 and $val<=24) $value++;
   if ($value>0) $rows2[]=array('label'=>'20-24%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=25 and $val<=29) $value++;
   if ($value>0) $rows2[]=array('label'=>'25-29%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=30 and $val<=34) $value++;
   if ($value>0) $rows2[]=array('label'=>'30-34%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=35 and $val<=39) $value++;
   if ($value>0) $rows2[]=array('label'=>'35-39%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=40 and $val<=44) $value++;
   if ($value>0) $rows2[]=array('label'=>'40-44%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=45 and $val<=49) $value++;
   if ($value>0) $rows2[]=array('label'=>'45-49%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=50 and $val<=54) $value++;
   if ($value>0) $rows2[]=array('label'=>'50-54%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=55 and $val<=59) $value++;
   if ($value>0) $rows2[]=array('label'=>'55-59%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=60 and $val<=64) $value++;
   if ($value>0) $rows2[]=array('label'=>'60-64%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=65 and $val<=69) $value++;
   if ($value>0) $rows2[]=array('label'=>'65-69%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=70 and $val<=74) $value++;
   if ($value>0) $rows2[]=array('label'=>'70-74%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=75 and $val<=79) $value++;
   if ($value>0) $rows2[]=array('label'=>'75-79%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=80 and $val<=84) $value++;
   if ($value>0) $rows2[]=array('label'=>'80-84%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=85 and $val<=89) $value++;
   if ($value>0) $rows2[]=array('label'=>'85-89%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=90 and $val<=94) $value++;
   if ($value>0) $rows2[]=array('label'=>'90-94%','value'=>$value);

   $value=0;
   foreach($rows as $val) 
    if ($val>=95 and $val<=100) $value++;
   if ($value>0) $rows2[]=array('label'=>'95-100%','value'=>$value);

  echo json_encode($rows2); 


} else die;

?>