<?php
  
  $sqlanaliz = false;
  $resultprice = 10; 
  $version = "1.0.6";
  
  $dblocation = "localhost";
  $dbname = "testlife";
  $dbuser = "root";
  $dbpasswd = "";
  $pnumber = 30;
  $sendmail = false;
  $valmail = "info@testlife.org";
  $valmail2 = "";
  $site = "";

  // Включить - выключить кэширование SQL запросов
  $enable_cache = false;
  
  // Размер файла
  $max_file_size = 3096576; 
  $max_file_size_str = "3";
  $file_types_array = array("txt","doc","lts","xls","qg2","mbk","docx","xlsx","pdf","ppt","pptx","rar","zip");
  $upload_dir = "uploads/";
  $xmlupload_dir = "xmluploads/";
  
  // Размер фотографии
  $photo_max_file_size = 1048576; 
  $photo_max_file_size_str = "1";
  $photo_file_types_array=array("jpeg","jpg","gif","png");
  $photo_upload_dir = "uploads/avatars/";
  $pa_upload_dir = "uploads/pavatars/";
  $resizing = 100;
 
 
  $mysqli = mysqli_connect($dblocation,$dbuser,$dbpasswd,$dbname);
  if (mysqli_connect_errno($mysqli)) {
  ?>
   "<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
    <html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head><body>
    <P>В настоящий момент сервер базы данных не доступен.</P></body></html>
  <?php
    exit();
  }
  
 date_default_timezone_set('Asia/Irkutsk');
 define("MYSQLND_QC_ENABLE_SWITCH", "qc=on");
 mysqli_query($mysqli,"SET time_zone = '+08:00';");  
 mb_internal_encoding('UTF-8');

?>