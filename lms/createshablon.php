<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";
require_once "header.php"; 
?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget { 
font-family: Verdana,Arial,sans-serif; font-size: 0.9em;}
.button_disabled { 
background: #D1D4D8;  }.button_enabled {  } 
p { 
font: 16px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner { 
display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%;   height: 400px;   bottom : 50px;  position: absolute; overflow: auto;} 
.ui-corner-left {
border-top-left-radius: 0px;
}
.ui-corner-left {
border-bottom-left-radius: 0px;
}
.ui-corner-right {
border-top-right-radius: 0px;
}
.ui-corner-right {
border-bottom-right-radius: 0px;
}
</style>
<?

$action = "";
$action = $_POST["action"];


if ($action=='steptwo')
{
    $paid = $_POST["paid"];
    $pmode = $_POST["mode"];
    $selpaid = $_POST["selpaid"];

            if ($pmode=='project')
            {
             $tot3 = mysql_query("SELECT count(*) FROM poptions WHERE proarrid='".$paid."'");
             $tot3cnt = mysql_fetch_array($tot3);
             $k = $tot3cnt['count(*)'];
             mysql_free_result($tot3);
             
             $totl = mysql_query("SELECT * FROM poptions WHERE proarrid='".$selpaid."' ORDER BY id;");
             mysql_query("LOCK TABLES poptions WRITE");
             mysql_query("START TRANSACTION;");
             while($member = mysql_fetch_array($totl))
             {
              if (LOWSUPERVISOR and $k>=2) break;
              $query = "INSERT INTO poptions VALUES (0,
                                        '".$member['name']."',
                                        '".$member['content']."',
                                        '".$member['files']."',
                                        $paid,
                                        '".$member['typetext']."',
                                        '".$member['youtube']."',
                                        '".$member['filetype']."',
                                        '".$member['fileformat']."',
                                        '".$member['doptext']."',
                                        '".$member['link']."',
                                        0);";
              mysql_query($query);
              $k++;
             }
             mysql_query("COMMIT");
             mysql_query("UNLOCK TABLES");
             mysql_free_result($totl);

             echo '<script language="javascript">';
             echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/poptions&paid='.$paid.'&tab=1");';
             echo '</script>';
             exit();
            }
            else
            {
             $oldgroupid = 0;
             $groupid = 0;

             $tot3 = mysql_query("SELECT count(*) FROM shablon WHERE proarrid='".$paid."'");
             $tot3cnt = mysql_fetch_array($tot3);
             $k = $tot3cnt['count(*)'];
             mysql_free_result($tot3);

             $totl = mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' ORDER BY id;");
             mysql_query("LOCK TABLES shablongroups WRITE");
             mysql_query("LOCK TABLES shablon WRITE");
             mysql_query("START TRANSACTION;");
             while($member = mysql_fetch_array($totl))
             {
              if (LOWSUPERVISOR and $k>=2) break;

              $gst2 = mysql_query("SELECT * FROM shablongroups WHERE id='".$member['groupid']."'");
              $member2 = mysql_fetch_array($gst2);
              if ($member['groupid'] != $oldgroupid)
              {
               $query = "INSERT INTO shablongroups VALUES (0,
                                        '".$member2['name']."',
                                        ".$member2['maxball'].",
                                        $paid,
                                        0);";
               if (mysql_query($query))
               {
                $groupid = mysql_insert_id();
                $query = "INSERT INTO shablon VALUES (0,
                                        '".$member['name']."',
                                        '".$member['info']."',
                                        '$groupid',
                                        ".$member['maxball'].",
                                        $paid, 
                                        ".$member['complex'].", 
                                        ".$member['iniball'].", 
                                        ".$member['digital'].");";
                mysql_query($query);                         
                $k++;
               }
              } else
              {
                $query = "INSERT INTO shablon VALUES (0,
                                        '".$member['name']."',
                                        '".$member['info']."',
                                        '$groupid',
                                        ".$member['maxball'].",
                                        $paid, 
                                        ".$member['complex'].", 
                                        ".$member['iniball'].", 
                                        ".$member['digital'].");";
                mysql_query($query);                         
                $k++;
              }
              $oldgroupid = $member['groupid']; 
              mysql_free_result($gst2);
             }
             mysql_query("COMMIT");
             mysql_query("UNLOCK TABLES");
             mysql_free_result($totl);
             echo '<script language="javascript">';
             echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/shablons&paid='.$paid.'&tab=2");';
             echo '</script>';
             exit();
            } 
}
else
if ($action=='stepone') 
{
  $paid = $_POST["paid"];
  $pmode = $_POST["mode"];
  $id = $_POST["radio"];
  
  $ans = mysql_query("SELECT * FROM adminshab WHERE id='".$id."' LIMIT 1;");
  if (!$ans) puterror("Ошибка при обращении к базе данных");
  $answer = mysql_fetch_array($ans);
  $name = $answer['name'];
  $selpaid = $answer['paid'];
  mysql_free_result($ans);
  
  if ($pmode=='project')
  {
   $strname = 'шаблона проекта';
   $strname2 = ' Если список параметров шаблона <strong>'.$name.'</strong> подходит для Ващей модели, нажмите кнопку "Создать". Если нет - "Назад" или "Отмена".';
  }
  else
  {
   $strname = 'критериев';
   $strname2 = ' Если список критериев <strong>'.$name.'</strong> подходит для Ващей модели, нажмите кнопку "Создать". Если нет - "Назад" или "Отмена".';
  }
  
?>
<script>
  $(function() {
    $("#spinner").fadeOut("slow");
    $( "#back" ).button();
    $( "#next" ).button();
    $( "#close" ).button();
 });
 $(document).ready(function(){
    $('form').submit(function(){
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
</head>
<body>
    <div id="spinner">
    </div>
    <h3 class='ui-widget-header ui-corner-all' align="center">
      <p>Создание <? echo $strname; ?>
      </p></h3>
    <div class="ui-widget">	
      <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">		
        <p>
          <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;">
          </span> <? echo $strname2; ?>
        </p>	
      </div>
    </div>
    <div id="buttonsetm">
    <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
          <p align='center'>
            <div id="menu_glide" class="menu_glide" style="margin-top:40px;">
              <table class=bodytable border="0" width='95%' height='100%' align='center' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
              <?
            $i=0;
            if ($pmode=='project')
            {
             $totl = mysql_query("SELECT * FROM poptions WHERE proarrid='".$selpaid."' ORDER BY id;");
             while($member = mysql_fetch_array($totl))
             {
              echo "<tr><td witdh='50'><p>".++$i."</p></td>";
              echo "<td><p><strong>".$member['name']."</strong></p></td>";
              echo "<td><p>".$member['doptext']."</p></td>";
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Строка</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'textarea')
         echo "<td align='center'><p>Несколько строк</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'yes' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Внешняя ссылка</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'yes' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Ссылка на ролик Youtube</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Файл</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Фотография (картинка)</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Несколько файлов</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Несколько фотографий (картинок)</p></td>";
               echo "</tr>";
             }
             mysql_free_result($totl);
            }
            else
            {
             $totl = mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' ORDER BY id;");
             while($member = mysql_fetch_array($totl))
             {
              echo "<tr><td witdh='50'><p align='center'>".++$i."</p></td>";
              echo "<td><p><strong>".$member['name']."</strong></p></td>";
              $gst2 = mysql_query("SELECT * FROM shablongroups WHERE id='".$member['groupid']."'");
              $member2 = mysql_fetch_array($gst2);
              echo "<td align='left'><p>Группа: ".$member2['name']."</p></td>";
              echo "<td align='left'><p>Максимальный балл: ".$member['maxball']."</p></td></tr>";
              mysql_free_result($gst2);
              echo "</tr>";
             }
             mysql_free_result($totl);
            } 
              ?>
              </table>
            </div>
          </p></td>
      </tr>
    </table>
    </div>
    <div id="buttonset">  
     <form id="step_one" style="display: inline;" action="createshablon" method="get">
     <input type="hidden" name="action" value="">
     <input type="hidden" name="paid" value="<? echo $paid ?>">
     <input type="hidden" name="mode" value="<? echo $pmode ?>">
      <button style="font-size: 1em;" id="back" onclick="$('#step_one').submit();">
        <i class='fa fa-arrow-left fa-lg'></i> Назад
      </button> 
     </form>    
     <form id="step_two" style="display: inline;" action="createshablon" method="post">
     <input type="hidden" name="action" value="steptwo">
     <input type="hidden" name="paid" value="<? echo $paid ?>">
     <input type="hidden" name="mode" value="<? echo $pmode ?>">
     <input type="hidden" name="selpaid" value="<? echo $selpaid ?>">
      <button style="font-size: 1em;" id="next" onclick="$('#step_two').submit();">
        <i class='fa fa-check fa-lg'></i> Создать
      </button>    
     </form>
      <button style="font-size: 1em;" id="close" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Отмена
      </button>  
    </div>
</body>
</html>
<?
}
else
if (empty($action)) 
{
  $paid = $_GET["paid"];
  $pmode = $_GET["mode"];
  if ($pmode=='project')
  {
   $strname = 'шаблона проекта';
   $strname2 = 'Выберите один шаблон проекта для Вашей модели.';
  }
  else
  {
   $strname = 'критериев';
   $strname2 = 'Выберите один набор критериев для Вашей модели.';
  }
?>
<script>
  $(function() {
    $("#spinner").fadeOut("slow");
    $( "#next" ).button();
    $( "#close" ).button();
    $( "#radio" ).buttonset();
  });
 $(document).ready(function(){
    $('form').submit(function(){
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
</head>
<body>
  <form id="step_one" action="createshablon" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="stepone">
    <input type="hidden" name="paid" value="<? echo $paid ?>">
    <input type="hidden" name="mode" value="<? echo $pmode ?>">
    <div id="spinner">
    </div>
    <h3 class='ui-widget-header ui-corner-all' align="center">
      <p>Создание <? echo $strname; ?>
      </p></h3>
    <div class="ui-widget">	
      <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">		
        <p>
          <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;">
          </span>		<? echo $strname2; ?>
        </p>	
      </div>
    </div>
    <div id="buttonsetm">
    <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
          <p align='center'>
            <div id="menu_glide" class="menu_glide" style="margin-top:20px;">
              <table class=bodytable border="0" width='95%' height='100%' align='center' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
                <tr><td><div id="radio">
                <?
           $ans = mysql_query("SELECT * FROM adminshab WHERE type='".$pmode."' ORDER BY id;");
           if (!$ans) puterror("Ошибка при обращении к базе данных");
           while($answer = mysql_fetch_array($ans))
           {
            $paid = $answer['paid'];
            if ($pmode=='project')
            {
             $totl = mysql_query("SELECT count(*) FROM poptions WHERE proarrid='".$paid."'");
             $totall = mysql_fetch_array($totl);
             $count = "количество праметров: ".$totall['count(*)'];  // Количество параметров шаблона проекта
             mysql_free_result($totl);
            }
            else
            {
             $totl = mysql_query("SELECT count(*) FROM shablon WHERE proarrid='".$paid."'");
             $totall = mysql_fetch_array($totl);
             $count = "количество критериев: ".$totall['count(*)'];  // Количество критериев
             mysql_free_result($totl);
            } 
            echo "<p><input type='radio' name='radio' id='check".$answer['id']."' value='".$answer['id']."'><label for='check".$answer['id']."'>".$answer['name']."</label>&nbsp;&nbsp;&nbsp;".$answer['content']." &middot; ".$count."</p>";
           }
           mysql_free_result($ans);
                ?>
                </div></td></tr>
              </table>
            </div>
          </p></td>
      </tr>
    </table>
   </div>
   <div id="buttonset">  
      <button style="font-size: 1em;" id="next" onclick="$('#step_one').submit();">
        <i class='fa fa-arrow-right fa-lg'></i> Далее
      </button>    
      <button style="font-size: 1em;" id="close" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Отмена
      </button>  
   </div>
  </form>
</body>
</html>
<?
}
} else die;
?>