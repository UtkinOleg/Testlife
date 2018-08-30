<?php
if(defined("IN_SUPERVISOR") AND USER_EXPERT_KIM) {
  
   require_once "config.php";  

   $grid = $_GET['id'];
   $kid = $_GET['kid'];
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

<div class="modal fade" id="CommentQuestion" tabindex="-1" role="dialog" aria-labelledby="CommentQuestionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="CommentQuestionLabel">Комментарий эксперта</h4>
      </div>
      <div class="modal-body">
       <form role="form">
        <input type="hidden" id="CommentQuestionhiddenInfoId" value="">
        <input type="hidden" id="CommentQuestionhiddenInfoGrId" value="">
          <div id="Infoformgroup" class="form-group">               
            <textarea class="form-control" rows="5" id="InputComment"></textarea>            
          </div>      
       </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="formCommentQuestion();">Ok</button>
        <button type="button" class="btn btn-primary" onclick="$('#CommentQuestion').modal('hide');">Отмена</button>
      </div>
    </div>
  </div>
</div>

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
  include "allnavigation.php";
  include "reminder.php";
?>
            
         <div id="quests">
         </div>
      </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="lms/scripts/myhelp.js?v=<?=$version?>"></script>
    <script type="text/javascript">
	  	
      $(document).ready(function() {
		  	$('.fancybox').fancybox();
        getexpertquests(<?=$grid?>,<?=$kid?>);
	  	});
      
      function closeFancyboxAndRedirectToUrl(url){
       $.fancybox.close();
       getexpertquests(<?=$grid?>,<?=$kid?>);
      }    
      
      function closeFancybox(){
       $.fancybox.close();
      } 
      
      function checkquestion(questid,check,qgroupid) 
      {
       $("#spinner").fadeIn("slow");
       $.post('getcheckquest.json',{qid:questid,c:check,qgid:qgroupid},  
       function(data){  
       eval('var obj='+data);         
       if(obj.ok=='Y')
       {
        $('#question'+questid).removeClass('panel-default').addClass('panel-success');        
        $('#buttonyes'+questid).addClass('disabled');        
        $('#buttonno'+questid).addClass('disabled');        
        $('#spinner').fadeOut("slow");
       } 
       else 
       if(obj.ok=='N')
       {
        $('#question'+questid).removeClass('panel-default').addClass('panel-danger');        
        $('#buttonyes'+questid).addClass('disabled');        
        $('#buttonno'+questid).addClass('disabled');        
        $('#spinner').fadeOut("slow");
       } 
       else 
       {
        $('#spinner').fadeOut("slow");
        alert("Ошибка при получении данных.");
       }
      });  
     }
     
     function formCommentQuestion() {
         var postParams = {
          qid: $('#CommentQuestionhiddenInfoId').val(),
          comment: $('#InputComment').val()
         }; 
         $('#Infoformgroup').removeClass('has-error');
         if ($('#InputComment').val().length==0) 
         {
           $('#Infoformgroup').addClass('has-error');
           $('#InputComment').focus();
         }
         else  
         {
          $('#Infoformgroup').removeClass('has-error');
          $('#CommentQuestion').modal('hide'); 
          $("#spinner").fadeIn("slow"); 
          $.post("expertcomment.json", postParams, 
          function (data) {
           $('#spinner').fadeOut("slow");
           eval("var obj=" + data);
           if(obj.ok=='1')
            $('#comments'+$('#CommentQuestionhiddenInfoId').val()).html(obj.comments);  
           else
            myInfoMsgShow("Ошибка при отправке комментария!");
          });
         } 
    }
   </script>                        
  </body>
</html>

<?
} else die;  
            