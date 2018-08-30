<?php

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

  $s = "";
  $s1 = "";
  
  $know = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge ORDER BY id DESC;");
  while($knowdata = mysqli_fetch_array($know))
  {
     $knowname =$knowdata['name'];
     $knowcontent =$knowdata['content'];
     $knowid = $knowdata['id'];
     
      $s1 = '
        <div class="row">';
      $s1.= '    <div class="col-lg-12">
                     <h3>'.$knowname.' <small>'.$knowcontent.'</small></h3>
                 </div>
             </div>    
        </div>
        <div class="row">';

  $ta = array();

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE external=1 AND testtype='check' AND knowsid=".$knowid." ORDER BY id DESC;");
  $s2 = "";
    
  while($member = mysqli_fetch_array($sql))
  {

    $b='';
    $qc=0;
    $tt=0;
    $sumball=0;
     
    if ($member['testkind']=='adaptive')
      $tt = '<span class="pull-right">
      <a title="Нравится тест" href="javascript:;" onclick="up(\''.$member['signature'].'\');"><span id="badge'.$member['signature'].'" class="badge"><i class="fa fa-thumbs-o-up fa-fw"></i>&nbsp;'.$member['upcnt'].'</span></a>
      </span>';
    else
    if ($member['testkind']=='standard')
      $tt = '<span class="pull-right">
      <a title="Нравится тест" href="javascript:;" onclick="up(\''.$member['signature'].'\');"><span id="badge'.$member['signature'].'" class="badge"><i class="fa fa-thumbs-o-up fa-fw"></i>&nbsp;'.$member['upcnt'].'</span></a>
      </span>';

    $s2 = '   
                  <div class="panel panel-success">
                        <div class="panel-heading"><strong>' . $member['name'] . '</strong> <small>' . $tt . '
                        </small></div>
                        <div class="panel-body">';
    
     
    if ($member['testkind']=='adaptive')
     $s2 .= '<p><button type="button" class="btn btn-outline btn-success" onclick="dialogOpen(\'viewadaptivetest&s='.$member['signature'].'\',0,0)">
     <i class="fa fa-dashboard fa-fw"></i>&nbsp;Тестирование&nbsp;<span class="badge pull-right" style="top: 1px;">
     <i class="fa fa-eye fa-fw"></i>&nbsp;'.$member['viewcnt'].'</span></button>
     &nbsp;<i class="fa fa-repeat fa-lg"></i>&nbsp;Адаптивный&nbsp;
     </p>';
    else
    if ($member['testkind']=='standard')
    {
     if ($member['psy']==1)
      $s2 .= '<p><button type="button" class="btn btn-success" onclick="dialogOpen(\'viewpsytest&s='.$member['signature'].'\',0,0)">
      <i class="fa fa-dashboard fa-fw"></i>&nbsp;Тестирование&nbsp;<span class="badge pull-right" style="top: 1px;">
      <i class="fa fa-eye fa-fw"></i>&nbsp;'.$member['viewcnt'].'</span></button>
      &nbsp;<i class="fa fa-heart-o fa-lg"></i>&nbsp;Психологический&nbsp;
      </p>';
     else
      $s2 .= '<p><button type="button" class="btn btn-success" onclick="dialogOpen(\'viewtest&s='.$member['signature'].'\',0,0)">
      <i class="fa fa-dashboard fa-fw"></i>&nbsp;Тестирование&nbsp;<span class="badge pull-right" style="top: 1px;">
      <i class="fa fa-eye fa-fw"></i>&nbsp;'.$member['viewcnt'].'</span></button>
      &nbsp;<i class="fa fa-sort-numeric-asc fa-lg"></i>&nbsp;Стандартный&nbsp;
      </p>';
    }
    $grq = 0;
    $adapt = false;
    
    $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='".$member['id']."' ORDER BY id");
    while($testdata = mysqli_fetch_array($td))
    {
       $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name, singleball, singletime FROM questgroups WHERE id='".$testdata['groupid']."' LIMIT 1");
       $questgroup = mysqli_fetch_array($qg);
    
       if ($member['testkind']=='standard')
       {
        if ($testdata['random']) 
         $c = "<i title='Случайная выборка вопросов' class='fa fa-random fa-fw'></i> ".$questgroup['name']; 
        else
         $c = "<i title='Стандартная выборка вопросов' class='fa fa-sort-numeric-asc fa-fw'></i> ".$questgroup['name'];
     
        if ($testdata['qcount']>0)
        {
         $b .= "<p>".$c."&nbsp;<i title='Вопросов в выборке' class='fa fa-question fa-fw'></i>&nbsp;".$testdata['qcount']."</p>";
         $qc += $testdata['qcount'];
         $tt += $questgroup['singletime']*$testdata['qcount'];
         $sumball += $questgroup['singleball']*$testdata['qcount'];
         $grq++;
        } 
       }
       else
       if ($member['testkind']=='adaptive' and $testdata['random'])
       {
        $c = "<i title='Адаптивная выборка вопросов' class='fa fa-repeat fa-fw'></i> ".$questgroup['name']; 
        if ($testdata['qcount']>0)
         $b .= "<p>".$c."</p>";
       }
      mysqli_free_result($qg); 
       
    }
    mysqli_free_result($td); 

    if ($member['testkind']=='adaptive')
     $bb = '';
    else
    if ($member['testkind']=='standard')
     $bb = '<i title="Групп вопросов" class="fa fa-question-circle fa-fw"></i>&nbsp'.$grq.'&nbsp;<i title="Всего вопросов" class="fa fa-question fa-fw"></i>&nbsp;'.$qc;
    
    $b = '                      <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapsegrp'.$member['id'].'">Разделы, темы (группы вопросов)</a>
                                            '.$bb.'
                                        </h4>
                                    </div>
                                    <div id="collapsegrp'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b.'</div>
                                    </div>
                                </div>';
    
     $s2.=$b;

     if (!empty($member['content']))
     {
     // mb_internal_encoding('UTF-8');
      if (mb_strlen($member['content'])>300)
       $s2.="<p><small>".mb_strcut($member['content'], 1, 300)."...</small></p>";
      else
       $s2.="<p><small>".$member['content']."</small></p>";
     }
     
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
      $s .= "<div class='col-md-4'>" . $t . "</div>";
     $s .= "</div><hr>";
    }
   }
  }
  mysqli_free_result($know);

  if (empty($s))
   $s.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Тесты не найдены.
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


echo json_encode($json); 
