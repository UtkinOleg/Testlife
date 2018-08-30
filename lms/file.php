<?php  

  include "config.php";
  require_once('filelib.php');

  $id = $_GET["id"];
  
  if (!empty($id)) 
  {
    $res3=mysql_query("SELECT * FROM projectdata WHERE secure='".$id."'");
    $param = mysql_fetch_array($res3);
    if (!empty($param['filename'])) { 
      $pathname = $upload_dir.$param['projectid'].$param['realfilename'];
     } 

    if (file_exists($pathname)) {
     //  session_write_close(); 
     $filename = $param['realfilename'];
     send_file($pathname, $filename);
    } 
    else
    {
     
     $res3=mysql_query("SELECT * FROM multiprojectdata WHERE secure='".$id."'");
     $param = mysql_fetch_array($res3);
     if (!empty($param['filename'])) { 
      $pathname = $upload_dir.$param['projectid'].$param['realfilename'];
     } 

     if (file_exists($pathname)) {
      //  session_write_close(); 
     
/*    $imgType = strtolower(substr($pathname, strrpos($pathname, '.')+1));
    
                if(($imgType == 'jpg') or ($imgType == 'jpeg'))
                {
                    $imgString = file_get_contents($pathname);
                    $image = imagecreatefromstring($imgString);
                    header ("Content-type: image/jpeg");
                    imagejpeg($image);
                    imagedestroy($image);
                }
                else if($imgType == 'png')
                {
                    $image = imagecreatefrompng($pathname);
                    header ("Content-type: image/png");
                    imagepng($image);
                    imagedestroy($image);
                }
                else if($imgType == 'gif')
                {
                    $image = imagecreatefromgif($pathname);
                    header ("Content-type: image/gif");
                    imagegif($image);
                    imagedestroy($image);
                }
                else
                {
                 */
                 $filename = $param['realfilename'];
                 send_file($pathname, $filename);
//                }
     
     } else
        die;

     }

  }
?>
