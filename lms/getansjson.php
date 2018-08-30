<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
{  
  include "config.php";

  $qid = $_POST["qid"];

  $ans = mysqli_query($mysqli,"SELECT * FROM answers WHERE questionid='".$qid."' ORDER BY id;");
  $s="<div id='menu_glide' style='padding:10px; background: #FFF;' class='ui-widget-content ui-corner-all'>";
  while($answer = mysqli_fetch_array($ans))
  {
           if ($answer['ball']>0)
            $s .= "<p><i class='fa fa-check fa-lg'></i> ".$answer['name']."</p>";
           else
            $s .= "<p><i class='fa fa-remove fa-lg'></i> ".$answer['name']."</p>";
  }
  $s .= '</div';
  mysqli_free_result($ans);
  $json['content'] = $s;  
  
  if(!empty($json['content']))  { 
             $json['ok'] = '1';  
  } else {  
             $json['ok'] = '0'; 
  }      
  echo json_encode($json); 
} else die;
?>