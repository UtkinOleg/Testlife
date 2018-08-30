<?php

if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

include "config.php";

$action = $_POST["action"];
if (!empty($action)) 
{
  $kid = $_POST["kid"];
  $qgid = $_POST["qgid"];
  $qid = $_POST["qid"];
  $mode = $_POST["m"];
  $answers = $_POST["cntanswers"];
  $content = mysqli_real_escape_string($mysqli,$_POST["content"]);
  $content2 = mysqli_real_escape_string($mysqli,$_POST["content2"]);
  
  //$content = htmlspecialchars($_POST["content"], ENT_QUOTES);
  $name = '';
  $qtype = $_POST["qtype"];
   
  if ($mode=='a')
  {
   mysqli_query($mysqli,"START TRANSACTION;");
   $query = "INSERT INTO questions VALUES (0,
      '$name',
      '$content',
      $qgid,
      '$qtype',
      '$content2')";
  
   if(mysqli_query($mysqli,$query)) {
    $questionid = mysqli_insert_id($mysqli);
    // Запишем ответы в базу
    if ($questionid>0)
    {
     for ($i = 1; $i <= $answers; $i++) {
      $answer = htmlspecialchars($_POST["answer".$i], ENT_QUOTES);
      if (!empty($answer))
      {
         if ($qtype == 'multichoice') 
          $ball = $_POST["ball".$i];
         else
          $ball = 1; 

         if ($qtype == 'accord')
         { 
           $answer2 = htmlspecialchars($_POST["answer_".$i], ENT_QUOTES);
           $answer .= '='.$answer2;
         }  

         $query = "INSERT INTO answers VALUES (0,
         '$answer',
         $questionid,
         $ball)";

         mysqli_query($mysqli,$query);
      }
    }
   }
  }
   
     mysqli_query($mysqli,"COMMIT");
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$kid.',"q");';
     echo '</script>';
     exit();
   }
   else
   if ($mode=='e')
   {
     // Сначала удалим старые ответы
     mysqli_query($mysqli,"START TRANSACTION;");
     mysqli_query($mysqli,"DELETE FROM answers WHERE questionid=".$qid);
//     mysqli_query($mysqli,"COMMIT");
//     mysqli_query($mysqli,"START TRANSACTION;");

     // Добавим ответы 
     for ($i = 1; $i <= $answers; $i++) {
      $answer = htmlspecialchars($_POST["answer".$i], ENT_QUOTES);
      if (!empty($answer))
      {
         if ($qtype == 'multichoice') 
          $ball = $_POST["ball".$i];
         else
          $ball = 1; 

         if ($qtype == 'accord')
         { 
           $answer2 = htmlspecialchars($_POST["answer_".$i], ENT_QUOTES);
           $answer .= '='.$answer2;
         }  

         $query = "INSERT INTO answers VALUES (0,
         '$answer',
         $qid,
         $ball)";

         mysqli_query($mysqli,$query);
      }
     }
     
     // Обновим вопрос
     $query = "UPDATE questions SET name = '".$name."'
            , content = '".$content."' 
            , qtype = '".$qtype."' 
            , content2 = '".$content2."' 
            WHERE id=".$qid;
     mysqli_query($mysqli,$query);
     mysqli_query($mysqli,"COMMIT");
     
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/listquestions&kid='.$kid.'&id='.$qgid.'");';
     echo '</script>';
     exit();
   } 
    
}
else
if (empty($action)) 
{
  $mode = $_GET["m"];
  $qid = $_GET["qid"];
  $qgid = $_GET["id"];
  $kid = $_GET["kid"];
  
  if ($mode=='e')
  {
   $modename = "Изменить вопрос и ответы";
   $query = "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$qid."' LIMIT 1;";
   $sql = mysqli_query($mysqli,$query);
   $question = mysqli_fetch_array($sql);
   
  }
  else
  if ($mode=='a')
  {
    $modename = 'Введите новый вопрос и ответы';
  }
  
  require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script src="lms/scripts/wysiwyg.js"></script>
<script type="text/javascript">

 $(function(){
   $("#spinner").fadeOut("slow");
   $("button").button();
   $("#qtype").selectmenu({
          width: 350,
          change: function( event, data ) {
          var typecontent = data.item.value;
          $("#answerfield").empty();
          $("#answertext").empty();
          if ( typecontent === "multichoice" ) {
            $("#answerfield").append("<input type='checkbox' name='ball' id='ball' onclick='checkball()'><label id='labelball' title='Правильный ответ' for='ball'><i class='fa fa-check fa-lg icon-invisible'></i></label>&nbsp;<input type='text' id='answer' name='answer' style='width:90%;'>");
            $("#answertext").append("<p>Ответы (несколько вариантов):</p>");
            $("#ball").button();
          } else if ( typecontent === "shortanswer" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:100%;'>");
            $("#answertext").append("<p>Правильный ответ (символы <strong>.*</strong> обозначают один или несколько любых символов):</p>");
          } else if ( typecontent === "sequence" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:100%;'>");
            $("#answertext").append("<p>Введите значение последовательности:</p>");
          } else if ( typecontent === "accord" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:45%;'>&nbsp;<i class='fa fa-arrows-h fa-lg'></i>&nbsp;<input type='text' id='answer_' name='answer_' style='width:45%;'>");
            $("#answertext").append("<p>Введите соответствия:</p");
          } 
          for (i = 1; i <= $("#cntanswers").val(); i++) 
           delanswer(i); 
          $("#cntanswers").val(0); 
          $('#showanswers').empty();
        }
        });
    <? if ($mode=='a') {?>    
     $("#answerfield").append("<input type='checkbox' name='ball' id='ball' onclick='checkball()'><label id='labelball' title='Правильный ответ' for='ball'><i class='fa fa-check fa-lg icon-invisible'></i></label>&nbsp;<input type='text' id='answer' name='answer' style='width:90%;'>");
     $("#ball").button();
    <?} else if ($mode=='e') {?>
          $("#answertext").empty();
          var typecontent = "<?=$question['qtype']?>";
          if ( typecontent === "multichoice" ) {
            $("#answerfield").append("<input type='checkbox' name='ball' id='ball' onclick='checkball()'><label id='labelball' title='Правильный ответ' for='ball'><i class='fa fa-check fa-lg icon-invisible'></i></label>&nbsp;<input type='text' id='answer' name='answer' style='width:90%;'>");
            $("#answertext").append("<p>Ответы (несколько вариантов):</p>");
            $("#ball").button();
          } else if ( typecontent === "shortanswer" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:100%;'>");
            $("#answertext").append("<p>Правильный ответ (символы <strong>.*</strong> обозначают один или несколько любых символов):</p>");
          } else if ( typecontent === "sequence" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:100%;'>");
            $("#answertext").append("<p>Введите значение последовательности:</p>");
          } else if ( typecontent === "accord" ) {
            $("#answerfield").append("<input type='text' id='answer' name='answer' style='width:45%;'>&nbsp;<i class='fa fa-arrows-h fa-lg'></i>&nbsp;<input type='text' id='answer_' name='answer_' style='width:45%;'>");
            $("#answertext").append("<p>Введите соответствия:</p");
          } 
          <? 
           $sql2 = mysqli_query($mysqli,"SELECT * FROM answers WHERE questionid='".$qid."' ORDER BY id;");
   while($answer = mysqli_fetch_array($sql2))
   {
     if ($question['qtype']=='accord')
     {
      $pieces = explode("=", trim(rtrim($answer['name'], "\n\r")));
      $name = $pieces[0];
      $name2 = $pieces[1];
          ?>
          name = "<?=$name?>";
          name2 = "<?=$name2?>";
          ball = <?=$answer['ball']?>;
          <?     
      } 
      else 
      {
          ?>
          name = "<?=trim(rtrim($answer['name'], '\n\r'))?>";
          name2 = "";
          ball = <?=$answer['ball']?>;
      <? } ?>
          addanswermanual(name, name2, ball);
          <?}    mysqli_free_result($sql2);
          ?>
    <?}?>
  });       

 function checkRegexp(n, t) {
        return t.test(n.val())
 }

 function delanswer(cnt)
 {
   $("#answer"+cnt).val('');
   $("#answer_"+cnt).val('');
   $("#ball"+cnt).val('');
   $('#danswer'+cnt).empty();
 }

 function checkanswer(cnt)
 {
   ball = document.getElementById('ballc'+cnt).checked;
   if (ball)       
   {
     $("#ball"+cnt).val('1');
     $("#labelball"+cnt).html('<span class="ui-button-text"><i class="fa fa-check fa-lg"></i></span>');
   }
   else
   {
     $("#ball"+cnt).val('0');
     $("#labelball"+cnt).html('<span class="ui-button-text"><i class="fa fa-check fa-lg icon-invisible"></i></span>');
   }
 }

 function checkball()
 {
   ball = document.getElementById('ball').checked;
   if (ball)       
   {
     $("#labelball").html('<span class="ui-button-text"><i class="fa fa-check fa-lg"></i></span>');
   }
   else
   {
     $("#labelball").html('<span class="ui-button-text"><i class="fa fa-check fa-lg icon-invisible"></i></span>');
   }
 }

 function addanswer()
 {
     var hasError = false; 
     var cnt = $("#cntanswers").val();
     var answer = $("#answer");
     var answer2;
     
     if ($("#qtype").val()=='accord')
      if (document.getElementById('answer_'))
       answer2 = $("#answer_");

     var ball = true;
     if (document.getElementById('ball'))
       ball = document.getElementById('ball').checked;
     
     if(answer.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите ответ');
            hasError = true;
     }
     
     if ($("#qtype").val()=='accord')
      if(answer2.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите ответ');
            hasError = true;
      }

     if(hasError == false) {     
      for (i = 1; i <= cnt; i++) { 
        if ($("#answer"+i).val() == answer.val())
        {
            $("#info2").empty();
            $("#info2").append('Такой ответ уже существует');
            hasError = true;
            break;
        }
      }
     }
      
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#answer").focus();
       if ($("#qtype").val()=='accord')
         if(answer2.val()=='') 
           $("#answer_").focus();
     }
     else
     {
      cnt++;
      $("#cntanswers").val(cnt);

      $('#hiddenanswers').append('<input type="hidden" id="answer'+cnt+'" name="answer'+cnt+'" value="' + answer.val() + '">'); 

      if (ball==true)       
       $('#hiddenanswers').append('<input type="hidden" id="ball'+cnt+'" name="ball'+cnt+'" value="1">');        
      else
       $('#hiddenanswers').append('<input type="hidden" id="ball'+cnt+'" name="ball'+cnt+'" value="0">');        

      if (document.getElementById('answer_'))
       $('#hiddenanswers').append('<input type="hidden" id="answer_'+cnt+'" name="answer_'+cnt+'" value="' + answer2.val() + '">');        

      if (document.getElementById('answer_'))
       $('#showanswers').append('<div id="danswer'+cnt+'"><p>' + answer.val() + '&nbsp;<i class="fa fa-arrows-h fa-lg"></i>&nbsp;' + answer2.val() + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      else
      if ($("#qtype").val()=='multichoice')
      {
       if (ball==true)       
        $('#showanswers').append('<div id="danswer'+cnt+'"><p><input type="checkbox" name="ballc'+cnt+'" id="ballc'+cnt+'" onclick="checkanswer('+cnt+');" checked><label id="labelball'+cnt+'" for="ballc'+cnt+'"><i class="fa fa-check fa-lg"></i></label> ' + answer.val() + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
       else
        $('#showanswers').append('<div id="danswer'+cnt+'"><p><input type="checkbox" name="ballc'+cnt+'" id="ballc'+cnt+'" onclick="checkanswer('+cnt+');"><label id="labelball'+cnt+'" for="ballc'+cnt+'"><i class="fa fa-check fa-lg icon-invisible"></i></label> ' + answer.val() + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      }
      else
      if ($("#qtype").val()=='shortanswer')
      {
        $('#showanswers').append('<div id="danswer'+cnt+'"><p>Правильный ответ: <strong>' + answer.val() + '</strong>&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      }
      else
      if ($("#qtype").val()=='sequence')
      {
        $('#showanswers').append('<div id="danswer'+cnt+'"><p>' + answer.val() + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      }
      $("#delb"+cnt).button();
      $("#ballc"+cnt).button();
      $("#answer").val('');
      if (document.getElementById('answer_'))
       $("#answer_").val('');
      $("#answer").focus();
     }
     
 }

 function addanswermanual(answer, answer2, ball)
 {
      var cnt = $("#cntanswers").val();
      cnt++;
      $("#cntanswers").val(cnt);

      $('#hiddenanswers').append('<input type="hidden" id="answer'+cnt+'" name="answer'+cnt+'" value="' + answer + '">'); 
      if (ball==true)       
       $('#hiddenanswers').append('<input type="hidden" id="ball'+cnt+'" name="ball'+cnt+'" value="1">');        
      else
       $('#hiddenanswers').append('<input type="hidden" id="ball'+cnt+'" name="ball'+cnt+'" value="0">');        
      $('#hiddenanswers').append('<input type="hidden" id="answer_'+cnt+'" name="answer_'+cnt+'" value="' + answer2 + '">');        

      if (document.getElementById('answer_'))
       $('#showanswers').append('<div id="danswer'+cnt+'"><p>' + answer + '&nbsp;<i class="fa fa-arrows-h fa-lg"></i>&nbsp;' + answer2 + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      else
      if ($("#qtype").val()=='multichoice')
      {
       if (ball > 0)       
        $('#showanswers').append('<div id="danswer'+cnt+'"><p><input type="checkbox" name="ballc'+cnt+'" id="ballc'+cnt+'" onclick="checkanswer('+cnt+');" checked><label id="labelball'+cnt+'" for="ballc'+cnt+'"><i class="fa fa-check fa-lg"></i></label> ' + answer + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
       else
        $('#showanswers').append('<div id="danswer'+cnt+'"><p><input type="checkbox" name="ballc'+cnt+'" id="ballc'+cnt+'" onclick="checkanswer('+cnt+');"><label id="labelball'+cnt+'" for="ballc'+cnt+'"><i class="fa fa-check fa-lg icon-invisible"></i></label> ' + answer + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
       $("#ballc"+cnt).button();
      }
      else
      if ($("#qtype").val()=='shortanswer')
      {
        $('#showanswers').append('<div id="danswer'+cnt+'"><p>Правильный ответ: <strong>' + answer + '</strong>&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      }
      else
      if ($("#qtype").val()=='sequence')
      {
        $('#showanswers').append('<div id="danswer'+cnt+'"><p>' + answer + '&nbsp;<button title="Удалить ответ" id="delb'+cnt+'" onclick="delanswer('+cnt+');"><i class="fa fa-minus-circle"></i></button></p></div>');        
      }
      $("#delb"+cnt).button();
 }
 
 jQuery(document).ready(function() {
    $( "#tabs" ).tabs();
    $('#content').juirte();
    $('#content2').juirte();
    $('#adds').submit(function()
    {
     if ($("#content").val() == '')
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Введите содержание вопроса');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#content-editor").focus();
      return false;
     }   

     var cnt = $("#cntanswers").val();
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
      $("#info2").append('Укажите ответы на вопрос');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#answer").focus();
      return false;
     }   
    });   
  });   

</script>
<style>
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
p { font: 14px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
.ui-wysiwyg .ui-button{font-size:90%;height:24px;min-width:30px}
.ui-wysiwyg .ui-button span{font-size:80%}
.ui-wysiwyg sub{font-size:6px;font-weight:700;line-height:1px}
.ui-wysiwyg sup{font-size:6px;font-weight:700}
.ui-wysiwyg-btn-forecolor span{text-decoration:underline}
.ui-wysiwyg-btn-italic span{font-weight:400}
.ui-wysiwyg-btn-strikeThrough span{font-weight:400;text-decoration:line-through}
.ui-wysiwyg-btn-underline span{font-weight:400;text-decoration:underline}
.ui-wysiwyg-colorinput{width:60px}
.ui-wysiwyg-dropdown{display:none;margin-left:2px;position:absolute;z-index:2229}
.ui-wysiwyg-dropdown ul{list-style:none;margin:0;padding:10px}
.ui-wysiwyg-dropdown ul li{cursor:pointer;padding:2px;text-decoration:underline}
.ui-wysiwyg-dropdown ul li h1,.ui-wysiwyg-dropdown ul li h2,.ui-wysiwyg-dropdown ul li h3,.ui-wysiwyg-dropdown ul li h4,.ui-wysiwyg-dropdown ul li h5,.ui-wysiwyg-dropdown ul li h6{margin:0;padding:0}
.ui-wysiwyg-justify-wrap{line-height:2px;margin-top:-4px;text-align:left}
.ui-wysiwyg-justify-center{text-align:center}
.ui-wysiwyg-justify-right{text-align:right}
.ui-wysiwyg-left{float:left;margin-bottom:4px}
.ui-wysiwyg-list-wrap{font-size:7px;line-height:6px;overflow:hidden;text-align:left}
.ui-wysiwyg-menu {margin:-4px 0 0 -1px;padding:0}
.ui-wysiwyg-menu-wrap {margin:4px 4px 0}
.ui-wysiwyg-row{clear:left;float:left}
.ui-wysiwyg-swatch{border:1px solid #000;display:table;height:12px;text-decoration:none;width:100%}
.ui-wysiwyg-fontbgcdropdown,.ui-wysiwyg-fontcldropdown{padding:4px; width: 80px}
.ui-wysiwyg-fontdropdown,.ui-wysiwyg-fontbgcdropdown,.ui-wysiwyg-fontcldropdown{ overflow: auto; height: 120px;}
.ui-wysiwyg-fontdropdown{ width: 200px}
.ui-wysiwyg-container{display:table-cell}
[class^="icon-"].icon-fixed-width, [class*=" icon-"].icon-fixed-width {
    text-align: center;
}
.icon-invisible {
    visibility: hidden;
}
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
 <form id="adds" action="addquestmanual" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="qgid" value="<?=$qgid?>">
  <input type="hidden" name="kid" value="<?=$kid?>">
  <input type="hidden" name="qid" value="<?=$qid?>">
  <input type="hidden" name="m" value="<?=$mode?>">
  <input type='hidden' id='cntanswers' name='cntanswers' value='0'>
   <table border="0" width='99%' cellpadding=0 cellspacing=0 bordercolorlight=gray bordercolordark=white>
    <tr>
     <td>
<div id="tabs" style="border: 0px;">
  <ul>
    <li style="margin-bottom: 0px;"><a href="#tabs-1">Вопрос</a></li>
    <li style="margin-bottom: 0px;"><a href="#tabs-2">Пояснение к неправильному ответу</a></li>
  </ul>
  <div id="tabs-1" style="padding: 0px;">
        <textarea id="content" name='content' style='width:100%;' rows='7'><? if ($mode=='e') echo $question['content']; ?></textarea>
  </div>
  <div id="tabs-2" style="padding: 0px;">
        <textarea id="content2" name='content2' style='width:100%;' rows='7'><? if ($mode=='e') echo $question['content2']; ?></textarea>
  </div>
</div>  
     </td>
    </tr>
    <tr>
        <td><p><select name='qtype' id='qtype' title='Тип ответа'>
          <? if ($mode=='a') {?>
          <option value='multichoice' selected>Выбор вариантов</option>
          <option value='shortanswer'>Ввод с клавиатуры</option>
          <option value='sequence'>Правильная последовательность</option>
          <option value='accord'>Соответствия</option>
          <?} else if ($mode=='e') {?>
           <? if ($question['qtype']=='multichoice') {?>
          <option value='multichoice' selected>Выбор вариантов</option>
          <option value='shortanswer'>Ввод с клавиатуры</option>
          <option value='sequence'>Правильная последовательность</option>
          <option value='accord'>Соответствия</option>
           <?} else if ($question['qtype']=='shortanswer') {?>
          <option value='multichoice'>Выбор вариантов</option>
          <option value='shortanswer' selected>Ввод с клавиатуры</option>
          <option value='sequence'>Правильная последовательность</option>
          <option value='accord'>Соответствия</option>
           <?} else if ($question['qtype']=='sequence') {?>
          <option value='multichoice'>Выбор вариантов</option>
          <option value='shortanswer'>Ввод с клавиатуры</option>
          <option value='sequence' selected>Правильная последовательность</option>
          <option value='accord'>Соответствия</option>
           <?} else if ($question['qtype']=='accord') {?>
          <option value='multichoice'>Выбор вариантов</option>
          <option value='shortanswer'>Ввод с клавиатуры</option>
          <option value='sequence'>Правильная последовательность</option>
          <option value='accord' selected>Соответствия</option>
          <?}}?>
         </select></p>
        </td>
    </tr><tr>
        <td><div id='answertext'><p>Ответы (несколько вариантов):</p></div></td>
    </tr>
    <tr>    
     <td>
        <div id="answerfield"></div>
        <div id="hiddenanswers"></div>
     </td>
    </tr>
    <tr>
     <td>
       <div id="showanswers"></div>
     </td>
    </tr>
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id="add" onclick="addanswer();"><i class="fa fa-plus fa-lg"></i> Добавить ответ</button>
            <button id='ok' onclick="$('#adds').submit();" ><i class='fa fa-check fa-lg'></i> Сохранить вопрос</button>
            <button id="close" onclick="parent.closeFancybox();"><i class='fa fa-times fa-lg'></i> Закрыть</button> 
            <button id="help" onclick="window.open('h&id=4');"><i class="fa fa-question fa-lg"></i> Помощь</button>
 </div>
</body></html>
<?
} 
} else die; 

?>