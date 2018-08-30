<?php
ini_set('display_errors', 0);
error_reporting(); // E_ALL

  //начинаем проверку логина и пароля
  $dblocation1 = "localhost";
  $dbname1 = "testlife";
  $dbuser1 = "root";
  $dbpasswd1 = "";
  $mysqlic = mysqli_connect($dblocation1,$dbuser1,$dbpasswd1,$dbname1);
  if (!$mysqlic) {
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head><body>
<P>В настоящий момент сервер базы данных не доступен.</P></body></html>
  <?
    exit();
  }

 $usernames = filter_var($_SESSION['login'], FILTER_SANITIZE_STRING);
 $passwords = filter_var($_SESSION['pass'], FILTER_SANITIZE_STRING);
 
 $usernames = mysqli_real_escape_string($mysqlic,$usernames);
 $passwords = mysqli_real_escape_string($mysqlic,$passwords);

 if (isset($_COOKIE['token']))
 {
  $token = htmlspecialchars($_COOKIE['token']); // на всякий сл.
  $res = mysqli_query($mysqlic,sprintf("SELECT * FROM users WHERE token='%s' LIMIT 1;",$token));
 }
 else
  $res = mysqli_query($mysqlic,sprintf("SELECT * FROM users WHERE username='%s' AND password='%s' LIMIT 1;",$usernames,$passwords));
 $r = mysqli_fetch_array($res);

// $pass1 = $_GET['restoreme'];

if (empty($r['id']))
{	
 //такого пользователя нет
 setcookie('token', '');

 $op = $_GET['op']; 
 mysqli_close($mysqlic);

 // Выбираем нужное нам действие 
 switch ($op) 
       { 
        // Главная страница
        case 'mp' :  {include "lms/bootstrapmainpage.php"; break;}  
        case 'tladmin' : {include "bootstrap.php"; break;} 
        case 'h' : {include "lms/fronthelp.php"; break;}     // Страница помощи
        
        // Проверочные тесты на главной странице
        //case 'getactivetmptests.json' : {include "lms/getactivetmptestsjson.php"; break;} 
        case 'viewtest' : {include "lms/viewtest.php"; break;}   // Страндартный тест
        case 'viewadaptivetest' : {include "lms/viewadaptivetest.php"; break;} // Адаптивный тест
        case 'viewpsytest' : {include "lms/viewpsytest.php"; break;}   // Страндартный тест
        case 'psytestresults': {include "lms/psyresults.php"; break;}    // Результат психологического теста
        case 'getq.json' : {include "lms/getqjson.php"; break;}
        case 'getadaptq.json' : {include "lms/getadaptqjson.php"; break;} 
        case 'getrightq.json' : {include "lms/getrightqjson.php"; break;}
        case 'getuptest.json' : {include "lms/getuptestjson.php"; break;}
        
        // Система 
        //case 'loginuser' : {include "lms/login.php"; break;} 
        case 'hlogin' : {include "lms/loginuser.php"; break;} 
        //case 'loginsupervisor' : {include "lms/loginsupervisor.php"; break;} 
        case 'file' : {include "lms/getfile.php"; break;} 
        case 'thumb' : {include "lms/file_thumb_picupload.php"; break;} 
        case 'msgajax' : {include "lms/msgajax.php"; break;} 
        
        default :  { if (empty($op)) include "lms/bootstrapmainpage.php"; else include "lms/bootstrapnotfound.php"; } // default page 
       }
}
else
{	
  session_start(); //инициализирум механизм сесссий
  $_SESSION['login']=$r['login'];	//устанавливаем login & pass
  $_SESSION['pass']=$r['pass'];
  
  //пользователь найден, можем выводить все что нам надо
  if ($r['usertype'] == 'admin') {
   define("IN_ADMIN", TRUE); 
   define("IN_SUPERVISOR", TRUE); 
   define("IN_EXPERT", TRUE); 
   define("IN_USER", TRUE); 
   define("USER_STATUS", "администратор"); 
   define("LOWSUPERVISOR", FALSE);
  }
  else
  if ($r['usertype'] == 'supervisor') {
   define("IN_SUPERVISOR", TRUE); 
   define("IN_EXPERT", TRUE); 
   define("IN_USER", TRUE); 
   if ($r['qcount'] > 0) 
   {
    define("LOWSUPERVISOR", TRUE);
    define("USER_STATUS", "супервизор"); 
   } 
   else
   {
    define("USER_STATUS", "супервизор"); 
    define("LOWSUPERVISOR", FALSE);
   } 
  }
  else
  if ($r['usertype'] == 'expert') { 
   define("IN_EXPERT", TRUE); 
   define("IN_USER", TRUE); 
   define("USER_STATUS", "эксперт"); 
  } 
  else
  if ($r['usertype'] == 'user') {
   define("IN_USER", TRUE); 
   define("USER_STATUS", "участник"); 
  }

  if ($r['passchanged'] == 0) define("PASS_NOT_CHANGED", TRUE);

  define("USER_FIO", $r['userfio']); 
  define("USER_ID", $r['id']); 
  define("USER_EMAIL", $r['email']); 

  define("USER_EXTMODE", $r['extmode']); 

  $img = '';
  if (!empty($r['photoname'])) 
  {
          if (stristr($r['photoname'],'http') === FALSE)
           $img = "<img class='img-circle' src='thumb' height='18'>"; 
          else
           $img = "<img class='img-circle' src='".$r['photoname']."' height='18'>"; 
  }  
  define("USER_PICT", $img); 

  if(defined("IN_USER") or defined("IN_ADMIN") or defined("IN_EXPERT") or defined("IN_SUPERVISOR")) { 
   define("USER_REGISTERED", TRUE); 
  } 

 //define("USER_PICT", $r['photoname']); 
 define("HELP_ENABLED", FALSE); 

 // Проверка пользователя на эксперта КИМ
 $sql = mysqli_query($mysqlic,"/*qc=on*/" . "SELECT count(*) FROM usergroups as ug, useremails as ue WHERE ug.usergrouptype=1 AND ue.usergroupid=ug.id AND ue.email='".USER_EMAIL."' LIMIT 1;");
 $total = mysqli_fetch_array($sql);
 define("USER_EXPERT_KIM", $total['count(*)']); 
 mysqli_free_result($sql);
 if (USER_EXPERT_KIM>0)
 {
  define("IN_SUPERVISOR", TRUE); 
 }
 
 if(defined("IN_SUPERVISOR"))
 { 
   $sql = mysqli_query($mysqlic,"/*qc=on*/SELECT SUM(summa) FROM money WHERE userid='".USER_ID."'");
   $sum = mysqli_fetch_array($sql, MYSQLI_NUM);
   $sum0 = $sum[0]/10;
   mysqli_free_result($sql);
   $q1 = "/*qc=on*/SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid='".USER_ID."' LIMIT 1;"; 
   $sql = mysqli_query($mysqlic,$q1);
   $total = mysqli_fetch_array($sql);
   $counter = $total['count(*)'];
   mysqli_free_result($sql);
   $sum0 -= $counter;
   if ($sum0<0) $sum0=0;
   define("SUPERVISOR_REST", $sum0); 
 }
 
 mysqli_close($mysqlic);

 // Получаем параметр op из URL 
 $op = $_GET['op']; 

// str_replace('/', '', $_SERVER['REQUEST_URI'], $count);
// echo $op."      ".$_SERVER['REQUEST_URI']."      ".$count;


 // Выбираем нужное нам действие 
 switch ($op) 
 { 
        case 'logout' : {include "lms/logout.php"; break;} 

        // Новый интерфейс

        // Главные страницы
        case 'ts': {include "lms/bootstraptests.php"; break;}  // Просмотр тестов
        case 'vr': {include "lms/bootstrapviewresults.php"; break;}  // Просмотр результатов
        case 'um' : {include "lms/usermessages.php"; break;}     // Сообщения
        case 'profile' : {include "lms/profile.php"; break;}     // Профиль пользователя

        // Страницы учителя
        case 'qt' : {include "lms/questionstests.php"; break;} // Создатель тестов
        case 'ug' : {include "lms/usergroups.php"; break;}     // Группы участников
        case 'h' : {include "lms/userhelp.php"; break;}     // Страница помощи
        case 'ed' : {include "lms/editquestions.php"; break;}     // Просмотр вопросов
        case 'psi' : {include "lms/psitest.php"; break;}  // Список психологических интерпретаций
        case 'res' : {include "lms/rtfresult.php"; break;}  // Выгрузка результатов в RTF
        
        // Страницы эксперта КИМ
        case 'ex' : {include "lms/expertquestions.php"; break;} // Экспретиза КИМ
        case 'edex' : {include "lms/editexpertquestions.php"; break;}     // Экспертиза вопросов
        
        // Страницы администратора
        case 'help' : {include "lms/bootstraphelp.php"; break;}     // Страницы помощи
        case 'supervisors' : {include "lms/bootstrapsupervisor.php"; break;}     // Супервизоры
        case 'scales': {include "lms/bootstrapscales.php"; break;}  // Шкалы оценок
        case 'reports': {include "lms/bootstrapreports.php"; break;}  // Комплексные отчеты

        // Диалоги FancyBox
        
        case 'edscale' : {include "lms/edscale.php"; break;} 
        case 'moveusergroup' : {include "lms/moveusergroup.php"; break;} 
        case 'eduserfolder' : {include "lms/eduserfolder.php"; break;} 
        case 'edhelppage' : {include "lms/edhelppage.php"; break;} 
        case 'viewtest' : {include "lms/viewtest.php"; break;}   // Страндартный тест
        case 'viewpsytest' : {include "lms/viewpsytest.php"; break;}   // Психологический тест
        case 'viewadaptivetest' : {include "lms/viewadaptivetest.php"; break;} // Адаптивный тест
        case 'testresults': {include "lms/results.php"; break;}    // Результат теста
        case 'psytestresults': {include "lms/psyresults.php"; break;}    // Результат психологического теста
        case 'resultprotocol' : {include "lms/testprotocol.php"; break;} // Протокол
        case 'createtest' : {include "lms/createtest.php"; break;}  // Создание и редактор теста
        case 'addquestmanual' : {include "lms/addquestmanual.php"; break;}  // Редакторо вопросов
        case 'eknows' : {include "lms/editknows.php"; break;}               // Редактор области
        case 'edusergroup' : {include "lms/editusergroup.php"; break;}    // Редактор группы пользователей
        case 'editugintest' : {include "lms/editugintest.php"; break;}    // Прикрепление группы пользователей к тесту
        case 'addquestgroup' : {include "lms/addquestgroup.php"; break;}  // Добавить группу вопросов
        case 'addquestfromfile' : {include "lms/addquestfromfile.php"; break;}  // Добавить вопросы из файла
        case 'editpsitest' : {include "lms/editpsitest.php"; break;}  // Изменить психологический тест
        case 'addmoney' : {include "lms/editmoney.php"; break;}  // Добавить сеансы тестирования

        // JSON 
        
        
        case 'getscales.json' : {include "lms/getscalesjson.php"; break;} 
        case 'delscale.json' : {include "lms/delscalejson.php"; break;}
        case 'expertcomment.json' : {include "lms/expertcommentjson.php"; break;} 
        case 'getcheckquest.json' : {include "lms/getcheckquestjson.php"; break;} 
        case 'getexpertquests.json' : {include "lms/getexpertquestsjson.php"; break;} 
        case 'getexpertquest.json' : {include "lms/getexpertquestjson.php"; break;} 
        case 'expertqgroup.json' : {include "lms/expertqgroupjson.php"; break;} 
        case 'deluserfolder.json' : {include "lms/deluserfolderjson.php"; break;} 
        case 'delsupervosor.json' : {include "lms/delsupervosorjson.php"; break;} 
        case 'getsupervisors.json' : {include "lms/getsupervisorjson.php"; break;} 
        case 'delpsy.json' : {include "lms/delpsyjson.php"; break;} 
        case 'getpsy.json' : {include "lms/getpsyjson.php"; break;} 
        case 'gettesttasks.json' : {include "lms/gettesttasksjson.php"; break;} 
        case 'getquests.json' : {include "lms/getquestsjson.php"; break;} 
        case 'getumsg.json' : {include "lms/getumsgjson.php"; break;} 
        case 'getallmsgs.json' : {include "lms/getallmsgsjson.php"; break;} 
        case 'getusermsgs.json' : {include "lms/getusermsgsjson.php"; break;} 
        case 'diag1.json' : {include "lms/diag1json.php"; break;} 
        case 'diag2.json' : {include "lms/diag2json.php"; break;} 
        case 'diag3.json' : {include "lms/diag3json.php"; break;} 
        case 'diag4.json' : {include "lms/diag4json.php"; break;} 
        case 'diag5.json' : {include "lms/diag5json.php"; break;} 
        case 'getadaptq.json' : {include "lms/getadaptqjson.php"; break;} 
        case 'getactivetests.json' : {include "lms/getactivetestsjson.php"; break;} 
        case 'getquest.json' : {include "lms/getquestjson.php"; break;} 
        case 'getusergroups.json' : {include "lms/getusergroupsjson.php"; break;} 
        case 'delquests.json' : {include "lms/delquestions.php"; break;} 
        case 'delquest.json' : {include "lms/delq.php"; break;} 
        case 'delgroup.json' : {include "lms/delquestgroup.php"; break;} 
        case 'picupload.json' : {include "lms/picupload.php"; break;} 
        case 'deltest.json' : {include "lms/deltest.php"; break;}    
        case 'delusergroup.json' : {include "lms/delusergroup.php"; break;}    
        case 'getq.json' : {include "lms/getqjson.php"; break;} 
        case 'delknow.json' : {include "lms/delknow.php"; break;}  
        case 'getresult.json' : {include "lms/getresultjson.php"; break;} 
        case 'delresult.json' : {include "lms/delresultjson.php"; break;} 
        case 'delhelppage.json' : {include "lms/delhelppage.php"; break;} 
        case 'gethelppages.json' : {include "lms/gethelppagesjson.php"; break;} 
        case 'getanswer.json' : {include "lms/getansjson.php"; break;}
        case 'getrightq.json' : {include "lms/getrightqjson.php"; break;}

        // Система
        case 'msgajax' : {include "lms/msgajax.php"; break;} 
        case 'file' : {include "lms/getfile.php"; break;} 
        case 'thumb' : {include "lms/file_thumb_picupload.php"; break;} 
        case 'success' : {include "lms/success.php"; break;} 
        case 'fail' : {include "lms/fail.php"; break;}

        default :  { if (empty($op)) include "lms/bootstraptests.php"; else die; } 
   }
}
mysql_close();
mysqli_close($mysqli);
?>