<?php
if(defined("IN_ADMIN")) 
{  
  include "config.php";

  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }

  function GetCnt($mysqli, $mode, $id)
  {
    if ($mode=='q')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE userid='".$id."' LIMIT 1;");
     else
    if ($mode=='t')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE ownerid='".$id."' LIMIT 1;");
    $groupsd = mysqli_fetch_array($groups);
    $cnt = $groupsd['count(*)'];
    mysqli_free_result($groups);
    return $cnt;
  }

  function GetFolderName($mysqli,$folderid,$name)
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE id='".$folderid."' LIMIT 1;");
   $folder = mysqli_fetch_array($sql);
   $user_grp_parentid = $folder['parentid'];
   $folder_name = $folder['name'];
   $s = $name;
   if ($user_grp_parentid > 0)
   {
    $s = GetFolderName($mysqli, $user_grp_parentid, $s);
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

  $t = $_POST["t"];
  if (empty($t))
   $t = 'supervisor';

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE usertype='".$t."' ORDER BY id DESC;");
  $i=0;
  while($member = mysqli_fetch_array($sql))
  {
     $row = array();

     $img = '';
     if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb&h=24&a=".$member['token']."' height='24'>"; 
          else
           $img = "<img class='img-circle' src='".$member['photoname']."' height='24'>"; 
        }  
     
    $row[] = ++$i;
    $row[] = $img."&nbsp;[".$member['id']."]&nbsp;".$member['userfio'];
    $row[] = $member['email'];
    $row[] = $member['social_id'];
    $ss="";    
       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM useremails WHERE email='".$member['email']."'");
       while ($cntgrps = mysqli_fetch_array($countu))
       {
        $user_grp = $cntgrps['usergroupid'];
        $sqlg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE id='".$user_grp."' LIMIT 1;");
        $grps = mysqli_fetch_array($sqlg);
        $user_grp_name = $grps['name'];
        $user_grp_folderid = $grps['folderid'];
        if ($user_grp_folderid > 0)
        {
         $user_grp_name = GetFolderName($mysqli, $user_grp_folderid, "");
         $ss = $user_grp_name." / ".$grps['name'];
        }
        else
        {
         $ss = $grps['name'];
        }
        mysqli_free_result($sqlg); 
       }
       mysqli_free_result($countu); 
       $row[] = $ss;

    if ($t == 'supervisor')
    {
     $row[] = GetCnt($mysqli, 'q', $member['id']);
     $row[] = GetCnt($mysqli, 't', $member['id']);
  
     $sql2 = mysqli_query($mysqli,"/*qc=on*/SELECT SUM(summa) FROM money WHERE userid='".$member['id']."'");
     $sum = mysqli_fetch_array($sql2, MYSQLI_NUM);
     $sum0 = $sum[0]/10;
     mysqli_free_result($sql2);
     $q1 = "/*qc=on*/SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid='".$member['id']."' LIMIT 1;"; 
     $sql2 = mysqli_query($mysqli,$q1);
     $total = mysqli_fetch_array($sql2);
     $counter = $total['count(*)'];
     mysqli_free_result($sql2);
     $sum0 -= $counter;
     if ($sum0<0) $sum0=0;

     $row[] = $counter;
     $row[] = $sum0.' <a href="javascript:;" onclick="dialogOpen(\'addmoney&id='.$member['id'].'\',500,240)" title="Добавить сеансы тестирования"><i class="fa fa-plus fa-fm"></i></a>';
    }
 
    $totex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions WHERE expertid='".$member['id']."' LIMIT 1;");
    $totalex = mysqli_fetch_array($totex);
    $row[] = $totalex['count(*)']; 
    mysqli_free_result($totex);

    $row[] = data_convert ($member['puttime'], 1, 0, 0);
    $row[] = '<a href="javascript:;" onclick="$(\'#DelSupervisorId\').val('.$member['id'].');$(\'#DelSupervisor\').modal(\'show\');" title="Удалить супервизора"><i class="fa fa-trash fa-lg"></i></a>';

    $rows[] = $row;    
  }
  mysqli_free_result($sql);

  $json['data'] = $rows;
  echo json_encode($json); 
} else die;  
?>
