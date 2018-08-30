
  function myInfoMsgShow(info) {
     $('#myInfoMsgContent').empty();
     $('#myInfoMsgContent').append(info);
     $('#myInfoMsg').modal('show');  
  }

  function formRegShow(check) {
     $('#myModalLogin').modal('hide');  
     $('#RegEmailformgroup').removeClass('has-error');
     $('#RegInputEmail').val('');
     $('#RegNameformgroup').removeClass('has-error');
     $('#RegInputName').val('');
     $('#RegJobformgroup').removeClass('has-error');
     $('#RegInputJob').val('');
     $('#RegSupervisor').prop('checked', check);
     if (check)
      $('#myModalLabelReg').html('Регистрация супервизора');
     else
      $('#myModalLabelReg').html('Регистрация участника или эксперта');
     $('#myModalReg').modal('show');  
  }

  function formRegIn() {
     var postParams;
     var tt = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
     
     $('#RegNameformgroup').removeClass('has-error');
     $('#RegEmailformgroup').removeClass('has-error');
     $('#RegJobformgroup').removeClass('has-error');
     if ($('#RegInputName').val().length==0) 
     {
         $('#RegNameformgroup').addClass('has-error');
         $('#RegInputName').focus();
     }
     else
     if ($('#RegInputEmail').val().length==0) 
     {
         $('#RegEmailformgroup').addClass('has-error');
         $('#RegInputEmail').focus();
     }
     else
     if (!tt.test($('#RegInputEmail').val())) 
     {
         $('#RegEmailformgroup').addClass('has-error');
         $('#RegInputEmail').focus();
     }
     else
     if ($('#RegInputJob').val().length==0)
     { 
         $('#RegJobformgroup').addClass('has-error');
         $('#RegInputJob').focus();
     }
     else
     {
         $('#RegNameformgroup').removeClass('has-error');
         $('#RegEmailformgroup').removeClass('has-error');
         $('#RegJobformgroup').removeClass('has-error');
         postParams = {
                    fio: $('#RegInputName').val(),
                    email: $('#RegInputEmail').val(),
                    job: $('#RegInputJob').val(),
                    supervisor: $('#RegSupervisor').prop('checked')
                }; 
         $('#myModalReg').modal('hide');  
         $.post("regajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? myInfoMsgShow("Регистрация прошла успешно! Проверьте Ваш ящик электронной почты.") : myInfoMsgShow("Ошибка при регистрации! " + obj.error)
                });
     }           
  }

  function formForgotShow() {
     $('#myModalLogin').modal('hide');  
     $('#ForgotEmailformgroup').removeClass('has-error');
     $('#ForgotInputEmail').val('');
     $('#myModalForgot').modal('show');  
  }

  function formForgotIn() {
     var postParams;
     var tt = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
     
     $('#ForgotEmailformgroup').removeClass('has-error');
     if ($('#ForgotInputEmail').val().length==0) 
     {
         $('#ForgotEmailformgroup').addClass('has-error');
         $('#ForgotInputEmail').focus();
     }
     else
     if (!tt.test($('#ForgotInputEmail').val())) 
     {
         $('#ForgotEmailformgroup').addClass('has-error');
         $('#ForgotInputEmail').focus();
     }
     else
     {
         $('#ForgotEmailformgroup').removeClass('has-error');
         postParams = {
                    email: $('#ForgotInputEmail').val()
                }; 
         $('#myModalForgot').modal('hide');  
         $.post("forgotajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ?  myInfoMsgShow("На Ваш ящик электронной почты " + $('#ForgotInputEmail').val() + " отправлена ссылка на изменение пароля.") : myInfoMsgShow("Ошибка при восстановлении пароля! " + obj.error)
                });
     }           
  }

  function formLoginShow() {
     $('#Loginformgroup').removeClass('has-error');
     $('#Passformgroup').removeClass('has-error');
     $('#InputLogin').val('');
     $('#InputPass').val('');
     $('#myModalLogin').modal('show');  
  }

  function formLoginIn() {
     var postParams;
     
     $('#Loginformgroup').removeClass('has-error');
     $('#Passformgroup').removeClass('has-error');
     if ($('#InputLogin').val().length==0) 
     {
         $('#Loginformgroup').addClass('has-error');
         $('#InputLogin').focus();
     }
     else
     if ($('#InputPass').val().length==0) 
     {
         $('#Passformgroup').addClass('has-error');
         $('#InputPass').focus();
     }
     else
     {
         $('#Loginformgroup').removeClass('has-error');
         $('#Passformgroup').removeClass('has-error');
         postParams = {
                    login: $('#InputLogin').val(),
                    password: $('#InputPass').val()
                }; 
         $('#myModalLogin').modal('hide');  
         $.post("loginajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? location.replace('ts') : myInfoMsgShow("Нет такого пользователя или логин и пароль введены неправильно!")
                });
     }           
  }

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
         $.post("msgajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? myInfoMsgShow("Ваше сообщение отправлено!") : myInfoMsgShow("Ошибка при отправке сообщения!")
                });
     }           
  }


