function DelWindow(n, t, i) {
    var f = i + "&id=" + n + "&start=" + t;
    confirm("\nВы действительно хотите удалить значение?\n") && (location.href = f)
}
function DelWindowPaid(n, t, i) {
    var f = i + "&id=" + n + "&paid=" + t;
    confirm("\nВы действительно хотите удалить значение?\n") && (location.href = f)
}
function DelWindowShablonPaid(n, t, i, r) {
    var e = r + "&id=" + n;
    confirm("\nВы действительно хотите удалить значение?\n") && (location.href = e)
}
function DelList(n, t, i) {
    var r = "dellist&id=" + n + "&start=" + t;
    confirm("\nВнимание! При удалении экспертного листа будет обнулена сумма средних баллов по участнику.\nУдалить экспертный лист №" + i + "?\n") && (location.href = r)
}
function DelExpertWindow(n, t) {
    var i = "delexpert&id=" + n + "&start=" + t;
    confirm("\nВнимание! При удалении эксперта будет обнулена информация по экспертным оценкам.\nУдалить эксперта №" + n + "?") && (location.href = i)
}
function DelProjectWindow(n, t) {
    var i = "delproject&id=" + n;
    confirm("\nУдалить проект №" + t + "?") && (location.href = i)
}

$(function () {
    function updateTips(n) {
        tips.text(n).addClass("ui-state-highlight"),
        setTimeout(function () {
            tips.removeClass("ui-state-highlight", 1500)
        }, 500)
    }
    function updateTips2(n) {
        tips2.text(n).addClass("ui-state-highlight"),
        setTimeout(function () {
            tips2.removeClass("ui-state-highlight", 1500)
        }, 500)
    }
    function updateRegTips(n) {
        regtips.text(n).addClass("ui-state-highlight"),
        setTimeout(function () {
            regtips.removeClass("ui-state-highlight", 1500)
        }, 500)
    }
    function updateForgotTips(n) {
        forgottips.text(n).addClass("ui-state-highlight"),
        setTimeout(function () {
            forgottips.removeClass("ui-state-highlight", 1500)
        }, 500)
    }
    function checkLengthBody1(n, t) {
        return n.val().length == 0 ? (n.addClass("ui-state-error"), updateTips("Заполните поле '" + t + "'!"), !1) : !0
    }
    function checkLengthBody(n, t) {
        return n.val().length == 0 ? (n.addClass("ui-state-error"), updateTips2("Заполните поле '" + t + "'!"), !1) : !0
    }
    function checkLengthRegBody(n, t) {
        return n.val().length == 0 ? (n.addClass("ui-state-error"), updateRegTips("Заполните поле '" + t + "'!"), !1) : !0
    }
    function checkLengthForgotBody(n, t) {
        return n.val().length == 0 ? (n.addClass("ui-state-error"), updateForgotTips("Заполните поле '" + t + "'!"), !1) : !0
    }
    function checkRegexp(n, t, i) {
        return t.test(n.val()) ? !0 : (n.addClass("ui-state-error"), updateTips(i), !1)
    }
    function checkRegexp2(n, t, i) {
        return t.test(n.val()) ? !0 : (n.addClass("ui-state-error"), updateTips2(i), !1)
    }
    function checkRegexpReg(n, t, i) {
        return t.test(n.val()) ? !0 : (n.addClass("ui-state-error"), updateRegTips(i), !1)
    }
    function checkRegexpForgot(n, t, i) {
        return t.test(n.val()) ? !0 : (n.addClass("ui-state-error"), updateForgotTips(i), !1)
    }
    function enter() {
                var bValid = !0,
                    postParams;
                allFields.removeClass("ui-state-error"),
                bValid = bValid && checkLengthBody1(name, "Логин"),
                bValid = bValid && checkLengthBody1(pass, "Пароль"),
                bValid = bValid && checkRegexp(name, /^[a-z]([0-9a-z_])+$/i, "Логин должен содержать символы a-z, 0-9 и начинаться с буквы."),
                bValid && ($("#spinner").fadeIn("slow"), postParams = {
                    login: name.val(),
                    password: pass.val()
                }, $.post("loginajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? location.reload() : alert("Нет такого пользователя или логин и пароль введены неправильно!")
                }), $("#spinner").fadeOut("slow"), $(this).dialog("close"))
    }
    var name = $("#name"),
        pass = $("#pass"),
        saveme = $("#saveme"),
        allFields = $([]).add(name).add(pass).add(saveme),
        tips = $(".validateTips"),
        name2 = $("#name2"),
        email = $("#email"),
        body = $("#body"),
        allFields2 = $([]).add(name2).add(email).add(body),
        tips2 = $(".validateTips2"),
        regname = $("#regname"),
        regemail = $("#regemail"),
        job = $("#job"),
        allFieldsReg = $([]).add(regname).add(regemail).add(job),
        regtips = $(".validateRegTips"),
        forgotemail = $("#forgotemail"),
        allFieldsForgot = $([]).add(forgotemail),
        forgottips = $(".validateForgotTips");
        
    $("#login-form").dialog({
        autoOpen: !1,
        height: 400,
        width: 350,
        resizable: !1,
        modal: !0,
        buttons: {
            "Вход": function () {
                enter();
            },
            "Отмена": function () {
                $(this).dialog("close")
            }
        },
        close: function () {
            allFields.val("").removeClass("ui-state-error")
        }
    }),
    $("#login-form").keypress(function(event) {
        if (event.which == '13') {
           enter();
        }
    }),
    $("#msg-form").dialog({
        autoOpen: !1,
        height: 400,
        width: 550,
        resizable: !1,
        modal: !0,
        buttons: {
            "Отправить сообщение": function () {
                var bValid = !0,
                    postParams;
                allFields2.removeClass("ui-state-error"),
                bValid = bValid && checkLengthBody(name2, "Имя"),
                bValid = bValid && checkLengthBody(email, "Email"),
                bValid = bValid && checkRegexp2(email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Пример поля Email - myname@anymail.com"),
                bValid = bValid && checkLengthBody(body, "Сообщение"),
                bValid && ($("#spinner").fadeIn("slow"), postParams = {
                    name: name2.val(),
                    email: email.val(),
                    body: body.val()
                }, $.post("msgajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? alert("Ваше сообщение отправлено!") : alert("Ошибка при отправке сообщения!")
                }), $("#spinner").fadeOut("slow"), $(this).dialog("close"))
            },
            "Отмена": function () {
                $(this).dialog("close")
            }
        },
        close: function () {
            allFields2.val("").removeClass("ui-state-error")
        }
    }),
    $("#reg-form").dialog({
        autoOpen: !1,
        height: 300,
        width: 550,
        resizable: !1,
        modal: !0,
        buttons: {
            "Регистрация участника": function () {
                var bValid = !0,
                    postParams;
                allFieldsReg.removeClass("ui-state-error"),
                bValid = bValid && checkLengthRegBody(regname, "Имя"),
                bValid = bValid && checkLengthRegBody(regemail, "Email"),
                bValid = bValid && checkRegexpReg(regemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Пример поля Email - myname@anymail.com"),
                bValid = bValid && checkLengthRegBody(job, "Место работы"),
                bValid && ($("#spinner").fadeIn("slow"), postParams = {
                    fio: regname.val(),
                    email: regemail.val(),
                    job: job.val()
                }, $.post("regajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? alert("Регистрация прошла успешно! Проверьте Ваш ящик электронной почты.") : alert("Ошибка при регистрации! " + obj.error)
                }), $("#spinner").fadeOut("slow"), $(this).dialog("close"))
            },
            "Отмена": function () {
                $(this).dialog("close")
            }
        },
        close: function () {
            allFieldsReg.val("").removeClass("ui-state-error")
        }
    }),
    $("#forgot-form").dialog({
        autoOpen: !1,
        height: 200,
        width: 550,
        resizable: !1,
        modal: !0,
        buttons: {
            "Восстановить пароль": function () {
                var bValid = !0,
                    postParams;
                allFieldsForgot.removeClass("ui-state-error"),
                bValid = bValid && checkLengthForgotBody(forgotemail, "Email"),
                bValid = bValid && checkRegexpForgot(forgotemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Пример поля Email - myname@anymail.com"),
                bValid && ($("#spinner").fadeIn("slow"), postParams = {
                    email: forgotemail.val()
                }, $.post("forgotajax.php", postParams, function (data) {
                    eval("var obj=" + data),
                    obj.ok == "1" ? alert("На Ваш ящик электронной почты " + forgotemail.val() + " отправлена ссылка на изменение пароля.") : alert("Ошибка при восстановлении пароля! " + obj.error)
                }), $("#spinner").fadeOut("slow"), $(this).dialog("close"))
            },
            "Отмена": function () {
                $(this).dialog("close")
            }
        },
        close: function () {
            allFieldsForgot.val("").removeClass("ui-state-error")
        }
    }),
    $("#login1").click(function () {
        $("#login-form").dialog("open")
    }),
    $("#msg1").click(function () {
        $("#msg-form").dialog("open")
    }),
    $("#reg1").click(function () {
        $("#login-form").dialog("close"),
        $("#reg-form").dialog("open")
    }),
    $("#forgot1").click(function () {
        $("#login-form").dialog("close"),
        $("#forgot-form").dialog("open")
    })
});