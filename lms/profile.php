<?php
if(defined("USER_REGISTERED")) {  

require_once "config.php";  


function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
function str2url($str) {
    $str = rus2translit($str);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = trim($str, "-");
    return $str;
}


$action = $_POST["action"];

if (!empty($action)) 
{
  $error = "";
  // Проверим есть ли такоq email и логин
  $email = $_POST["email"];
  $login = $_POST["login"]; 
  
  if (!empty($email))
  {
   $em = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(email) FROM users WHERE id<>".USER_ID." AND email='".strtolower(trim($email))."'");
   $totalemail = mysqli_fetch_array($em);
   $countemail = $totalemail['count(email)'];
   mysqli_free_result($em);
  }
  else 
   $countemail = 0;
  
  if (!empty($login))
  {
   $lo = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(username) FROM users WHERE id<>".USER_ID." AND username='".strtolower(trim($login))."'");
   $totallogin = mysqli_fetch_array($lo);
   $countlogin = $totallogin['count(username)'];
   mysqli_free_result($lo);
  }
  else 
   $countlogin = 0;

  if ($countemail>0)
   $error .= '<div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Такой электронный адрес уже существует.
          </div>';

  if ($countlogin>0)
   $error .= '<div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Такой логин уже существует.
          </div>';

  if ($_FILES["photo"]["name"]!="") { 
          $origfilename = $_FILES["photo"]["name"]; 
          $filename = explode(".", $_FILES["photo"]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          for($x=0;$x<count($photo_file_types_array);$x++){ 
            if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
            } 
          } 

          if ($file_ext_allow) { 
            if($_FILES["photo"]["size"]>104800) 
               $error .= '<div class="alert alert-danger alert-dismissable">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            		'.$origfilename.' превышает размер 100 кбайт.</div>';
             
          } else { 
               $error .= '<div class="alert alert-danger alert-dismissable">
               <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            		Расширение файла '.$origfilename.' не поддерживается.</div>';
          } 
    } 

    if ($error=="")
    {

     mysqli_query($mysqli,"START TRANSACTION;");

     $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT photoname FROM users WHERE id='".USER_ID."' LIMIT 1;");
     $param = mysqli_fetch_array($res3);

     if($_FILES["photo"]["name"]!="") { 
      $filedata = $_FILES["photo"]["name"]; 
      $filename = explode(".", $_FILES["photo"]["name"]); 
      $filenameext = $filename[count($filename)-1]; 
      $realfiledata = str2url($filename[0]).".".$filenameext;
      $filesize = $_FILES["photo"]["size"]; 

      // Удалим файл - есди пользователь заменил его
      if (!empty($param['photoname'])) 
       unlink("picavatars/".USER_ID.$param['photoname']);
     
      if (!empty($_FILES["photo"]["name"])) { 
       $query2 = "UPDATE users SET photoname = '".$realfiledata."' WHERE id='".USER_ID."'";
       mysqli_query($mysqli,$query2);
      } 
     
     }
     mysqli_free_result($res3);
     

     if (!empty($_POST["pass1"])) 
     {
      $_SESSION['pass'] = md5($_POST['pass1']);
      $query = "UPDATE users SET username = '".$_POST["login"]."'
            , userfio = '".$_POST["name"]."' 
            , email = '".$_POST["email"]."' 
            , password = '".md5($_POST["pass1"])."' 
            , info = '".$_POST["info"]."' 
           WHERE id=".USER_ID;
     }
     else
      $query = "UPDATE users SET username = '".$_POST["login"]."'
            , userfio = '".$_POST["name"]."' 
            , email = '".$_POST["email"]."' 
            , info = '".$_POST["info"]."' 
           WHERE id=".USER_ID;
     if (mysqli_query($mysqli,$query))
      {
        if($_FILES["photo"]["name"]!="")
        {
         // Запишем файл
         mkdir("picavatars/".USER_ID, 0700); 
         move_uploaded_file($_FILES["photo"]["tmp_name"], "picavatars/".USER_ID."/".$realfiledata); 
         // Создадим Thumb 24
         $pathname = "picavatars/".USER_ID."/".$realfiledata;
         $handle = fopen("picavatars/".USER_ID."/thumb_".$realfiledata, "w");
         $height = 24;
         list($w, $h) = getimagesize($pathname);
         $ratio = $height/$h;
         $width = $w * $ratio;
         $h = ceil($height / $ratio);
         $x = ($w - $width / $ratio) / 2;
         $w = ceil($width / $ratio);
                
         $imgType = strtolower(substr($pathname, strrpos($pathname, '.')+1));
    
                if(($imgType == 'jpg') or ($imgType == 'jpeg'))
                {
                    $imgString = file_get_contents($pathname);
                    $image = imagecreatefromstring($imgString);
                    $tmp = imagecreatetruecolor($width, $height);
                    imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);
                    imagejpeg($tmp,$handle);
                    imagedestroy($image);
                    imagedestroy($tmp);
                    fclose($handle);
                }
                else if($imgType == 'png')
                {
                    $image = imagecreatefrompng($pathname);
                    $tmp = imagecreatetruecolor($width,$height);
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                    imagecopyresampled($tmp, $image,0,0,$x,0,$width,$height,$w, $h);
                    imagepng($tmp,$handle);
                    imagedestroy($image);
                    imagedestroy($tmp);
                    fclose($handle);
                }
                else if($imgType == 'gif')
                {
                    $image = imagecreatefromgif($pathname);
                    $tmp = imagecreatetruecolor($width,$height);
                    $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                    imagefill($tmp, 0, 0, $transparent);
                    imagealphablending($tmp, true); 
                    imagecopyresampled($tmp, $image,0,0,0,0,$width,$height,$w, $h);
                    imagegif($tmp,$handle);
                    imagedestroy($image);
                    imagedestroy($tmp);
                    fclose($handle);
                }
         
        }
        
         $error = '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		Ваш профиль обновлен. 
          </div>';
      }
     mysqli_query($mysqli,"COMMIT;");
   
    }
}
?>

<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание тестов онлайн">
    <link rel="icon" href="ico/favicon.ico">
    <title>Test Life</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  </head>
<body>
      
<div id="spinner"></div>

<div id="wrapper">
<?php
  include "allnavigation.php";
  include "reminder.php";

  function GetFolderName($mysqli,$folderid,$name)
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE id='".$folderid."' LIMIT 1;");
   $folder = mysqli_fetch_array($sql);
   $user_grp_parentid = $folder['parentid'];
   $folder_name = $folder['name'];
   $s = $name;
   if ($user_grp_parentid > 0)
   {
    $s = GetFolderName($mysqli, $user_grp_parentid, $s);
   }
   mysqli_free_result($sql); 
   if ($user_grp_parentid==0)
   {
    return $folder_name;
   }
   else
   {
    return $s.' / '.$folder_name;
   }
  }
  
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".USER_ID."' LIMIT 1;");
  $member = mysqli_fetch_array($sql);

  if (!empty($error))
   echo $error;


/*    function parse_string($string) {
        $params = array('inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng');
        $data = $out = array();
        foreach ($params as $param) {
            if (preg_match('#<' . $param . '>(.*)</' . $param . '>#is', $string, $out)) {
                $data[$param] = trim($out[1]);
            }
        }
        return $data;
    }

        $ch = curl_init('http://ipgeobase.ru:7020/geo?ip=' . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $string = curl_exec($ch);
        $data = parse_string($string);
        echo '<pre>';
print_r($data);
echo '</pre>'; */

//$xml_file = 'http://ipgeobase.ru:7020/geo?ip='.$_SERVER['REMOTE_ADDR'];
//$xml_object = new DOMDocument();
//$xml_object->load($xml_file);
//$elem_city = $xml_object->getElementsByTagName('city');
//$result_city = $elem_city->item(0)->nodeValue;

?>
  <p></p>
  <div id="msgs"></div>
            <div class="row">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                            Профиль 
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
<form role="form" action="profile" id="save" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="post">

                                        <div class="form-group">
                                            <label>Имя:</label>
                                            <input id="name" name="name" class="form-control" value="<?=$member['userfio'] ?>">
                                        </div>                        
                                        <div class="form-group">
                                            <label>Логин:</label>
                                            <input id="login" name="login" class="form-control" value="<?=$member['username'] ?>">
                                        </div>                        
                                        <div class="form-group">
                                            <label>Электронная почта:</label>
                                            <input id="email" name="email" class="form-control" value="<?=$member['email']?>">
                                            <? if (empty($member['email'])) echo '<p class="form-control-static">email@example.com</p>'; ?>
                                        </div>                        
                                        <div class="form-group">
                                            <label>Новый пароль:</label>
                                            <input id="pass1" name="pass1" type="password" class="form-control">
                                        </div>                        
                                        <div class="form-group">
                                            <label>Повторите новый пароль:</label>
                                            <input id="pass2" name="pass2" type="password" class="form-control">
                                        </div>                        
<?
        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='form-group'><img class='img-circle' src='thumb&h=70' height='70'></div>"; 
          else
           echo "<div class='form-group'><img class='img-circle' src='".$member['photoname']."' height='70'></div>"; 
        }  

?>
                                        <div class="form-group">
                                            <label>Фотография:</label>
                                            <input type="file" name="photo">
                                            <p class="form-control-static">Размер фотографии не должен превышать 100кб.</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Информация о себе:</label>
                                            <textarea name="info" class="form-control" rows="3"><?=$member['info']?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Группа:</label>
<?
       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM useremails WHERE email='".USER_EMAIL."'");
       while ($cntgrps = mysqli_fetch_array($countu))
       {
        $user_grp = $cntgrps['usergroupid'];
        echo "<p>";
        $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE id='".$user_grp."' LIMIT 1;");
        $grps = mysqli_fetch_array($sql);
        $user_grp_name = $grps['name'];
        $user_grp_folderid = $grps['folderid'];
        if ($user_grp_folderid > 0)
        {
         $user_grp_name = GetFolderName($mysqli, $user_grp_folderid, "");
         echo $user_grp_name." / ".$grps['name']."</p>";
        }
        else
        {
         echo $grps['name']."</p>";
        }
        mysqli_free_result($sql); 
       }
       mysqli_free_result($countu); 
?>                                            
                                        </div>
</form>                        
                           <p>
                            <button type="button" class="btn btn-outline btn-primary btn-sm" onclick="$('#save').submit();"><i class="fa fa-user fa-fw"></i> Изменить профиль</button>
                           </p>
                        </div>
                   </div>              
                </div>
            </div>
      </div>
    </div>
    <!-- /#page-wrapper -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
    <script src="js/sbadmin.js"></script>
    <script src="lms/scripts/myhelp.js?v=<?=$version?>"></script>
    <script>

     function checkRegexp(n, t) {
        return t.test(n.val())
     }

     function closeFancybox(){
       $.fancybox.close();
     }    
    
     $(document).ready(function(){
	  	$('.fancybox').fancybox();
      $('#save').submit(function()
      {
    
     var hasError = false;
     var s = '<div class="alert alert-danger alert-dismissable">'+
             '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
     if($("#name").val()=='') {
            $("#msgs").empty();
            $("#msgs").append(s+'Введите имя.</div>');
            $("#name").focus();
            hasError = true;
     } 
     else 
     if($("#login").val()=='') {
            $("#msgs").empty();
            $("#msgs").append(s+'Введите логин.</div>');
            $("#login").focus();
            hasError = true;
     } 
     else
     if($("#email").val()=='') {
            $("#msgs").empty();
            $("#msgs").append(s+'Введите адрес электронной почты.</div>');
            $("#email").focus();
            hasError = true;
     }
     else  
     if (checkRegexp($("#email"), /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i)==0)
     {
            $("#msgs").empty();
            $("#msgs").append(s+'Адрес электронной почты указан неправильно.</div>');
            $("#email").focus();
            hasError = true;
     }
     else
     if(($("#pass1").val()!='' || $("#pass2").val()!='') && ($("#pass1").val()!=$("#pass2").val())) {
            $("#msgs").empty();
            $("#msgs").append(s+'Символы пароля введены неправильно.</div>');
            if ($("#pass1").val()=='') 
             $("#pass1").focus();
            else
            if ($("#pass2").val()=='') 
             $("#pass2").focus();
            else 
             $("#pass1").focus();
            hasError = true;
     }
     if (!hasError)
        $("#spinner").fadeIn("slow"); 
     return !hasError; 
    });   
  });

      function getusermsgs() 
      {
       $.post('getusermsgs.json',{},  
        function(data){  
         eval('var obj='+data);         
         $('#usermsgsoper').prop("onclick",null);        
         $('#usermsgs').empty();  
         if(obj.ok=='1')
          $('#usermsgs').append(obj.content);        
         else 
          $('#usermsgs').append('Ошибка при загрузке сообщений.');        
        }); 
      }

<?  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {?>
  
      function closeFancyboxAndRedirectToUrl(kid, mode){
       $.fancybox.close();
       getquest(kid, mode);
      }    

  function dialogOpen(phref, pwidth, pheight) {
				if (pwidth==0)
         pwidth = document.documentElement.clientWidth;
				if (pheight==0)
         pheight = document.documentElement.clientHeight;
        $.fancybox.open({
					href : phref,
					type : 'iframe',
          width : pwidth,
          height : pheight,
          fitToView : true,
          autoSize : false,          
          modal : true,
          showCloseButton : false,
					padding : 5
				});
  }

  function gettesttasks() 
  {
    $.post('gettesttasks.json',{},  
     function(data){  
      eval('var obj='+data);         
      $('#testtasks').empty();  
      if(obj.ok=='1')
       $('#testtasks').append(obj.content);        
      else 
       $('#testtasks').append('Ошибка при загрузке текущих сеансов.');        
    }); 
  }
<?}?>
  
  </script>

  </body>
</html>

<?
} else die;  
?>            