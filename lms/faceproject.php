<?php
   require_once "config.php";
   require_once "func.php";
   require_once "lib/func2.php";
  
   $pid = $_GET["a"];
   if (empty($pid)) die;

   $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$pid."' LIMIT 1;");
   if (!$gst) puterror("Ошибка при обращении к базе данных");
   $member = mysqli_fetch_array($gst);
   $paid = $member['proarrid'];
   $projectname = $member['info'];
   $regdate = $member['regdate'];

   $pa1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
   $proarray = mysqli_fetch_array($pa1);
   $daf = $proarray['defaultshablon'];
   $paname = $proarray["name"];
   $openexpert = $proarray['openexpert'];

   $btot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."' LIMIT 1;");
   if (!$btot) puterror("Ошибка при обращении к базе данных");
   $totbcnt = mysqli_fetch_array($btot);
   $countb = $totbcnt['count(*)'];
   
   $cntp = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM pindicator WHERE proarrid='".$paid."' LIMIT 1;");
   $param3cntp = mysqli_fetch_array($cntp);
   $countp = $param3cntp['count(*)'];
   mysqli_free_result($cntp);        

   $cntc = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM comments WHERE projectid='".$pid."' LIMIT 1;");
   $param3 = mysqli_fetch_array($cntc);
   $countcomm = $param3['count(*)'];
   mysqli_free_result($cntc);        

   $cntc = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE memberid='".$pid."' AND LENGTH(info)>0 LIMIT 1;");
   $param3 = mysqli_fetch_array($cntc);
   $countcomm2= $param3['count(*)'];
   mysqli_free_result($cntc);        

   mysqli_free_result($gst);        
   mysqli_free_result($btot);        

   include "lib/mainhead.php";
?>  
<div id="spinner"></div>
   
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
                <li id="dropdown2" class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Проект <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a id="scroll_content" href="javascript:;"><?='Содержание проекта '.$projectname?></a></li>
                    <? if ($countp>0) {?><li><a id="scroll_indicator" href="javascript:;">Показатели проекта <span class="badge"><?=$countp?></span></a></li><?}?>
                    <? if ($countcomm>0) {?><li><a id="scroll_comments" href="javascript:;">Комментарии к проекту <span class="badge"><?=$countcomm?></span></a></li><?}?>
                    <? if ($countcomm2>0) {?><li><a id="scroll_comments2" href="javascript:;">Комментарии экспертов <span class="badge"><?=$countcomm2?></span></a></li><?}?>
                  </ul>
                </li>
                <li id="dropdown1" class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Опубликовнные проекты <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a id="scroll_intro" href="javascript:;"><?='Опубликованные проекты конкурса '.$paname?></a></li>
                    <li><a id="scroll_projects2" href="javascript:;">Еще опубликованные проекты</a></li>
                  </ul>
                </li>
                <li><a id="scroll_tops" href="javascript:;">Рейтинги</a></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>

<div id="content" class="container marketing" style="padding-top:60px;">
<?php 
 if (($openexpert==0 and $member['status']=='published') or ($openexpert>0 and 
 ( $member['status']=='published' or $member['status']=='accepted' or $member['status']=='inprocess')))
 {
  
  if (empty($daf))
   $v.= '<hr class="featurette-divider"><h3 class="text-center">'.$member['info'].' <small>дата создания: '.data_convert ($regdate, 1, 0, 0).'</small></h3><hr class="featurette-divider">';
  else
   $v.= '<hr class="featurette-divider"><h3 class="text-center">'.$member['info'].' <small>'.$daf.' &middot; дата создания: '.data_convert ($regdate, 1, 0, 0).'</small></h3><hr class="featurette-divider">';

  mysqli_free_result($gst);        
  mysqli_free_result($btot);        
  
  $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$paid."' AND multiid='0' ORDER BY id;");
  $slidercounter=0;
  if ($sql) {
   while($param = mysqli_fetch_array($sql))
    $v .= newview($mysqli, $pid, $param, $tabid2, $upload_dir, ++$slidercounter);
   echo $v;
  }
  
  mysqli_free_result($sql);        
}
?>
</div>
<?php if ($countb>0){?> 
<div class="container marketing">
 <p>&nbsp;</p>
 <p class="text-center"><a class="btn  btn-primary" href="javascript:;" onclick="addcontent();" role="button">Далее</a></p>
 <p>&nbsp;</p>
</div>
<?}?>

<?php if ($countp>0){?> 

<section id="intro4" data-speed="8" data-type="background">
<div id="indicator" class="container marketing" style="padding-top:30px;">

<div class="thumbnail">
      <hr class="featurette-divider">
         <h3 class="text-center">Показатели проекта <?=$projectname?></h3>
      <hr class="featurette-divider">
  <p>&nbsp;</p>
<div class="table-responsive">
  <table class="table text-center">

<tr>
  <th class="active text-center"><h4>№</h4></th>
  <th class="active text-center"><h4>Наименование показателя</h4></th>
  <th width="100" class="success text-center"><h4>Значение</h4></th>
</tr>

<?php 
  
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM pindicator WHERE proarrid='".$paid."' ORDER BY id");
  $z=0;
  while($param = mysqli_fetch_array($res3))
   { 
      $iname = $param['name'];
      $z++;
      $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$pid."' AND optionsid='".$param['poptionid1']."'");
      $param4 = mysqli_fetch_array($res4);
      $ind1 = $param4['content'];
      mysqli_free_result($res4);        

      $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$pid."' AND optionsid='".$param['poptionid2']."'");
      $param4 = mysqli_fetch_array($res4);
      $ind2 = $param4['content'];
      mysqli_free_result($res4);        
      
      if ($param['operation'] == 'mul')
       $res = $ind1 * $ind2;
      else
      if ($param['operation'] == 'div') 
      {
          if ($ind2>0)
           $res = round(($ind1 / $ind2) * 100,2);
          else
           $res = 0;
      }
      else
      if ($param['operation'] == 'sum') 
        $res = $ind1 + $ind2;
      else
      if ($param['operation'] == 'sub') 
        $res = $ind1 - $ind2;
      ?>
<tr>
  <th class="active text-center"><h4><?=$z?></h4></th>
  <th class="active"><h4><?=$iname?></h4></th>
  <th width="100" class="success text-center"><h4><?=$res?></h4></th>
</tr>
      <?php  
   }
   mysqli_free_result($res3);        
?>

 </table>
</div>
</div>
</div>
</section>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?}?>

<?php if ($countcomm>0){?> 
<div id="comments" class="container marketing">

      <hr class="featurette-divider">
         <h3 class="text-center">Комментарии к проекту <strong><?=$projectname?></strong></h3>
      <hr class="featurette-divider">
  <p>&nbsp;</p>

<?php 
  
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM comments WHERE projectid='".$pid."' ORDER BY id DESC;");
  $z=0;
  while($param = mysqli_fetch_array($res3))
   { 
     echo "<h4><small>".data_convert ( $param['cdate'], 1, 0, 0).":</small> ".$param['content']."</h4><hr style='border-top: 1px solid #497787;'>";   
   }
   mysqli_free_result($res3);        
?>
</div>
<?}?>

<?php if ($countcomm2>0){?> 
<div id="comments2" class="container marketing">

      <hr class="featurette-divider">
         <h3 class="text-center">Комментарии экспертов к проекту <strong><?=$projectname?></strong></h3>
      <hr class="featurette-divider">
  <p>&nbsp;</p>

<?php 
  
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablondb WHERE memberid='".$pid."' ORDER BY puttime");
  $z=0;
  while($param = mysqli_fetch_array($res3))
   { 
    if (!empty($param['info']))
     echo "<h4><small>".data_convert ( $param['puttime'], 1, 0, 0).":</small> ".$param['info']."</h4><hr style='border-top: 1px solid #497787;'>";   
   }
   mysqli_free_result($res3);        
?>

</div>
<p>&nbsp;</p>
<?}?>

<?php 
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  $exlistname = $proarray["exlistname"];

  $title="Опубликованные проекты модели конкурса <strong>".$paname."</strong><span class='text-muted'> c ".data_convert ( $proarray['startdate'], 1, 0, 0)." по ".data_convert ( $proarray['stopdate'], 1, 0, 0)."</span>";
?>

<section id="intro" data-speed="8" data-type="background">
 <div class="container marketing" style="padding-top:30px;">
  <div class="thumbnail">
      <hr class="featurette-divider">
         <h3 class="text-center"><?echo $title;?></h3>
      <hr class="featurette-divider">
  <div class="panel-group" id="accordion">

<?php

 $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid."' ORDER BY maxball DESC;");
 if (!$gst) puterror("Ошибка при обращении к базе данных");
 $top=0;
  while($member = mysqli_fetch_array($gst))
  {
   $top++;
   if (($openexpert==0 and $member['status']=='published') or ($openexpert>0 and 
   ( $member['status']=='published' or $member['status']=='accepted' or $member['status']=='inprocess')))
   { 

     echo '<div class="panel panel-default">
     <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$top.'">'.$member['info'].'</a> <span class="badge" style="font-size:1em;">'.round($member['maxball'],2).' баллов</span>
      </h4>
     </div>
     <div id="collapse'.$top.'" class="panel-collapse collapse">
      <div class="panel-body">';
     
    echo "<p>Дата создания: ".data_convert ($member['regdate'], 1, 0, 0)."</p>";
    echo "<p>Просмотр проекта: <a class='btn btn-primary' role='button' href='project?a=".$member['id']."' title='Просмотр проекта ".htmlspecialchars($member['info'])."'><strong>".htmlspecialchars($member['info'])."</strong></a></p>"; 

    $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
    if (!$res3) puterror("Ошибка при обращении к базе данных");
    while($param = mysqli_fetch_array($res3))
    { 
    $res4=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysqli_fetch_array($res4);

    if ($param['files']=="yes") 
    {
     if (!empty($param4['filename'])) 
     { 
     $kb = round($param4['filesize']/1024,2);
     if ($kb>1000) 
      $mb = round($param4['filesize']/1048576,2);
     if ($param['filetype']=="file") 
     { 
      echo "<p><a href='file.php?id=".$param4['secure']."' target='_blank'>";
      $filename1 = explode(".", $param4['filename']); 
      $filenameext1 = $filename1[count($filename1)-1]; 
      echo "<img class='img-thumbnail' style='display: inline;' ";
      if (strtolower($filenameext1)=='pdf')
       echo "src='img/pdf.png'";
      else
      if (strtolower($filenameext1)=='zip' || strtolower($filenameext1)=='rar')
       echo "src='img/zip.jpg'";
      else
      if (strtolower($filenameext1)=='xls' || strtolower($filenameext1)=='xlsx')
       echo "src='img/xls.gif'";
      else
      if (strtolower($filenameext1)=='doc' || strtolower($filenameext1)=='docx')
       echo "src='img/doc.gif'";
      else
       echo "src='img/f32.jpg'";
      
      echo" height='20' alt='Загрузить ".$param4['filename']."'> ".$param4['filename']."</a> ";
      if ($kb>1000) 
       echo "<span class='badge'>".$mb." Мб</span></p>";
      else 
       echo "<span class='badge'>".$kb." кб</span></p>";
     } 
     }  
    }  
    }
    mysqli_free_result($res3);
    mysqli_free_result($res4);
    echo '</div></div></div>'; 
  }
 } 
 mysqli_free_result($gst);
?>

</div>
</div>
</div>
</section>

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
   var maxtops = <?=$cnt?>;
   var maxpublics = <?=$cnt?>;
   var maxtab = <?=$countb?>;
   var tabid1 = 0;
   var id1 = <?=$pid?>;
   var paid1 = <?=$paid?>;

   $('#scroll_content').click(function () {
         $('#dropdown2').removeClass('open');
         $('body,html').animate({scrollTop: $("#content").offset().top - 40}, 500); return false;
   });

   $('#scroll_indicator').click(function () {
         $('#dropdown2').removeClass('open');
         $('body,html').animate({scrollTop: $("#indicator").offset().top - 40}, 500); return false;
   });

   $('#scroll_comments').click(function () {
         $('#dropdown2').removeClass('open');
         $('body,html').animate({scrollTop: $("#comments").offset().top - 40}, 500); return false;
   });

   $('#scroll_comments2').click(function () {
         $('#dropdown2').removeClass('open');
         $('body,html').animate({scrollTop: $("#comments2").offset().top - 40}, 500); return false;
   });

   $('#scroll_intro').click(function () {
         $('#dropdown1').removeClass('open');
         $('body,html').animate({scrollTop: $("#intro").offset().top - 40}, 500); return false;
   });
   $('#scroll_projects2').click(function () {
         $('#dropdown1').removeClass('open');
         $('body,html').animate({scrollTop: $("#projects").offset().top - 20}, 500); return false;
   });
   
   function addcontent() { 
    if (tabid1 < maxtab) {  
     $("#spinner").fadeIn("slow");
     $.post('viewtabjson.php',{id:id1, paid: paid1, tabid: tabid1}, 
     function(data){  
      $("#spinner").fadeOut("slow");
      eval('var obj='+data);         
      if(obj.ok=='1') $('#content').append(obj.content);        
     });
     tabid1++;
    }
   }  
  </script>
  <script src="scripts/newscript.pack.js"></script>
<?
  include "lib/counters.php";
?>
 </body>
</html>