<?
  include "config.php";
  include "func.php";

  $pid = $_GET["id"];
  $paid = $_GET["paid"];
  $tabid = $_GET["tabid"];
  $slidercounter=0;

  $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$paid."' AND multiid='".$tabid."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  while($param = mysql_fetch_array($res3))
   { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$pid."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);

    if ($param['content']=="yes" and $param['youtube']=="no" and $param['link']=="no") 
    {
     if ($param['name']!="[empty]")
      $v.="<p style='font-size:16px;'>".$param['name']."</p>";
     $v.="<p>".htmlspecialchars_decode($param4['content'])."</p>";
    }
    else
    if ($param['youtube']=="yes" and !empty($param4['content'])) 
    {
     if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $param4['content'], $matches)) 
     {
      $v.="<p align='center'><td><iframe width='640' height='360' src='http://www.youtube.com/embed/".$matches[1]."?feature=player_detailpage' frameborder='0' allowfullscreen></iframe></p>";
     }
     else
      $v.="<p align='center'><td><iframe width='640' height='360' src='http://www.youtube.com/embed/".$param4['content']."?feature=player_detailpage' frameborder='0' allowfullscreen></iframe></p>";
    }
    else
    if ($param['link']=="yes" and !empty($param4['content'])) 
    {
       $v.="<p style='font-size:16px;'>".$param['name'].": &nbsp;";
       if (isUrl($param4['content'])) 
        $v.="<strong><a href='".$param4['content']."' target='_blank'>".$param4['content']."</a></strong></p>";
       else
        $v.="<strong>".$param4['content']."</strong></p>";
    }
    else
    if ($param['files']=="yes") {
    
     if ($param['fileformat']=="ajax") 
     {
      $mres4=mysql_query("SELECT * FROM multiprojectdata WHERE projectid='".$pid."' AND optionsid='".$param['id']."'");
      if (!$mres4) puterror("Ошибка при обращении к базе данных");
      $realcnt=0;
      
      $slidercounter++;
      $v.="<script>";
      $v.="$(function () {";
      $v.="$('#slides".$tabid.$slidercounter."').responsiveSlides({";
      $v.="        auto: true,";
      $v.="        pagination: true,";
      $v.="        nav: false,";
      $v.="        fade: 500,";
      $v.="        speed: 3500,";
      $v.="        maxwidth: 650";
      $v.="      });";
      $v.="    });";
      $v.="</script>";
      if ($param['filetype']=="foto") 
      {
           $v.="<div class='rslides_container'>";
           $v.="<ul id='slides".$tabid.$slidercounter."' class='rslides'>";
      }
      while($mparam4 = mysql_fetch_array($mres4))
      {
       if (!empty($mparam4['filename'])) 
       { 
        if ($param['filetype']=="file") { 
         $v.="<p align='center'><iframe src='http://docs.google.com/viewer?url=http%3A%2F%2Fexpert03.ru%2Ffile.php%3Fid%3D".$mparam4['secure']."&embedded=true' width='100%' height='800' style='border: none;'></iframe>";
         $v.="</p>";
        }
        else
        if ($param['filetype']=="foto") 
        {
         list($width, $height, $type, $attr) = getimagesize($upload_dir.$mparam4['projectid'].$mparam4['realfilename']);
         if ($width>650) {
          $resize = round(65000/$width);
          $new_width = round((($resize/100)*$width));
          $new_height = round((($resize/100)*$height));
         } else
         {
          $new_width = $width;
          $new_height = $height;
         }
         $v.="<li><img src='file.php?id=".$mparam4['secure']."' target='_blank' title='".$mparam4['filename']."' width='".$new_width."' height='".$new_height."' /></li>";
        }
       }
      }
      if ($param['filetype']=="foto") 
      {
           $v.="</ul></div>";
      }
       
     }
     else 
     if ($param['fileformat']=="simple") {

     if (!empty($param4['filename'])) {
        $v.="<p align='center' style='font-size:18px;'>".$param['name']."</p>";
        if ($param['filetype']=="file") 
        {
          $v.="<p align='center'><iframe src='http://docs.google.com/viewer?url=http%3A%2F%2Fexpert03.ru%2Ffile.php%3Fid%3D".$param4['secure']."&embedded=true' width='100%' height='800' style='border: none;'></iframe>";
          $v.="</p>";
        }
        else
        if ($param['filetype']=="foto") {
         list($width, $height, $type, $attr) = getimagesize($upload_dir.$param4['projectid'].$param4['realfilename']);
         if ($width>650) {
          $resize = round(65000/$width);
          $new_width = round((($resize/100)*$width));
          $new_height = round((($resize/100)*$height));
         } else
         {
          $new_width = $width;
          $new_height = $height;
         }
         $v.="<p align='center'><img src='file.php?id=".$param4['secure']."' target='_blank' title='".$param['name']."' width='".$new_width."' height='".$new_height."'></p>";
        }
       }
    }
    }
   }
   
   echo $v; 
?>