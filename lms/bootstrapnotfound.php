<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание адаптивных и стандартных тестов онлайн и расширенный анализ результатов">
    <meta name="keywords" content="тестирование, онлайн тестирование, адаптивный тест, адаптивное тестирование, online test, online тестирование, анализ результатов тестирования, освоение тем теста, решаемость заданий, психологическое тестирование, психологический тест" /> 
    <meta name="copyright" content="Oleg Utkin" /> 
    <meta name="author" content="Oleg Utkin" />
    <meta property="og:image" content="http://testlife.org/img/testlife.png" />
    <title>Test Life</title>
    <link rel="icon" href="http://testlife.org/ico/favicon.ico">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
  </head>
<body>

<?php
 include "bootstrapsocial.php";
?>

<div class="modal fade" id="myModalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">    
  <div class="modal-dialog">       
    <div class="modal-content">           
      <div class="modal-header">               
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;       
        </button>               
        <h4 class="modal-title" id="myModalLabel1"></h4>           
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
        <button type="button" class="btn btn-primary" onclick="formSend();">Отправить сообщение       
        </button>               
        <button type="button" class="btn btn-primary" onclick="$('#myModalMsg').modal('hide');">Закрыть       
        </button>           
      </div>       
    </div>   
  </div>
</div>      


<div id="wrapper">                         

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" style="margin-top: 4px;" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Меню</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="http://testlife.org"><img src="img/testlife.png" height="12"></a>
            </div>
            <div class="navbar-collapse collapse">
            <ul class="nav navbar-top-links navbar-left" style="background-color: #f8f8f8;">
                        <li>
                            <a href="h">Документация</a>
                        </li>
                        <li>
                            <a href="javascript:;" title="Отправить сообщение" onclick="formShow('Отправить сообщение','Сообщение');">Контакты</a>
                        </li>
            </ul>
            <ul class="nav navbar-top-links navbar-right" style="background-color: #f8f8f8;">
                        <li>
                            <a href="javascript:;" onclick="$('#tlLoginForm').modal('show');">Вход</a>
                        </li>
            </ul>
            </div>
       </nav>

     
<div class="row">
                <div class="col-lg-12">
                    <div class="jumbotron">
                        <h3><strong>Ошибка 404</strong></h3>
                        <p>Страница не найдена</p>
                    </div>
                </div>
</div>
     
</div>
           
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    
	  	$(document).ready(function() {

        <? $err = $_GET["err"];
        $msg = '';
        if (!empty($err))
        {
         if ($err=='address')
          $msg = 'Пользователь с таким адресом электронной почты уже зарегистрирован.';
         else 
         if ($err=='login')
          $msg = 'Ошибка авторизации пользователя.';
         else 
         if ($err=='dbase')
          $msg = 'Ошибка сохранения данных пользователя.';
         else 
         if ($err=='token')
          $msg = 'Ошибка при передаче данных пользователя.';
        }
        if ($msg!='') {?>
        $('#myInfoMsgContent').html('<?=$msg?>');
        $('#myInfoMsg').modal('show');  
        <?}?>
      });

      
  function formShow(title,info) {
     $('#Nameformgroup').removeClass('has-error');
     $('#Emailformgroup').removeClass('has-error');
     $('#Infoformgroup').removeClass('has-error');
     $('#myModalLabel1').html(title);
     $('#LabelInfo').html(info);
     $('#hiddenInfo').val(info);
     $('#InputInfo').val('');
     $('#myModalMsg').modal('show');  
  }

  function formSend() {
     var postParams;
     var tt = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
     
     $('#Nameformgroup').removeClass('has-error');
     $('#Emailformgroup').removeClass('has-error');
     $('#Infoformgroup').removeClass('has-error');
     if ($('#InputName').val().length==0) 
     {
         $('#Nameformgroup').addClass('has-error');
         $('#InputName').focus();
     }
     else
     if ($('#InputEmail').val().length==0) 
     {
         $('#Emailformgroup').addClass('has-error');
         $('#InputEmail').focus();
     }
     else
     if (!tt.test($('#InputEmail').val())) 
     {
         $('#Emailformgroup').addClass('has-error');
         $('#InputEmail').focus();
     }
     else
     if ($('#InputInfo').val().length==0)
     { 
         $('#Infoformgroup').addClass('has-error');
         $('#InputInfo').focus();
     }
     else
     {
         $('#Nameformgroup').removeClass('has-error');
         $('#Emailformgroup').removeClass('has-error');
         $('#Infoformgroup').removeClass('has-error');
         postParams = {
                    name: $('#InputName').val(),
                    email: $('#InputEmail').val(),
                    title: $('#hiddenInfo').val(),
                    body: $('#InputInfo').val()
                }; 
         $('#myModalMsg').modal('hide');  
         $.post("msgajax", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? myInfoMsgShow("Ваше сообщение получено! В ближайшее время мы свяжемся с Вами.") : myInfoMsgShow("Ошибка при отправке сообщения!")
                });
     }           
  }    
  function myInfoMsgShow(info) {
     $('#myInfoMsgContent').html(info);
     $('#myInfoMsg').modal('show');  
  }    
   
   </script>
  </body>
</html>

            