<?php
  include "config.php";
?>
<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание адаптивных, психологических и стандартных тестов онлайн, внешняя экспертиза тестов, проведение онлайн тестирования и расширенный анализ результатов">
    <meta name="keywords" content="тестирование, онлайн тестирование, адаптивный тест, адаптивное тестирование, online test, online тестирование, анализ результатов тестирования, освоение тем теста, решаемость заданий, психологическое тестирование, психологический тест, внешняя экспертиза" /> 
    <meta name="copyright" content="Oleg Utkin" /> 
    <meta name="author" content="Oleg Utkin" />
    <meta name='yandex-verification' content='6dcc51c08b1fd8e9' />
    <meta name="google-site-verification" content="L7sugM88OlBgf0Duo_Kr_hwhNo0OCyzTY4BB9u-Q_hA" />
    <meta property="og:image" content="img/testlife.png" />
    <title>Test Life</title>
    <link rel="icon" href="ico/favicon.ico">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  </head>
<body>

<?php
 include "bootstrapsocial.php";
?>

<div class="modal fade" id="myModalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">    
  <div class="modal-dialog">       
    <div class="modal-content">           
      <div class="modal-header">               
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;       
        </button>               
        <h4 class="modal-title" id="myModalLabel1"></h4>           
      </div>           
      <div class="modal-body">      
        <form role="form">           
          <div id="Nameformgroup" class="form-group">               
            <input type="text" class="form-control" id="InputName" placeholder="Имя">           
          </div>           
          <div id="Emailformgroup" class="form-group">               
            <input type="email" class="form-control" id="InputEmail" placeholder="Email">           
          </div>           
          <input type="hidden" id="hiddenInfo" value="">           
          <div id="Infoformgroup" class="form-group">               
            <label for="InputInfo" id="LabelInfo">          
            </label>     
            <textarea class="form-control" rows="5" id="InputInfo"></textarea>            
          </div>      
        </form>           
      </div>           
      <div class="modal-footer">               
        <button type="button" class="btn btn-primary" onclick="formSend();">Отправить сообщение       
        </button>               
        <button type="button" class="btn btn-primary" onclick="$('#myModalMsg').modal('hide');">Закрыть       
        </button>           
      </div>       
    </div>   
  </div>
</div>      

<div id="spinner"></div>

<div id="wrapper">                         

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" style="margin-top: 4px;" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Меню</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a title="TestLife version <?=$version?>" class="navbar-brand" href="http://testlife.org"><img src="img/testlife.png" height="12"></a>
            </div>
            <div class="navbar-collapse collapse">
            <ul class="nav navbar-top-links navbar-left" style="background-color: #f8f8f8;">
                        <li>
                            <a href="javascript:;" onclick="$('body,html').animate({scrollTop: $('#page-wrapper-user').offset().top - 40}, 500);"><i class="fa fa-dashboard fa-fw"></i> Открытые тесты</a>
                        </li>
                        <li>
                            <a href="h">Документация</a>
                        </li>
                        <li>
                            <a href="javascript:;" onclick="$('body,html').animate({scrollTop: $('#about').offset().top - 40}, 500);">О сервисе</a>
                        </li>
                        <li>
                            <a href="javascript:;" title="Отправить сообщение" onclick="formShow('Отправить сообщение','Сообщение');">Контакты</a>
                        </li>
            </ul>
            <ul class="nav navbar-top-links navbar-right" style="background-color: #f8f8f8;">
                        <li>
                            <a href="javascript:;" onclick="$('#tlLoginForm').modal('show');">Вход</a>
                        </li>
            </ul>
            </div>
       </nav>

       
<section class="slider-container" style="margin-top: 20px; background-image: url(img/bg.png);"> 
  <div class="container hidden-xs hidden-sm"> 
    <div class="row"> 
      <div class="col-md-12"> 
        <div class="slides"> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Тестирование онлайн</h2> 
              <p>TestLife &ndash; сервис для создания адаптивных, психологических, стандартных<br />
               онлайн тестов с возможностью внешней экспертизы, проведения онлайн тестирования и последующего анализа полученных результатов. 
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/slide1.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Адаптивное тестирование</h2> 
              <p>Создавайте самостоятельно адаптивные тесты для<br />формирования индивидуальных траекторий обучения.<br />
              Анализируйте результаты на основе модели Item Response Theory.
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/adapt.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Психологическое тестирование</h2> 
              <p>Создавайте самостоятельно стандартизованные тесты, тесты достижений<br /> или тесты на профессиональную пригодность.
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/psy.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Стандартное тестирование</h2> 
              <p>Стандартные тесты на определение уровня знаний -<br />формируются из определенного количества вопросов<br />
              на основе нескольких разделов или тем. 
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/slide2.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Освоение разделов или тем теста</h2> 
              <p>Диаграмма освоения разделов или тем в тесте<br />
              позволяет отслеживать уровень знаний по каждой дидактической единице,<br />
              как индивидуально, так и для группы тестируемых. 
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/analiz1.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Решаемость заданий</h2> 
              <p>Диаграмма позволяет оценить уровень подготовки тестируемых <br />
              по всем вопросам в выбранном тесте.
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/analiz2.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slide"> 
            <div class="slide-content"> 
              <h2> <small>TestLife</small>Плотность распределения баллов</h2> 
              <p>Диаграмма позволяет судить о характере распределения результатов<br />
              для любой выборки тестируемых или группы тестируемых.
              </p> 
            </div> 
            <div class="slide-image"> 
              <a href="#"> 
                <img src="img/analiz3.png" class="img-responsive" /> </a> 
            </div> 
          </div> 
          <div class="slides-nextprev-nav"> 
            <a href="#" class="prev"> 
              <i class="fa fa-caret-left fa-fw" style="margin-left: -3px;"></i> </a> 
            <a href="#" class="next"> 
              <i class="fa fa-caret-right fa-fw" style="margin-left: 3px;"></i> </a> 
          </div> 
        </div> 
      </div> 
    </div> 
  </div> 
</section> 
       
<div id="page-wrapper-user" style="padding: 0px 20px;">
     <div class="row">
        <div class="col-md-12">
          <h3><span class="text-muted">Примеры открытых проверочных тестов</span></h3>
        </div>
     </div>

<?php

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
      &nbsp;<i class="fa fa-male fa-lg"></i>&nbsp;Психологический&nbsp;
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
   
   echo $s;  
?>     

     <div id="about" class="row">
        <div class="col-md-7">
          <h3>TestLife <span class="text-muted">&ndash; сервис для создания <strong><a href="h&id=6">адаптивных</a>, <a href="h&id=13">психологических</a> и <a href="h&id=5">стандартных</a></strong> тестов с возможностью <strong><a href="h&id=20">внешней экспертизы тестовых заданий</a></strong>, проведения <strong>онлайн</strong> тестирования и <strong><a href="h&id=8">расширенного анализа</a></strong> полученных результатов.</span></h3>
        </div>
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/slide1.png" alt="TestLife">
        </div>
     </div>
     <hr>
     <div class="row">
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/adapt.png" alt="TestLife">
        </div>
        <div class="col-md-7">
          <h3><strong><a href="h&id=6">Адаптивное</a> тестирование</strong> TestLife <span class="text-muted">&ndash; формирование индивидуальной траектории обучения и вычисление результатов на основе модели Item Response Theory.</span></h3>
        </div>
     </div>
     <hr>
     <div class="row">
        <div class="col-md-7">
          <h3><strong><a href="h&id=13">Психологическое</a> тестирование</strong> <span class="text-muted">&ndash; создание психологических тестов: стандартизованного, теста достижений и теста на профессиональную пригодность.</span></h3>
        </div>
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/psy.png" alt="TestLife">
        </div>
     </div>
     <hr>
     <div class="row">
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/analiz1.png" alt="TestLife">
        </div>
        <div class="col-md-7">
          <h3>Диаграмма <strong><a href="h&id=10">Освоение разделов или тем теста</a></strong> <span class="text-muted">позволяет отслеживать уровень знаний по каждой дидактической единице (группе вопросов), как индивидуально, так и для группы тестируемых.</span></h3>
        </div>
     </div>
     <hr>
     <div class="row">
        <div class="col-md-7">
          <h3>Диаграмма <strong><a href="h&id=11">Решаемость заданий</a></strong> <span class="text-muted">позволяет оценить уровень подготовки тестируемых по всем вопросам в выбранном тесте.</span></h3>
        </div>
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/analiz2.png" alt="TestLife">
        </div>
     </div>
     <hr>
     <div class="row">
        <div class="col-md-5">
          <img class="img-thumbnail" width="550" src="img/analiz3.png" alt="TestLife">
        </div>
        <div class="col-md-7">
          <h3>Диаграмма <strong><a href="h&id=12">Плотность распределения баллов</a></strong> <span class="text-muted">позволяет судить о характере распределения результатов для любой выборки тестируемых или группы тестируемых.</span></h3>
        </div>
     </div>
     <hr>

<?php
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions LIMIT 1;");
  $total = mysqli_fetch_array($sql);
  $qcnt = $total['count(*)'];
  mysqli_free_result($sql);
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups LIMIT 1;");
  $total = mysqli_fetch_array($sql);
  $tcnt = $total['count(*)'];
  mysqli_free_result($sql);
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult LIMIT 1;");
  $total = mysqli_fetch_array($sql);
  $rcnt = $total['count(*)'];
  mysqli_free_result($sql);
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM expertquestions LIMIT 1;");
  $total = mysqli_fetch_array($sql);
  $excnt = $total['count(*)'];
  mysqli_free_result($sql);
?>

<div class="row">
  <div class="col-lg-12">
             <h3 class="text-center">
               Вопросов создано&nbsp;&middot;&nbsp;<strong><?=$qcnt;?></strong>&nbsp;&middot;&nbsp;
               Тестов создано&nbsp;&middot;&nbsp;<strong><?=$tcnt;?></strong>&nbsp;&middot;&nbsp;
               Экспертиз заданий&nbsp;&middot;&nbsp;<strong><?=$excnt;?></strong>&nbsp;&middot;&nbsp;
               Результатов тестирования&nbsp;&middot;&nbsp;<strong><?=$rcnt;?></strong>&nbsp;&middot;&nbsp;
             </h3>
  </div>
</div>
     
<div class="row">
                <div class="col-lg-12">
                    <div class="jumbotron">
                        <h3>Зарегистрируйтесь бесплатно, создавайте <a href="h&id=5">стандартные</a>, <a href="h&id=6">адаптивные</a> или <a href="h&id=13">психологические</a> тесты, <a href="h&id=7">организуйте онлайн тестирование</a> и <a href="h&id=8">анализируйте результаты</a>.</h3>
                        <p></p>
                        <p>
                <a class="btn btn-primary" title="через ВКонтакте" href="hlogin?provider=vkontakte&u=s">          
                  <i class="fa fa-vk"></i></a>             
                <a class="btn btn-primary" title="через Facebook" href="hlogin?provider=facebook&u=s">          
                  <i class="fa fa-facebook"></i></a>             
                <a class="btn btn-danger" title="через Yandex" href="hlogin?provider=yandex&u=s">          
                  <strong>Я</strong></a>             
                <a class="btn btn-primary" title="через Mail.ru" href="hlogin?provider=mailru&u=s">          
                  <i class="fa fa-at"></i></a>             
                <a class="btn btn-info" title="через Twitter" href="hlogin?provider=twitter&u=s">          
                  <i class="fa fa-twitter"></i></a>             
                <a class="btn btn-primary" title="через LinkedIn" href="hlogin?provider=linkedin&u=s">          
                  <i class="fa fa-linkedin"></i></a>             
                <a class="btn btn-warning" title="через Google" href="hlogin?provider=google&u=s">          
                  <i class="fa fa-google-plus"></i></a>             
                       </p>
                    </div>
                </div>
</div>

     
</div>
           
</div>


<footer class="site-footer"> 
  <div class="container"> 
    <div class="row"> 
      <div class="col-sm-6">TestLife v.<?=$version?> &copy; 2015 <a href="mailto:siberia-soft@yandex.ru?subject=TestLife">Олег Уткин</a>  
      </div> 
      <div class="col-sm-6"> 
       <span class="pull-right" style="top: 1px;">
        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,moimir,gplus" data-yashareTheme="counter">
        </div>
       </span> 
      </div> 
    </div> 
  </div> 
</footer>      

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="js/tweenmax.js"></script>
    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
    <script type="text/javascript">
    
	  	$(document).ready(function() {

		  	$('.fancybox').fancybox();

        //activetests();

    		var $sliders = $(".slides");
		
    		if($sliders.length && $.isFunction($.fn.neonSlider))
    		{
	    		$sliders.neonSlider({
		   		itemSelector: '.slide',
	   			autoSwitch: 5
		    	});
	    	}	  	
        <?php $err = $_GET["err"];
        $msg = $_GET["msg"];
        if (!empty($err))
        {
         if ($err=='address')
          $msg = 'Пользователь с таким адресом электронной почты уже зарегистрирован.';
         else 
         if ($err=='login')
          $msg = 'Ошибка авторизации пользователя. '.$msg;
         else 
         if ($err=='login2')
          $msg = 'Ошибка авторизации пользователя.';
         else 
         if ($err=='login3')
          $msg = 'Ошибка авторизации пользователя.';
         else 
         if ($err=='dbase')
          $msg = 'Ошибка сохранения данных пользователя.';
         else 
         if ($err=='token')
          $msg = 'Ошибка при передаче данных пользователя.';
        }
        if ($msg!='') {?>
        $('#myInfoMsgContent').html('<?=$msg?>');
        $('#myInfoMsg').modal('show');  
        <?php } ?>
      });

      function closeFancyboxAndRedirectToUrl(url){
       $.fancybox.close();
       location.replace(url);
      }    
      function closeFancybox(){
       $.fancybox.close();
      }    
      
      function dialogOpen(phref, pwidth, pheight) {
				if (pwidth==0)
         pwidth = document.documentElement.clientWidth;
				if (pheight==0)
         pheight = document.documentElement.clientHeight;
        $.fancybox.open({
					href : phref,
					type : 'iframe',
          width : pwidth,
          height : pheight,
          fitToView : true,
          autoSize : false,          
          modal : true,
          showCloseButton : false,
					padding : 5
				});
      }
      
      function activetests() 
      {
        $("#spinner").fadeIn("slow");
        $.post('getactivetmptests.json',{},  
        function(data){  
         eval('var obj='+data);         
         $('#spinner').fadeOut("slow");
         if(obj.ok=='1')
          $('#tests').html(obj.content);        
        });  
      }

      function up(sign) 
      {
        $.post('getuptest.json',{s:sign},  
        function(data){  
         eval('var obj='+data);  
         if(obj.ok=='1')
          $('#badge'+sign).html('<i class="fa fa-thumbs-o-up fa-fw"></i>&nbsp;'+obj.cnt);        
        });  
      } 
      
function formShow(title,info) {
     $('#Nameformgroup').removeClass('has-error');
     $('#Emailformgroup').removeClass('has-error');
     $('#Infoformgroup').removeClass('has-error');
     $('#myModalLabel1').html(title);
     $('#LabelInfo').html(info);
     $('#hiddenInfo').val(info);
     $('#InputInfo').val('');
     $('#myModalMsg').modal('show');  
  }

  function formSend() {
     var postParams;
     var tt = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
     
     $('#Nameformgroup').removeClass('has-error');
     $('#Emailformgroup').removeClass('has-error');
     $('#Infoformgroup').removeClass('has-error');
     if ($('#InputName').val().length==0) 
     {
         $('#Nameformgroup').addClass('has-error');
         $('#InputName').focus();
     }
     else
     if ($('#InputEmail').val().length==0) 
     {
         $('#Emailformgroup').addClass('has-error');
         $('#InputEmail').focus();
     }
     else
     if (!tt.test($('#InputEmail').val())) 
     {
         $('#Emailformgroup').addClass('has-error');
         $('#InputEmail').focus();
     }
     else
     if ($('#InputInfo').val().length==0)
     { 
         $('#Infoformgroup').addClass('has-error');
         $('#InputInfo').focus();
     }
     else
     {
         $('#Nameformgroup').removeClass('has-error');
         $('#Emailformgroup').removeClass('has-error');
         $('#Infoformgroup').removeClass('has-error');
         postParams = {
                    name: $('#InputName').val(),
                    email: $('#InputEmail').val(),
                    title: $('#hiddenInfo').val(),
                    body: $('#InputInfo').val()
                }; 
         $('#myModalMsg').modal('hide');  
         $.post("msgajax", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? myInfoMsgShow("Ваше сообщение получено! В ближайшее время мы свяжемся с Вами.") : myInfoMsgShow("Ошибка при отправке сообщения!")
                });
     }           
  }    
  function myInfoMsgShow(info) {
     $('#myInfoMsgContent').html(info);
     $('#myInfoMsg').modal('show');  
  }    
   
   </script>
   <script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter27317486 = new Ya.Metrika({id:27317486, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/27317486" style="position:absolute; left:-9999px;" alt="" /></div></noscript>                       
   <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-53631745-2', 'auto');
    ga('send', 'pageview');
   </script>
  </body>
</html>

            