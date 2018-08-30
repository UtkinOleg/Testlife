<?
session_start(); //инициализирум механизм сесссий

include "config.php";

function generate_password($number)
 
   {
 
     $arr = array('a','b','c','d','e','f',
 
                  'g','h','i','j','k','l',
 
                  'm','n','o','p','r','s',
 
                  't','u','v','x','y','z',
 
                  'A','B','C','D','E','F',
 
                  'G','H','I','J','K','L',
 
                  'M','N','O','P','R','S',
 
                  'T','U','V','X','Y','Z',
 
                  '1','2','3','4','5','6',
 
                  '7','8','9','0');
 
     // Генерируем пароль
 
     $pass = "";
 
     for($i = 0; $i < $number; $i++)
 
     {
 
       // Вычисляем случайный индекс массива
 
       $index = rand(0, count($arr) - 1);
 
       $pass .= $arr[$index];
 
     }
 
     return $pass;
 
   }

require_once 'lib/LoginzaAPI.class.php';
require_once 'lib/LoginzaUserProfile.class.php';
$LoginzaAPI = new LoginzaAPI();

if (!empty($_POST['token'])) 
{
	// получаем профиль авторизованного пользователя
	$UserProfile = $LoginzaAPI->getAuthInfo($_POST['token']);
	
	// проверка на ошибки
	if (!empty($UserProfile->error_type)) {
		// есть ошибки, выводим их
		// в рабочем примере данные ошибки не следует выводить пользователю, так как они несут информационный характер только для разработчика
	  Header("Location: mp?err=login");
  } elseif (empty($UserProfile)) {
		// прочие ошибки
	  Header("Location: mp?err=login");
    
	} else {
  
     // объект генерации недостаюих полей (если требуется)
   	 $LoginzaProfile = new LoginzaUserProfile($UserProfile);
     $login = $LoginzaProfile->genNickname();
     $password = generate_password(7);
     $cpassword = md5($password);
     $fio = $LoginzaProfile->genFullName();
     $email1 = $UserProfile->email;
     $socialid = $UserProfile->identity;
     $socialpage = $LoginzaProfile->genUserSite();
     $provider = $UserProfile->provider;
     $dob = date('Y-m-d', strtotime($UserProfile->dob));
     $avatar = $UserProfile->photo;

     // Проверим на дублирование Email
     if (!empty($email1))
     {
      $em = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(email) FROM users WHERE social_id<>'".$socialid."' AND email='".strtolower(trim($email1))."' LIMIT 1;");
      $totalemail = mysqli_fetch_array($em);
      $countemail = $totalemail['count(email)'];
      mysqli_free_result($em);
     }
     else 
      $countemail = 0;

     if ($countemail==0)
     {
  	 
     $res2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE social_id='".$socialid."'");
  	 if(mysqli_num_rows($res2)==0)
     {	
     //такого пользователя нет - запишем нового
     mysqli_query($mysqli,"START TRANSACTION;");
     $query = "INSERT INTO users VALUES (0,
                                        '$login',
                                        '$cpassword',
                                        '$fio',
                                        'user',
                                        NOW(),
                                        '$email1',
                                        0,
                                        '',
                                        NOW(),
                                        '',
                                        0,
                                        '',
                                        '$avatar',
                                        '',
                                        '',
                                        '',
                                        'offline',
                                        0,
                                        '',
                                        '',
                                        0,0,0,0,0,0,0,
                                        '$socialid','$socialpage','male','{$dob}','$provider',0);";
      if(mysqli_query($mysqli, $query))
      {

       $userid = mysqli_insert_id($mysqli);
       $query = "INSERT INTO money VALUES (0,
       0,
       $userid,
       50,NOW());";
       mysqli_query($mysqli,$query);
       mysqli_query($mysqli,"COMMIT;");
       
       $toemail = $valmail2;
       $title = "Зарегистрирован новый участник";
       $body = "Зарегистрирован новый участник - ФИО: ".$fio."\n
       логин - ".$login."\n
       пароль - ".$password."\n
       id соц сети - ".$socialid."\n
       страница - ".$socialpage."\n
       email - ".$email1."\n";

       require_once('lib/unicode.inc');

       $mimeheaders = array();
       $mimeheaders[] = 'Content-type: '. mime_header_encode('text/plain; charset=UTF-8; format=flowed; delsp=yes');
       $mimeheaders[] = 'Content-Transfer-Encoding: '. mime_header_encode('8Bit');
       $mimeheaders[] = 'X-Mailer: '. mime_header_encode('TestLife');
       $mimeheaders[] = 'From: '. mime_header_encode('TestLife <'.$valmail.'>');
       mail($toemail,
       mime_header_encode($title),
       str_replace("\r", '', $body),
       join("\n", $mimeheaders));  

       $_SESSION['login'] = $login;	//устанавливаем login & pass
 	     $_SESSION['pass'] = $cpassword;
       $token = $_POST['token'];
       setcookie('token', $token, time() + 60 * 60 * 24 * 14);
       mysqli_query($mysqli,"START TRANSACTION;");
       mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$login."'AND password='".$cpassword."'");
       mysqli_query($mysqli,"COMMIT;");

    	 Header("Location: qt");
      } 
      else
      {
       mysqli_query($mysqli,"COMMIT");
    	 Header("Location: mp?err=dbase");	
      }
    }
    else
    {
      $res22 = mysqli_fetch_array($res2);
    	$_SESSION['login'] = $res22['username'];	//устанавливаем login & pass
 	    $_SESSION['pass'] = $res22['password'];
      $token = $_POST['token'];
      setcookie('token', $token, time() + 60 * 60 * 24 * 14);
      mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$res22['username']."'AND password='".$res22['password']."'");
      Header("Location: qt");	
    }
    
    }
    else// дублирование email
   	 Header("Location: mp?err=address");	
          
	}       
 }  
 else Header("Location: mp?err=token");


?>