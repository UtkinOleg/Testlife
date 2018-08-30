<?php
if(defined("USER_REGISTERED")) 
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

  $offset = intval($_POST['offset']); 

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM msgs WHERE touser='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($sql);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($sql); 

  $cntr = 0;
  if ($count_gs>0)
  {
  
  $s="<div class='table-responsive'>
          <table class='table'>
          <tbody>";
  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$z);
  $day=$z[1][0];
  $month=$z[2][0];
  $year=$z[3][0];
  $ts_now = (mktime(0, 0, 0, $month, $day, $year));

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM msgs WHERE touser='".USER_ID."' ORDER BY id DESC LIMIT $offset, 10;");
  $i=$offset; 
  while ($param = mysqli_fetch_array($sql)) 
  {
     $cntr++;
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT photoname, userfio, token FROM users WHERE id='".$param['fromuser']."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);

     $date1 = $param['msgdate'];
     $arr1 = explode(" ", $date1);
     $arrdate1 = explode("-", $arr1[0]);
     $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
     if ($timestamp1 == $ts_now)
      $msgdate = 'Сегодня';
     else
      $msgdate = data_convert ($param['msgdate'], 1, 0, 0);

     $img = '';
     if (!empty($fromuser['photoname'])) 
        {
          if (stristr($fromuser['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb&h=24&a=".$fromuser['token']."' height='24'>"; 
          else
           $img = "<img class='img-circle' src='".$fromuser['photoname']."' height='24'>"; 
        }  
     if (empty($img))
      $img = $fromuser['userfio'];
     else 
      $img .= " ".$fromuser['userfio'];

     $s.= "<tr><td width='30'><p>".++$i."</p></td>"; 
     $s.= "<td width='50'>".$msgdate."</td>";
     if ($param['readed']==0)
      $s.="<td width='50'><div id='badge".$param['signature']."'><span class='badge'>Новое</span></div></td>";
     else
      $s.="<td width='50'></td>";
     $s.= "<td width='300'>".$img."</td>";
     if ($param['readed']==0)
      $s.= "<td><div id='msg".$param['signature']."'><a href='javascript:;' onclick='getmsg(\"".$param['signature']."\");'>".$param['title']."</a></div></td>";
     else
      $s.= "<td>".$param['body']."</td>";
     $s.= "</tr>";
      
     mysqli_free_result($from);
  }
  mysqli_free_result($sql);

  $s.= "</tbody></table></div>";
  }

  if ($cntr==0)
   $json['ok'] = '0'; 
  else
  {
   $json['content'] = $s;  
   $json['ok'] = '1';  
  }
} else 
   $json['ok'] = '0'; 
echo json_encode($json);
?>
