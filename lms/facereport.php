<?php
  require_once "config.php";
  require_once "func.php";
  
  $selpaid = $_GET["a"];
  if (empty($selpaid)) die;

  $res5 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$selpaid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysqli_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  $paname = $proarray["name"];
  $exlistname = $proarray["exlistname"];
  $openexpert = $proarray["openexpert"];

  $title="Итоговый рейтинг модели конкурса <strong>".$paname."</strong> по всем участникам <span class='text-muted'> c ".data_convert ( $proarray['startdate'], 1, 0, 0)." по ".data_convert ( $proarray['stopdate'], 1, 0, 0)."</span>";
  include "lib/mainhead.php";

?>  
 
    <div class="navbar-wrapper">
      <div class="container">

        <div id="mainbar" class="navbar navbar-inverse navbar-static-top" role="navigation" style="position:fixed;">
          <div class="container">
            <div class="navbar-header" style="padding-left:15px;">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Навигация</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?=$site?>"><img src="img/logoexpert.gif"></a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li><a href="javascript:;" onclick="formLoginShow();">Вход</a></li>
                <li><a id="scroll_tops" href="javascript:;">Рейтинги</a></li>
                <li><a id="scroll_projects" href="javascript:;">Проекты</a></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

<section id="intro" data-speed="8" data-type="background">

<div class="container marketing" style="padding-top:60px;">

<div class="thumbnail">
      <hr class="featurette-divider">
         <h3 class="text-center"><?echo $title;?></h3>
      <hr class="featurette-divider">
  <p>&nbsp;</p>
<div class="table-responsive">
  <table class="table text-center">

<tr>
  <th class="danger text-center"><h4>Место в рейтинге</h4></th>
  <th class="active text-center"></th>
  <th class="active text-center"><h4>Наименование проекта</h4></th>
  <th width="100" class="danger text-center"><h4>Сумма средних баллов</h4></th>
  <th width="100" class="warning text-center"><h4>Средний балл по рейтингу</h4></th>
  <th width="100" class="success text-center"><h4>Проведено экспертиз</h4></th>
</tr>

<?php
 $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published')");
 $lst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published') ORDER BY maxball DESC;");
 if (!$lst || !$tot) puterror("Ошибка при обращении к базе данных");
 $r=0;
 while($list = mysqli_fetch_array($lst))
  {

    $lst3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
    if (!$lst3) puterror("Ошибка при обращении к базе данных");
    $i=0;
    $newprcent = 0;
    
    while($list3 = mysqli_fetch_array($lst3))
     {
   
      $subit = "";

      $lst4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT SUM(ball),SUM(maxball) FROM shablondb WHERE userid='".$list3['expertid']."' AND memberid='".$list['id']."' AND exlistid='0'");
      if (!$lst4) puterror("Ошибка при обращении к базе данных");
      $list4 = mysqli_fetch_array($lst4);
      
      $cntlst4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT COUNT(ball) FROM shablondb WHERE userid='".$list3['expertid']."' AND memberid='".$list['id']."' AND exlistid='0'");
      if (!$cntlst4) puterror("Ошибка при обращении к базе данных");
      $cntlist4 = mysqli_fetch_array($cntlst4);
      
      if ($list4['SUM(maxball)']!=0) 
      {
       $percent = ($list4['SUM(ball)'] / $list4['SUM(maxball)']) * $ocenka;  
       $i += $cntlist4['COUNT(ball)'];
      }
      else
       $percent = 0;

      $newprcent = $newprcent + $percent; 
      $yessubit = false;
      if (($percent>0) || ($list4['SUM(maxball)']!=0)) {
        if (!empty($exlistname))
         $subit .= $exlistname." - ";
        $subit .= $list4['SUM(ball)']." из ".$list4['SUM(maxball)']." (".round($percent,2).")";
        $yessubit = true;
      }
       
      $ex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM expertcontentnames WHERE proarrid='".$selpaid."' ORDER BY id");
      if (!$ex) puterror("Ошибка при обращении к базе данных");
      while($exmember = mysqli_fetch_array($ex))
      {  
     
      $lst4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT SUM(ball),SUM(maxball) FROM shablondb WHERE userid='".$list3['expertid']."' AND memberid='".$list['id']."' AND exlistid='".$exmember['id']."'");
      if (!$lst4) puterror("Ошибка при обращении к базе данных");
      $list4 = mysqli_fetch_array($lst4);
      
      $cntlst4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT COUNT(ball) FROM shablondb WHERE userid='".$list3['expertid']."' AND memberid='".$list['id']."' AND exlistid='".$exmember['id']."'");
      if (!$cntlst4) puterror("Ошибка при обращении к базе данных");
      $cntlist4 = mysqli_fetch_array($cntlst4);
      
      if ($list4['SUM(maxball)']!=0) 
      {
       $percent = ($list4['SUM(ball)'] / $list4['SUM(maxball)']) * $ocenka;  
       $i += $cntlist4['COUNT(ball)'];
      }
      else
       $percent = 0;
      $newprcent = $newprcent + $percent; 
      if (($percent>0) || ($list4['SUM(maxball)']!=0)) {
        if ($yessubit)
         $subit .= ", ";
        $subit .= $exmember['name']." - ".$list4['SUM(ball)']." из ".$list4['SUM(maxball)']." (".round($percent,2).")";
       }
      }
   

   }
   mysqli_free_result($lst3);
   
   if ($openexpert>0)
    {
      $etot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND p.id='".$list['id']."'");
      $etotal = mysqli_fetch_array($etot);
      $ecount = $etotal['count(*)'];
      if ($ecount>0)
      {
       $i = $ecount;
       if ($list['maxball']>0)
        $aball = $list['maxball'] / $i;
       elseif ($newprcent>0)
        $aball = $newprcent / $i;
       else 
        $aball = 0;
      }
      else
       $aball = 0;
    }
    else
    if ($i>0) 
    {
     if ($list['maxball']>0)
      $aball = $list['maxball'] / $i;
     elseif ($newprcent>0)
      $aball = $newprcent / $i;
     else 
      $aball = 0;
    }
    else
     $aball = 0;

   if (($openexpert==0 and $list['status']=='published') or ($openexpert>0 and 
   ( $list['status']=='published' or $list['status']=='accepted' or $list['status']=='inprocess')))
    $bname = "<h4><a href='project?a=".$list['id']."' title='Просмотр проекта ".htmlspecialchars($list['info'])."'>".$list['info']."</a></h4>"; 
   else
    $bname = "<h4>".$list['info']."</h4>";
   
   $number = "<h4><small>".$list['id']."</small></h4>";
     
   $namer = '';  
   $res3cnt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE memberid='".$list['id']."' AND LENGTH(info)>0");
   $param3cnt = mysqli_fetch_array($res3cnt);
   if ($param3cnt['count(*)']>0)
   {
     $namer = $bname.'<p></p><div class="panel panel-default" style="margin-bottom: 10px; border: none; -webkit-box-shadow: none; box-shadow: none;">
     <div class="panel-heading" style="border-bottom: none;">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$r.'">Комментарии экспертов</a> <span class="badge" style="font-size:0.7em; background-color: #999;">'.$param3cnt['count(*)'].'</span>
     </div>
     <div id="collapse'.$r.'" class="panel-collapse collapse">
      <div class="panel-body">';
     
     $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablondb WHERE memberid='".$list['id']."' AND LENGTH(info)>0");
     
     while($param3 = mysqli_fetch_array($res3))
      $namer .= "<p><small>".data_convert ( $param3['puttime'], 1, 0, 0)."</small> ".$param3['info']."</p>";   
     
     mysqli_free_result($res3);
      
     $namer .= '</div></div></div>'; 
   } 
   else
    $namer = $bname;
   mysqli_free_result($res3cnt);
   
   $r++; 
?>

<tr>
  <th width="100" class="danger text-center"><h4><?= $list['maxball']>0 ? '<span class="badge" style="font-size:1em;">'.$r.'</span>' : ($newprcent>0 ? '<span class="badge" style="font-size:1em;">'.$r.'</span>' : '-') ?></h4></th>
  <th class="active text-center"><?= $number?></th>
  <th class="active text-left"><?= $namer?></th>
  <th width="100" class="danger text-center"><h4><?= $list['maxball']>0 ? round($list['maxball'],2) : ($newprcent>0 ? round($newprcent,2) : "-") ?></h4></th>
  <th width="100" class="warning text-center"><h4><?= $aball>0 ? round($aball,2) : "-" ?></h4></th>
  <th width="100" class="success text-center"><h4><?= $aball>0 ? $i : "-" ?></h4></th>
</tr>
  
<?php  
  }
  
  mysqli_free_result($lst);
  mysqli_free_result($tot);
  mysqli_free_result($res5);
?>
  </table>
</div>
</div>
</div>
</section>

 <div class="container">
  <?php
  echo "<hr class='featurette-divider'><h3 class='text-center'>Пояснения к таблице итогового рейтинга</h3><hr class='featurette-divider'>";
  echo "<p>Итоговый рейтинг формируется в режиме реального времени в процессе оценки проектов экспертами. По мере того, как эксперты 
  заполняют экспертные листы, рейтинг автоматически пересчитывается. На оценку всех проектов отводится определенный срок. 
  После экспертизы всех проектов сформируется окончательный рейтинг.</p>";
  echo "<p>Итоговый рейтинг формируется по показателю <b>Сумма средних баллов</b>, который вычисляется на основании количества проведенных 
  экспертиз по данному проекту, полученному среднему баллу (обычно по стобалльной системе) по каждой экспертизе и суммарному итогу всех средних баллов.</p>";
  echo "<p>Показатель <b>Средний балл по рейтингу</b> вычисляется как отношение суммы средних баллов к количеству проведенных экспертиз.</p>"; 
  ?>
 </div>
 
<?php
 include "lib/topcarusel.php";
 include "lib/publiccarusel.php";
 include "lib/modalfooter.php";
?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/docs.min.js"></script>
  <script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
  <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
  <script>
   var maxtops = <?echo $cnt;?>;
   var maxpublics = <?echo $cnt;?>;
  </script>
  <script src="scripts/newscript.pack.js"></script>
<?
  include "lib/counters.php";
?>
  
 </body>
</html>