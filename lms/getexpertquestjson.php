<?php
if(defined("IN_SUPERVISOR") AND USER_EXPERT_KIM) {

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

  function GetCnt($mysqli, $kid, $mode)
  {
    $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' LIMIT 1;");
    $groupsd = mysqli_fetch_array($groups);
    $cnt = $groupsd['count(*)'];
    mysqli_free_result($groups);
    return $cnt;
  }

  $kid = $_POST["kid"];

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge WHERE id='".$kid."' LIMIT 1;");
  $kname = mysqli_fetch_array($sql);
  $knowname = $kname['name'];
  $knowcontent = $kname['content'];
  $know_usergroupid = $kname['usergroupid'];
  mysqli_free_result($sql);

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='".$kid."' ORDER BY id DESC;");

  $s = "<div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='50'><i title='Баллов за вопрос' class='fa fa-calculator fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Время ответа на вопрос (минут)' class='fa fa-clock-o fa-lg'></i></td>
              <td align='center' witdh='50'></td>
              <td align='center' witdh='50'><i title='Проведено экспертиз' class='fa fa-check fa-lg'></i></td>
              <td align='center' witdh='100'><i title='Дата создания группы' class='fa fa-calendar fa-lg'></i></td>
          </thead>
          <tbody>";

  $i=0;
  $countq=0;
  while($member = mysqli_fetch_array($sql))
  {
    $countq++;

    $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata as d, singleresult as s WHERE s.testid=d.testid AND d.groupid='".$member['id']."' LIMIT 1;");
    $cnttests = mysqli_fetch_array($counttest);
    $count_res = $cnttests['count(*)'];
    mysqli_free_result($counttest); 

    $totq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='".$member['id']."'");
    $total = mysqli_fetch_array($totq);
  
    $s.= "<tr><td align='center' witdh='30'><p>".++$i."</p></td>";
    $s.= "<td width='300'>
    <p>".$member['name'].'</p>';
    
    if (!empty($member['comment'])) 
     $s.="<p><small>".$member['comment']."</small></p>";
    $s.="</td>";
    
    $s.="<td align='center'><p>".$member['singleball']."</p></td>";
    $s.="<td align='center'><p>".$member['singletime']."</p></td>";
    $s.='<td align="center"><div id="qlist'.$member['id'].'">';
    if ($total['count(*)']>0) 
     $s.='<p align="center"><i class="fa fa-check-circle fa-fw fa-inverse" style="color:#FF2F66;"></i> <a href="edex&id='.$member['id'].'&kid='.$kid.'">Экспертиза заданий</a></p>';
    $s.='</div></td>';
    
    $totex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions WHERE qgroupid='".$member['id']."' AND expertid='".USER_ID."' LIMIT 1;");
    $totalex = mysqli_fetch_array($totex);

    $s.='<td align="center">';
    if ($totalex['count(*)']>0) 
     $s.='<p><span class="badge">'.$totalex['count(*)'].'</span></p>';
    $s.='</td>';
    
    mysqli_free_result($totex);
    mysqli_free_result($totq);

    $s.="<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s.="</tr>";
  }
  mysqli_free_result($sql);
  
  $s.="</tbody></table></div";

  $json['content'] = $s;  

  $json['knowname'] = "Область знаний <strong>".$knowname."</strong>";  
  $json['knowcontent'] = '<p>'.$knowcontent.'</p>';
  $json['ok'] = '1';  
} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
