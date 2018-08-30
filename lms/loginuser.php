<?
session_start(); //инициализирум механизм сесссий

include "config.php";

$provider_name = $_REQUEST["provider"];
$user_type = $_REQUEST["u"];

if (empty($user_type))
{
 $user_type = 'user';
} 

if ($user_type==='s')
{
 $user_type = 'supervisor';
}

if ($user_type==='u')
{
 $user_type = 'user';
}

if (!empty($provider_name)) 
{

    try
	  {
     $dir = dirname(__FILE__);
     $config = $dir . '/lib/hybridauth/hybridauth/config.php';
		 require_once ( $dir . "/lib/hybridauth/hybridauth/Hybrid/Auth.php" );
 
		 // initialize Hybrid_Auth class with the config file
		 $hybridauth = new Hybrid_Auth( $config );
 
		 // try to authenticate with the selected provider
		 $adapter = $hybridauth->authenticate( $provider_name );
 
		 // then grab the user profile 
		 $user_profile = $adapter->getUserProfile();
    }
    catch( Exception $e )
	  {
		 header("Location: mp?err=login&msg=".$e->getMessage());
     exit;
	  }

     $socialid = $provider_name . $user_profile->identifier;
     $email1 = $user_profile->email;
     $login = $user_profile->displayName;
     
     if ($user_profile->identifier === NULL)    
     {
 		  header("Location: mp?err=login2");
      exit;
     }
     
     if (strlen(trim($user_profile->identifier))==0 or strlen(trim($login))==0)
     {
 		  header("Location: mp?err=login3");
      exit;
     }
     
     $cpassword = md5( str_shuffle( "0123456789abcdefghijklmnoABCDEFGHIJ" ) );
     $fio = $user_profile->displayName;
     $avatar = $user_profile->photoURL;
     $socialpage = $user_profile->webSiteURL;
     
     // Проверим на дублирование Email
     $countemail = 0;
     if (strlen(trim($email1))>0)
     {
      $em = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(email) FROM users WHERE social_id<>'".$socialid."' AND email='".strtolower(trim($email1))."' LIMIT 1;");
      $totalemail = mysqli_fetch_array($em);
      $countemail = $totalemail['count(email)'];
      mysqli_free_result($em);
     }

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
                                        '$user_type',
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
                                        '$socialid','$socialpage','male','','$provider_name',0,0,0,0);";
      if(mysqli_query($mysqli, $query))
      {
       if ($user_type==='supervisor')
       {
        $userid = mysqli_insert_id($mysqli);
        $query = "INSERT INTO money VALUES (0,0,$userid,50,NOW());";
        mysqli_query($mysqli,$query);
       }
       
       mysqli_query($mysqli,"COMMIT;");
       
       $toemail = $valmail2;
       $title = "Зарегистрирован $user_type";
       $body = "Зарегистрирован $user_type - ФИО: ".$fio."\n
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
       
       $token = md5(time().$socialid);
       
       setcookie('token', $token, time() + 60 * 60 * 24 * 14);
       mysqli_query($mysqli,"START TRANSACTION;");
       mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$login."'AND password='".$cpassword."'");
       mysqli_query($mysqli,"COMMIT;");

    	 Header("Location: ts");
       
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

      $token = md5(time().$socialid);

      setcookie('token', $token, time() + 60 * 60 * 24 * 14);
      mysqli_query($mysqli,"UPDATE users SET token='$token' WHERE username='".$res22['username']."'AND password='".$res22['password']."'");
      Header("Location: ts");	
    }

    }
    else // дублирование email
   	{
      Header("Location: mp?err=address");	
    }      
}  
else 
{
 Header("Location: mp?err=token");
}

?>