<?php  
  $f = $_SERVER['DOCUMENT_ROOT'] . '/'. urldecode($_GET['f']);
  
if (file_exists($f)) {
   header("Content-type: image/png");
   readfile($f);
}
else
{
 echo 'Not found';
}
?>