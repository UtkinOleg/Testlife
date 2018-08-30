<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
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

  $s='';

  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$z);
  $day=$z[1][0];
  $month=$z[2][0];
  $year=$z[3][0];
  $ts_now = (mktime(0, 0, 0, $month, $day, $year));
  
  $coltasks = 0;
  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE active=1 AND testtype='pass' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE active=1 AND testtype='pass' AND ownerid='".USER_ID."' ORDER BY id DESC;");
  
  while($member = mysqli_fetch_array($sql))
  {
    $sql2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergrp WHERE testid='".$member['id']."' ORDER BY id");
    while($usergrp = mysqli_fetch_array($sql2))
    {
       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails WHERE usergroupid='".$usergrp['usergroupid']."' LIMIT 1;");
       $cntusers = mysqli_fetch_array($countu);
       $count_users = $cntusers['count(*)'];
       mysqli_free_result($countu); 
      
       $date1 = $usergrp['startdate'];
       $date2 = $usergrp['stopdate'];
       $arr1 = explode(" ", $date1);
       $arr2 = explode(" ", $date2);  
       $arrdate1 = explode("-", $arr1[0]);
       $arrdate2 = explode("-", $arr2[0]);
       $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
       $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
       
       if ($coltasks<5 and $count_users > 0 and $ts_now >= $timestamp1 and $ts_now <= $timestamp2) 
       {
        $coltasks++;
        $attempts=0;
        $res = mysqli_query($mysqli,"SELECT count(*) FROM singleresult as s, users as u, useremails as e WHERE e.email=u.email AND u.id=s.userid AND e.usergroupid='".$usergrp['usergroupid']."' AND s.testid='".$member['id']."' LIMIT 1;");
        $resdata = mysqli_fetch_array($res);
        $attempts=$resdata['count(*)'];
        mysqli_free_result($res); 
       
        if ($attempts>=$count_users)
         $percent = 100;
        else 
         $percent = (int) round($attempts/$count_users*100);
        
        if ($percent==100)
         $ac='';
        else
         $ac=' active';  
        if ($percent==100)
         $ac2='progress-bar-success';
        else
         $ac2='progress-bar-primary';  

         
        $s.='            <li style="padding: 5px;">
                                <div>
                                    <p>
                                        <strong>Тест: '.$member['name'].'</strong>
                                        <span class="pull-right text-muted"><i class="fa fa-users fa-fw"></i> '.$count_users.'</span>
                                    </p>
                                    <div class="progress progress-striped'.$ac.'">
                                        <div class="progress-bar '.$ac2.'" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%">
                                            <span>'.$percent.'% завершили</span>
                                        </div>
                                    </div>
                                </div>
                        </li>';
       }
    }
    mysqli_free_result($sql2);
  }
  mysqli_free_result($sql);
  
  if ($coltasks>0)
   $json['content'] = $s;  
  else
   $json['content'] = 'Нет активных сеансов тестирования.';  
  
  $json['ok'] = '1';  
  
  echo json_encode($json); 

} else die;