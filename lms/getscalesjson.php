<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 

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

  if (defined("IN_ADMIN")) 
   $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM scales LIMIT 1;");
  else
  if (defined("IN_SUPERVISOR")) 
   $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM scales WHERE ownerid='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($countg); 

  if ($count_gs>0)
  {
  $s= "<div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='100'><i title='Дата создания' class='fa fa-calendar fa-lg'></i></td>
          </thead>
          <tbody>";

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales ORDER BY id;");
  else
  if (defined("IN_SUPERVISOR")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales WHERE ownerid='".USER_ID."' LIMIT 1;");

  $i=0;
  while($member = mysqli_fetch_array($sql))
  {
    $news = '<i class="fa fa-arrows-h fa-fw"></i>';
      
    $s.= "<tr><td witdh='30'><p>".++$i."&nbsp;".$news."</p></td>"; 
    $s.= "<td width='300'>
    <p>" .$member['name']. " <a onclick='dialogOpen(\"edscale&m=e&id=".$member['id']."\",0,0)' href='javascript:;' title='Редактировать шкалу'><i class='fa fa-cog fa-lg'></i></a>";
    
    $countr = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE scale='".$member['id']."' LIMIT 1;");
    $cntr = mysqli_fetch_array($countr);
    $count_res = $cntr['count(*)'];
    mysqli_free_result($countr); 

    if ($count_res==0) {
      $s.= '&nbsp;<a href="javascript:;" onclick="$(\'#DelScalehiddenInfoId\').val('.$member['id'].');$(\'#DelScale\').modal(\'show\');" title="Удалить шкалу"><i class="fa fa-trash fa-lg"></i></a>';
    }
    
    $s.= '</p></td>';

    $s.=  "<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s.= "</tr>";
  }
  mysqli_free_result($sql);

  $s.= "</tbody></table></div>";
  }

  $json['content'] = $s;  
  $json['ok'] = '1';  
  
} else 
   $json['ok'] = '0'; 
echo json_encode($json);
?>
