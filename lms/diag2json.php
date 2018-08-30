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
   
  $selector2 = "";

  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid) and empty($groupid) and empty($folder_parent_id))
   {
     $selector2 = ", usergroups as g, useremails as e, users as u WHERE g.folderid='".$folderid."' AND e.usergroupid=g.id AND e.email=u.email AND r.userid=u.id";
   } 
   else
   if (empty($folderid) and empty($groupid) and !empty($folder_parent_id))
   {
     $selector2 = ", folders as f, usergroups as g, useremails as e, users as u WHERE f.parentid='".$folder_parent_id."' AND g.folderid=f.id AND e.usergroupid=g.id AND e.email=u.email AND r.userid=u.id";
   }
  } 
  
  if (!empty($groupid) and empty($folderid) and empty($folder_parent_id))
//  if (!empty($groupid))
  {
     $selector2 = ", useremails as e, users as u WHERE e.usergroupid='".$groupid."' AND e.email=u.email AND r.userid=u.id";
  }
  
  if (!empty($begindate) and !empty($enddate))
   {
    if (strlen($selector2)>0)
     $selector2 .= " AND s.testid=r.testid AND s.signature=r.signature AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
    else
     $selector2 = " WHERE s.testid=r.testid AND s.signature=r.signature AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
   } 

  if (!empty($testid))
  {
    if (strlen($selector2)>0)
     $selector2 .= " AND r.testid='".$testid."'";
    else
     $selector2 = " WHERE r.testid='".$testid."'";
  }

  if (!empty($userid))
   {
    if (strlen($selector2)>0)
     $selector2 .= " AND r.userid='".$userid."'";
    else
     $selector2 = " WHERE r.userid='".$userid."'";
   } 


  if(defined("IN_ADMIN"))
   $q = "SELECT r.* FROM testresults as r, singleresult as s".$selector2." ORDER BY r.id"; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector2 == "")
    $q = "SELECT r.* FROM testgroups as t, testresults as r, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY r.id"; 
   else
    $q = "SELECT r.* FROM testgroups as t, testresults as r, singleresult as s".$selector2." AND s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY r.id"; 
  }
  

  $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);
  while($testres = mysqli_fetch_array($td))
  {
    $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testres['qgroupid']."' LIMIT 1;");
    $questgroup = mysqli_fetch_array($qg);
    $qname = $questgroup['name'];
    mysqli_free_result($qg);
        
    $blocked = false;
    
    if(defined("IN_ADMIN"))
    {
        if (array_key_exists($qname, $rows))
        {
         $rows[$qname][0] += $testres['rightball'];
         $rows[$qname][1] += $testres['allball'];
        }
        else
         $rows[$qname] = array($testres['rightball'],$testres['allball']);
       
    }
    else
    {
    if(defined("IN_SUPERVISOR"))
     $blocked = isBlockedSignature($mysqli, $sum0, $testres['signature']);

    if(defined("IN_SUPERVISOR"))
    {
      if (!$blocked)
      {
        if (array_key_exists($qname, $rows))
        {
         $rows[$qname][0] += $testres['rightball'];
         $rows[$qname][1] += $testres['allball'];
        }
        else
         $rows[$qname] = array($testres['rightball'],$testres['allball']);
       }
    }
    else
    {
        if (array_key_exists($qname, $rows))
        {
         $rows[$qname][0] += $testres['rightball'];
         $rows[$qname][1] += $testres['allball'];
        }
        else
         $rows[$qname] = array($testres['rightball'],$testres['allball']);
    }

   }     
      
  }
  mysqli_free_result($td);

  foreach($rows as $key => list($a,$b)) 
   $rows2[]=array('x'=>$key,'y'=>round($a / $b, 2));

//  $json['ok'] = $q; 
//  echo json_encode($json); 

  echo json_encode($rows2); 


} else die;

?>