<?php

function puterror($message)
{
    echo("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body><p align='center'>$message</p></body></html>");
    exit();
}

function data_convert($data, $year, $time, $second) {
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
}

function GetAttempts($mysqli, $testid, $testattempts)
 {
   $attempts = 0;
   if ($testattempts>0)
   {
     $res = mysqli_query($mysqli,"SELECT count(*) FROM singleresult WHERE userid='".USER_ID."' AND testid='".$testid."' LIMIT 1;");
     $resdata = mysqli_fetch_array($res);
     $attempts = $resdata['count(*)'];
     mysqli_free_result($res); 
     $res = mysqli_query($mysqli,"SELECT count(*) FROM attemptsresult WHERE userid='".USER_ID."' AND testid='".$testid."' LIMIT 1;");
     $resdata = mysqli_fetch_array($res);
     $attempts += $resdata['count(*)'];
     mysqli_free_result($res); 
     return $attempts;
   }
   else
    return null;
}

include "config.php";

spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
});

$action = $_POST["action"];

if (!empty($action)) 
{

  if ($action="begintest")
  {

   $id = $_POST["id"];
   $signature = $_POST["signature"];
   $questid = $_POST["questid"];
   $groupid = $_POST["groupid"];
   $hours = $_POST["hours"];
   $minutes = $_POST["minutes"];
   $mode = $_POST["m"];

   if ($questid==0)
    puterror("Ошибка при формировании теста.");

   $bdinfo = '';

   $test = new Test($mysqli, $id, $signature, USER_ID);
   if (!empty($test))
   {   

    if(defined("USER_ID")) 
     $userid = USER_ID;
    else
     $userid=0;

    $token = md5(time().$test->getId().$userid);  // Уникальная сигнатура теста
    $signature = $test ->getSignature();
    
    // Внешний тест - увеличим просмотр
    if ($test->getType()=='check' and $test ->getExternal()==1)
    {
     $testviewcnt = $test ->getViewcnt();
     $testviewcnt++;
     mysqli_query($mysqli,"START TRANSACTION;");
     mysqli_query($mysqli,"UPDATE testgroups SET viewcnt = ".$testviewcnt." WHERE id=".$test->getId());
     mysqli_query($mysqli,"COMMIT;");
    }
    
    if ($test->getType()=='pass')
    {
     if(!defined("USER_REGISTERED")) die;  
     // Просканируем попытки пройти тест (для умных)   
     if (md5($signature."check")==$mode)
     {
     } // Переход в проверочный режим для создателя
     else
     {
      $attempts = GetAttempts($mysqli, $test->getId(),$test->getAttempt());
      if (!empty($attempts))
      {
        mysqli_query($mysqli,"START TRANSACTION;");
        if ($attmpts > $test->getAttempt()) 
         $bdinfo = 'Попытки пройти тест закончились';
        // Запишем попытку тестирования
        mysqli_query($mysqli,"INSERT INTO attemptsresult VALUES (0,
                                        ".$test->getId().",
                                        ".$userid.",
                                        '".$test->getSignature()."',
                                        NOW());");
        mysqli_query($mysqli,"COMMIT;");
      }
     }
    }
   }
   else
    puterror("Ошибка при создании теста.");
   
?>
<!DOCTYPE html>
<html lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lms/css/custom-theme/jquery-ui-1.10.3.custom.css">
    <link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget { font-family: Verdana,Arial,sans-serif; font-size: 0.9em;}
.red_bg { 	color: red;
    font-weight: bold;
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
.is-countdown {
}
.countdown-rtl {
	direction: rtl;
}
.countdown-holding span {
	color: #888;
}
.countdown-row {
	clear: both;
	width: 100%;
	padding: 0px 2px;
	text-align: center;
}
.countdown-show1 .countdown-section {
	width: 98%;
}
.countdown-show2 .countdown-section {
	width: 48%;
}
.countdown-show3 .countdown-section {
	width: 32.5%;
}
.countdown-show4 .countdown-section {
	width: 24.5%;
}
.countdown-show5 .countdown-section {
	width: 19.5%;
}
.countdown-show6 .countdown-section {
	width: 16.25%;
}
.countdown-show7 .countdown-section {
	width: 14%;
}
.countdown-section {
	display: block;
	float: left;
	font-size: 75%;
	text-align: center;
}
.countdown-amount {
    font-size: 200%;
}
.countdown-period {
    display: block;
}
.countdown-descr {
	display: block;
	width: 100%;
}
#defaultCountdown { 
 display:block;
 font-family:Arial;
 text-align: center; 
 width: 240px; 
 height: 40px; 
 font-size: 0.7em;
 left: 50%;
 position: absolute;
 margin-left: -120px;
}
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 700px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -350px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 0px; bottom : 50px;  position: absolute; overflow: auto;} 
.timer-top {
  text-align: center;
  width: 100%;
  margin: auto;
  left: 0; bottom: 0; right: 0;
  top:0px;
 }
.timer-top-but {
 display:block;
 bottom:0;
 margin:0 0 0 100%;
 padding:6px 12px 4px;
 color:white;
 font-family:Arial;
 font-size: 0.5em;
 text-decoration: none;
 }
.ui-progressbar {
    position: relative;
 }
.progress-label {
    position: absolute;
    left: 45%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
 } 
.sequence ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
.sequence li { margin: 10px; padding: 10px; width: 95%; border: 2px solid #969090; }
.accord ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
.accord li { margin: 10px; padding: 10px; width: 95%; border: 2px solid #969090; }
.icon-invisible {
    visibility: hidden;
}
</style>
<?   

 if ($bdinfo=='') {

?>
</head>
<body>
 <div id="spinner">
 </div>
 <div id="dialog-form">
 </div>
 <div id="defaultCountdown">
 </div>
 <div id="buttonsetm">

  <? if ($test->getType()=='check') {?>
<div class="panel panel-primary">
  <?} else {?>
<div class="panel panel-primary" style="margin-top:40px;">
  <?}?>
                        <div class="panel-heading">
                            <strong><?=$test->getName()?></strong><?if(defined("USER_FIO")) echo "<span class='pull-right'>".USER_FIO."</span>"; ?>
                        </div>
                        <div class="panel-body">

          <div id="qresult"></div>
<?   
        echo "<input type='hidden' id='tid' value='".$token."'>";
        echo "<input type='hidden' id='group' value='".$groupid."'>";
        echo "<input type='hidden' id='nextq' value='".$questid."'>";
?>    
 </div>
 </div>
</div>
<div id="buttonset">
  <? if ($test->getType()=='check') {?>
  <button class="ui-button-primary" style="font-size: 1em" id="next" onclick="getrightq();"><i class='fa fa-arrow-right fa-lg'></i> Следующий вопрос</button> 
  <button class="ui-button-primary" style="font-size: 1em" id="close" onclick="getrightresq();"><i class='fa fa-times fa-lg'></i> Завершить</button> 
  <?} else {?>
  <button class="ui-button-primary" style="font-size: 1em" id="next" onclick="getq()"><i class='fa fa-arrow-right fa-lg'></i> Следующий вопрос</button> 
  <button class="ui-button-primary" style="font-size: 1em" id="close" onclick="resquestion('Вы действительно хотите завершить тестирование?')"><i class='fa fa-times fa-lg'></i> Завершить</button> 
  <?}?>         
</div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
 <script src="js/bootstrap.min.js"></script>
 <script src="lms/scripts/jquery-ui/jquery-ui.min.js"></script>
 <script type="text/javascript" src="lms/scripts/jquery.plugin.min.js"></script>
 <script type="text/javascript" src="lms/scripts/jquery.countdown.min.js"></script>
 <script type="text/javascript" src="lms/scripts/jquery.countdown-ru.js"></script>
 <script>
  
  var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
  var CloseReq = false;
  var testtime;
   
  function watchCountdown()
  {
   var periods = $('#defaultCountdown').countdown('getTimes');
   if (periods[5]==0) {
    $('#defaultCountdown').addClass('red_bg'); 
   }
  }
  
  function res(){
   $("#spinner").fadeIn("slow");
   var checks = [$("#allcheck").val()];
   ansid = $("#ansqid").val();
   strkbd = $("#kbd").val();
   for (i=0; i<$("#allcheck").val(); i++) {
    c = i+1+'';
    checks[i] = $("#check"+c).val();
   }
   ans = checks.join('-');
   tid = '<?=$token?>';
   sign = '<?=$signature?>';
   model = '<?=$mode?>';
   
   var strseq = $("#seqdata").val();
   if (strseq!=null) strseq = Base64.decode(strseq);
   var stracc1 = $("#accdata1").val();
   if (stracc1!=null) stracc1 = Base64.decode(stracc1);
   var stracc2 = $("#accdata2").val();
   if (stracc2!=null) stracc2 = Base64.decode(stracc2);
   
   $.post('getadaptq.json',
    {
     writeonly:1, 
     direction:0, 
     questid:qid, 
     numid:n, 
     token:tid,
     signature:sign, 
     m:model, 
     strmulti:ans, 
     ansqid:ansid, 
     kbd:strkbd, 
     seq:strseq, 
     acc1:stracc1, 
     acc2:stracc2
    },  
    function(data){  
      eval('var obj='+data);         
       <? if ($sqlanaliz) {?>
       console.log(obj.sqltime);
       console.log(obj.log1);
       console.log(obj.log2);
       console.log(obj.log3);
       console.log(obj.log4);
       <?}?>
      $("#spinner").fadeOut("slow");
      sign = '<?=$signature?>';
      <? if (md5($signature."check")==$mode or $test->getType()=='check') {?>
      parent.closeFancybox();
      <?} else {?>
      parent.closeFancybox();
      parent.dialogOpen('testresults&tid='+sign+'&sign='+tid,0,0);
      <?}?>
    }); 
  } 
  
  jQuery(document).ready(function() {  
    $("#next").button();
    $("#close").button();
    $("#close2").button();
    $('#close').addClass('button_enabled'); 
    $('#close2').addClass('button_enabled'); 
    $('#next').addClass('button_enabled'); 
    getq();  
  });  

  function getrightq()
  {
   $("#spinner").fadeIn("slow");
   tid = '<?=$token?>';
   model = '<?=$mode?>';
   sign = '<?=$signature?>';
   n = $("#num").val(); 
   if(n == undefined) 
     n = 1; 
   else 
     n++; 

   var checks = [$("#allcheck").val()];
   strkbd = $("#kbd").val();
   ansid = $("#ansqid").val();
   for (i=0; i<$("#allcheck").val(); i++) {
    c = i+1+'';
    checks[i] = $("#check"+c).val();
   }
   ans = checks.join('-');

   var strseq = $("#seqdata").val();
   if (strseq!=null) strseq = Base64.decode(strseq);
   var stracc1 = $("#accdata1").val();
   if (stracc1!=null) stracc1 = Base64.decode(stracc1);
   var stracc2 = $("#accdata2").val();
   if (stracc2!=null) stracc2 = Base64.decode(stracc2);
   
   $.post('getrightq.json',
    {
     numid:n, 
     token:tid, 
     m:model, 
     signature:sign,
     strmulti:ans, 
     ansqid:ansid, 
     kbd:strkbd, 
     seq:strseq, 
     acc1:stracc1, 
     acc2:stracc2
    },  
    function(data){  
      eval('var obj='+data);         
      $("#spinner").fadeOut("slow");
      if(obj.ok=='1') {
       if (obj.right)
         rightquestion(540,'<p><strong><center><font color="green" size="+2">Правильно!</font></center></strong></p>')
       else
       if (!obj.right && !!obj.rightdata)
         rightquestion(800,'<p><strong><center><font color="red" size="+2">Неправильно!</font></center></strong></p><p><font size="+1"><strong>Правильный ответ:</strong></font></p>'+obj.rightdata);
      }
    });   
  }

  function getrightresq()
  {
   $("#spinner").fadeIn("slow");
   tid = '<?=$token?>';
   model = '<?=$mode?>';
   sign = '<?=$signature?>';
   n = $("#num").val(); 
   if(n == undefined) 
     n = 1; 
   else 
     n++; 

   var checks = [$("#allcheck").val()];
   strkbd = $("#kbd").val();
   ansid = $("#ansqid").val();
   for (i=0; i<$("#allcheck").val(); i++) {
    c = i+1+'';
    checks[i] = $("#check"+c).val();
   }
   ans = checks.join('-');

   var strseq = $("#seqdata").val();
   if (strseq!=null) strseq = Base64.decode(strseq);
   var stracc1 = $("#accdata1").val();
   if (stracc1!=null) stracc1 = Base64.decode(stracc1);
   var stracc2 = $("#accdata2").val();
   if (stracc2!=null) stracc2 = Base64.decode(stracc2);
   
   $.post('getrightq.json',
    {
     numid:n, 
     token:tid, 
     m:model, 
     signature:sign,
     strmulti:ans, 
     ansqid:ansid, 
     kbd:strkbd, 
     seq:strseq, 
     acc1:stracc1, 
     acc2:stracc2
    },  
    function(data){  
      eval('var obj='+data);         
      $("#spinner").fadeOut("slow");
      if(obj.ok=='1') {
       if (obj.right)
         rightresquestion(540,'<p><strong><center><font color="green" size="+2">Правильно!</font></center></strong></p>')
       else
       if (!obj.right && !!obj.rightdata)
         rightresquestion(800,'<p><strong><center><font color="red" size="+2">Неправильно!</font></center></strong></p><p><font size="+1"><strong>Правильный ответ:</strong></font></p>'+obj.rightdata);
      }
    });   
  }


  function getq(){    
   $('#next').addClass('disabled').attr('disabled', true); 
   $("#spinner").fadeIn("slow");
   n = $("#num").val(); 
   qid = $("#nextq").val(); 
   if (qid==0)
   {
    res();
    return;
   }
   grid = $("#group").val(); 
   if(n == undefined) 
     n = 1; 
   else 
     n++; 
   tid = '<?=$token?>';
   sign = '<?=$signature?>';
   model = '<?=$mode?>';
   d=1; 
   var checks = [$("#allcheck").val()];
   strkbd = $("#kbd").val();
   ansid = $("#ansqid").val();
   for (i=0; i<$("#allcheck").val(); i++) {
    c = i+1+'';
    checks[i] = $("#check"+c).val();
   }
   ans = checks.join('-');

   var strseq = $("#seqdata").val();
   if (strseq!=null) strseq = Base64.decode(strseq);
   var stracc1 = $("#accdata1").val();
   if (stracc1!=null) stracc1 = Base64.decode(stracc1);
   var stracc2 = $("#accdata2").val();
   if (stracc2!=null) stracc2 = Base64.decode(stracc2);
   
   $('#qresult').empty();
    
   $.post('getadaptq.json',
    {
     writeonly:0, 
     direction:d, 
     questid:qid, 
     group:grid, 
     numid:n, 
     token:tid, 
     signature:sign, 
     m:model, 
     strmulti:ans, 
     ansqid:ansid, 
     kbd:strkbd, 
     seq:strseq, 
     acc1:stracc1, 
     acc2:stracc2
    },  
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1') {
       <? if ($sqlanaliz) {?>
       console.log(obj.sqltime);
       console.log(obj.log1);
       console.log(obj.log2);
       console.log(obj.log3);
       console.log(obj.log4);
       <?}?>
       $("#nextq").val( obj.nextq );
       $("#group").val( obj.group );
       $("#spinner").fadeOut("slow");
       $('#qresult').html(obj.content);        

       <? if ($test->getType()=='pass') {?>
       var sec = obj.minutes * 60;
       $('#defaultCountdown').countdown('destroy');
       $('#defaultCountdown').countdown({until: +sec, onExpiry: getq, onTick: watchCountdown});
       <?}?>

       if (obj.nextq == 0)
       {
         $('#next').addClass('disabled').attr('disabled', true); 
         res();
       }
       else
        $('#next').removeClass('disabled').attr('disabled', false); 
      }
      else 
      if(obj.ok=='0') { 
       $("#spinner").fadeOut("slow");
       $('#close').addClass('disabled').attr('disabled', true); 
       res();
      }                
    }); 
  }  

  function rightquestion(l,msg) {
           dialog = $("#dialog-form").dialog({
						autoOpen: true,
						modal: true,
            width: l,
            maxHeight: 300,
						buttons: {
							"Следующий вопрос": function() {
								dialog.dialog("close");
                getq();;
							},
							"Отмена": function() {
								dialog.dialog("close");
							}
						}
 					 }).html(msg);
          dialog.dialog("open");
  }   

  function rightresquestion(l,msg) {
           dialog = $("#dialog-form").dialog({
						autoOpen: true,
						modal: true,
            width: l,
            maxHeight: 300,
						buttons: {
							"Завершить тестирование": function() {
								dialog.dialog("close");
                res();
							},
							"Отмена": function() {
								dialog.dialog("close");
							}
						}
 					 }).html(msg+'Вы действительно хотите завершить тестирование?');
          dialog.dialog("open");
  }   

  function resquestion(msg) {
          dialog = $("#dialog-form").dialog({
						autoOpen: true,
						modal: true,
            width: 440,
						buttons: {
							"Да": function() {
								dialog.dialog("close");
                res();
							},
							"Нет": function() {
								dialog.dialog("close");
							}
						}
					}).html('<p><font size="+1">'+msg+'</font></p>');
          dialog.dialog("open");
  }   

 </script>
</body>
</html>

<?    }
      else
      {
      // Завершим тест изза необоснованных попыток
?>
<script>
  jQuery(document).ready(function() {  
    $("#close").button();
  });  
</script>
</head><body>                           
<div id="spinner"></div>
<div id="buttonset">
   <?
      echo '
      <button class="ui-button-danger" style="font-size: 1em" id="close" onclick="parent.closeFancybox()">'.$bdinfo.'</button>  
      ';
   ?>
</div>
</body></html>
      
<?
      }

    }
}
else
if (empty($action)) 
{

   $id = $_GET['id'];
   $signature = $_GET['s'];
   $mode = $_GET['m'];
   if (empty($m)) $m=0;

   $bdinfo = '';

   $test = new Test($mysqli, $id, $signature, USER_ID);
   if (!empty($test))
   {   
    if ($test->getType()=='pass')
    { 
     // Для зачетного теста - Просканируем попытки пройти тест   
     if (md5($signature."check")==$mode)
     {}// Переход в проверочный режим для создателя
     else
     {
      $attempts = GetAttempts($mysqli, $test->getId(),$test->getAttempt());
      if (!empty($attempts))
       if ($attmpts > $test->getAttempt()) 
      $bdinfo = 'Попытки пройти тест закончились';
     }
    }
   }
   else
    puterror("Ошибка при создании теста.");
   
?>
<!DOCTYPE html>
<html lang="ru"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lms/css/custom-theme/jquery-ui-1.10.3.custom.css">
    <link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
    <style>
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 0px; bottom : 50px;  position: absolute; overflow: auto;} 
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.9em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
p {
  font: 16px / 1.4 'Helvetica', 'Arial', sans-serif;
}
</style>
</head>
<body>
<form id='bt' action='viewadaptivetest' method='post'>
<input type='hidden' name='action' value='begintest'>
<? if (!empty($id)) { ?><input type='hidden' name='id' value='<?=$id?>'> <?}?>
<? if (!empty($signature)) { ?><input type='hidden' name='signature' value='<?=$signature?>'> <?}?>
<? if (!empty($mode)) { ?><input type='hidden' name='m' value='<?=$mode?>'> <?}?>
<div id="buttonsetm">
<div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Здравствуйте<?if(defined("USER_FIO")) echo " ".USER_FIO?>!</strong>
                        </div>
                        <div class="panel-body">
<p>Вам предлагается пройти адаптивный тест <strong>"<?=$test->getName()?>"</strong>, состоящий из следующих областей знаний и групп вопросов (разделов, тем):</p>
<div class='table-responsive'>
      <table class='table' width='95%'>
          <thead>
              <td class='success' align='left' witdh='300'>Область знаний</td>
              <td class='success' align='left' witdh='400'>Группа вопроcов (раздел, тема)</td>
          </thead>
          <tbody>   
<?         
      // Покажем группы
      foreach($test->getGroups() as $group) 
      {
        echo "<tr><td><p>".$group->getKnowname()."</p>";
        echo "</td>";
        echo "<td><p>".$group->getName()."</p>";
        if ($group->getComment() != "")
         echo "<p style='font-size:0.7em;'>".$group->getComment()."</p>";
        echo "</td>";
        echo "</tr>";
      }
      echo "</tbody>
       </table>
       </div>
      </div>
      <div class='panel-footer'>";

      echo "<p>".$test->getContent()."</p>";

      // Найдем первый вопрос теста
      $questid = 0;
      $groupid = 0;
      $minutes = 0;

      // 1. Найдем группу со средней сложностью (если групп со сложностями две - берем наименьшую 
      // - предполагая что уровень знаний изначально низкий)
      $group = $test->getSuperAverageGroup();
      $groupid = $group->getId();
      $questid = $group->getFirstQuestion()->getId();
      $minutes = $group->getFirstQuestion()->getTime();
      
      echo "<input type='hidden' name='hours' value='0'>";
      echo "<input type='hidden' name='minutes' value='".$minutes."'>";
      echo "<input type='hidden' name='questid' value='".$questid."'>";
      echo "<input type='hidden' name='groupid' value='".$groupid."'>";
?>    
<p>При адаптивном тестировании, система будет предлагать вопросы в зависимости от Вашего уровня знаний по выбранной теме. Вернуться на предыдущий вопрос в адаптивном тесте нельзя.</p>
   
   </div>
  </div>
 </div>
</form>
  <div id="buttonset">
   <?
     if ($bdinfo=='')
     {
      echo '
      <button class="ui-button-success" style="font-size: 1em;" id="begintest" onclick="$(\'#bt\').submit()"><i class="fa fa-dashboard fa-lg"></i> Тестирование</button>  
      <button class="ui-button-success" style="font-size: 1em;" id="close" onclick="parent.closeFancybox()">Отмена</button>  
      ';
     }
     else
     {
      echo '
      <button class="ui-button-danger" style="font-size: 1em;" id="begintest" onclick="parent.closeFancybox()">'.$bdinfo.'</button>  
      ';
     }
   ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="lms/scripts/jquery-ui/jquery-ui.min.js"></script>
    <script>
     $(function() {
      $( "#begintest" ).button();
      $( "#close" ).button();
      var isInIFrame = (window.location != window.parent.location);
      if(isInIFrame==false)
      {
       $("#begintest").button( "option", "disabled", true ); 
       $("#close").button( "option", "disabled", true ); 
      }
     });
   </script>
</body>
</html>
<?
}
?>