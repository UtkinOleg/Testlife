<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";
  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  $kid = $_GET["kid"];
  $mode = $_GET["m"];
  if (empty($mode))
   $mode = 'q';
   
  $title=$titlepage="Конструктор тестов";
  
  include "topadmin.php";

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

  function GetChild($mysqli, Knows $ks, Know $k)
  {
   echo "<ul>";
   foreach($ks->getKnows($k->getId()) as $tmpknow) 
   {
    echo "<li data-id='".$tmpknow->getId()."' title='".$tmpknow->getContent()."'>".$tmpknow->getName();
    
    $i = GetCnt($mysqli, $tmpknow->getId(), 'q');
    if ($i>0) 
     echo "&nbsp;<span class='badge2'><i class='fa fa-question'></i> ".$i;
    $ii = GetCnt($mysqli, $tmpknow->getId(), 't');
    if ($ii>0) 
     echo " <i class='fa fa-sitemap'></i> ".$ii;
    if ($i>0) 
     echo "</span>";
    
    GetChild($mysqli, $ks, $tmpknow);
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
  
  // Если параметр не задан - Найдем область с группами вопросов и покажем ее
  if (empty($kid))
  {
   $roots = $knows->getKnows(0);
   foreach($roots as $know) 
   {
    if (GetCnt($mysqli, $know->getId(), 'q')>0)
     {
      $kid = $know->getId();
      break;
     }
   }
  }

?>
<link rel="stylesheet" href="lms/scripts/treeview/themes/default/style.css" />
<style>
.badge2 {
display: inline-block;
min-width: 10px;
padding: 3px 7px;
font-size: 12px;
font-weight: 700;
line-height: 1;
color: #fff;
text-align: center;
white-space: nowrap;
vertical-align: baseline;
border-radius: 10px;
background: #497787 url("lms/scripts/jquery-ui/images/ui-bg_inset-soft_75_497787_1x100.png") 50% 50% repeat-x;
}
#spinner
{
  display: none;
  position: fixed;
	top: 50%;
	left: 50%;
	margin-top: -22px;
	margin-left: -22px;
	background-position: 0 -108px;
	opacity: 0.8;
	cursor: pointer;
	z-index: 8060;
  width: 44px;
	height: 44px;
	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;
  border-radius:7px;
}
</style>
<script src="lms/scripts/treeview/jstree.min.js"></script>
<script src="lms/scripts/testengine.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
    var act = '<?=$mode?>';
    $("#tabs").tabs();
    InitKnow(<?=( empty($kid) ? 'null' : $kid )?>);
    if (act==='t') 
     $("#tabs").tabs( "option", "active", 1 );
	});
</script>    
<div id="spinner"></div>

<?

  if ($knows->getCount()>0)
  {
   ?>
<div id="menu_glide" class="menu_glide">
<table width="99%" align="center" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
    <tr valign="top">
     <td width="40%">
      <h3 class='ui-widget-header ui-corner-all' align="center"><p><i class='fa fa-mortar-board fa-lg'></i> Области знаний <a onclick='dialogOpen("eknows&m=a&p=0",500,360)' title='Добавить корневую область' href='javascript:;'><i class='fa fa-plus fa-lg'></i></a></p></h3>
      <div id="jstree">
      <ul>
   <?         

   $rootknows2 = $knows->getKnows(0);
   foreach($rootknows2 as $know) 
   {
    echo "<li data-id='".$know->getId()."' title='".$know->getContent()."'>".$know->getName();
    
    $i = GetCnt($mysqli, $know->getId(), 'q');
    if ($i>0) 
     echo "&nbsp;<span class='badge2'><i class='fa fa-question'></i> ".$i;
    $ii = GetCnt($mysqli, $know->getId(), 't');
    if ($ii>0) 
     echo " <i class='fa fa-sitemap'></i> ".$ii;
    if ($i>0) 
     echo "</span>";
    
    GetChild($mysqli, $knows, $know);
    echo "</li>";
   }
  
  ?>
   </ul></div></td>
   <td width="60%"> 
    <div id="tabs">
     <ul>
      <li><a href="#questions"><i class='fa fa-question fa-lg'></i> Группы вопросов</a></li>
      <li><a href="#tests"><i class='fa fa-sitemap fa-lg'></i> Тесты</a></li>
     </ul>
     <div id='questions'>  
      <div id="content">
      </div>
     </div> 
     <div id='tests'>  
      <div id="content2">
      </div>
     </div> 
    </div> 
   </td>
   </tr>
  </table>
 </div> 
 <br> 
  <?

  }
  include "bottomadmin.php";
} else die;  
?>