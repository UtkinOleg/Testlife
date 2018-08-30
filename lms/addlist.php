<?php

$error = "";
$action = $_POST["action"];

if (empty($action)) 
{

 $paid = $_GET["paid"];
 $ext = $_GET["ext"];
 $exlistid = $_GET["exlist"];
 $openaddlist = false;
 $secret = $_GET["sl"];

// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";
 
   $pa3 = mysqli_query($mysqli,"SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
   if (!$pa3) puterror("Ошибка при обращении к базе данных");
   $projectarray = mysqli_fetch_array($pa3);

 if (!empty($secret))
 {
   if ($projectarray['openexpert']>0 and $projectarray['openexperturl']==$secret)
    $openaddlist = true;
   else
   {
       include "header.php";
?>
</head><body>           
           <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p align="center">Неверный ключ открытой экспертизы!</p>
            	</div>
           </div>  
<p></p>
<p align="center"><a href='<? echo $site; ?>'>Экспертная система оценки проектов</a></p>
</body></html>
               <?
       die; 
   } 
 }


 if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT") or $openaddlist) 
 {

  if ($openaddlist)
   $title=$titlepage="Новая открытая экспертиза проекта ('".$projectarray['name']."')";
  else
   $title=$titlepage="Новая экспертиза проекта ('".$projectarray['name']."')";
  include "topadmin.php";


  // Проверим на дату начала и окончания экспертизы
  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$ik);
  $day=$ik[1][0];
  $month=$ik[2][0];
  $year=$ik[3][0];
  $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
  $date1 = $projectarray['checkdate1'];
  $date2 = $projectarray['checkdate2'];
  $arr1 = explode(" ", $date1);
  $arr2 = explode(" ", $date2);  
  $arrdate1 = explode("-", $arr1[0]);
  $arrdate2 = explode("-", $arr2[0]);
  $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
  $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
  if ($timestamp3 < $timestamp1)
  {
    ?>
           <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p align="center"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Экспертиза проектов еще не началась!</p>
            	</div>
           </div>  
               <?
       die;
  } 
  else
  if ($timestamp3 > $timestamp2)
  {
    ?>
           <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p align="center"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Экспертиза проектов завершена!</p>
            	</div>
           </div>  

               <?
       die;
  }  

  if (!empty($exlistid)) {
    $ex = mysqli_query($mysqli,"SELECT name FROM expertcontentnames WHERE id='".$exlistid."'");
    if (!$ex) puterror("Ошибка при обращении к базе данных");
    $exmember = mysqli_fetch_array($ex);
    $exname = $exmember['name'];
    echo "<p align='center'><h1 class='z1'>Экспертный лист - ".$exname."</h1></p>";
  } 
  else
   $exlistid = 0;

?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.8em;
}
</style>
<script type="text/javascript">
$(document).ready(function() {

    $( "#submit1" ).button();
    $( "#submit2" ).button();
 
    $('#submit1').click(function() { 
        $(".iferror").hide();
        var hasError = false;
 
        var projectVal = $("#projectid").val();
        if(projectVal == '') {
            $("#submit1").after('<span class="iferror" style="text-align:center;">Необходимо выбрать проект для экспертизы!</span>');
            hasError = true;
        }
        if(hasError == true) { return false; }
    });
    $('#submit2').click(function() { 
        $(".iferror").hide();
        var hasError = false;
 
        var projectVal = $("#projectid").val();
        if(projectVal == '') {
            $("#submit2").after('<span class="iferror" style="text-align:center;">Необходимо выбрать проект для экспертизы!</span>');
            hasError = true;
        }
        if(hasError == true) { return false; }
    });

});
</script>

<div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Выберите один проект для экспертизы и нажмите кнопку <strong>Продолжить</strong></p>
            	</div>
</div><p></p> 

<div id='menu_glide' class='ui-widget-content ui-corner-all'>
<table style="table-layout:fixed" width="70%" border="0" cellpadding="3" cellspacing="3" bordercolorlight="white" bordercolordark="white" align="center">

<form action="addlist" method="post">
<input type="hidden" name="paid" value="<? echo $paid; ?>">
<input type="hidden" name="secret" value="<? echo $secret; ?>">
<input type="hidden" name="ext" value="<? echo $ext; ?>">
<input type="hidden" name="exlistid" value="<? echo $exlistid; ?>">
<input type="hidden" name="action" value="post1">
<input type="hidden" id="defparams" name="defparams" value="1">
<? 

  $options = array();
  $values = array();
  
  $memrealcnt = 0;
  if (!$openaddlist) 
  {
   $res1 = mysqli_query($mysqli,"SELECT p.* FROM projects as p, proexperts as e WHERE (p.status='inprocess' OR p.status='finalized') AND p.proarrid=e.proarrid AND p.proarrid='".$paid."' AND e.expertid='".USER_ID."' ORDER BY p.info ASC");
   while($member = mysqli_fetch_array($res1))
   { 
   $res43 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE userid='".USER_ID."' AND memberid='".$member['id']."' AND exlistid='".$exlistid."'");
   if (!$res43)  {
    $action = ""; 
    $error = $error."<LI>Ошибка при обращении к базе данных\n";
   }
   $cnt33 = mysqli_fetch_array($res43);
   $count23 = $cnt33['count(*)'];
   if ($count23==0) 
    {
     // $options[$memrealcnt] = "<option value='".$member[id]."'>№".$member[id]." (".$member['info'].")</option>"; 
     $options[$memrealcnt] = "<li class='ui-widget-content' id='".$member['id']."'><p>Проект <b>№".$member['id']."</b> ".$member['info']."</p></li>";
     $values[$memrealcnt] = $member['id'];
     $memrealcnt++;
    }
   }       
  } else {
   $res1 = mysqli_query($mysqli,"SELECT p.* FROM projects as p WHERE (p.status='inprocess' OR p.status='finalized') AND p.proarrid='".$paid."' ORDER BY p.info ASC");
   while($member = mysqli_fetch_array($res1))
   { 
     $options[$memrealcnt] = "<li class='ui-widget-content' id='".$member['id']."'><p>Проект <b>№".$member['id']."</b> ".$member['info']."</p></li>";
     $values[$memrealcnt] = $member['id'];
     $memrealcnt++;
   }       
  }
?>

    <tr>
        <td colspan='1' align='center'>
        </td>
    </tr>           

    <tr>
        <td colspan='1' align='center'>
            <?
            if ($memrealcnt>0) {?>
             <input type="submit" value="Продолжить" id="submit1">&nbsp;
            <? } ?>
        </td>
    </tr>           

<?
    
  if ($memrealcnt>0) {?>
    <tr><td>

<style>
  #feedback { font-size: 1em; }
  #selectable .ui-selecting { background: #9CBED4; }
  #selectable .ui-selected { background: #9CBED4; }
  #selectable { list-style-type: none; margin: 1; padding: 1; width: 90%; }
  #selectable li { margin: 2px; padding: 0.5em; font-size: 1em; height: auto; }
</style>
<script>
  $(function() {
    $( "#selectable" ).selectable({
        selecting: function(event, ui){
            if( $(".ui-selected, .ui-selecting").length > 1){
                  $(ui.selecting).removeClass("ui-selecting");
            }
        },
        stop: function() {
        $( ".ui-selected", this ).each(function() {
          var index = $(this).attr('id');
          $( "#projectid" ).val( index );
        });   
      }
    });
  });
</script>
<input type="hidden" name="projectid" id="projectid" style="border: 0; font-weight: bold;">

    <p align='center'>
    <div class="error" style="display:none;">
      <span></span>.<br clear="all"/>
    </div>
    <ol id="selectable">         
      <? 
       foreach ($options as $item) {
        echo $item;
       }
      ?>
       </ol>
       
     <!--    </select>
        </td></tr></table>  -->
        
        </p>
   <?}?>     

    </td>
    </tr>
    <tr>
        <td colspan='1' align='center'>
            <?
            if ($memrealcnt>0) {?>
            <? } else { ?>
            <p>
            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p align="center"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> Экспертиза завершена для всех проектов!</p>
            	</div>
            </div>         
            </p>        
            <? } ?>
        </td>
    </tr>   
    <tr>
        <td colspan='1' align='center'>
            <?
            if ($memrealcnt>0) {?>
             <input type="submit" value="Продолжить" id="submit2">&nbsp;
            <? } ?>
        </td>
    </tr>           


</form>
</table>
<p></p>
</div>
<p></p>
<p align="center"><a href='<? echo $site; ?>'>Экспертная система оценки проектов</a></p>
<?    
include "bottomadmin.php";
} else die;
}
else
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action=="post2") 
{

 $ext = $_POST["ext"];
 $exlistid = $_POST["exlistid"];
 $projectid = $_POST["projectid"];
 $paid = $_POST["paid"];
 $comment = "".$_POST["comment"];
 $secret = $_POST["secret"];
 $openaddlist = false;


 // Устанавливаем соединение с базой данных
 include "config.php";
 include "func.php";

 if (!empty($secret))
 {
   $pa3 = mysqli_query($mysqli,"SELECT openexpert, openexperturl, name FROM projectarray WHERE id='".$paid."' LIMIT 1");
   if (!$pa3) puterror("Ошибка при обращении к базе данных");
   $projectarray = mysqli_fetch_array($pa3);
   if ($projectarray['openexpert']>0 and $projectarray['openexperturl']==$secret)
    $openaddlist = true;
   else
    die; 
 }

 if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT") or $openaddlist) 
 {

  if ($openaddlist)
   $title=$titlepage="Новая открытая экспертиза проекта (модель '".$projectarray['name']."')";
  else
   $title=$titlepage="Новая экспертиза проекта (модель '".$projectarray['name']."')";
  include "topadmin.php";

  $tot = mysqli_query($mysqli,"SELECT count(s.id) FROM shablon as s, shablongroups as gr WHERE s.proarrid='".$paid."' AND s.groupid=gr.id AND gr.exlistid='".$exlistid."'");
  if (!$tot)  {
    $action = ""; 
    $error = $error."<LI>Ошибка при обращении к базе данных\n";
  }
  $total = mysqli_fetch_array($tot);
  $count = $total['count(*)'];
  if ($openaddlist)
   $userid = 0; // Открытая экспертиза - кто оценил? - инкогнито
  else
   $userid = USER_ID;
  
  $ball = 0;
  $maxball = 0;
  $res10=mysqli_query($mysqli,"SELECT s.* FROM shablon as s, shablongroups as gr WHERE s.proarrid='".$paid."' AND s.groupid=gr.id AND gr.exlistid='".$exlistid."' ORDER BY s.id");
  while($mb10 = mysqli_fetch_array($res10))
  {
   $i = $mb10['id'];
   $type = $_POST["paramtype".$i]; 

   // Простой критерий
   if ($type=='common') {
    $tparam = $_POST["param".$i];
    $maxparam = $_POST["maxparam".$i];
    if ($tparam>$maxparam) 
     $tparam=$maxparam;
    $ball = $ball + $tparam;
    
    $maxball = $maxball + $mb10['maxball'];
   } else
   // Цифровой критерий
   if ($type=='digital') {
    $ball = $ball + $_POST["param".$i];
//  Первый вариант - максимальный балл по итогу в группе    
    $maxball = $maxball + $mb10['maxball'];
//  Второй вариант вычисления максимального балла    
//    $maxball = $maxball + $_POST["param".$i];
   }
   // Комплексный критерий
   if ($type=='complex') {
    $iniball = $_POST["param".$i];
    $iniball0 = $_POST["param".$i];
    $cgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$i."' ORDER BY id");
    if (!$cgst) puterror("Ошибка при обращении к базе данных");
    while($cmember = mysqli_fetch_array($cgst))
      {
       $cid = $cmember['id'];
       $iniball = $iniball + $_POST["complex".$cid];
      }
    $ball = $ball + $iniball;
    if ($iniball0>0)
     $maxball = $maxball + $iniball0;
    else 
     $maxball = $maxball + $iniball;
   }

  }

/*  $maxball = 0;
  $res1=mysqli_query($mysqli,"SELECT maxball FROM shablon WHERE proarrid='".$paid."'");
  while($mb = mysqli_fetch_array($res1))
  {
   $maxball = $maxball + $mb['maxball'];
  } */
  
  
  if (!$openaddlist) // Узнаем не введен ли экспетрный лист
  {
  
  $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE userid='".$userid."' AND memberid='".$projectid."' AND exlistid='$exlistid'");
  if (!$res4)  {
    $action = ""; 
    $error = $error."<LI>Ошибка при обращении к базе данных\n";
  }
  $cnt2 = mysqli_fetch_array($res4);
  $count2 = $cnt2['count(*)'];
  if ($count2>0) {
    $action = ""; 
    $error = $error."<LI>На выбранный проект экспертный лист уже введен.\n";
  }
  
  }
  
  if (!empty($error)) 
  {
    print "<P><font color=green>Во время добавления записи произошли следующие ошибки: </font></P>\n";
    print "<UL>\n";
    print $error;
    print "</UL>\n";
    print "<input type='button' name='close' value='Назад' onclick='history.back()'>"; 
    exit();
  }
  
  mysqli_query($mysqli,"START TRANSACTION;");
  $query = "INSERT INTO shablondb VALUES (0,$userid,$projectid,$ball,$maxball,'$comment',NOW(),$exlistid)";
  if(!mysqli_query($mysqli,$query)) {
      echo "<a href='lists&paid=".$paid."&ext=".$ext."'>Вернуться</a>";
      echo("<P> Ошибка при добавлении экспертного листа</P>");
      exit();
  }
  $shablondbid = mysqli_insert_id($mysqli);
  define('SHABLONDB_ID',mysqli_query($mysqli,"SELECT LAST_INSERT_ID()"));
  mysqli_query($mysqli,"COMMIT");

  writelog("Добавлена экспертиза проекта №".$projectid.".");

  // Найдем оценку проекта
  $res5=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT ocenka FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysqli_fetch_array($res5);
  $ocenka = $proarray['ocenka'];
  $openexpert = $proarray['openexpert'];

  $res=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$projectid."' LIMIT 1");
  if (!$res) puterror("Ошибка при обращении к базе данных");
  $r = mysqli_fetch_array($res);

  if ($maxball==0) {
   $aball = $r['maxball'];
  }
  else 
   $aball = $r['maxball']+($ball/$maxball) * $ocenka;

  mysqli_query($mysqli,"START TRANSACTION;");

  $query3 = "UPDATE projects SET maxball='".$aball."' WHERE id='".$projectid."' LIMIT 1;"; 
  if(!mysqli_query($mysqli,$query3)) {
      echo "<a href='lists&paid=".$paid."&ext=".$ext."'>Вернуться</a>";
      echo("<P> Ошибка при обновлении экспертного листа</P>");
      exit();
  }

 $res_2=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM shablongroups WHERE proarrid='".$paid."' AND exlistid='".$exlistid."' ORDER BY id");
 while($group = mysqli_fetch_array($res_2))
  { 
    
  $res10=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablon WHERE proarrid='".$paid."' AND groupid='".$group['id']."' ORDER BY id");
  while($mb10 = mysqli_fetch_array($res10))
  {
   
   
    
   $i = $mb10['id'];
   $tparam = $_POST["param".$i]; 
   
   
   $type = $_POST["paramtype".$i]; 

   if ($type=='common') {
    $tparam = $_POST["param".$i]; 
    $maxparam = $_POST["maxparam".$i];
    if ($tparam>$maxparam) 
     $tparam=$maxparam;
   } else
   if ($type=='digital') {
    $tparam = $_POST["param".$i]; 
   }
   if ($type=='complex') {
    $iniball = $_POST["param".$i];
    $cgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$i."' ORDER BY id");
    if (!$cgst) puterror("Ошибка при обращении к базе данных");
    while($cmember = mysqli_fetch_array($cgst))
      {
       $cid = $cmember['id'];
       $iniball = $iniball + $_POST["complex".$cid];
      }
    $tparam = $iniball;
   }


    $query2 = "INSERT INTO leafs VALUES (0, 
    $shablondbid, 
    $i, 
    $tparam 
    )";
    
    if(!mysqli_query($mysqli,$query2))
    {
      echo "<a href='lists&paid=".$paid."&ext=".$ext."'>Вернуться</a>";
      echo("<P> Ошибка при добавлении экспертного листа</P>");
      exit();
    }
  }
 } 

 mysqli_query($mysqli,"COMMIT");

 // Изменим статус - проекта если все эксперты дали свои оценки
   if (!$openaddlist) 
   {
   
   $totmem = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM proexperts WHERE proarrid='".$paid."'");
   if (!$totmem) puterror("Ошибка при обращении к базе данных");
   $total = mysqli_fetch_array($totmem);
   $expertscount = $total['count(*)'];

   $tot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE memberid='".$projectid."' AND exlistid='$exlistid'");
   if (!$tot) puterror("Ошибка при обращении к базе данных");
   $total = mysqli_fetch_array($tot);
   $count = $total['count(*)'];
   $allcnt = $expertscount - $count;
   
   // Проверим также свойство - openexpert
   if ($allcnt==0 && $openexpert==0) 
   {
    $query = "UPDATE projects SET status = 'finalized' WHERE id='".$projectid."'";
    if(!mysqli_query($mysqli,$query))
    {
      echo "<a href='lists&paid=".$paid."&ext=".$ext."'>Вернуться</a>";
      echo("<P> Ошибка при добавлении экспертного листа</P>");
      echo("<P> $query</P>");
      exit();
    }
    // Отправим сообщение пользователю, что экспертиза завершена
      require_once('lib/unicode.inc');
      
      $gst3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$r['userid']."' LIMIT 1;");
      if (!$gst3) puterror("Ошибка при обращении к базе данных");
      $user = mysqli_fetch_array($gst3);
      
      $toid = $user['id'];
      $toemail = $user['email'];

      $title = "По Вашему проекту №".$projectid." все эксперты дали оценки. Экспертиза проекта завершена.";

      $body = msghead($r['userfio'], $site);
      $body .= "<p>По Вашему проекту №".$projectid." (".$r['info'].") все эксперты дали оценки. Экспертиза завершена.</p>
      <p>Итоговый балл составил: <strong>".round($aball,2).".</strong></p
      <p>Более подробную информацию Вы можете посмотреть в личном кабинете.</p>
      <p>Дополнительно, Вы можете разрешить публикацию проекта в сети Интернет и получить прямую ссылку на Ваш проект.</p>";

      $body2 = "<p>По Вашему проекту №".$projectid." (".$r['info'].") все эксперты дали оценки. Экспертиза завершена.</p>
      <p>Итоговый балл составил: <strong>".round($aball,2).".</strong></p
      <p>Более подробную информацию Вы можете посмотреть в личном кабинете.</p>
      <p>Дополнительно, Вы можете разрешить публикацию проекта в сети Интернет и получить прямую ссылку на Ваш проект.</p>";

      $body .= msgtail($site);

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode('info@expert03.ru <info@expert03.ru>');

      if (!empty($toemail))
      {
       if (!mail(
       $toemail,
       mime_header_encode($title),
       str_replace("\r", '', $body),
       join("\n", $mimeheaders)
       )) puterror("Ошибка при отправке сообщения.");
      }
      $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        0,
                                        '$title',
                                        '$body2',0,NOW());";
      if(!mysqli_query($mysqli,$query))
        puterror("Ошибка при обращении к базе данных.");
     }
    } 
    
    if ($openaddlist)
    { // открытая экспертиза - еще введем лист
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=addlist&paid=".$paid."&ext=".$ext."&sl=".$secret."'>\n";
      print "</HEAD></HTML>\n";
    }
    else
    {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=lists&paid=".$paid."&ext=".$ext."'>\n";
      print "</HEAD></HTML>\n";
    }
    exit();
} else die;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else
if ($action=="post1") 
{

 // Проверяем правильность ввода информации в поля формы
 if (empty($_POST["projectid"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не выбрали проект\n";
    print "<UL>\n";
    print $error;
    print "</UL>\n";
    print "<input type='button' name='close' value='Назад' onclick='history.back()'>"; 
    exit;
  }

 $paid = $_POST["paid"];
 $ext = $_POST["ext"];
 $exlistid = $_POST["exlistid"];
 $projectid = $_POST["projectid"];
 $defparams = $_POST["defparams"];
  
 $secret = $_POST["secret"];
 $openaddlist = false;

 // Устанавливаем соединение с базой данных
 include "config.php";
 include "func.php";

 
 if (!empty($secret))
 {
   $pa3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT openexpert, openexperturl, name FROM projectarray WHERE id='".$paid."' LIMIT 1;");
   if (!$pa3) puterror("Ошибка при обращении к базе данных");
   $projectarray = mysqli_fetch_array($pa3);
   if ($projectarray['openexpert']>0 and $projectarray['openexperturl']==$secret)
    $openaddlist = true;
   else
    die; 
 }

 if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT") or $openaddlist) 
 {
  
  if ($openaddlist)
   $title=$titlepage="Заполнение экспертного листа (открытая экспертиза)";
  else
   $title=$titlepage="Заполнение экспертного листа";
  
  require_once "header.php"; 

?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="css/errors.css">
<style>
.ui-slider .ui-slider-handle {
width: 1.4em;
height: 1.4em;
}
.ui-slider-horizontal .ui-slider-handle {
top: -.4em;
margin-left: -.5em;
}
.ui-widget,p {
font-family: Verdana,Arial,sans-serif;
font-size: .9em;
}   
#buttonset { 
 display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 100%;   height: 100px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -50%; } 
#buttonsetm { 
 display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 60px; bottom : 100px;  position: absolute; overflow: auto;} 
#pagewidth {
 margin-top: 0px;
}</style>
<script type="text/javascript">
$(document).ready(function() {
 
    $( "#savelist" ).button();
    $( "#submit" ).button();
    $( "#back" ).button();
 
    $('#submit').click(function() { 
 
        $(".iferror").hide();
        var hasError = false;

<?

 $res2=mysqli_query($mysqli,"SELECT * FROM shablongroups WHERE proarrid='".$paid."' AND exlistid='".$exlistid."' ORDER BY id");
 while($group = mysqli_fetch_array($res2))
  { 
   $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablon WHERE proarrid='".$paid."' AND groupid='".$group['id']."' ORDER BY id");
    while($param = mysqli_fetch_array($res3))
   { 
        if ($param['complex']==1) {

         $cgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$param['id']."' ORDER BY id");
         while($cmember = mysqli_fetch_array($cgst))
           {
             $cid = $cmember['id'];
             $cgst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncparams WHERE shabloncid='$cid' ORDER BY id");
             if (!$cgst2) puterror("Ошибка при обращении к базе данных");
            
?>
        var complexVal<? echo $cid; ?> = $("#complex<? echo $cid; ?>").val();
        if(complexVal<? echo $cid; ?> == '') {
            $("#complex<? echo $cid; ?>").after('<span class="iferror">Требуется указать оценку!</span>');
            hasError = true;
        }
<?
        }
        }
        else
        {
?>
        var paramVal<? echo $param['id']; ?> = $("#param<? echo $param['id']; ?>").val();
        if(paramVal<? echo $param['id']; ?> == '') {
            $("#param<? echo $param['id']; ?>").after('<span class="iferror">Требуется указать оценку!</span>');
            hasError = true;
        }

<?
        }
        }
        }
?>
        if(!document.getElementById('savelist').checked) {
            $("#savelist").after('<span class="iferror">Требуется согласие на сохранение экспертного листа!</span>');
            hasError = true;
        }
        if(hasError == true) { return false; }

    });
});
</script>             
</head>
<body>
            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center"><strong>Внимание!</strong> Экспертная оценка (экспертиза) проекта сохраняется только один раз! Перед тем, как нажать на кнопку <strong>Сохранить экспертный лист</strong>, проверьте все значения.</p>
            	</div>
            </div> 

<form action="addlist" method="post">
<input type="hidden" name="paid" value="<? echo $paid; ?>">
<input type="hidden" name="secret" value="<? echo $secret; ?>">
<input type="hidden" name="ext" value="<? echo $ext; ?>">
<input type="hidden" name="exlistid" value="<? echo $exlistid; ?>">
<input type="hidden" name="action" value="post2">
<input type="hidden" name="projectid" value="<? echo $projectid; ?>">

<div id="buttonsetm">
<p align='center'>
<table width="100%" align="center" border='0' cellpadding='1' cellspacing='1' bordercolorlight=gray bordercolordark=white>
<tr><td>

<? 
   $gst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
   if (!$gst2) puterror("Ошибка при обращении к базе данных");
   $member2 = mysqli_fetch_array($gst2);
   $nodown = $member2['nodownload'];
   if ($member2['nowindow']==1) 
   {
    $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$projectid."' LIMIT 1");
    $member = mysqli_fetch_array($gst);
    if (!empty($member)) 
     {
        // Покажем проект 
        ?>
        <script src="scripts/slider.js"></script>
        <?
        echo viewp($mysqli, $projectid, $upload_dir);
     }
   }
   else 
   {
?>


<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}
</style>

<script type="text/javascript">

		$(document).ready(function() {
			$('.fancybox').fancybox();
		});

		$(document).ready(function() {
    	$("#fancybox<?php echo $projectid; ?>").click(function() {
				$.fancybox.open({
					href : 'viewproject3&id=<? echo $projectid; ?>',
					type : 'iframe',
					padding : 5
				});
			});
      
      $("#fancybox-manual-<?php echo $projectid; ?>").click(function() {
				$.fancybox.open([
        <?php
  $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$paid."' ORDER BY id");
  while($param = mysqli_fetch_array($res3))
   { 
    $res4=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$projectid."' AND optionsid='".$param['id']."'");
    $param4 = mysqli_fetch_array($res4);
    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) { 
     if ($param['filetype']=="foto") {
      echo "{ href : '".$upload_dir.$param4['projectid'].$param4['realfilename']."' },";
     }
     }  
    } 
    }
        ?>
          
				], {
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});      
      
		});
</script>

    <?

  $res33=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT info FROM projects WHERE id='".$projectid."' LIMIT 1;");
  if (!$res33) puterror("Ошибка при обращении к базе данных");
  $paramname = mysqli_fetch_array($res33);
  ?>
            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center"><strong>Внимание!</strong> Загрузка проекта может занять продолжительное время в зависимости от размера прикрепленных файлов. Если в проекте встречаются загруженные архивы 'zip', 'rar', необходимо загружать данные файлы отдельно и разархивировать.</p>
            	</div>
            </div> 
  <p></p>          
  <h3 class='ui-widget-header ui-corner-all' align="center"><p><a title='Открыть просмотр' href='viewproject3&id=<? echo $projectid; ?>' target='_blank'>Просмотр проекта №<? echo $projectid; ?> <? echo $paramname['info']?></a></p></h3>
  <?

  }

 echo "</td></tr>";
 $c=0;
  if ($exlistid>0) 
  {
    $ex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM expertcontentnames WHERE id='".$exlistid."' LIMIT 1");
    if (!$ex) puterror("Ошибка при обращении к базе данных");
    $exmember = mysqli_fetch_array($ex);
    $exname = $exmember['name'];
    echo "<tr><td align='center'><h1 class='z1' align='center'>Экспертный лист - ".$exname."</h1></td></tr>";
  } 
  else
    echo "<tr><td align='center'><h1 class='z1' align='center'>Экспертный лист</h1></td></tr>";
    
 echo "<tr><td>";
 
 // echo "<div class='menu_glide_tops'><table align='center' width='80%' border='0' cellpadding='2' cellspacing='2'>";


 $res2=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablongroups WHERE proarrid='".$paid."' AND exlistid='".$exlistid."' ORDER BY id");
 while($group = mysqli_fetch_array($res2))
  { 
    ?>
<div id='menu_glide' class='ui-widget-content ui-corner-all'>
<h3 class='ui-widget-header ui-corner-all' style='font-size: 14px; padding: 10px; margin-top: 0px; text-align: left; background: #497787 url("scripts/jquery-ui/images/ui-bg_inset-soft_75_497787_1x100.png") 50% 50% repeat-x;'><? echo $group['name']; ?></h3>
    <?
 echo "<table align='center' width='100%' border='0' cellpadding='5' cellspacing='5'><tr><td>";
    
/*    echo"<tr>";
        echo"<td>";
            echo"<p><b>".$group['name']."</b></td><td></td>";
    echo"</tr>"; */
    
   $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablon WHERE proarrid='".$paid."' AND groupid='".$group['id']."' ORDER BY id");
   while($param = mysqli_fetch_array($res3))
   { 
    echo "<div id='menu_glide' class='menu_glide'><table width='100%' border='0'>";
    echo"<tr>";
        if ($param['complex']==1) {
         echo"<td><p class=ptd><b>".$param['name']."* (Составной критерий)</b></td>";
         echo"<input type='hidden' name='param".$param['id']."' value='".$param['iniball']."'>";
         echo"<input type='hidden' name='paramtype".$param['id']."' value='complex'>";
         echo"<td></td>";

         $cgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$param['id']."' ORDER BY id");
         if (!$cgst) puterror("Ошибка при обращении к базе данных");
         while($cmember = mysqli_fetch_array($cgst))
           {
             echo"<tr><td><p>".$cmember['name']." *</p></td>";
             $c+=1;
             $cid = $cmember['id'];
             $cgst2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shabloncparams WHERE shabloncid='$cid' ORDER BY id");
             if (!$cgst2) puterror("Ошибка при обращении к базе данных");

         ?>
         <script>
          $(function() {
             $( "#complex<? echo $cid; ?>" ).selectmenu({ width: 80 });
          });
         </script> 
         <?
            
             echo"<td><select id='complex".$cid."'  name='complex".$cid."'><option value=''></option>";
             while($cmember2 = mysqli_fetch_array($cgst2))
             {
               if ($cmember2['type']==1) $val = $cmember2['value'];
               else
               if ($cmember2['type']==-1) $val = - $cmember2['value'];
               else
               if ($cmember2['type']==0) $val = 0;
               echo"<option value='".$val."'>".$cmember2['paramname']."</option>";
             }
            echo"</select></td>";
            echo "</tr>";
            echo"<tr><td><hr></td><td><hr></td></tr>";
           }
        }
        else
        if ($param['digital']==1) {
         echo"<input type='hidden' name='paramtype".$param['id']."' value='digital'>";
         echo"<td><p>".$param['name']." * (Введите оценку. Максимальная - ".$param['maxball']." балл(ов))</p></td>";

         ?>
         <script>
          $(function() {
    $( "#spparam<? echo $param['id'];?>" ).spinner({
      min: 0,
      max: <? echo $param['maxball'];?>,
      spin: function( event, ui ) {
          $( "#param<? echo $param['id'];?>" ).val(ui.value);
      }
    });
          });
         </script> 
         <?

         if ($defparams == 1)
          echo "<td><input readonly='1' type=text id='spparam".$param['id']."' name='spparam".$param['id']."' size='5'><input type=hidden id='param".$param['id']."' name='param".$param['id']."' value=''></td>";
         else
          echo "<td><input readonly='1' type=text id='spparam".$param['id']."' name='spparam".$param['id']."' size='5'><input type=hidden id='param".$param['id']."' name='param".$param['id']."' value=''></td>";
        }
        else {
         echo"<input type='hidden' name='paramtype".$param['id']."' value='common'>";
         echo"<td><p>".$param['name']." *</p></td>";

         // +++++++++++++++++++++++++++++++++++++ Слайдер Qquery UI (механизм без заполнения оценок) 10.11.13
         ?>
         <script>
          $(function() {
          $( "#slider<?echo $param['id'];?>" ).slider({
           value:0,
           min: 0,
           max: <?echo $param['maxball'];?>,
           step: 1,
          slide: function( event, ui ) {
           $( "#<?echo"param".$param['id'];?>" ).val(ui.value);
           }
          });
          $( "#<?echo"param".$param['id'];?>" ).val($( "#slider<?echo $param['id'];?>" ).slider( "value" ));
          });
         </script>
         <td width="200">
         <input type="hidden" name="maxparam<?echo $param['id'];?>" value="<?echo $param['maxball'];?>">
         <p>Баллов: <input type="text" readonly="1" id="param<?echo $param['id'];?>" name="param<?echo $param['id'];?>" style="border: 0; margin: 2px; padding: 2px; font-weight: bold; font-size:14px; background:#F0F0F0"/></p>
         <div id="slider<?echo $param['id'];?>"></div>
         </td>
         <?
         // ------------------------------------------- Слайдер Qquery UI
         
       }
    echo"</tr>";
    echo"<tr><td><p><font face='Tahoma,Arial' size='-2'>".$param['info']."</font></p></td></tr>";
   // echo"<tr><td><hr></td><td><hr></td></tr>";
    echo "</table></div><p></p>";
   } 
   echo "</td></tr></table></div><p></p>";
  }       

 echo"<input type='hidden' name='complexcnt' value='".$c."'>";
 echo "<td><tr>";
?> 

    <tr>
        <td>
            <p><strong>Рецензия или комментарий к экспертизе:</strong></p>
        </td>
    </tr>   
    <tr>
        <td>
            <textarea style="width:100%;font-size: 14px;" rows="7" name="comment"></textarea>
        </td>
    </tr>   
</table></p>
</div>
<div id="buttonset">
      <p></p>
        <input type="checkbox" id="savelist" name="savelist"><label for='savelist'>Я согласен(согласна) сохранить экспертный лист</label>
      <p></p>
       <input type="submit" id="submit" value="Сохранить экспертный лист">&nbsp;
       <input type="button" id="back" name="close" value="Назад" onclick="history.back()"> 
</div>
</form>
</body></html>
<?php
// include "bottomadmin.php";
} else die;
}

?>