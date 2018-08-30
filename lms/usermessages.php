<?php
if(defined("USER_REGISTERED")) {  
  
  require_once "config.php";  

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
  </head>
<body>
<div id="spinner"></div>
<div id="wrapper">
<?php
  include "allnavigation.php";
  include "reminder.php";
?>
            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            Сообщения
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                         <div id="messages"></div>
                        </div>
                   </div>              
                   <button type="button" id="msgbutton" class="btn btn-outline btn-primary btn-sm" onclick="getallmessages()"><i class="fa fa-envelope fa-fw"></i> Ещё сообщения...</button>
                </div>
            </div>
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script src="lms/scripts/myhelp.js?v=<?=$version?>"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script type="text/javascript">
	  	var mo;
      
      $(document).ready(function() {
		  	$('.fancybox').fancybox();
        getallmessages();
	  	});
      
      function closeFancyboxAndRedirectToUrl(url){
       $.fancybox.close();
       location.replace(url);
      }    
      
      function closeFancybox(){
       $.fancybox.close();
      }  

<?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
  function gettesttasks() 
  {
    $.post('gettesttasks.json',{},  
     function(data){  
      eval('var obj='+data);         
      $('#testtasks').empty();  
      if(obj.ok=='1')
       $('#testtasks').append(obj.content);        
      else 
       $('#testtasks').append('Ошибка при загрузке текущих сеансов.');        
    }); 
  }
<?}?>
      
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
      function getusermsgs() 
      {
       $.post('getusermsgs.json',{},  
        function(data){  
         eval('var obj='+data);         
         $('#usermsgsoper').prop("onclick",null);        
         $('#usermsgs').empty();  
         if(obj.ok=='1')
          $('#usermsgs').append(obj.content);        
         else 
          $('#usermsgs').append('Ошибка при загрузке сообщений.');        
        }); 
      }

      function getallmessages() 
      {
       if (mo==undefined)
        mo=0;
       else
        mo+=10;
       $("#spinner").fadeIn("slow");
       $.post('getallmsgs.json',{offset:mo},  
        function(data){  
         eval('var obj='+data);         
         $("#spinner").fadeOut("slow");
         if(obj.ok=='1')
          $('#messages').append(obj.content);        
         else 
          $('#msgbutton').addClass('disabled');        
        }); 
      }

      function getmsg(sign) 
      {
       $("#spinner").fadeIn("slow");
       $.post('getumsg.json',{s:sign},  
        function(data){  
         eval('var obj='+data);         
         $("#spinner").fadeOut("slow");
         $('#msg'+sign).empty();  
         $('#badge'+sign).empty();  
         if(obj.ok=='1')
         {
          $('#msg'+sign).append(obj.content); 
          if (obj.count>0)
           $('#msgcount').text(''+obj.count);       
          else
           $('#msgcount').empty();
         }
         else 
          $('#msg'+sign).append('Ошибка при загрузке сообщения.');        
        }); 
      }
   </script>                        
  </body>
</html>

<?
} else die;  
?>            