<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";
  require_once "resultblocker.php";
  
  $testid = $_POST["tid"];
  $begindate = $_POST["bdate"];
  $enddate = $_POST["edate"];
  $groupid = $_POST["grid"];
  $folderid = $_POST["frid"];
  $folder_parent_id = $_POST["frpid"];

  $begindate1 = $_POST["bdate"];
  $enddate1 = $_POST["edate"];

  if(defined("IN_SUPERVISOR"))
   $sum0 = getTestCount($mysqli);

  if (!empty($testid))
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM testgroups WHERE signature = '".$testid."' LIMIT 1;");
   $test = mysqli_fetch_array($sql);
   $testid = $test['id'];
   mysqli_free_result($sql);
  }
  
  if (!empty($begindate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $begindate.' 00:00:00'); // Начало суток
   $begindate = $DateTime1->format('Y-m-d H:i:s');
  }
  
  if (!empty($enddate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $enddate.' 23:59:59');  // Конец суток
   $enddate = $DateTime1->format('Y-m-d H:i:s');
  }

  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
   $userid = $_POST["uid"];
  else
  if (defined("IN_USER"))
   $userid = USER_ID;
  else
   die;
   
  $selector2 = "";

  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid) and empty($groupid) and empty($folder_parent_id))
   {
     $selector2 = ", usergroups as g, useremails as e, users as u WHERE g.folderid='".$folderid."' AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   } 
   else
   if (empty($folderid) and empty($groupid) and !empty($folder_parent_id))
   {
     $selector2 = ", folders as f, usergroups as g, useremails as e, users as u WHERE f.parentid='".$folder_parent_id."' AND g.folderid=f.id AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   }
  } 
  
  if (!empty($groupid) and empty($folderid) and empty($folder_parent_id))
//  if (!empty($groupid))
  {
     $selector2 = ", useremails as e, users as u WHERE e.usergroupid='".$groupid."' AND e.email=u.email AND s.userid=u.id";
  }
  
  if (!empty($begindate) and !empty($enddate))
   {
    if (strlen($selector2)>0)
     $selector2 .= " AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
    else
     $selector2 = " WHERE s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
   } 

  if (!empty($testid))
  {
    if (strlen($selector2)>0)
     $selector2 .= " AND s.testid='".$testid."'";
    else
     $selector2 = " WHERE s.testid='".$testid."'";
  }

  if (!empty($userid))
   {
    if (strlen($selector2)>0)
     $selector2 .= " AND s.userid='".$userid."'";
    else
     $selector2 = " WHERE s.userid='".$userid."'";
   } 


  if(defined("IN_ADMIN"))
   $q = "SELECT s.* FROM singleresult as s".$selector2." ORDER BY s.id"; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector2 == "")
    $q = "SELECT s.* FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY s.id"; 
   else
    $q = "SELECT s.* FROM testgroups as t, singleresult as s".$selector2." AND s.testid=t.id AND t.ownerid=".USER_ID." ORDER BY s.id"; 
  }
  
  $kk=0;
  $tdi = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);
  while($result = mysqli_fetch_array($tdi))
  {
    $kk++;
    $tid = $result['testid'];
    $userid = $result['userid'];
    $sign = $result['signature'];

    $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmptest WHERE testid='".$tid."' AND userid='".$userid."' AND signature='".$sign."' ORDER BY id;");
    while($testdata = mysqli_fetch_array($td))
    {
  
    $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$testdata['questionid']."' ORDER BY id LIMIT 1;");
    $quest = mysqli_fetch_array($qq);
    $qname = $quest['content'];
    $qtype = $quest['qtype'];
    mysqli_free_result($qq); 
       
    $blocked = false;
    
    if(defined("IN_ADMIN"))
    {
      if ($qtype=='multichoice')
      {
       $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$testdata['questionid']."' ORDER BY id");
       while($answer = mysqli_fetch_array($ans))
       {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer WHERE questionid='".$testdata['questionid']."' AND answerid='".$answer['id']."' AND signature='".$sign."' LIMIT 1;");
         if ($userans)
         {
          $useranswer = mysqli_fetch_array($userans);
          
          $ku = $useranswer['value'];
          
          if ($answer['ball']>0)
          {
           $ansname = '<i class="fa fa-check fa-fw"></i>'.$answer['name'];
          }
          else
          {
           $ansname = $answer['name'];
          }
           
          if (empty($ku))
          {
           $ku = 0;
          }
          
          if ($ku>0)
          {
           if (array_key_exists($qname.$answer['name'], $rows))
           {
            $rows[$qname.$answer['name']][0] ++;
           }
           else
            $rows[$qname.$answer['name']] = array(0,$qname,$ansname);
          }
          else
          {
           if (!array_key_exists($qname.$answer['name'], $rows))
            $rows[$qname.$answer['name']] = array(0,$qname,$ansname);
          }
         }
         mysqli_free_result($userans);   
       } 
       mysqli_free_result($ans);   
     }
     else
     if ($qtype=='shortanswer')
     {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpshortanswer WHERE questionid='".$testdata['questionid']."' AND signature='".$sign."' ORDER BY id LIMIT 1;");
         if ($userans)
         {
          $useranswer = mysqli_fetch_array($userans);
          $kustr = trim(mb_strtolower($useranswer['value'],'UTF-8'));

          $verk='';
          $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$testdata['questionid']."' ORDER BY id");
          while($answer = mysqli_fetch_array($ans))
          {
            $ansk = trim(mb_strtolower($answer['name'],'UTF-8'));
            if (mb_ereg_match($ansk,$kustr))
             $verk = '<i class="fa fa-check fa-fw"></i>';
          }
          mysqli_free_result($ans);   

          if (array_key_exists($qname.$kustr, $rows))
          {
           $rows[$qname.$kustr][0] ++;
          }
          else
          {
           $rows[$qname.$kustr] = array(1,$qname,$verk.$kustr);
          }
         }
         mysqli_free_result($userans);   
     }
     
    }
    else
    if(defined("IN_SUPERVISOR"))
    {
     $blocked = isBlockedSignature($mysqli, $sum0, $testres['signature']);
     if (!$blocked)
      {
      }
    }

      
  }
  mysqli_free_result($td);
  
  }
  mysqli_free_result($tdi);
  
//  $s="<p>".$q."-".$kk."</p>";
  
  $oldqname = '';
  $kk=0;
  foreach($rows as $key => list($c,$qname,$ansname)) 
  {
   if ($qname!=$oldqname)
   {
   
    if ($kk>0)
    {
/*     $s.=" ],
      xkey: 'y',
      ykeys: ['a'],
      labels: ['Ответ:']
     });
     </script>";
     */
     $s.='</div></div>';
    } 
    $s.='<div class="panel panel-default">
                                        <div class="panel-heading">
                                           <h3 class="panel-title">'.++$kk.'<i class="fa fa-question fa-fw"></i> '.$qname.'</h3>
                                        </div>
                                        <div class="panel-body">';
//    $s.="<p>Вопрос №".++$kk.". <strong>".$qname."</strong>";

/*    $s.="<div id='qbc".$kk."'></div><script>
    Morris.Bar({
     element: 'qbc".$kk."',
     data: [";*/

    $oldqname = $qname;
   }
   
   $s.="<p><div class='poll__answer__item_bar-wrapper'> ".$ansname." <span class='poll__answer__item_bar' style='width:".$c."0px;'></span> <span class='badge'>".$c."</span></div></p>";
  
//   $s.="   { y: '".$ansname."', a: ".$c."}, ";
   
  } 

  $json['content'] = $s;  
  $json['ok'] = '1';  
  echo json_encode($json);



} else die;

?>