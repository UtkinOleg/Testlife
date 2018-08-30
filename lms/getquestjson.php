<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
{  
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

  function GetCnt($mysqli, $kid, $mode)
  {
    if(defined("IN_ADMIN"))
    {
     if ($mode=='q')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' LIMIT 1;");
     else
     if ($mode=='t')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE knowsid='".$kid."' LIMIT 1;");
    }
    else
    if (defined("IN_SUPERVISOR"))
    {
     if ($mode=='q')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' AND userid='".USER_ID."' LIMIT 1;");
     else
     if ($mode=='t')
      $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE knowsid='".$kid."' AND ownerid='".USER_ID."' LIMIT 1;");
    }
    $groupsd = mysqli_fetch_array($groups);
    $cnt = $groupsd['count(*)'];
    mysqli_free_result($groups);
    return $cnt;
  }

  $kid = $_POST["kid"];

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge WHERE id='".$kid."' LIMIT 1;");
  $kname = mysqli_fetch_array($sql);
  $knowname = $kname['name'];
  $knowcontent = $kname['content'];
  $know_usergroupid = $kname['usergroupid'];

  if (defined("IN_ADMIN")) 
  {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT photoname, userfio, token, info FROM users WHERE id='".$kname['userid']."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     $img = '';
     if (!empty($fromuser['photoname'])) 
        {
          if (stristr($fromuser['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb&h=24&a=".$fromuser['token']."' height='24'>"; 
          else
           $img = "<img class='img-circle' src='".$fromuser['photoname']."' height='24'>"; 
        }  
     if (empty($img))
      $img = $fromuser['userfio'];
     else 
      $img .= " ".$fromuser['userfio'];
     mysqli_free_result($from);
     $knowname .= " ".$img." (ID ".$kname['userid']." INFO ".$fromuser['info'].") ";
  }

  mysqli_free_result($sql);

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='".$kid."' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='".$kid."' AND userid='".USER_ID."' ORDER BY id DESC;");
  else
   die;

  $s = "<div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='50'><i title='Баллов за вопрос' class='fa fa-calculator fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Время ответа на вопрос (минут)' class='fa fa-clock-o fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Добавить вопросы из файла' class='fa fa-file fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Добавить вопросы вручную' class='fa fa-hand-o-down fa-lg'></i></td>
              <td align='center' witdh='200'><i title='Количество вопросов в группе' class='fa fa-question-circle fa-lg'></i></td>
              <td align='center' witdh='50'></td>
              <td align='center' witdh='100'><i title='Дата создания группы' class='fa fa-calendar fa-lg'></i></td>
          </thead>
          <tbody>";

  $i=0;
  $countq=0;
  while($member = mysqli_fetch_array($sql))
  {
    $countq++;

    $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata as d, singleresult as s WHERE s.testid=d.testid AND d.groupid='".$member['id']."' LIMIT 1;");
    $cnttests = mysqli_fetch_array($counttest);
    $count_res = $cnttests['count(*)'];
    mysqli_free_result($counttest); 

    $totq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='".$member['id']."'");
    $total = mysqli_fetch_array($totq);
  
    $s.= "<tr><td align='center' witdh='30'><p>".++$i."</p></td>";
    $s.= "<td width='300'>
    <p>".$member['name']." <a onclick='dialogOpen(\"addquestgroup&m=e&kid=".$kid."&id=".$member['id']."\",700,440)' href='javascript:;' title='Редактировать группу вопросов'><i class='fa fa-cog fa-lg'></i></a>";

    if ($know_usergroupid!=0)
    {
      $s.='&nbsp;<a href="javascript:;" onclick="$(\'#QGroupId\').val('.$member['id'].');$(\'#UserGroupKIMId\').val('.$know_usergroupid.');$(\'#ExpertQGroup\').modal(\'show\');" title="Отправить запрос на экспертизу группы вопросов"><i class="fa fa-check-circle fa-lg"></i></a>';
    }
      $totex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions WHERE qgroupid='".$member['id']."' LIMIT 1;");
      $totalex = mysqli_fetch_array($totex);
      if ($totalex['count(*)']>0) 
       $s.='&nbsp;<span class="badge" style="background-color: #0FD227;"  title="Количество проведенных экспертиз">'.$totalex['count(*)'].'</span>';
      mysqli_free_result($totex);


    if ($total['count(*)']==0) {
      $s.='&nbsp;<a href="javascript:;" onclick="$(\'#DelQGrouphiddenInfoKid\').val('.$kid.');$(\'#DelQGrouphiddenInfoId\').val('.$member['id'].');$(\'#DelQGroup\').modal(\'show\');" title="Удалить группу вопросов"><i class="fa fa-trash fa-lg"></i></a>';
    }
    $s.= '</p>';
    
    if (!empty($member['comment'])) 
     $s.="<p><small>".$member['comment']."</small></p>";
    $s.="</td>";
    
    $s.="<td align='center'><p>".$member['singleball']."</p>";
    $s.="</td><td align='center'><p>".$member['singletime']."</p></td>
    <td>";

    if ($count_res==0) 
     $s.='<p align="center"><a onclick="dialogOpen(\'addquestfromfile&kid='.$kid.'&id='.$member['id'].'\',700,210)" href="javascript:;"><i class="fa fa-plus-square fa-lg" title="Добавить вопросы из файла"></i></a></p>';

    $s.='</td><td align="center">';

    if ($count_res==0) 
     $s.='<p align="center"><a onclick="dialogOpen(\'addquestmanual&m=a&kid='.$kid.'&id='.$member['id'].'\',0,0)" href="javascript:;"><i class="fa fa-plus-square-o fa-lg" title="Добавить вопросы вручную"></i></a></p>';
    
    $s.='</td><td align="center"><div id="qlist'.$member['id'].'">';
    
    if ($total['count(*)']>0) 
    {
     $s.='<p align="center"><a title="Список вопросов" href="ed&id='.$member['id'].'&kid='.$kid.'"><i class="fa fa-question fa-lg"></i> '.$total['count(*)'].'</a></p>';
    }
    $s.='</div></td><td align="center"><div id="cqlist'.$member['id'].'">';
    
    if ($count_res==0 and $total['count(*)']>0) 
    {
      $s.='<p align="center"><a href="javascript:;" onclick="$(\'#DelAllQhiddenInfoId\').val('.$member['id'].');$(\'#DelAllQ\').modal(\'show\');" title="Удалить все вопросы"><i class="fa fa-minus-square fa-lg"></i></a></p>';
    }
    
    $s.='</div></td>';
    mysqli_free_result($totq);

    $s.="<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s.="</tr>";
  }
  mysqli_free_result($sql);
  
  $s.="</tbody></table><p><button type='button' class='btn btn-outline btn-primary btn-sm' onclick='dialogOpen(\"addquestgroup&m=a&kid=".$kid."\",700,440)'><i class='fa fa-question-circle fa-fw'></i> Новая группа вопросов</button></p></div";
  //$s.="</table>";

  $json['content'] = $s;  

  $reminder = '';
  $qreminder = '';
  $areminder = '';
  $areminder2 = '';
  $mindreminder = '';
  $userreminder = '';
  
  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE knowsid='".$kid."' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE knowsid='".$kid."' AND ownerid='".USER_ID."' ORDER BY id DESC;");

  $s2 = "<div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='50'><i title='Итоговый балл' class='fa fa-calculator fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Время тестирования' class='fa fa-clock-o fa-lg'></i></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='50'><i title='Просмотр теста' class='fa fa-play-circle fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Результаты тестирования' class='fa fa-bar-chart fa-lg'></i></td>
              <td align='center' witdh='100'><i title='Дата создания теста' class='fa fa-calendar fa-lg'></i></td>
          </thead>
          <tbody>";

  $i=0;
  $countt=0;
  while($member = mysqli_fetch_array($sql))
  {
    $countt++;
    $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult WHERE testid='".$member['id']."' LIMIT 1;");
    $cnttests = mysqli_fetch_array($counttest);
    $count_res = $cnttests['count(*)'];
    mysqli_free_result($counttest); 


    if ($member['testtype']=='pass') { 
     $ic = "<i class='fa fa-check-square-o fa-lg' title='Зачетный тест'></i> "; 
    } else 
    if ($member['testtype']=='check') {
     $ic = "<i class='fa fa-share-square-o fa-lg' title='Проверочный тест'></i> "; 
    }

    if ($member['active']==1)
    {
     if ($member['external']==1)
      $s2.= "<tr class='info'><td witdh='30'><p>".++$i."</p></td>"; 
     else
      $s2.= "<tr class='success'><td witdh='30'><p>".++$i."</p></td>"; 
    }
    else
     if ($member['external']==1)
      $s2.= "<tr class='info'><td witdh='30'><p>".++$i."</p></td>"; 
    else
     $s2.= "<tr><td witdh='30'><p>".++$i."</p></td>"; 

    $s2.= "<td width='300'>
    <p>" .$ic . $member['name']. " <a onclick='dialogOpen(\"createtest&m=e&kid=".$kid."&id=".$member['id']."\",0,0)' href='javascript:;' title='Редактировать тест'><i class='fa fa-cog fa-lg'></i></a>";
//    if ($member['testtype']=='pass')
    $s2.="&nbsp;<a onclick='dialogOpen(\"editugintest&kid=".$kid."&id=".$member['id']."\",800,500)' href='javascript:;' title='Редактировать группы участников'><i class='fa fa-users fa-fw'></i></a>";
    if ($member['psy']==1)
     $s2.="&nbsp;<a href='psi&kid=".$kid."&id=".$member['id']."' title='Редактировать психологическую интерпретацию результатов'><i class='fa fa-comment-o fa-fw'></i></a>";
    if ($count_res==0) {
      $s2.='&nbsp;<a href="javascript:;" onclick="$(\'#DelTesthiddenInfoKid\').val('.$kid.');$(\'#DelTesthiddenInfoId\').val('.$member['id'].');$(\'#DelTest\').modal(\'show\');" title="Удалить тест"><i class="fa fa-trash fa-lg"></i></a>';
    }
    
    if (!empty($member['content']))
    {
     // mb_internal_encoding('UTF-8');
      if (mb_strlen($member['content'])>300)
       $s2.="<p><small>".mb_strcut($member['content'], 1, 300)."...</small></p>";
      else
       $s2.="<p><small>".$member['content']."</small></p>";
    }
    
    $s2.= '</p></td>';

    $qc=0;
    $tt=0;
    $sumball=0;
    $b = '';
    
    $pass = '';
    $acusers = 0;
    
   // if ($member['testtype']=='pass')
   // {
     $sql2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergrp WHERE testid='".$member['id']."' ORDER BY id");
     while($usergrp = mysqli_fetch_array($sql2))
     {
       if (defined("IN_ADMIN")) 
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$usergrp['usergroupid']."' LIMIT 1;");
       else
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM usergroups WHERE id='".$usergrp['usergroupid']."' AND userid='".USER_ID."' LIMIT 1;");
       $questgroup = mysqli_fetch_array($qg);

       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails WHERE usergroupid='".$usergrp['usergroupid']."' LIMIT 1;");
       $cntusers = mysqli_fetch_array($countu);
       $count_users = $cntusers['count(*)'];
       mysqli_free_result($countu); 
       
       $acusers += $count_users;
       $pass .= "<p>С ".data_convert ($usergrp['startdate'], 1, 1, 0)." по ".data_convert ($usergrp['stopdate'], 1, 1, 0)." <i class='fa fa-users fa-fw'></i> ".$questgroup['name']. " - ".$count_users."</p>";
       
       mysqli_free_result($qg); 
     }
     mysqli_free_result($sql2); 
   // } 
    
    $grq = 0;
    $adapt = false;
    $nonadaptive = false;
    
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
         $b .= "<p>".$c." <i title='Вопросов в выборке' class='fa fa-question fa-fw'></i> ".$testdata['qcount']."</p>";
    
        $qc += $testdata['qcount'];
        
        $tt += $questgroup['singletime']*$testdata['qcount'];
        $sumball += $questgroup['singleball']*$testdata['qcount'];
        if ($testdata['qcount']>0)
         $grq++;
       }
       else
       if ($member['testkind']=='adaptive' and $testdata['random'])
       {
        $c = "<i title='Адаптивная выборка вопросов' class='fa fa-repeat fa-fw'></i> ".$questgroup['name']; 

        if ($testdata['qcount']>0)
         $b .= "<p>".$c." <i title='Вопросов в выборке' class='fa fa-question fa-fw'></i> ".$testdata['qcount']."</p>";
    
        $qc += $testdata['qcount'];
        if ($testdata['qcount']<7) $nonadaptive = true;

        $tt += $questgroup['singletime']*$testdata['qcount'];
        $sumball += $questgroup['singleball']*$testdata['qcount'];
        if ($testdata['qcount']>0)
         $grq++;
         
        // Проверим критерии адаптивности групп
        if ($oldsb>0) 
         if ($oldsb!=$questgroup['singleball'])
          $adapt = true;
        $oldsb = $questgroup['singleball']; 
       }
      mysqli_free_result($qg); 
       
    }
    mysqli_free_result($td); 

    $b = '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapsegrp'.$member['id'].'">Группы вопросов</a>
                                            <i title="Групп вопросов" class="fa fa-question-circle fa-fw"></i> '.$grq.' <i title="Всего вопросов" class="fa fa-question fa-fw"></i> '.$qc.'
                                        </h4>
                                    </div>
                                    <div id="collapsegrp'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$b.'</div>
                                    </div>
                                </div>';

  //  if ($member['testtype']=='pass')
     $pass = '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="font-size:14px;">
                                            <a data-toggle="collapse" href="#collapseuser'.$member['id'].'">Пользователи</a>
                                            <i title="Количество пользователей" class="fa fa-users fa-fw"></i> '.$acusers.'
                                        </h4>
                                    </div>
                                    <div id="collapseuser'.$member['id'].'" class="panel-collapse collapse">
                                        <div class="panel-body">'.$pass.'</div>
                                    </div>
                                </div>';
    
    if ($member['testkind']=='adaptive')
     $s2.="<td align='center'><p>~".$sumball."</p></td>";
    else
     $s2.="<td align='center'><p>".$sumball."</p></td>";

    if ($tt>=60) 
     {
       $hours = (int) floor($tt / 60);
       $minutes = $tt % 60;
       if ($member['testkind']=='adaptive')
        $s2.="<td align='center'><p>~".$hours." ч. ".$minutes." мин.</p></td>";
       else
        $s2.="<td align='center'><p>".$hours." ч. ".$minutes." мин.</p></td>";
     } 
     else
     {
      if ($member['testkind']=='adaptive')
       $s2.="<td align='center'><p>~".$tt." мин.</p></td>";
      else
       $s2.="<td align='center'><p>".$tt." мин.</p></td>";
     }
    
     $s2.="<td width='300' align='center'>";
     $s2.=($qc>0)?$b:"";
     $s2.=($acusers>0)?$pass:"";
     $s2.="</td>";
    
     if ($qc>0)
     {
//      if ($member['psy']==0)
//      {
       if ($member['testkind']=='adaptive' and $qc>3 and $adapt)
        $s2.="<td><a title='Просмотр адаптивного теста' onclick='dialogOpen(\"viewadaptivetest&s=".$member['signature']."&m=".md5($member['signature']."check")."\",0,0)' href='javascript:;'><i class='fa fa-play-circle fa-lg'></i></a></td>";
       else
       if ($member['testkind']=='standard')
        {
         if ($member['psy']==1)
          $s2.="<td><a title='Просмотр психологического теста' onclick='dialogOpen(\"viewpsytest&s=".$member['signature']."&m=".md5($member['signature']."check")."\",0,0)' href='javascript:;'><i class='fa fa-play-circle fa-lg'></i></a></td>";
         else
          $s2.="<td><a title='Просмотр теста' onclick='dialogOpen(\"viewtest&s=".$member['signature']."&m=".md5($member['signature']."check")."\",0,0)' href='javascript:;'><i class='fa fa-play-circle fa-lg'></i></a></td>";
        }
//      }
//      else
//       $s2.="<td></td>";
     }
     else
     {
      $reminder .= $member['name'].' &middot; ';
      $s2.="<td></td>";
     }
    
     if ($acusers>0 and $member['active']==0)   
      $qreminder .= $member['name'].' &middot; ';
     
     if ($member['testkind']=='adaptive' and $qc<7)
      $areminder .= $member['name'].' &middot; ';

     if ($member['testkind']=='adaptive' and $nonadaptive)
      $areminder2 .= $member['name'].' &middot; ';

     if ($member['testkind']=='adaptive' and !$adapt)
      $mindreminder .= $member['name'].' &middot; ';
    
     if ($member['active']==1 and $member['testtype']=='pass' and $acusers==0)
      $userreminder .= $member['name'].' &middot; ';
    
     if ($count_res>0)
      $s2.="<td><a title='Результаты - ".$count_res."' href='vr&tid=".$member['signature']."'><i class='fa fa-bar-chart fa-lg'></i></a></td>";
     else
      $s2.="<td></td>";

     $s2.="<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
     $s2.="</tr>";
  }
  mysqli_free_result($sql);

  $s2.="</tbody></table>";

  if (strlen($qreminder)>0)
   $s2.='<div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		К тестам: <strong>'.$qreminder.'</strong> приглашены пользователи. Теперь надо активизировать тесты.
          </div>';

  if (strlen($reminder)>0)
   $s2.='<div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Параметры выборки вопросов не установлены для тестов: <strong>'.$reminder.'</strong>
          </div>';

  if (strlen($areminder)>0)
   $s2.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Недостаточное количество вопросов в адаптивных тестах: <strong>'.$areminder.'</strong>.
          </div>';

  if (strlen($areminder2)>0)
   $s2.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Недостаточное количество вопросов в группах в адаптивных тестах: <strong>'.$areminder2.'</strong>. Для активации алгоритма адаптивного тестирования, в каждой группе должно быть не менее семи вопросов.
          </div>';

  if (strlen($mindreminder)>0)
   $s2.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Одинаковая сложность!</strong> Для адаптивных тестов: <strong>'.$mindreminder.'</strong> выбранные группы вопросов имеют одинаковую сложность.
          </div>';

  if (strlen($userreminder)>0)
   $s2.='<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Зачетные тесты: <strong>'.$userreminder.'</strong> переведены в активное состояние, но не имеют участников.</strong>
          </div>';

  $s2.="<p><button type='button' class='btn btn-outline btn-primary btn-sm' onclick='dialogOpen(\"createtest&m=a&kid=".$kid."\",0,0)'><i class='fa fa-dashboard fa-fw'></i> Новый тест</button></p></div";

  $json['content2'] = $s2;  

  $json['knowname'] = "Область знаний <strong>".$knowname."</strong> 
   <a onclick='dialogOpen(\"eknows&m=e&id=".$kid."\",600,450)' href='javascript:;' title='Изменить область'><i class='fa fa-cog fa-lg'></i></a>";  

  // Удаление области возможно только если нет групп и нет детей
  if (defined("IN_ADMIN"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM knowledge WHERE parentid=".$kid." LIMIT 1;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM knowledge WHERE parentid=".$kid." AND userid='".USER_ID."' LIMIT 1;");
  $know = mysqli_fetch_array($sql);
  $childcnt = $know['count(*)'];
  mysqli_free_result($sql);

  if ($countq==0 and $countt==0 and $childcnt==0)
   $json['knowname'] .= " <a onclick='$(\"#hiddenInfoKnow\").val(".$kid.");$(\"#DelKnow\").modal(\"show\");' href='javascript:;' title='Удалить область знаний'><i class='fa fa-trash fa-lg'></i></a>";  
  
  $json['knowcontent'] = '<p>'.$knowcontent.'</p>
  <p><button type="button" class="btn btn-outline btn-primary btn-sm" onclick="dialogOpen(\'eknows&m=a&p='.$kid.'\',500,360)"><i class="fa fa-mortar-board fa-fw"></i> Новая подобласть</button>
  </p>';
    
  $json['ok'] = '1';  
  
} else 
   $json['ok'] = '0'; 
echo json_encode($json); 
