<?php

  function GetFolderName($mysqli, $folderid, $name, $href)
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE id='".$folderid."' LIMIT 1;");
   $folder = mysqli_fetch_array($sql);
   $user_grp_parentid = $folder['parentid'];
   
   if ($user_grp_parentid == 0)
   {
    $href1 .= $href."&frid=".$folderid;
   }
   else
   {
    $href1 .= $href."&frpid=".$folderid;
   }
   
   $folder_name = "<a href='".$href1."'>".$folder['name']."</a>";
   $s = $name;
   
   if ($user_grp_parentid > 0)
   {
    $s = GetFolderName($mysqli, $user_grp_parentid, $s, $href);
   }
   
   mysqli_free_result($sql); 
   
   if ($user_grp_parentid==0)
   {
    return $folder_name;
   }
   else
   {
    return $s.' / '.$folder_name;
   }
  }

  function data_convert($data, $year, $time, $second)
  {
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }
  
  function isBlocked($mysqli, $sum, $id)
  {
    $q1 = "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM (SELECT s.id FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY s.id ASC LIMIT ".$sum.") as q WHERE q.id=".$id." LIMIT 1;"; 
    $sql = mysqli_query($mysqli,$q1);
    $total = mysqli_fetch_array($sql);
    $counter = $total['count(*)'];
    mysqli_free_result($sql);
    return $counter==0;
  }

  function isBlockedSignature($mysqli, $sum, $sign)
  {
    $q1 = "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM (SELECT s.signature FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY s.id ASC LIMIT ".$sum.") as q WHERE q.signature='".$sign."' LIMIT 1;"; 
    $sql = mysqli_query($mysqli,$q1);
    $total = mysqli_fetch_array($sql);
    $counter = $total['count(*)'];
    mysqli_free_result($sql);
    return $counter==0;
  }

  function getTestCount($mysqli)
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT SUM(summa) FROM money WHERE userid='".USER_ID."'");
   $sum = mysqli_fetch_array($sql, MYSQLI_NUM);
   $sum0 = $sum[0]/10;
   mysqli_free_result($sql);
   return $sum0;
  }
  
?>