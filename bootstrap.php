<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание тестов онлайн">
    <meta name="keywords" content="онлайн тестирование, конструктор тестов, создать тест, адаптивное тестирование, психологический тест, редактор тестов, тесты онлайн, бесплатные тесты, тесты бесплатно, тесты без смс, создать тест онлайн" />    
    <meta name="author" content="Oleg Utkin">
    <link rel="icon" href="ico/favicon.ico">
    <title>Test Life</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
  </head>
  <body>

<div class="modal fade" id="myModalLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabelLogin" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabelLogin">Вход в систему</h4>
      </div>
      <div class="modal-body">
<form role="form">
  <div id="Loginformgroup" class="form-group">
    <input type="text" class="form-control" id="InputLogin" placeholder="Логин">
  </div>
  <div id="Passformgroup" class="form-group">
    <input type="password" class="form-control" id="InputPass" placeholder="Пароль">
  </div>
  <div class="text-center form-group">
        <button type="button" class="btn btn-primary" onclick="formLoginIn();">Вход</button>
        <button type="button" class="btn btn-primary" onclick="$('#myModalLogin').modal('hide');">Отмена</button>
  </div>
</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="myInfoMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel2">Сообщение</h4>
      </div>
      <div id="myInfoMsgContent" class="modal-body">
      </div>
    </div>
  </div>
</div>

    <div class="container">

      <div class="masthead">
        <img src="img/testlife.png" height="22" title="Создание тестов и проведение тестирования онлайн">
        <p></p>
        <ul class="nav nav-justified">
          <li><a href="javascript:;" onclick="formLoginShow();">Вход</a></li>
        </ul>
      </div>
    </div> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/newscript_1.js"></script>
    <script>
      formLoginShow();
    </script>
  </body>
</html>
                                