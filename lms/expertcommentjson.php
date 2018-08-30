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

  $questionid = intval($_POST['qid']);  
  $comment = mysqli_real_escape_string($mysqli,$_POST["comment"]);
  $expertid = USER_ID;
  
  mysqli_query($mysqli,"START TRANSACTION;");
  $query = "INSERT INTO expertcomments VALUES (0,
  $questionid,
  $expertid,
  NOW(),
  '$comment')";
  mysqli_query($mysqli,$query);
  mysqli_query($mysqli,"COMMIT");

  $b = '';
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertcomments WHERE questionid='".$questionid."' ORDER BY id DESC;");
  while($member = mysqli_fetch_array($sql))
   $b .= '<p>'.data_convert ($member['commentdate'], 1, 1, 0).': <strong>'.$member['comment'].'</strong></p>';
  mysqli_free_result($sql);

  $b = '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapsecomm'.$questionid.'">Комментарии экспертов</a>
                                        </h4>
                                    </div>
                                    <div id="collapsecomm'.$questionid.'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b.'</div>
                                    </div>
                                </div>';
   $json['comments'] = $b;  
   $json['ok'] = '1'; 
} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
