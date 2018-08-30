<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";
  require_once('emailmsg.php');
  require_once('lib/unicode.inc');

  $action = $_POST["action"];

if (!empty($action)) 
{
  $kid = $_POST["kid"];
  $testid = $_POST["id"];
  $users = $_POST["cntusers"];
   
  // Сначала удалим старые 
  mysqli_query($mysqli,"START TRANSACTION;");
  mysqli_query($mysqli,"DELETE FROM usergrp WHERE testid=".$testid);
  mysqli_query($mysqli,"COMMIT;");

  mysqli_query($mysqli,"START TRANSACTION;");
  // Добавим  
  for ($i = 1; $i <= $users; $i++) 
  {

      // Запишем группу
      $usergroup = $_POST["usr".$i];

      $startdate = $_POST["startdate".$i];
      $stopdate = $_POST["stopdate".$i];
      $startdate1 = $_POST["startdate".$i];
      $stopdate1 = $_POST["stopdate".$i];
      preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$startdate,$sd1);
      $day=$sd1[1][0];
      $month=$sd1[2][0];
      $year=$sd1[3][0];
      preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$stopdate,$sd2);
      $day2=$sd2[1][0];
      $month2=$sd2[2][0];
      $year2=$sd2[3][0];

      $starttime = $_POST["starttime".$i];
      $stoptime = $_POST["stoptime".$i];
      $starttimes = explode(":", $starttime);
      $stoptimes = explode(":", $stoptime);
      
      $startdate=$year."-".$month."-".$day." ".$starttimes[0].":".$starttimes[1].":00";
      $stopdate=$year2."-".$month2."-".$day2." ".$stoptimes[0].":".$stoptimes[1].":00";

      if (!empty($testid)) { 
           $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name, content FROM testgroups WHERE id='".$testid."' LIMIT 1;");
           $testname = mysqli_fetch_array($test);
           $tname = $testname['name']; 
           $tcontent = $testname['content'];
           mysqli_free_result($test);
      }  
            
      if (!empty($usergroup))
      {
       $query = "INSERT INTO usergrp VALUES (0,
       '$startdate',
       '$stopdate',
       $usergroup,
       $testid)";
       mysqli_query($mysqli,$query);

      // Отправим сообщения пользователям
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM useremails WHERE usergroupid='".$usergroup."' ORDER BY id;");
      while ($param = mysqli_fetch_array($sql)) 
      {
        $to = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, userfio FROM users WHERE email='".$param['email']."' LIMIT 1;");
        $touser = mysqli_fetch_array($to);

        if (!empty($touser))
         {
          $toid = $touser['id'];
          $signature = md5(time().$toid.$tname);  // Уникальная сигнатура сообщения
          $query = "INSERT INTO msgs VALUES (0,
                                        $toid,
                                        ".USER_ID.",
                                        'Пройти тест <strong>".$tname."</strong>!',
                                        'Вам необходимо пройти тест <strong>".$tname."</strong> с ".$startdate1." ".$starttimes[0].":".$starttimes[1]." по ".$stopdate1." ".$stoptimes[0].":".$stoptimes[1]."',
                                        0,
                                        NOW(),
                                        '$signature');";
          mysqli_query($mysqli,$query);
         }
         // Отправим сообщение
      
         $title = "Вам отправлено приглашение пройти тест ".$tname." на сайте testlife.org";
         $body = msghead($touser['userfio'], $site);
         $body .= "<p>Вам отправлено приглашение пройти зачетный тест <a href='".$site."/ts'><strong>".$tname."</strong></a></p>";
         if (!empty($tcontent))
         {
           $body .="<p>".$tcontent."</p>";
         } 
         $body .= "<p>Пройти тестирование Вы можете в любое удобное для Вас время в период с ".$startdate1." ".$starttimes[0].":".$starttimes[1]." по ".$stopdate1." ".$stoptimes[0].":".$stoptimes[1].".</p>";
         $body .= "<p>Если Вы еще не зарегистрированы на сайте <a href='".$site."' target='_blank'>TestLife</a> - пройдите процедуру регистрации через популярные социальные сети.</p>";
         $body .= "<p>Обращаем внимание, что при регистрации (в профиле) необходимо использовать электронную почту: <strong>".$param['email']."</strong>. При использовании в профиле другого адреса, тестирование будет недоступно.</p>";
         $body .= msgtail($site);
         $mimeheaders = array();                                                                                                                                                           
         $mimeheaders[] = 'Content-type: '. mime_header_encode('text/html; charset=UTF-8; format=flowed; delsp=yes');
         $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
         $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
         $mimeheaders[] = 'From: '. mime_header_encode('TestLife <'.$valmail.'>');

         mail(
           $param['email'],
           mime_header_encode($title),
           str_replace("\r", '', $body),
           join("\n", $mimeheaders)
          );
        
         mysqli_free_result($to);
      
      }
      mysqli_free_result($sql);
      }
     }
     mysqli_query($mysqli,"COMMIT;");
     
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$kid.',"t");';
     echo '</script>';
     exit();
   
}
else
if (empty($action)) 
{
  $testid = $_GET["id"];
  $kid = $_GET["kid"];
  $modename = '<i class="fa fa-users fa-fw"></i> Добавить группы участников к тесту';
  
  require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script src="lms/scripts/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">

 $(function(){
   $("#spinner").fadeOut("slow");

   $("button").button();
   
   $.datepicker.regional['ru'] = { 
       closeText: 'Закрыть', 
       prevText: '&#x3c;Пред', 
       nextText: 'След&#x3e;', 
       currentText: 'Сегодня', 
       monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
       'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
       monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
       'Июл','Авг','Сен','Окт','Ноя','Дек'], 
       dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
       dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
       dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], 
       dateFormat: 'dd.mm.yy', 
       firstDay: 1, 
       isRTL: false 
    }; 
   $.datepicker.setDefaults($.datepicker.regional['ru']);
 
   $( "#usergroup" ).selectmenu({ width: 400 });

   $( "#startdate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#stopdate" ).datepicker( "option", "minDate", selectedDate );
      }
   });
   $( "#stopdate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#startdate" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
   
   $('#starttime').timepicker();
   $('#stoptime').timepicker();
   
   <?  
   $sql2 = mysqli_query($mysqli,"SELECT * FROM usergrp WHERE testid='".$testid."' ORDER BY id;");
   while($usergr = mysqli_fetch_array($sql2))
   {
    if(defined("IN_ADMIN"))
     $query = "SELECT * FROM usergroups WHERE id='".$usergr['usergroupid']."' LIMIT 1;";
    else
     $query = "SELECT * FROM usergroups WHERE id='".$usergr['usergroupid']."' AND userid='".USER_ID."' LIMIT 1;";
    $sql3 = mysqli_query($mysqli,$query);
    if ($sql3==false)
    {
     $usergroupname = '';
     $usergroupid = 0;
    }
    else
    {
     $usergroup = mysqli_fetch_array($sql3);
     $usergroupname = $usergroup['name'];
     $usergroupid = $usergroup['id'];
     mysqli_free_result($sql3);
    } 
   ?>
    id = <?=$usergroupid?>;
    name = "<?=trim(rtrim($usergroupname, '\n\r'))?>";
    allstart = "<?=trim(rtrim(data_convert ($usergr['startdate'], 1, 1, 0), '\n\r'))?>";
    allstop = "<?=trim(rtrim(data_convert ($usergr['stopdate'], 1, 1, 0), '\n\r'))?>";
    startdate = "<?=trim(rtrim(data_convert ($usergr['startdate'], 1, 0, 0), '\n\r'))?>";
    stopdate = "<?=trim(rtrim(data_convert ($usergr['stopdate'], 1, 0, 0), '\n\r'))?>";
    starttime = "<?=trim(rtrim(data_convert ($usergr['startdate'], 0, 1, 0), '\n\r'))?>";
    stoptime = "<?=trim(rtrim(data_convert ($usergr['stopdate'], 0, 1, 0), '\n\r'))?>";
    addusermanual(id, name, allstart, allstop, startdate, stopdate, starttime, stoptime);
   <?
   }    
   mysqli_free_result($sql2);
   ?>
  });       

 function checkRegexp(n, t) {
        return t.test(n.val())
 }

   
    function testDate(str,strtime) {
     str2=str.split(".");
     strt=strtime.split(":");
     if(str2.length!=3){return false;}
     if(strt.length!=2){return false;}
     str2=str2[2] +'-'+ str2[1]+'-'+ str2[0]+' '+strt[0]+':'+strt[1]+':00';
     if (new Date(str2)=='Invalid Date')
      return false;
     else   
      return true;
    }

    function getDate(str) {
     str2=str.split(".");
     if(str2.length!=3){return false;}
     str2=str2[2] +'-'+ str2[1]+'-'+ str2[0];
     if (new Date(str2)=='Invalid Date')
      return false;
     else   
      return new Date(str2);
    }

 function deluser(cnt)
 {
   $("#usr"+cnt).val('');
   $("#startdate"+cnt).val('');
   $("#stopdate"+cnt).val('');
   $('#duser'+cnt).empty();
 }

 function adduser()
 {
     var hasError = false; 
     var cnt = $("#cntusers").val();
     var id = $("#usergroup");
     var name = $("#usergroup option:selected").text();
     var startdate = $("#startdate").val();
     var stopdate = $("#stopdate").val();
     var starttime = $("#starttime").val();
     var stoptime = $("#stoptime").val();
     
     if(id.val()=='') {
            $("#info2").empty();
            $("#info2").append('Необходимо выбрать группу участников.');
            id.focus();
            hasError = true;
     }

     if(testDate(startdate,starttime)==false) {
            $("#info2").empty();
            $("#info2").append('Введена некорректная дата или время начала тестирования.');
            $("#startdate").focus();
            hasError = true;
     }

     if(testDate(stopdate,stoptime)==false) {
            $("#info2").empty();
            $("#info2").append('Введена некорректная дата или время окончания тестирования.');
            $("#stopdate").focus();
            hasError = true;
     }

     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
     }
     else
     {
      cnt++;
      $("#cntusers").val(cnt);
      $('#hiddenusers').append('<input type="hidden" id="usr'+cnt+'" name="usr'+cnt+'" value="' + id.val() + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="startdate'+cnt+'" name="startdate'+cnt+'" value="' + startdate + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="stopdate'+cnt+'" name="stopdate'+cnt+'" value="' + stopdate + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="starttime'+cnt+'" name="starttime'+cnt+'" value="' + starttime + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="stoptime'+cnt+'" name="stoptime'+cnt+'" value="' + stoptime + '">'); 
      $('#showusers').append('<div id="duser'+cnt+'"><p>Группа <strong>' + name + '</strong>&nbsp;с&nbsp;'+startdate+'&nbsp;'+starttime+'&nbsp;по&nbsp;'+stopdate+'&nbsp;'+stoptime+'&nbsp;<button title="Удалить группу" id="delb'+cnt+'" onclick="deluser('+cnt+');"><i class="fa fa-users"></i> <i class="fa fa-minus-circle"></i></button></p></div>');        
      $("#delb"+cnt).button();
      $("#usergroup").focus();
     }
     
 }

 function addusermanual(id, name, allstart, allstop, startdate, stopdate, starttime, stoptime)
 {
      var cnt = $("#cntusers").val();
      cnt++;
      $("#cntusers").val(cnt);
      $('#hiddenusers').append('<input type="hidden" id="usr'+cnt+'" name="usr'+cnt+'" value="' + id + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="startdate'+cnt+'" name="startdate'+cnt+'" value="' + startdate + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="stopdate'+cnt+'" name="stopdate'+cnt+'" value="' + stopdate + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="starttime'+cnt+'" name="starttime'+cnt+'" value="' + starttime + '">'); 
      $('#hiddenusers').append('<input type="hidden" id="stoptime'+cnt+'" name="stoptime'+cnt+'" value="' + stoptime + '">'); 
      $('#showusers').append('<div id="duser'+cnt+'"><p>Группа <strong>' + name + '</strong>&nbsp;с&nbsp;'+allstart+'&nbsp;по&nbsp;'+allstop+'&nbsp;<button title="Удалить группу" id="delb'+cnt+'" onclick="deluser('+cnt+');"><i class="fa fa-users"></i> <i class="fa fa-minus-circle"></i></button></p></div>');        
      $("#delb"+cnt).button();
 }
 
 jQuery(document).ready(function() {
    $('#save').submit(function()
    {

     var cnt = $("#cntusers").val();
     if (cnt>0)
     {
      $('#ok', $(this)).attr('disabled', 'disabled');
      $("#spinner").fadeIn("slow");
      return true;
     } 
     else 
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Укажите группы участников тестирования.');
      $("#usergroup").focus();
      return false;
     }   
    });   
  });   

</script>
<style>
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.8em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
p { font: 14px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2"><?=$modename?></div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<div id="buttonsetm">
 <form id="save" action="editugintest" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="id" value="<?=$testid?>">
  <input type="hidden" name="kid" value="<?=$kid?>">
  <input type='hidden' id='cntusers' name='cntusers' value='0'>
   <table border="0" width='90%' cellpadding=0 cellspacing=0>
    <tr>
        <td width="30%"><p>Даты начала и окончания тестирования для группы:</p></td>
        <td>
          с&nbsp;<input type='text' id="startdate" name="startdate" value='<?=date("d.m.Y")?>' size="10"> 
          <input type='text' id="starttime" name="starttime" value='00:00' size="5"> 
          по&nbsp;<input type='text' id="stopdate" name='stopdate' value='<?=date("d.m.Y")?>' size="10">
          <input type='text' id="stoptime" name="stoptime" value='23:59' size="5"> 
        </td>
    </tr>
    <tr>
        <td width="30%">
         <p>Список групп участников тестирования:</p>
        </td>
        <td>
               <select id="usergroup" name="usergroup">     
                <option value=""></option>   
<?
  
  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetChildFolders($mysqli, Folders $ks, $fid)
  {
    $ss = '';
    foreach($ks->getFolders($fid) as $tmpfolder) 
    {
     $ss .= "<option disabled='disabled'>".$tmpfolder->getName()."</option>";
     if (defined("IN_ADMIN")) 
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$tmpfolder->getId()."' AND usergrouptype='0' ORDER BY id DESC;");
     else
     if (defined("IN_SUPERVISOR"))
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$tmpfolder->getId()."' AND usergrouptype='0' AND userid='".USER_ID."' ORDER BY id DESC;");
     while($member = mysqli_fetch_array($sql))
      $ss .= '<option value="'.$member['id'].'">' . $member['name'] . '</option>';          
     mysqli_free_result($sql); 
     
     $ss .= GetChildFolders($mysqli, $ks, $tmpfolder->getId());
     //$ss .= "</optgroup>";
    }
    return $ss;
  }

  // Инициализация папок
  if (defined("IN_ADMIN"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders ORDER BY id;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE userid='".USER_ID."' ORDER BY id;");

  $folders = new Folders();
  
  while($member = mysqli_fetch_array($sql))
   $folders->addFolder(new Folder($member['id'], 
                            $member['name'], 
                            $member['parentid'], 
                            $member['userid']));
  mysqli_free_result($sql);
  
  echo GetChildFolders($mysqli, $folders, 0);

  if (defined("IN_ADMIN")) 
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='0' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='0' AND userid='".USER_ID."' ORDER BY id DESC;");
  while($member = mysqli_fetch_array($sql))
      echo '<option value="'.$member['id'].'">'.$member['name'].'</option>';          
  mysqli_free_result($sql); 

?>
               </select>            
        </td>
    </tr>
    <tr>    
     <td>
        <div id="hiddenusers"></div>
     </td>
     <td>
       <div id="showusers"></div>
     </td>
    </tr>
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id="add" onclick="adduser();"><i class="fa fa-users fa-fw"></i> Добавить группу</button>
            <button id='ok' onclick="$('#save').submit();" >Сохранить</button>
            <button id="close" onclick="parent.closeFancybox();">Отмена</button> 
 </div>
 </td></tr></table>
</body></html>
<?
} 
} else die; 

?>