<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";

  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetCnt($mysqli, $kid, $mode)
  {
    if ($mode=='q')
     $groups = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' LIMIT 1;");
    else
    if ($mode=='t')
     $groups = mysqli_query($mysqli,"SELECT count(*) FROM testgroups WHERE knowsid='".$kid."' AND ownerid='".USER_ID."' LIMIT 1;");
    $groupsd = mysqli_fetch_array($groups);
    $cnt = $groupsd['count(*)'];
    mysqli_free_result($groups);
    return $cnt;
  }

  function GetChildCnt($mysqli, Knows $ks, Know $k)
  {
   $t=0;
   foreach($ks->getKnows($k->getId()) as $tmpknow) $t++;
   return $t;
  }

  function GetChild($mysqli, Knows $ks, Know $k, $level)
  {

    $i = GetCnt($mysqli, $k->getId(), 'q');
    $span = "";
    if ($i>0) 
     $span .= "&nbsp;<span class='badge'><i class='fa fa-question'></i> ".$i;
    $ii = GetCnt($mysqli, $k->getId(), 't');
    if ($ii>0) 
     $span .= " <i class='fa fa-dashboard'></i> ".$ii;
    if ($i>0) 
     $span .= "</span>";

   if ($level==3)
    echo "<ul class='nav nav-third-level'>";
   else
    echo "<ul class='nav nav-other-level'>";
   
   echo "
   <li>
    <a onclick='getquest(".$k->getId().")' href='javascript:;'>".$k->getName()." ".$span."</a>
   </li>";
   
   foreach($ks->getKnows($k->getId()) as $tmpknow) 
   {
    echo "<li>";

    if (GetChildCnt($mysqli, $ks, $tmpknow)>0)
    {
     echo "<a href='#'>".$tmpknow->getName()." <span class='fa arrow'></span></a>";
     GetChild($mysqli, $ks, $tmpknow,4);
    }
    else  
     echo "<a onclick='getquest(".$tmpknow->getId().")' href='javascript:;'>".$tmpknow->getName()." ".$span."</a>";
    
    echo "</li>";
   }
   echo "</ul>";
  }
  
  // Инициализация областей
  if (defined("IN_ADMIN"))
   $sql = mysqli_query($mysqli,"SELECT * FROM knowledge ORDER BY id;");
  else
   $sql = mysqli_query($mysqli,"SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY id;");

  $knows = new Knows();
  
  while($member = mysqli_fetch_array($sql))
   $knows->addKnow(new Know($member['id'], 
                            $member['name'], 
                            $member['content'], 
                            $member['parentid'], 
                            $member['userid']));
  mysqli_free_result($sql);
  
?>

<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание тестов онлайн">
    <link rel="icon" href="ico/favicon.ico">
    <title>Test Life</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/dataTables.responsive.css" rel="stylesheet">
    <link href="css/customadmin.css" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="lms/scripts/myboot.js"></script>
  </head>
  <body>
      
      <div id="spinner"></div>

      <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">TestLife &middot; создание тестов онлайн</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>1111</strong>
                                    <span class="pull-right text-muted">
                                        <em>Вчера</em>
                                    </span>
                                </div>
                                <div>Сообщение...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="usermsgs">
                                <strong>Все сообщения</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Все задачи</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <?=USER_FIO?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="edituser"><i class="fa fa-user fa-fw"></i> Профиль</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="logout"><i class="fa fa-sign-out fa-fw"></i> Выход</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i> Тесты</a>
                        </li>
                        <li>
                            <a href="vr"><i class="fa fa-bar-chart-o fa-fw"></i> Результаты</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Создание тестов<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a onclick='dialogOpen("eknows&m=a&p=0",500,360)' href="javascript:;"><i class='fa fa-mortar-board fa-fw'></i> Новая область знаний</a>
                                </li>
                                <?php
   $rootknows2 = $knows->getKnows(0);
   foreach($rootknows2 as $know) 
   {
    echo "<li>";

    $i = GetCnt($mysqli, $know->getId(), 'q');
    $span = "";
    if ($i>0) 
     $span .= "&nbsp;<span class='badge'><i class='fa fa-question'></i> ".$i;
    $ii = GetCnt($mysqli, $know->getId(), 't');
    if ($ii>0) 
     $span .= " <i class='fa fa-dashboard'></i> ".$ii;
    if ($i>0) 
     $span .= "</span>";

   if (GetChildCnt($mysqli, $knows, $know)>0)
    {
     echo "<a href='#'>".$know->getName()." <span class='fa arrow'></span></a>";
     GetChild($mysqli, $knows, $know, 3);
    }
    else 
     echo "<a onclick='getquest(".$know->getId().")' href='javascript:;'>".$know->getName()." ".$span."</a>";
    
    echo "</li>";
   }   
                                ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper">

<?php

         $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT email FROM users WHERE id='".USER_ID."' LIMIT 1");
         if (!$tot2) puterror("Ошибка при обращении к базе данных");
         $total2 = mysqli_fetch_array($tot2);
         $email = $total2['email'];       
         mysqli_free_result($tot2); 
         if ($_SERVER['REQUEST_URI'] != "/edituser")
         {

          if (empty($email))
          {
          ?>
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Внимание:</strong> В вашем профиле не заполнено поле - электронная почта. <strong><a href="edituser">Перейти в профиль</a></strong></p>
          </div>
          <?
          }
         }


} else die;  
?>