<?php
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$title=$titlepage="Регистрация участника";
$error = "";
$error1 = "";
$action = "";


require_once('lib/recaptchalib.php');

// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LfYu78SAAAAAEPsziEdDFvkV06loBUAwa44-JhI";
$privatekey = "6LfYu78SAAAAABVIuDTGQFe76fcXW3XGu-HqRrHp";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];


// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{

  
  // Проверяем правильность ввода информации в поля формы

  if (empty($_POST["fio"])) 
  {
    $action = ""; 
    $error1 = $error1."<li>Вы не ввели ФИО</li>";
  }
  if (empty($_POST["email"])) 
  {
    $action = ""; 
    $error1 = $error1."<li>Вы не ввели адрес электронной почты</li>";
  }
  if (empty($_POST["job"])) 
  {
    $action = ""; 
    $error1 = $error1."<LI>Вы не указали место работы.\n";
  }
/*  if (empty($_POST["person"])) 
  {
    $action = ""; 
    $error1 = $error1."<LI>Вы не ввели Должность.\n";
  }
  if (empty($_POST["rules"])) 
  {
    $action = ""; 
    $error1 = $error1."<LI>Вы не согласились с условиями договора-офферты.\n";
  }*/

//  if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {

  $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
                   $error1 = $error1."<li>Неверно введен цифровой код с картинки</li>";
  }

//  unset($_SESSION['captcha']);


  // Проверим есть ли такоq email 
  $tot = mysql_query("SELECT max(id) FROM users ORDER BY id;");
  $gst = mysql_query("SELECT count(email) FROM users WHERE email='".strtolower(trim($_POST["email"]))."'");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");
  $totalemail = mysql_fetch_array($gst);
  $countemail = $totalemail['count(email)'];

  if ($countemail>0)
      $error1 = $error1."<li>Такой электронный адрес уже существует</li>";

  if (!empty($error1)) 
  {
  include "topadmin.php";
	echo"
	<p align=center>
  <div id='menu_glide' class='menu_glide'>
	<table border='0' align='center' class='bodytable' border='0' cellpadding=2 cellspacing=2>
	<tr><td>";
  echo"<ul><font face='Tahoma, Arial' size='-1'>".$error1."</font></ul></td></tr>";
  echo"<tr><td align='center'><input type='button' name='close' value='Назад' onclick='history.back()'>
  </td></tr></table><div></p>"; 
  include "bottomadmin.php";
  exit();
  }

  // Генерация логина и пароля
    $total = mysql_fetch_array($tot);
    $count = $total['max(id)']+1;

    $login = 'user'.$count;
    $password = generate_password(7);
    $cpassword = md5($password);
  
    $fio = $_POST["fio"];
    $email = strtolower(trim($_POST["email"]));
    $job = $_POST["job"];
    $person = $_POST["person"];
    $phone = $_POST["phone"];
    $region = $_POST["region"];
    $city = $_POST["city"];


    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO users VALUES (0,
                                        '$login',
                                        '$cpassword',
                                        '$fio',
                                        'user',
                                        NOW(),
                                        '$email',
                                        0,
                                        '',
                                        NOW(),
                                        '$job',
                                        0,
                                        '',
                                        '',
                                        '$phone',
                                        '$person',
                                        '',
                                        'offline',
                                        0,
                                        '$region',
                                        '$city',
                                        0,0,0,0,0,0,0,'','','male',0,'reg',0);";
    if(mysql_query($query))
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");

      if ($enable_cache) update_cache('SELECT id,userfio,email,usertype FROM users ORDER BY userfio');
      
      writelog("Зарегистрирован новый пользователь ".$login." (".$fio.").");

      $toemail = $email;
      $title = "Добро пожаловать в экспертную систему оценки проектов!";
      $body = "Здравствуйте, ".$fio."!\nСпасибо за регистрацию в экспертной системе оценки проектов!\n
      Для входа в систему: введите логин - ".$login."\nпароль - ".$password."\n\n
      Вам присвоен статус УЧАСТНИКА. Статус УЧАСТНИКА позволяет создавать проекты. В дальнейшем все созданные проекты, после прохождения проверки, будут отправлены на экспертную оценку.\n
      Ваша задача - зайти на сайт и создать проект (при желании, к проекту можно прикреплять файлы).\n
      Эксперты могут оставлять замечания и комментарии к Вашим проектам. Уведомления об этом Вы будете получать по электронной почте.\n
      \nВ дальнейшем, возможно получение статуса ЭКСПЕРТА.\n
      С уважением, Экспертная система оценки проектов.";
      $fromid = USER_ID;

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
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
       )) {
           puterror("Ошибка при отправке сообщения.");
          }   
      }  

      $toemail = $valmail;
      $title = "Зарегистрирован новый пользователь";
      $body = "Зарегистрирован новый пользователь - ФИО: ".$fio."\n
      логин - ".$login."\n
      пароль - ".$password."\n
      email - ".$email."\n";

      require_once('lib/unicode.inc');

      $mimeheaders = array();
      $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
      $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
      $mimeheaders[] = 'X-Mailer: '. mime_header_encode('ExpertSystem');
      $mimeheaders[] = 'From: '. mime_header_encode(admin.' <'.$valmail2.'>');

      if (!empty($toemail))
      {
       if (!mail(
        $toemail,
        mime_header_encode($title),
        str_replace("\r", '', $body),
        join("\n", $mimeheaders)
       )) {
           puterror("Ошибка при отправке сообщения.");
          }   
      }  

      // Возвращаемся на страницу логина если всё прошло удачно
      include "topadmin.php";
    	echo"
    	<p align=center>
      <div id='menu_glide' class='menu_glide'>
    	<table border='0' align='center' class='bodytable' border='0' cellpadding=2 cellspacing=2>
    	<tr><td>";
      echo"<font face='Tahoma, Arial' size='-1'>Регистрация участника прошла успешно. На Ваш электронный адрес <b>".$email."</b> выслано письмо с логином и паролем.</font></td></tr>";
      echo"<tr><td></td></tr><tr><td align='center'><form method='POST' action='news'>
      <input type='submit' name='close' value='Продолжить'></form></td></tr></table></div></p>"; 
      include "bottomadmin.php";
      exit();
      
    }
    else
    {
      // Выводим сообщение об ошибке в случае неудачи
      echo "<a href='reg'>Вернуться</a>";
      echo("<P> Ошибка при регистрации</P>");
      echo("<P> $query</P>");
      exit();
    }
    
  
}

if (empty($action)) 
{

  include "topadmin.php";
  
  $gst = mysql_query("SELECT * FROM users ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  while($member = mysql_fetch_array($gst))
  {
   $region = $region.$member['region'].",";
   $city = $city.$member['city'].",";
  }
  
?>

<script type="text/javascript">
 var RecaptchaOptions = {
                lang : 'ru',     theme : 'white'
 };
</script>


<script type="text/javascript">
$(document).ready(function() {
 
    $('#submit').click(function() { 
 
        $(".iferror").hide();
        var hasError = false;
 
        var regVal1 = $("#fio").val();
        if(regVal1 == '') {
            $("#fio").after('<span class="iferror"> Необходимо заполнить поле ФИО!</span>');
            hasError = true;
        }

        var regVal2 = $("#email").val();
        if(regVal2 == '') {
            $("#email").after('<span class="iferror"> Необходимо заполнить поле "адрес электронной почты"!</span>');
            hasError = true;
        }

        var regVal3 = $("#job").val();
        if(regVal3 == '') {
            $("#job").after('<span class="iferror"> Необходимо заполнить поле "место работы"!</span>');
            hasError = true;
        }

        if(!document.getElementById('personal').checked) {
            $("#personal").after('<span class="iferror"> Требуется согласие с условиями соглашения о персональных данных!</span>');
            hasError = true;
        }
 
        if(hasError == true) { return false; }
 
    });
});
</script>

	<form method="post" action="reg">
	<table width="100%" height="100%">
  <input type="hidden" name="action" value="post">
	<tr><td align=center>
<?

  echo"<div id='menu_glide' class='menu_glide'>";
	echo"<table border=0 class='bodytable' border=0 cellpadding=2 cellspacing=2>";
	
  echo"<tr><td><table>";
	
  echo"<tr><td><font face='Tahoma, Arial'><b>Фамилия Имя Отчество: *</b></font></td>";
  echo"<td><input type='text' id='fio' name='fio' size='35'></td></tr>";

//	echo"<tr><td><font face='Tahoma, Arial' size='-1'><b>Регион: *</b></font></td>";
//  echo"<td><div class='field required'><input type='text' name='region' size='35' id='region'><span class='iferror'>Поле требуется заполнить</span></div></td></tr>";

//	echo"<tr><td><font face='Tahoma, Arial' size='-1'><b>Город или населенный пункт: *</b></font></td>";
//  echo"<td><div class='field required'><input type='text' name='city' size='35' id='city'><span class='iferror'>Поле требуется заполнить</span></div></td></tr>";

//	echo"<tr><td><font face='Tahoma, Arial' size='-1'><b>Должность: *</b></font></td>";
//  echo"<td><div class='field required'><input type='text' name='person' size='35'><span class='iferror'>Поле требуется заполнить</span></div></td></tr>";

	echo"<tr><td><font face='Tahoma, Arial'><b>Адрес электронной почты: *</b></font></td>";
  echo"<td><input type='text' id='email' name='email' size='35'></td></tr>";

	echo"<tr><td><font face='Tahoma, Arial'><b>Место работы: *</b></font></td>";
  echo"<td><input type='text' id='job' name='job' size='35'></td></tr>";
  
  echo"<tr><td></td><td><label><input type='checkbox' id='personal' name='personal'>
  Я согласен(согласна) с условиями <a href='docs/personal.rtf' target='_blank'>соглашения о персональных данных.</a></label></td></tr>";
//echo"	<tr><td><font face='Tahoma, Arial' size='-1'>Телефон:</font></td>
//echo"  <td><input type='text' name='phone' size='35'></td></tr>

//echo"<tr><td><font face='Tahoma, Arial' size='-1'><b>Договор-офферта: *</b></   font></td><td><input class=input type=checkbox name=rules ><font face='Tahoma, Arial' size='-1'> С условиями <A href=docs/dogoferta.rtf target=_blank>договора-офферты</a> ознакомлен, обязуюсь выполнять.</font></td></tr>";
?>

	</table>
	</td></tr>
  <tr><td>
  <? echo recaptcha_get_html($publickey,$error); ?>
	</td></tr><tr><td align=center><input id="submit" type="submit" name="ok" value="Продолжить">&nbsp;<input type='button' name='close' value='Отмена' onclick='history.back()'></td></tr>
	</table></div>
	</td></tr>
	</table>
	</form>
<script type="text/javascript">

  //<![CDATA[
  var a1;
  var a2;

  jQuery(function() {
    var onAutocompleteSelect = function(value, data) {
      $('#selection').html('<img src="\/global\/flags\/small\/' + data + '.png" alt="" \/> ' + value);
      //alert(data);
    }

    var options = {
      serviceUrl: '/projects/autocomplete/service/autocomplete.ashx',
      width: 300,
      delimiter: /(,|;)\s*/,
      onSelect: onAutocompleteSelect,
      deferRequestBy: 0, //miliseconds
      params: { country: 'Yes' },
      noCache: false //set to true, to disable caching
    };

    a1 = $('#regions').autocomplete({
      width: 300,
      delimiter: /(,|;)\s*/,
      lookup: '<? echo $region; ?>'.split(',')
    });



    $('#navigation a').each(function() {
      $(this).click(function(e) {
        var element = $(this).attr('href');
        $('html').animate({ scrollTop: $(element).offset().top }, 300, null, function() { document.location = element; });
        e.preventDefault();
      });

    });
  });
//]]>
</script>
	
<?
  include "bottomadmin.php";
}
?>
