<?php
if(defined("IN_SUPERVISOR") AND USER_EXPERT_KIM) {

  $kid = $_GET["kid"];
  $mode = 'q';
   
  require_once "config.php";  

  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetCnt($mysqli, $kid, $mode)
  {
    $groups = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='".$kid."' LIMIT 1;");
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
    if ($i>0) 
     $span .= "</span>";

   if ($level==3)
    echo "<ul class='nav nav-third-level'>";
   else
    echo "<ul class='nav nav-other-level'>";
   
   echo "
   <li>
    <a title='Вопросы и тесты области знаний ".$k->getName()."' onclick='getquest(".$k->getId().")' href='javascript:;'><i class='fa fa-ellipsis-h fa-fw'></i> ".$span."</a>
   </li>";
   
   foreach($ks->getKnows($k->getId()) as $tmpknow) 
   {
    echo "<li>";

    $i = GetCnt($mysqli, $tmpknow->getId(), 'q');
    $span = "";
    if ($i>0) 
     $span .= "&nbsp;<span class='badge'><i class='fa fa-question'></i> ".$i;
    if ($i>0) 
     $span .= "</span>";

    if (GetChildCnt($mysqli, $ks, $tmpknow)>0)
    {
     echo "<a href='#'>".$tmpknow->getName()." <span class='fa arrow'></span></a>";
     GetChild($mysqli, $ks, $tmpknow,4);
    }
    else  
     echo "<a onclick='getquest(".$tmpknow->getId().",\"q\")' href='javascript:;'>".$tmpknow->getName()." ".$span."</a>";
    
    echo "</li>";
   }
   echo "</ul>";
  }
  
  // Инициализация областей для эксперта
  
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT kn.* FROM knowledge as kn, usergroups as ug, useremails as ue WHERE ug.usergrouptype=1 AND ue.usergroupid=ug.id AND ue.email='".USER_EMAIL."' AND kn.usergroupid=ug.id ORDER BY kn.id;");

  $knows = new Knows();
  
  while($member = mysqli_fetch_array($sql))
   $knows->addKnow(new Know($member['id'], 
                            $member['name'], 
                            $member['content'], 
                            0, 
                            $member['userid']));
  mysqli_free_result($sql);

  // Если параметр не задан - Найдем область с группами вопросов и покажем ее
  if (empty($kid))
  {
   $kid=0;
   $z=0;
   $roots = $knows->getKnows(0);
   foreach($roots as $know) 
   {
    if ($z==0)
     $kkid = $know->getId();
    if (GetCnt($mysqli, $know->getId(), 'q')>0)
     {
      $kid = $know->getId();
      break;
     }
    $z++; 
   }
   if ($kid==0)
    $kid = $kkid;
  }
  
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
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="lms/scripts/myboot.js?v=<?=$version?>"></script>
  </head>
<body>
      
<div id="spinner"></div>

<div class="modal fade" id="myInfoMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelWarning" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabelWarning">Сообщение системы</h4>
      </div>
      <div id="myInfoMsgContent" class="modal-body">
      </div>
    </div>
  </div>
</div>

<div id="wrapper">
<?php 
 include "hornavigation.php";
 include "leftsidebar.php";
 
// print_r(opcache_get_status());
?>
                        <li>
                            <a href="qt"><i class="fa fa-wrench fa-fw fa-inverse" style="color:#5ED52D;"></i> Создание тестов<span class="fa arrow"></span></a>
                        </li>
                        <li>
                            <a href="scales"><i class="fa fa-arrows-h fa-fw fa-inverse" style="color:#5ED52D;"></i> Шкалы оценок</a>
                        </li>
                        <li>          
                            <a href="#"><i class="fa fa-check-circle fa-fw fa-inverse" style="color:#FF2F66;"></i> Экспертиза заданий<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse in">
<?php
   $rootknows2 = $knows->getKnows(0);
   foreach($rootknows2 as $know) 
   {
    echo "<li>";

    $i = GetCnt($mysqli, $know->getId(), 'q');
    $span = "";
    if ($i>0) 
     $span .= "&nbsp;<span class='badge'><i class='fa fa-question'></i> ".$i;
    if ($i>0) 
     $span .= "</span>";

   if (GetChildCnt($mysqli, $knows, $know)>0)
    {
     echo "<a href='#'><i class='fa fa-mortar-board fa-fw'></i> ".$know->getName()." <span class='fa arrow'></span></a>";
     GetChild($mysqli, $knows, $know, 3);
    }
    else 
     echo "<a onclick='getexpertquest(".$know->getId().",\"q\")' href='javascript:;'><i class='fa fa-mortar-board fa-fw'></i> ".$know->getName()." ".$span."</a>";
    
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

<div class="modal fade" id="myModalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelQ" aria-hidden="true">    
  <div class="modal-dialog">       
    <div class="modal-content">           
      <div class="modal-header">               
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;       
        </button>               
        <h4 class="modal-title" id="myModalLabelQ"></h4>           
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
        <button type="button" class="btn btn-primary" onclick="formSend();">Отправить       
        </button>               
        <button type="button" class="btn btn-primary" onclick="$('#myModalMsg').modal('hide');">Закрыть       
        </button>           
      </div>       
    </div>   
  </div>
</div>      

<div class="modal fade" id="myHelpMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelHelp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabelHelp">Сообщение</h4>
      </div>
      <div id="myHelpMsgContent" class="modal-body">
      </div>
    </div>
  </div>
</div>
        
<div id="page-wrapper">
<?php
 include "reminder.php";
 
  if (count($knows->getKnows(0))>0)
  { 
?>
            <div class="row">
                <div class="col-lg-12" id="datacontent">
                    <div class="panel panel-default">
                        <div class="panel-heading" id="knowname">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="knowcontent"></div>
                            <div <?=( $mode=='q' ? 'class="tab-pane fade in active"' : 'class="tab-pane fade"' )?> id="questions">
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
            <!-- /.row -->
<?}?>            
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="lms/scripts/myhelp.js?v=1.0.1"></script>
    <script type="text/javascript">
	  	$(document).ready(function() {
		  	$('.fancybox').fancybox();
        getexpertquest(<?=( empty($kid) ? 'null' : $kid )?>,'<?=$mode?>');
	  	});
      function closeFancyboxAndRedirectToUrl(kid, mode){
       $.fancybox.close();
       getexpertquest(kid, mode);
      }    
      function closeFancybox(){
       $.fancybox.close();
      }    
   </script>                        
  </body>
</html>

<?php
} 
else 
if(defined("IN_USER"))
 Header("Location: ts");
else 
 die; 
?>            