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

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT kn.* FROM knowledge as kn, usergroups as ug, useremails as ue WHERE kn.id=".$kid." AND ug.usergrouptype=1 AND ue.usergroupid=ug.id AND ue.email='".USER_EMAIL."' AND kn.usergroupid=ug.id LIMIT 1;");
  $total = mysqli_fetch_array($sql);
  $knc = $total['id']; 
  mysqli_free_result($sql);
  if ($knc==$kid)
  {
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$grid."' LIMIT 1;");
  $qgname = mysqli_fetch_array($sql);
  $qgroupname = $qgname['name'];
  $qgroupcomment = $qgname['comment'];
  mysqli_free_result($sql); 
  
  $s1 = '
         <div class="row">';
  $s1.= '    <div class="col-lg-12">
                     <button type="button" class="btn btn-outline btn-primary btn-sm" onclick="location.href=\'ex&kid='.$kid.'\'"><i class="fa fa-question-circle fa-fw"></i> Назад</button>
                     <h3>Экспертиза группы вопросов <strong>'.$qgroupname.'</strong> <small>'.$qgroupcomment.'</small></h3>
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
    
    $exsql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertquestions WHERE questionid='".$member['id']."' AND expertid='".USER_ID."' LIMIT 1;");
    $exmember = mysqli_fetch_array($exsql);
    $check = $exmember['ocenka'];
    $panelclass = 'panel-default';
    if (!empty($exmember))
    {
     if ($check==1)
      $panelclass = 'panel-success';
     else
     if ($check==0)
      $panelclass = 'panel-danger';
    }
    mysqli_free_result($exsql); 
     
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
                     <div id="question'.$member['id'].'" class="panel '.$panelclass.'">
                        <div class="panel-heading">№'.$qc.' <strong>' . $tt . '</strong>
                        </div>
                        <div class="panel-body">';

    if (!empty($member['content']))
    {
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
   $b2 .= '<p>'.data_convert ($cmember['commentdate'], 1, 1, 0).': <strong>'.$cmember['comment'].'</strong></p>';
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

     if ($panelclass == 'panel-default')
     { 
      $s2 .= '<button type="button" id="buttonyes'.$member['id'].'" class="btn btn-success btn-circle" title="Вопрос составлен правильно" onclick="checkquestion('.$member['id'].',1,'.$grid.')"><i class="fa fa-check fa-fw"></i></button>';
      $s2 .= '&nbsp;<button type="button" id="buttonno'.$member['id'].'" class="btn btn-danger btn-circle" title="Вопрос составлен неправильно" onclick="checkquestion('.$member['id'].',0,'.$grid.')"><i class="fa fa-close fa-fw"></i></button>';
     }
     $s2.= '&nbsp;<button type="button" class="btn btn-primary btn-circle" onclick="$(\'#CommentQuestionhiddenInfoId\').val('.$member['id'].');$(\'#CommentQuestionhiddenInfoGrId\').val('.$grid.');$(\'#CommentQuestion\').modal(\'show\');" title="Комментарий к вопросу"><i class="fa fa-comment-o fa-fw"></i></button>';
     
     $s2.="</div></div>";
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
  }
  else
  {
   $json['ok'] = '0';
  }
  
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
