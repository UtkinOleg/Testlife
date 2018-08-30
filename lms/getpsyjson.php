<?php
if(defined("USER_REGISTERED")) {  
 
  include "config.php";


  if ($sqlanaliz)
  {
   $mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $tstart = $mtime; 
  }

  $testid = intval($_POST['id']);  
  $kid = intval($_POST['kid']);  

  $s = "";
  $s1 = "";

  $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult as s WHERE s.testid='".$testid."' LIMIT 1;");
  $cnttests = mysqli_fetch_array($counttest);
  $count_res = $cnttests['count(*)'];
  mysqli_free_result($counttest); 

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$testid."' LIMIT 1;");
   else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$testid."' AND ownerid='".USER_ID."' LIMIT 1;");
  $tname = mysqli_fetch_array($sql);
  $testname = $tname['name'];
  $testcontent = $tname['content'];
  if (!empty($testcontent))
  {
      if (mb_strlen($testcontent)>300)
       $testcontent = mb_strcut($testcontent, 1, 300)."...";
  }
  mysqli_free_result($sql); 
  
  $s1 = '
         <div class="row">';
  $s1.= '    <div class="col-lg-12">
                     <h3>Тест <strong>'.$testname.'</strong> <small>'.$testcontent.'</small></h3>
                 </div>
             </div>    
        </div>
        <div class="row">';

  $ta = array();

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM psymode WHERE testid='".$testid."' ORDER BY id;");
  $s2 = "";
  $qc=0;  
  while($member = mysqli_fetch_array($sql))
  {
     $qc++;
     $s2 = '   
                  <div id="qlist'.$member['id'].'">
                     <div class="panel panel-default">
                        <div class="panel-heading">Интерпретация №' . $qc . '
                        </div>
                        <div class="panel-body">';

     if (!empty($member['content']))
     {
      if (mb_strlen($member['content'])>300)
       $s2.="<p>".mb_strcut($member['content'], 1, 300)."...</p>";
      else
       $s2.="<p>".$member['content']."</p>";
     }

    $b='';
    $cc = 0;
    $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM psyquestions WHERE psyid='".$member['id']."' ORDER BY id;");
    while($psy = mysqli_fetch_array($ans))
    {
     $cc++;      
     if ($psy['selected']==true)
      $b.="<p>№" . $cc . ". " .$psy['name'] . "</p>";
    }
    mysqli_free_result($ans); 

    $b = '                      <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapseans'.$member['id'].'">Входящие вопросы</a>
                                        </h4>
                                    </div>
                                    <div id="collapseans'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b.'</div>
                                    </div>
                                </div>';
    
     $s2.=$b;

     if ($count_res==0)
     {
      $s2 .= '<button type="button" class="btn btn-primary btn-circle" onclick="dialogOpen(\'editpsitest&m=e&tid='.$testid.'&id='.$member['id'].'\',0,0)"><i class="fa fa-cog fa-fw"></i></button>';
      $s2.= '&nbsp;<button type="button" class="btn btn-primary btn-circle" onclick="$(\'#DelPsyhiddenInfoId\').val('.$member['id'].');$(\'#DelPsyhiddenInfoTestId\').val('.$testid.');$(\'#DelPsy\').modal(\'show\');" title="Удалить интерпретацию"><i class="fa fa-trash fa-fw"></i></button>';
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
         		Интерпретации не найдены.
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
