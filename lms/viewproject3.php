<?php
 include "config.php";
 include "func.php";

 $pid = $_GET["id"];
 $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$pid."' LIMIT 1");
 if (!$sql) die;
 $member = mysqli_fetch_array($sql);
 $sql2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT openexpert FROM projectarray WHERE id='".$member['proarrid']."' LIMIT 1");
 if (!$sql2) die;
 $pa = mysqli_fetch_array($sql2);
 $openexpert = $pa['openexpert'];
 
 if (($openexpert==0 and $member['status']=='published') or ($openexpert>0 and 
 ( $member['status']=='published' or $member['status']=='accepted' or $member['status']=='inprocess')))
 {
   /*  $title = "Просмотр проекта №".$pid;
     include "topadmin5.php";
     echo viewp($mysqli, $pid, $upload_dir);
     echo "</body></html>";  */                     
 }
 else
 if(defined("IN_USER")) {  
  if (!empty($member)) 
  {
   if ($member['userid']==USER_ID or defined("IN_ADMIN"))
   {
    $title = "Просмотр проекта №".$pid;
    include "topadmin5.php";
    echo viewp($mysqli, $pid, $upload_dir);
    echo "</body></html>";
   } 
   else
   if (defined("IN_EXPERT") or defined("IN_SUPERVISOR"))
   {
    $cnt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM proexperts as e, projects as p WHERE e.expertid=".USER_ID." AND p.id=".$pid." AND e.proarrid = p.proarrid");
    if (!$cnt) puterror("Ошибка при обращении к базе данных");
    $totalexpert = mysqli_fetch_array($cnt);
    if ($totalexpert['count(*)']>0)
    {
     $title = "Просмотр проекта №".$pid;
     include "topadmin5.php";
     echo viewp($mysqli, $pid, $upload_dir);
     echo "</body></html>";
    }
   }
  }
 } else die;
?>