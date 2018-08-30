<?


if(!isset($_POST['ok'])) {
// если форма не заполнена, то выводим ее
  include "config.php";
  $token = $_GET['token'];
  if (empty($token))
  {
   $uid = USER_ID;
   $token = md5(time().$_POST['login']);
   setcookie('token', $token, time() + 60 * 60 * 24 * 14);
   mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE id='".$uid."'");
   mysqli_query($mysqli,"COMMIT");
  }
  mysql_close();
  mysqli_close($mysqli);
   
	echo"
	<html>
	<head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<title>Страница ввода пароля</title>
	</head>
	<body>
	<table width='100%' height='100%'>
	<form method='POST' action='changepass.php'>
  <input type=hidden name=token value='".$token."'>
	<tr><td align=center>
	<table border='0' bgcolor='#eeeeee' border='0' cellpadding=2 cellspacing=2>
	<tr><td>
	<table>
	<tr><td><font face='Tahoma, Arial'>Установите новый пароль:</font></td></tr>
	<tr><td><font face='Tahoma, Arial'>Новый пароль:</font></td><td><input type='password'
        name='pass1' size='15'></td></tr>
	<tr><td><font face='Tahoma, Arial'>Повторите ввод пароля:</font></td><td><input
        type='password' name='pass2' size='15'></td></tr>
	</table>
	</td></tr>
	<tr><td align=center><input type='submit' name='ok'
        value='Изменить пароль'></td></tr>
	</table>
	</td></tr>
	</form>
	</table>
	</body>
	</html>
	";
}
else
{	
	
  if($_POST['pass1']!=$_POST['pass2'])
  {	//пароль неверный
	echo"
	<html>
	<head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<title>Ошибка проверки пароля</title>
	</head>
	<body>
	<table width='100%' height='100%'>
	<tr><td align=center>
	<table border='0' bgcolor='#eeeeee' border='0' cellpadding=2 cellspacing=2>
	<tr><td>
	<table>
	<tr><td><font face='Tahoma, Arial'>Пароли не совпадают.</font></td></tr>
	</table>
	</td></tr>
	<tr><td align=center><input type='button' name='close' value='Назад' onclick='history.back()'></td></tr>
	</table>
	</td></tr>
	</table>
	</body>
	</html>";
	}
  else
  if(empty($_POST['pass1']) or empty($_POST['pass2']))
  {	//пароль пуст
	echo"
	<html>
	<head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<title>Ошибка проверки пароля</title>
	</head>
	<body>
	<table width='100%' height='100%'>
	<tr><td align=center>
	<table border='0' bgcolor='#eeeeee' border='0' cellpadding=2 cellspacing=2>
	<tr><td>
	<table>
	<tr><td><font face='Tahoma, Arial'>Установите значение нового пароля.</font></td></tr>
	</table>
	</td></tr>
	<tr><td align=center><input type='button' name='close' value='Назад' onclick='history.back()'></td></tr>
	</table>
	</td></tr>
	</table>
	</body>
	</html>";
	}
	else
  {	
   //пароль изменен
   include "config.php";
   $query = "UPDATE users SET password = '".md5($_POST["pass1"])."', passchanged = 1 WHERE token='".$_POST["token"]."'";
   if(mysqli_query($mysqli,$query))
   {
    mysqli_query($mysqli,"COMMIT");
		$_SESSION['pass'] = md5($_POST['pass1']);
   	mysql_close();
   	mysqli_close($mysqli);
    Header("Location: projects");	// перенаправляем на 
	 } else 
   {
   	mysql_close();
   	mysqli_close($mysqli);
   }
	}
}


?>