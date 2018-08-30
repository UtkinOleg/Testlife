<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR") or defined("IN_EXPERT")) 
{  

  $paid = $_GET['paid'];
  $projectid = $_GET['id'];

  include "config.php";
  include_once('createzip.php');
header('Content-type: application/zip');
header('Content-Disposition: attachment; filename=test.zip');
$fileContents = 'Hello world!';
$createzip = new createzip;
$createzip -> addDirectory('test/');
$createzip -> addFile($fileContents, 'test/test.txt');
echo($createzip -> getZippedfile());

//  header('Content-type: application/zip');
//  $name = 'Content-Disposition: attachment; filename=project_'.$projectid.'.zip';
//  header($name);
//  $createZip = new createZip;

/*  $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$paid."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  while($param = mysql_fetch_array($res3))
   { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$projectid."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);

    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) { 
      if ($param['filetype']=="file") { 
      $fileContents = file_get_contents($upload_dir.$param4['projectid'].$param4['realfilename']);
      $createZip->addFile($fileContents, $param4['realfilename']);
     }
    } 
    }
   }   */
//  echo($createZip -> getZippedfile());
} else die;   
?>