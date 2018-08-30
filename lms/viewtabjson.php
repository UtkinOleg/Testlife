<?
  include_once "config.php";
  require_once "lib/func2.php";

  $pid = intval($_POST["id"]);
  $paid = intval($_POST["paid"]);
  $tabid = intval($_POST["tabid"]);
  $slidercounter=0;
  $v="";

  $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$pid."' LIMIT 1");
  $member = mysqli_fetch_array($gst);

  $pa1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT openexpert, defaultshablon FROM projectarray WHERE id='".$paid."' LIMIT 1");
  $paa1 = mysqli_fetch_array($pa1);
  $daf = $paa1['defaultshablon'];
  $openexpert = $paa1['openexpert'];

  $btot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."'");
  $totbcnt = mysqli_fetch_array($btot);
  $countb = $totbcnt['count(*)'];

 if (($openexpert==0 and $member['status']=='published') or ($openexpert>0 and 
 ( $member['status']=='published' or $member['status']=='accepted' or $member['status']=='inprocess')))
 {
  
  if ($countb==0)
  {
    if (empty($daf))
     $v.= '<hr class="featurette-divider"><h3 class="text-center">'.$member['info'].'</h3><hr class="featurette-divider">';
    else
     $v.= '<hr class="featurette-divider"><h3 class="text-center">'.$member['info'].' <small>'.$daf.'</small></h3><hr class="featurette-divider">';
  }
  else
  {
    $bgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id;");
    if ($bgst)
    {
     $z=0;
     while ($block = mysqli_fetch_array($bgst))
     {
      $blockinfo = $block['name'];
      $blockinfo2 = $block['info'];
      $tabid2 = $block['id'];
      if ($z == $tabid) break;
      $z++;
     }
     $v.= '<hr class="featurette-divider"><h3 class="text-center">'.$blockinfo.' <small>'.$blockinfo2.'</small></h3><hr class="featurette-divider">';
    } 
    mysqli_free_result($bgst);        
  }

  mysqli_free_result($gst);        
  mysqli_free_result($pa1);        
  mysqli_free_result($btot);        
  
  if ($countb==0)
   $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$paid."' AND multiid='0' ORDER BY id;");
  else
   $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$paid."' AND multiid='".$tabid2."' ORDER BY id;");
  
  if ($sql) 
  {
   while($param = mysqli_fetch_array($sql)) 
    $v .= newview($mysqli, $pid, $param, $tabid2, $upload_dir, ++$slidercounter);
   $json['ok'] = '1';  
   $json['content'] = $v;
  }
  else
   $json['ok'] = '0';  
  mysqli_free_result($sql);        
}
else
   $json['ok'] = '0';  
  
mysqli_free_result($gst);        
mysqli_free_result($paa1);        
mysqli_free_result($btot);     
   
echo json_encode($json);
?>  