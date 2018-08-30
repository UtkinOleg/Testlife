<?
if(defined("USER_REGISTERED")) {  
include "config.php";

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_unset();
session_destroy();	//удаляем текущую сессию
$_SESSION = array();
setcookie('token', '');
Header("Location: ".$site);	
} else die;
?>
   
