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

  if ($sqlanaliz)
  {
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $tstart = $mtime; 
  }

  $grid = intval($_POST['gid']);  
  $kid = intval($_POST['kid']);  

  $s = "";
  $s1 = "";

  $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata as d, singleresult as s WHERE s.testid=d.testid AND d.groupid='".$grid."' LIMIT 1;");
  $cnttests = mysqli_fetch_array($counttest);
  $count_res = $cnttests['count(*)'];
  mysqli_free_result($counttest); 

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$grid."' LIMIT 1;");
   else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$grid."' AND userid='".USER_ID."' LIMIT 1;");
  $qgname = mysqli_fetch_array($sql);
  $qgroupname = $qgname['name'];
  $qgroupcomment = $qgname['comment'];
  mysqli_free_result($sql); 
  
  $s1 = '
         <div class="row">';
  $s1.= '    <div class="col-lg-12">
                     <button type="button" class="btn btn-outline btn-primary btn-sm" onclick="location.href=\'qt&kid='.$kid.'\'"><i class="fa fa-question-circle fa-fw"></i> Назад</button>
                     <h3>Группа вопросов <strong>'.$qgroupname.'</strong> <small>'.$qgroupcomment.'</small></h3>
                 </div>
             </div>    
        </div>
        <div class="row">';

  $ta = array();

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE qgroupid='".$grid."' ORDER BY id;");
  $s2 = "";
  $qc=0;  
  while($member = mysqli_fetch_array($sql))
  {
    $qc++;

    $check1=0;
    $check2=0;
    $exsql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions WHERE questionid='".$member['id']."' AND ocenka=1 LIMIT 1;");
    $exmember = mysqli_fetch_array($exsql);
    $check1 = $exmember['count(*)'];
    mysqli_free_result($exsql); 
    $exsql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions WHERE questionid='".$member['id']."' AND ocenka=0 LIMIT 1;");
    $exmember = mysqli_fetch_array($exsql);
    $check2 = $exmember['count(*)'];
    mysqli_free_result($exsql); 

    $ch='';
    if ($check1==0 and $check2==0)
     $panelclass = 'panel-default';
    else
    {
     $ch='&nbsp;<span class="badge"><i class="fa fa-check fa-fw"></i> '.$check1.'</span>&nbsp;<span class="badge"><i class="fa fa-close fa-fw"></i> '.$check2.'</span>';
     if ($check1>$check2)
      $panelclass = 'panel-success';
     else
      $panelclass = 'panel-danger';
    }
   
    if ($member['qtype']=='multichoice')
     $tt = "<i title='Закрытый' class='fa fa-check-square-o fa-lg'></i> Выбор вариантов";
    else 
    if ($member['qtype']=='shortanswer')
     $tt = "<i title='Открытый' class='fa fa-square-o fa-lg'></i> Ввод с клавиатуры";
    else 
    if ($member['qtype']=='sequence')
     $tt = "<i title='Последовательнсть' class='fa fa-reorder fa-lg'></i> Последовательнсть";
    else 
    if ($member['qtype']=='accord')
     $tt = "<i title='Соответствия' class='fa fa-random fa-lg'></i> Соответствия";

     $s2 = '   
                  <div id="qlist'.$member['id'].'">
                     <div class="panel '.$panelclass.'">
                        <div class="panel-heading">№' .$qc. ' <strong>' .$tt. '</strong>' .$ch. '
                        </div>
                        <div class="panel-body">';

     if (!empty($member['content']))
     {
      mb_internal_encoding('UTF-8');
      if (mb_strlen($member['content'])>300)
       $s2.="<p>".mb_strcut($member['content'], 1, 300)."...</p>";
      else
       $s2.="<p>".$member['content']."</p>";
     }

    $b='';
    $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$member['id']."' ORDER BY id");
    while($answer = mysqli_fetch_array($ans))
         {
           if ($member['qtype']=='accord')
           {
             $pieces = explode("=", $answer['name']);
             $name = $pieces[0];
             $name2 = $pieces[1];
             $b.="<p>".$name." <i class='fa fa-arrows-h fa-lg'></i> ".$name2."</p>";
           }
           else
           {
           if ($answer['ball']>0)
            $b.="<p><i class='fa fa-check fa-lg'></i> ".$answer['name']."</p>";
           else
            $b.="<p>".$answer['name']."</p>";
           } 
         }
    mysqli_free_result($ans); 

    $b = '                      <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapseans'.$member['id'].'">Ответы</a>
                                        </h4>
                                    </div>
                                    <div id="collapseans'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b.'</div>
                                    </div>
                                </div>';
    
  $b2 = '';
  $comm = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertcomments WHERE questionid='".$member['id']."' ORDER BY id DESC;");
  while($cmember = mysqli_fetch_array($comm))
  {
   $usersql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$cmember['expertid']."' LIMIT 1;");
   $usermember = mysqli_fetch_array($usersql);
   $img = '';
   if (!empty($usermember['photoname'])) 
        {
          if (stristr($usermember['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb&h=24&a=".$usermember['token']."' height='24'>"; 
          else
           $img = "<img class='img-circle' src='".$usermember['photoname']."' height='24'>"; 
        }  
   $img .= "&nbsp;".$usermember['userfio'];
   mysqli_free_result($usersql);

   $b2 .= '<p>'.$img.'&nbsp;<small>'. data_convert ($cmember['commentdate'], 1, 1, 0).'</small>: <strong>'.$cmember['comment'].'</strong></p>';
  }
  mysqli_free_result($comm);

  if (strlen($b2)>0)
   $b2 = '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapsecomm'.$member['id'].'">Комментарии экспертов</a>
                                        </h4>
                                    </div>
                                    <div id="collapsecomm'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b2.'</div>
                                    </div>
                                </div>';
                                
     $b.='<div id="comments'.$member['id'].'">'.$b2.'</div>';

     $s2.=$b;

     if ($count_res==0)
     {
      $s2 .= '<button type="button" class="btn btn-primary btn-circle" onclick="dialogOpen(\'addquestmanual&m=e&id='.$grid.'&qid='.$member['id'].'\',0,0)"><i class="fa fa-cog fa-fw"></i></button>';
      $s2.= '&nbsp;<button type="button" class="btn btn-primary btn-circle" onclick="$(\'#DelQuestionhiddenInfoId\').val('.$member['id'].');$(\'#DelQuestionhiddenInfoGrId\').val('.$grid.');$(\'#DelQuestion\').modal(\'show\');" title="Удалить вопрос"><i class="fa fa-trash fa-fw"></i></button>';
     }
     
     $s2.="</div></div></div>";
     $ta[] = $s2;
  }
  mysqli_free_result($sql);
  
  $cc = count($ta);
  if ($cc>0)
   {
    if ($cc==1)
     $s .= $s1 . "<div class='col-md-12'>" . $ta[0] . "</div></div><hr>";
    else
    if ($cc==2)
     $s .= $s1 . "<div class='col-md-6'>" . $ta[0] . "</div><div class='col-md-6'>" . $ta[1] . "</div></div><hr>";
    else
    {
     $s .= $s1;
     foreach ($ta as $t)
      $s .= "<div class='col-md-6'>" . $t . "</div>";
     $s .= "</div><hr>";
    }
   }

  if (empty($s))
   $s.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Вопросы не найдены.
          </div>';
   
  $json['content'] = $s;  
  $json['ok'] = '1';  

  if ($sqlanaliz)
  {
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        $mtime = $mtime[1] + $mtime[0]; 
        $tend = $mtime; 
        $tpassed = ($tend - $tstart); 
        $json['sqltime'] = $tpassed;
  }


} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
