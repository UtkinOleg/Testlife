<?php  
if(defined("USER_REGISTERED")) {

  include "config.php";
  //require_once('filelib.php');

  $a = $_GET['a'];
  $height = $_GET['h'];
  if (empty($height))
   $height = 24;


  if (empty($a))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, photoname FROM users WHERE id='".USER_ID."' LIMIT 1;");
  else
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, photoname FROM users WHERE token='".$a."' LIMIT 1;");
  $member = mysqli_fetch_array($sql);
  $id = $member['id'];

  if (stristr($member['photoname'],'http') === FALSE)
  {
   $pathname = $_SERVER['DOCUMENT_ROOT']."/picavatars/".$id."/thumb_".$member['photoname'];
   $pathnameorig = $_SERVER['DOCUMENT_ROOT']."/picavatars/".$id."/".$member['photoname'];
  }
  else 
  {
   $pathname = $member['photoname'];
   $pathnameorig = $member['photoname'];
  }
  mysqli_free_result($sql);

  if ($height==24)
  {
   if (file_exists($pathname))
   { 
header('Content-type: image/png');
    readfile($pathname);
    }
  }
  else
  {
   if (file_exists($pathnameorig))
   { 
    header('Content-type: image/png');
    readfile($pathnameorig);
    }
  }

/*    list($w, $h) = getimagesize($pathname);
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
                }  */
} else die;                
?>
