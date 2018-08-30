<?php
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";


  
  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  

  // Выводим шапку страницы
  //include "topadmin.php";

  require_once "header.php"; 
?>
<script type="text/javascript" src="scripts/jquery.mousewheel-3.0.6.pack.js"></script>
<script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
<script type="text/javascript" src="scripts/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>
<link rel="stylesheet" href="scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="scripts/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
<?

echo"</head><body><center><p>";

// Найдем оценку проекта
  $res5=mysql_query("SELECT id, openproject, ocenka, name, photoname, openexpert FROM projectarray WHERE id='".$selpaid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysql_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $openexpert = $proarray['openexpert'];
  $ocenka = $proarray['ocenka'];
  $paname = $proarray["name"];
  
    if (!empty($proarray['photoname']))
     {      
       if (stristr($proarray['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$proarray['id'].$proarray['photoname']."' height='80'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$proarray['photoname']."' height='80'><div>"; 
     } 
  
  echo "<font face='Tahoma, Arial' size='+1'>Опубликованные проекты '".$paname."'</font></p>";

  $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$selpaid."' ORDER BY maxball DESC");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
    ?>

<center>
<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}
</style>
<script>
  $(document).ready(function() {
			$('.fancybox').fancybox();
	});
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
      collapsible: true
    });
  });
  </script>
  
  <div id="accordion">
       <?         
  
  $top=0;
  while($member = mysql_fetch_array($gst))
  {
   $top++;
   if (($openexpert==0 and $member['status']=='published') or ($openexpert>0 and 
   ( $member['status']=='published' or $member['status']=='accepted' or $member['status']=='inprocess')))
   { 
    
    echo "<h3 style='font-size: 100%;'><b>".$member['info'].
    "</b>, итоговый балл: <b>".
    round($member['maxball'],2)."</b>, место в рейтинге: <b>".
    $top."</b></h3><div>";

    echo "<p>Дата создания проекта: ".data_convert ($member['regdate'], 1, 0, 0)."</p>";
    echo "<p>Просмотр проекта: <a href='view/".$member['id']."' title='Прямая ссылка на просмотр проекта ".htmlspecialchars($member['info'])."' target='_blank'>expert03.ru/view/".$member['id']."</a></p>"; 

    $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
    if (!$res3) puterror("Ошибка при обращении к базе данных");
    while($param = mysql_fetch_array($res3))
    { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);

    if ($param['files']=="yes") 
    {
     if (!empty($param4['filename'])) 
     { 
     $kb = round($param4['filesize']/1024,2);
     if ($kb>1000) 
      $mb = round($param4['filesize']/1048576,2);
     if ($param['filetype']=="file") 
     { 
      echo "<p><a class='menu' href='file.php?id=".$param4['secure']."'
      target='_blank'>";
      $filename1 = explode(".", $param4['filename']); 
      $filenameext1 = $filename1[count($filename1)-1]; 
      
      if (strtolower($filenameext1)=='pdf')
       echo "<img src='img/pdf.png'";
      else
      if (strtolower($filenameext1)=='zip' || strtolower($filenameext1)=='rar')
       echo "<img src='img/zip.jpg'";
      else
      if (strtolower($filenameext1)=='xls' || strtolower($filenameext1)=='xlsx')
       echo "<img src='img/xls.gif'";
      else
      if (strtolower($filenameext1)=='doc' || strtolower($filenameext1)=='docx')
       echo "<img src='img/doc.gif'";
      else
       echo "<img src='img/f32.jpg'";
      
      echo" height='20' alt='Загрузить ".$param4['filename']."'> ".$param4['filename']."</a> ";
      if ($kb>1000) 
       echo"(".$mb." Мб)</p>";
      else 
       echo"(".$kb." кб)</p>";
     } 
     }  
    }  
    }
    echo "</div>"; 
   }
  }
  echo "</div></center>";
//  echo "<br><p align='center'><input type='button' name='close' value='Назад' onclick='history.back()'></p>";
  
//include "social.php";
echo "</center><p></p></body></html>";  


//include "bottomadmin.php";
?>