<?php
  require_once "config.php";
  require_once "func.php";
  include "lib/mainhead.php";
  $selpaid = $_GET["a"];
  if (empty($selpaid)) die;
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


<?php 
  
  $res5 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$selpaid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysqli_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  $paname = $proarray["name"];
  $exlistname = $proarray["exlistname"];
  $openexpert = $proarray["openexpert"];

  $title="Опубликованные проекты модели конкурса <strong>".$paname."</strong><span class='text-muted'> c ".data_convert ( $proarray['startdate'], 1, 0, 0)." по ".data_convert ( $proarray['stopdate'], 1, 0, 0)."</span>";
?>

<section id="intro" data-speed="8" data-type="background">
 <div class="container marketing" style="padding-top:60px;">
  <div class="thumbnail">
      <hr class="featurette-divider">
         <h3 class="text-center"><?echo $title;?></h3>
      <hr class="featurette-divider">
  <div class="panel-group" id="accordion">

<?php
 $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$selpaid."' ORDER BY maxball DESC;");
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
 mysqli_free_result($res5);
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
   var maxtops = <?echo $cnt;?>;
   var maxpublics = <?echo $cnt;?>;
  </script>
  <script src="scripts/newscript.pack.js"></script>
<?
  include "lib/counters.php";
?>
  
 </body>
</html>