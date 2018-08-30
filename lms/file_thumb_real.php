<?php  

  include "config.php";
  require_once('filelib.php');

  $id = $_GET["id"];
  $width = $_GET["w"];
  $height = $_GET["h"];
  
  if (!empty($id)) 
  {

    $res3=mysql_query("SELECT photoname FROM projectarray WHERE id='".$id."' LIMIT 1");
    $member = mysql_fetch_array($res3);
    if (!empty($member['photoname']))
     {      
     
       if (stristr($member['photoname'],'http') === FALSE)
           $pathname = $pa_upload_dir.$id.$member['photoname']; 
          else
           $pathname = $member['photoname']; 
     }
    
    if (!file_exists($pathname)) 
      die;

    list($w, $h) = getimagesize($pathname);
    $ratio = max($width/$w, $height/$h);
    $h = ceil($height / $ratio);
    $x = ($w - $width / $ratio) / 2;
    $w = ceil($width / $ratio);
                
    $imgType = strtolower(substr($pathname, strrpos($pathname, '.')+1));
    
                if(($imgType == 'jpg') or ($imgType == 'jpeg'))
                {
                    /* Get binary data from image */
                    $imgString = file_get_contents($pathname);
                    /* create image from string */
                    $image = imagecreatefromstring($imgString);
                    $tmp = imagecreatetruecolor($width, $height);
                    imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);
                    header ("Content-type: image/jpeg");
                    imagejpeg($tmp);
                    imagedestroy($image);
                    imagedestroy($tmp);
                }
                else if($imgType == 'png')
                {
                    $image = imagecreatefrompng($pathname);
                    $tmp = imagecreatetruecolor($width,$height);
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                    imagecopyresampled($tmp, $image,0,0,$x,0,$width,$height,$w, $h);
                    header ("Content-type: image/png");
                    imagepng($tmp);
                    imagedestroy($image);
                    imagedestroy($tmp);
                }
                else if($imgType == 'gif')
                {
                    $image = imagecreatefromgif($pathname);
                    $tmp = imagecreatetruecolor($width,$height);
                    $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                    imagefill($tmp, 0, 0, $transparent);
                    imagealphablending($tmp, true); 
                    imagecopyresampled($tmp, $image,0,0,0,0,$width,$height,$w, $h);
                    header ("Content-type: image/gif");
                    imagegif($tmp);
                    imagedestroy($image);
                    imagedestroy($tmp);
                }
  }
?>
