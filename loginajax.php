<?
  session_start(); //инициализирум механизм сесссий

  include "lms/config.php";
  //include "lms/func.php";
  
  $login = $_POST['login'];
  $pass = $_POST['password'];
  $json['ok'] = '0'; 
  $json['login'] = $login; 
  $json['password'] = $pass; 

	//проверяем есть ли пользователь с таким login'ом и password'ом
	$res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE username='".$login."' AND password='".md5($pass)."' LIMIT 1;");
  $e = strtolower(trim($login));
	$res2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE email='".$e."' AND password='".md5($pass)."' LIMIT 1;");

	if(mysqli_num_rows($res)!=0) {	// есть login
  $res11 = mysqli_fetch_array($res);

  $token = md5(time().$login);
//  if ($_POST['saveme']) {
    setcookie('token', $token, time() + 60 * 60 * 24 * 14);
    mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$login."'AND password='".md5($pass)."'");
//  }
//    writelog("Вход в систему ".$login);
		$_SESSION['login'] = $login;	//устанавливаем login & pass
		$_SESSION['pass'] = md5($pass);

    // Проверка пользователя на участие в проектах и экспертизах
    //check_user_in_system($mysqli, $res11['email']);

    $json['ok'] = '1';

	} 
  else
	if(mysqli_num_rows($res2)!=0) 
  {	//есть email
  $res22 = mysqli_fetch_array($res2);
  
  $token = md5(time().$res22['username']);
//  if ($_POST['saveme']) {
    setcookie('token', $token, time() + 60 * 60 * 24 * 14);
    mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$res22['username']."'AND password='".md5($pass)."'");
//  }
//    writelog("Вход в систему ".$res22['username']);
		$_SESSION['login'] = $res22['username'];	//устанавливаем login & pass
		$_SESSION['pass'] = md5($pass);

    // Проверка пользователя на участие в проектах и экспертизах
   // check_user_in_system($mysqli, $res22['email']);

		$json['ok'] = '1';
  } 
     
 echo json_encode($json);  
?>