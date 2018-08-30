<?php
if(defined("USER_REGISTERED")) {  
 
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

  if (!empty(USER_EMAIL))
  {

/*  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$z);
  $day=$z[1][0];
  $month=$z[2][0];
  $year=$z[3][0];
  $ts_now = (mktime(0, 0, 0, $month, $day, $year));
*/
  $ts_now = time();

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

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE active=1 AND knowsid=".$knowid." ORDER BY id DESC;");
  $s2 = "";
    
  while($member = mysqli_fetch_array($sql))
  {

    $b='';
    $qc=0;
    $tt=0;
    $sumball=0;

    $acusers = 0;
    $testdate = '';
    $passed = false;
    $sql2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergrp WHERE testid='".$member['id']."' ORDER BY id");
    while($usergrp = mysqli_fetch_array($sql2))
    {
       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails WHERE email='".USER_EMAIL."' AND usergroupid='".$usergrp['usergroupid']."' LIMIT 1;");
       $cntusers = mysqli_fetch_array($countu);
       $count_users = $cntusers['count(*)'];
       mysqli_free_result($countu); 
       $acusers += $count_users;
       
/*       $date1 = $usergrp['startdate'];
       $date2 = $usergrp['stopdate'];
       $arr1 = explode(" ", $date1);
       $arr2 = explode(" ", $date2);  
       $arrdate1 = explode("-", $arr1[0]);
       $arrdate2 = explode("-", $arr2[0]);
       $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
       $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));   */
       
       if ($count_users > 0 and $ts_now >= strtotime($usergrp['startdate']) and $ts_now <= strtotime($usergrp['stopdate'])) 
       {
           if ($usergrp['startdate']==$usergrp['stopdate'])
            $testdate .= " Тестирование можно пройти ".data_convert ($usergrp['startdate'], 1, 1, 0).".";
           else
            $testdate .= " Тестирование можно пройти с ".data_convert ($usergrp['startdate'], 1, 1, 0)." по ".data_convert ($usergrp['stopdate'], 1, 1, 0).".";
           $passed = true;
       }  
    }
    mysqli_free_result($sql2); 

    $attempts=0;
    if ($member['attempt']>0)
    {
     $res = mysqli_query($mysqli,"SELECT count(*) FROM singleresult WHERE userid='".USER_ID."' AND testid='".$member['id']."' LIMIT 1;");
     $resdata = mysqli_fetch_array($res);
     $attempts=$resdata['count(*)'];
     mysqli_free_result($res); 
     $res = mysqli_query($mysqli,"SELECT count(*) FROM attemptsresult WHERE userid='".USER_ID."' AND testid='".$member['id']."' LIMIT 1;");
     $resdata = mysqli_fetch_array($res);
     $attempts +=$resdata['count(*)'];
     mysqli_free_result($res); 
    }
    
    if ($acusers>0 and $passed)
    {

     if ($member['testtype']=='pass')
     { $tt = 'зачетный'; $ff = 'danger'; }
     else
     if ($member['testtype']=='check')
     { $tt = 'проверочный'; $ff='success'; }
     
     if ($member['testkind']=='adaptive')
      $tt = '&nbsp;<i class="fa fa-repeat fa-lg"></i>&nbsp;адаптивный&nbsp;&middot;&nbsp;'.$tt;
     else
     if ($member['testkind']=='standard')
      if ($member['psy'])
       $tt = '&nbsp;<i class="fa fa-sort-numeric-asc fa-lg"></i>&nbsp;психологический&nbsp;&middot;&nbsp;'.$tt;
      else 
       $tt = '&nbsp;<i class="fa fa-sort-numeric-asc fa-lg"></i>&nbsp;стандартный&nbsp;&middot;&nbsp;'.$tt;

     $s2 = '   
                  <div class="panel panel-'.$ff.'">
                        <div class="panel-heading"><strong>' . $member['name'] . '</strong> <small>' . $tt . '
                        </small></div>
                        <div class="panel-body">';
    
     if ($member['testtype']=='pass')
     {
     if ($member['testkind']=='adaptive')
      $bd = 'btn btn-outline btn-danger';
     else
     if ($member['testkind']=='standard')
      $bd = 'btn btn-danger';
     }
     else
     if ($member['testtype']=='check')
     {
     if ($member['testkind']=='adaptive')
      $bd = 'btn btn-outline btn-success';
     else
     if ($member['testkind']=='standard')
      $bd = 'btn btn-success';
     }
     
     $bdinfo = '';
     if ($member['attempt']>0) 
     {
      if ($attempts>=$member['attempt'])
      {
       $bd = 'btn btn-danger disabled';
       $bdinfo = '<strong>Попытки пройти тест закончились.</strong> ';
      }
      else
      {
       $kz = $member['attempt']-$attempts;
       $bdinfo = '<strong>Осталось попыток: <span class="badge">'.$kz.'</span></strong> ';
     }
    }
    
    if ($member['attempt']>0 and $attempts>=$member['attempt']) 
    {
     $s2 .= '<p>' . $bdinfo . '</p>';
    }
    else
    {
    if ($member['testkind']=='adaptive')
     $s2 .= '<p><button type="button" class="'.$bd.'" onclick="dialogOpen(\'viewadaptivetest&s='.$member['signature'].'\',0,0)"><i class="fa fa-dashboard fa-fw"></i> Тестирование</button></p><p>' . $bdinfo . $testdate . '</p>';
    else
    if ($member['testkind']=='standard')
     $s2 .= '<p><button type="button" class="'.$bd.'" onclick="dialogOpen(\'viewtest&s='.$member['signature'].'\',0,0)"><i class="fa fa-dashboard fa-fw"></i> Тестирование</button></p><p>' . $bdinfo . $testdate . '</p>';
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
         $b .= "<p>".$c." <i title='Вопросов в выборке' class='fa fa-question fa-fw'></i> ".$testdata['qcount']."</p>";
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
     $bb = '<i title="Групп вопросов" class="fa fa-question-circle fa-fw"></i>&nbsp;'.$grq.'&nbsp;<i title="Всего вопросов" class="fa fa-question fa-fw"></i>&nbsp;'.$qc;
    
    $b = '                      <div class="panel panel-'.$ff.'">
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

    
     if ($member['testtype']=='pass' and !$member['psy'])
     {
      if ($member['testkind']!='adaptive')
       $s2.="<p>Максимальный балл: ".$sumball;

      if ($tt>=60) 
      {
       $hours = (int) floor($tt / 60);
       $minutes = $tt % 60;
       if ($member['testkind']!='adaptive')
        $s2.=" Время тестирования: ".$hours." ч. ".$minutes." мин.</p>";
      } 
      else
      {
      if ($member['testkind']!='adaptive')
       $s2.=" Время тестирования: ".$tt." мин.</p>";
      }
     }
     
     $s2.=$b;

     if (!empty($member['content']))
     {
      if (mb_strlen($member['content'])>300)
       $s2.="<p><small>".mb_strcut($member['content'], 1, 300)."...</small></p>";
      else
       $s2.="<p><small>".$member['content']."</small></p>";
     }

     if ($member['testtype']=='pass')
     {
      $tot = mysqli_query($mysqli,"SELECT count(*) FROM singleresult WHERE userid=".USER_ID." AND testid=".$member['id']." LIMIT 1;");
      $total = mysqli_fetch_array($tot);
      $counter = $total['count(*)'];
      mysqli_free_result($tot);
      if ($counter==0)
       $s2 .= "<p><strong>Вы еще не проходили данный тест.</strong></p>";
      else
      {
       if ($member['testkind']=='adaptive' and !$member['psy'])
       {
        $tot = mysqli_query($mysqli,"SELECT MAX(rightball) FROM singleresult WHERE testid=".$member['id']." AND userid=".USER_ID." LIMIT 1;");
        $total = mysqli_fetch_array($tot);
        $pnb = $total['MAX(rightball)'];
        mysqli_free_result($tot);
        $s2 .= "<p><strong>Ваш лучший результат (максимальный балл):</strong> <span class='badge'>".$pnb."</span> <a title='Результаты' href='vr&tid=".$member['signature']."'><i class='fa fa-line-chart fa-lg'></i></a></p>";
       }
       else
       if ($member['testkind']=='standard' and !$member['psy'])
       {
        $tot = mysqli_query($mysqli,"SELECT MAX(rightball/allball) FROM singleresult WHERE testid=".$member['id']." AND userid=".USER_ID." LIMIT 1;");
        $total = mysqli_fetch_array($tot);
        $pnb = round($total['MAX(rightball/allball)']*100);
        mysqli_free_result($tot);
        $s2 .= "<p><strong>Ваш лучший результат (уровень знаний):</strong> <span class='badge'>".$pnb."%</span> <a title='Результаты' href='vr&tid=".$member['signature']."'><i class='fa fa-line-chart fa-lg'></i></a></p>";
       }
      }
     }
     
     $s2.="</div></div>";
     $ta[] = $s2;
    } 
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
         		Активные тесты не найдены.
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


 }
 else 
  $json['ok'] = '0'; 
} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
