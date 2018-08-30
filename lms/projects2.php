<?php
  if(defined("IN_USER") or defined("IN_ADMIN")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $all = $_GET["all"];
  $paid = $_GET["paid"];

  if (!empty($paid))
  {
    $pa1 = mysql_query("SELECT name FROM projectarray WHERE id='".$paid."' LIMIT 1");
    $paa1 = mysql_fetch_array($pa1);
    $paname = $paa1['name'];
  }

  if(defined("IN_ADMIN"))
  {
   if (empty($paid))
    $title=$titlepage="Все проекты";
   else 
    $title=$titlepage="Проекты &#8220;".$paname."&#8221;";
  } 
  else
  {
   if (empty($paid))
    $title=$titlepage="Мои проекты";
   else 
    $title=$titlepage="Проекты &#8220;".$paname."&#8221;";
  }
  $helppage='';

  // Выводим шапку страницы
  include "topadmin.php";


?>

<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
  
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    window.location = url;
    window.location.reload();
   }    
</script>

<style type="text/css">
.fancybox-custom .fancybox-skin {
	box-shadow: 0 0 50px #222;
}
.ui-accordion {
  padding: 10px;
}
</style>

<?
  // Стартовая точка
//  $start = $_GET["start"];
//  if (empty($start)) $start = 0;
//  $start = intval($start);
//  if ($start < 0) $start = 0; 

  $sort = $_GET["sort"];
  if (empty($sort)) 
   $sort='id';

  if (empty($paid))
   $pa = mysql_query("SELECT * FROM projectarray ORDER BY id DESC");
  else
   $pa = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");

  $pai=0;
  while($paa = mysql_fetch_array($pa))
  {
  $pai++;
  $paid2 = $paa['id'];
  $pan = $paa['name'];
  $pap = $paa['photoname'];
  $df = $paa['defaultshablon'];
  $nodown = $paa['nodownload'];
  $addcomment = $paa['addcomment'];
   
  if(defined("IN_ADMIN"))
  {
   if ($all==1)
    $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid2."'");
   else 
   {
    if (empty($paid))
     $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."'");
    else
     $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
    }
  }
  else
  {
   if (empty($paid))
    $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."'");
   else
    $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
  } 

  if (!$tot) puterror("Ошибка при обращении к базе данных");
  
  if(defined("IN_ADMIN"))
  {
   if ($all==1)
    $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$paid2."' ORDER BY ".$sort." ASC");
   else
   {
    if (empty($paid))
     $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."' ORDER BY ".$sort." ASC");
    else 
     $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$paid."' ORDER BY ".$sort." ASC");
   }
  } 
  else
  {
    if (empty($paid))
     $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."' ORDER BY ".$sort." ASC");
    else 
     $gst = mysql_query("SELECT * FROM projects WHERE proarrid='".$paid."' ORDER BY ".$sort." ASC");
  } 
   
  if (!$gst) puterror("Ошибка при обращении к базе данных");

  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  if ($count>0) 
  {
   $ccount = $count;
   $tableheader = "class=tableheaderhide";
    ?>
<script>
  $(function() {
    $( "#accordion<? echo $pai; ?>" ).accordion({
      heightStyle: "content",
      active: false,
      collapsible: true
    });
  });
</script>

<div id="spinner"><img src="img/ajax-loader.gif"></div>  
<div id='menu_glide' class='ui-widget-content ui-corner-all'>
<h3 class='ui-widget-header ui-corner-all'><? echo $pan ?></h3>
<div id="accordion<? echo $pai ?>">

 <?         

  while($member = mysql_fetch_array($gst))
  {
    switch ($member['status']) 
       { 
        case 'created' : $st = '<img src="img/menu/profile.png" height="20" title="Проходит создание">'; break;
        case 'accepted' : $st = '<img src="img/menu/myprojects.png" height="20" title="Проект подготовлен">'; break;
        case 'inprocess' : $st = '<img src="img/menu/experts.png" height="20" title="Проект проходит экспертизу">'; break;
        case 'finalized' : $st = '<img src="img/menu/reports.png" height="20" title="Экспертиза проекта завершена">'; break;
        case 'published' : $st = '<img src="img/menu/msgs.png" height="20" title="Проект опубликован в сети">'; break;
       } 
    switch ($member['status']) 
       { 
        case 'created' : echo '<h1 title="Проходит создание">'; break;
        case 'accepted' : echo '<h1 title="Проект подготовлен">'; break;
        case 'inprocess' : echo '<h1 title="Проект проходит экспертизу">'; break;
        case 'finalized' : echo '<h1 title="Экспертиза проекта завершена">'; break;
        case 'published' : echo '<h1 title="Проект опубликован в сети">'; break;
       } 
    
    echo $st." ".$i." (ID".$member['id'].") <b>".$member['info']."</b> [".data_convert ($member['regdate'], 1, 0, 0)."]";
    echo "</h1><div>";
    
    
    if(defined("IN_ADMIN"))
    {
     $from = mysql_query("SELECT userfio FROM users WHERE id='".$member['userid']."' LIMIT 1");
     if (!$from) puterror("Ошибка при обращении к базе данных");
     $fromuser = mysql_fetch_array($from);
     echo "<p><a class='menu' href='edituser&id=".$member['userid']."' title='Данные участника'><p class=zag2>".$fromuser['userfio']."</p></a>";
    }

   
    ?>
    
<script type="text/javascript">

	$(document).ready(function() {

    	$("#fancybox<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewproject3&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 1000,
					padding : 5
				});
			});

    	$("#fancybox-chs<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'chsproject2&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 700,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

    	$("#comment<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'addcomment&id=<? echo $member['id'] ?>&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 700,
          height : 300,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});

      $("#fancybox-manual-<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open([
        <?php
  $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  while($param = mysql_fetch_array($res3))
   { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);
    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) { 
     if ($param['filetype']=="foto") {
      echo "{ href : '".$upload_dir.$param4['projectid'].$param4['realfilename']."' },";
     }
     }  
    } 
    }
        ?>
          
				], { 
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});      

    $("#sharing<?php echo $member['id']; ?>").fancybox({
        type: 'inline',
        beforeLoad: function (){
         share(<?php echo $member['id']; ?>);
        },
        href: 'share<?php echo $member['id']; ?>'
    });

		});
    
  function share(id){    
   $("#spinner").fadeIn("slow");
   $.post('sharejson.php',{project:id}, 
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
       $('#share<?php echo $member['id']; ?>').append(obj.content);        
    }); 
   $("#spinner").fadeOut("slow");
  }  
    
</script>

    <p><a title="Просмотр проекта" id="fancybox<? echo $member['id'] ?>" href="javascript:;"><img src='img/view.jpg' width='20' height='20'> Просмотр проекта <? echo $member['info']; ?></a>
    
    <?
    $lastinfo = $member['info'];
     
 /*   $from3 = mysql_query("SELECT count(*) FROM comments WHERE projectid='".$member['id']."' AND readed='0'");
    if (!$from3) puterror("Ошибка при обращении к базе данных");
    $newcomments = mysql_fetch_array($from3);
    if ($newcomments['count(*)']>0)
     echo "&nbsp;<img title='Есть комментарии к проекту' src='img/b_newtbl.png'";   */

   echo "</p>";
    

  $res3=mysql_query("SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  $viewphoto = false; 
  while($param = mysql_fetch_array($res3))
   { 
    $res4=mysql_query("SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysql_fetch_array($res4);

    if ($param['files']=="yes") {
     if (!empty($param4['filename'])) { 
     if ($param['filetype']=="file") { 

      $filename1 = explode(".", $param4['filename']); 
      $filenameext1 = $filename1[count($filename1)-1]; 
      
      if (($nodown>0) and (!empty($paid)))
       echo "<p>";
      else
       echo "<p><a class='menu' href='file.php?id=".$param4['secure']."' target='_blank'>";
      
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

      $kb = round($param4['filesize']/1024,2);
      if ($kb>1000) 
       $mb = round($param4['filesize']/1048576,2);

      if (($nodown>0) and (!empty($paid)))
       echo" height='20'> ".$param4['filename']."</p>";
      else
      {
       echo" height='20'> ".$param4['filename']."</a> ";
       if ($kb>1000) 
        echo"(".$mb." Мб)</p>";
       else 
        echo"(".$kb." кб)</p>";
      }
     } 
     else
     if ($param['filetype']=="foto" && !$viewphoto) {
     
      $viewphoto = true;
      list($width, $height, $type, $attr)= getimagesize($upload_dir.$param4['projectid'].$param4['realfilename']);

      if ($width>$resizing) {
          $resize = round(($resizing*100)/$width);
          $new_width = round((($resize/100)*$width));
          $new_height = round((($resize/100)*$height));
         } 
      else
         {
          $new_width = $width;
          $new_height = $height;
         }

      echo "<p><a title='Фотогалерея проекта #1' id='fancybox-manual-".$member['id']."' href='javascript:;'>
      <img src='file_thumb.php?id=".$param4['secure']."&w=".$new_width."&h=".$new_height."' width='".$new_width."'
       height='".$new_height."'></a></p>";
     }
     
     
     }  
    } 
    else
    if ($param['link']=="yes" and !empty($param4['content'])) {
       echo "<p>".$param['name'].":";
       if (isUrl($param4['content'])) 
        echo" <a href='".$param4['content']."' target='_blank'>".$param4['content']."</a></p>";
       else
        echo" <a href='http://".$param4['content']."' target='_blank'>".$param4['content']."</a></p>";
    }     
    
    }
    
    if ($member['status']!='finalized' or $member['status']!='published') 
    {
      if ($member['userid']!=USER_ID and $addcomment>0) 
       {
       ?>
        <hr><p><a title="Оставить комментарий" id="comment<? echo $member['id'] ?>" href="javascript:;"><img src='img//b_docs.png' width='16' height='16'> Оставить комментарий</a></p>
       <?  
       
  $res3cnt=mysql_query("SELECT count(*) FROM comments WHERE projectid='".$member['id']."'");
  $param3cnt = mysql_fetch_array($res3cnt);
  $ccount = $param3cnt['count(*)'];
  if ($ccount>0) 
  {
  $v= "";   
  
  $cres3=mysql_query("SELECT * FROM comments WHERE projectid='".$member['id']."' ORDER BY cdate DESC");
  while($cparam3 = mysql_fetch_array($cres3))
   { 
      $cres4=mysql_query("SELECT userfio FROM users WHERE id='".$cparam3['expertid']."' LIMIT 1");
      $cparam4 = mysql_fetch_array($cres4);
      $v.= "<p><img src='img//b_docs.png' width='16' height='16'> <b>Эксперт ".$cparam4['userfio']." от ".data_convert ( $cparam3['cdate'], 1, 1, 0).":</b>
      ".$cparam3['content']."</p>";   
   } 

   echo $v;
  }
       
       }
    }
    
    if ($member['status']=='created')
    {
     if (defined("IN_ADMIN")) 
     {
      echo "<hr><p><a href='editproject&id=".$member['id']."' title='Изменить проект'><img src='img/b_edit.png' width='16' height='16'> Изменить проект</a>"; 
      echo "&nbsp;<a href='#' onClick='DelProjectWindow(".$member['id'].",".$member['id'].");' title='Удалить'><img src='img/b_drop.png' width='16' height='16'> Удалить проект</a></p>"; 
     } 
     else
     if ($member['userid']==USER_ID)
     {
      echo "<hr><p><a href='editproject&id=".$member['id']."' title='Изменить проект'>
      <img src='img/b_edit.png' width='16' height='16'> Изменить проект</a>"; 
      echo "&nbsp;|&nbsp;<a href='#' onClick='DelProjectWindow(".$member['id'].",".$member['id'].");' title='Удалить'><img src='img/b_drop.png' width='16' height='16'> Удалить проект</a>"; 
      echo "&nbsp;|&nbsp;<a id='sharing".$member['id']."' href='#share".$member['id']."' title='Расшарить проект'><img src='img/b_newtbl.png' width='16' height='16'> Открыть проект</a> <div style='display:none'><div id='share".$member['id']."'></div></div></p>"; 
     }
    }
    
    if ($member['status']=='created') 
    {
     $iscreate = 1;
     echo "<p>Статус - <b>Создание проекта</b> ";
      {
      if ($member['userid']==USER_ID || defined("IN_ADMIN") || defined("IN_SUPERVISOR")) 
       {
       ?>
        <a title="Изменить статус проекта - подготовлен к экспертизе" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><img src='img/s_process.png' width='16' height='16'> Изменить статус</a></p>
       <?
       }
      }
    }
    else
    if ($member['status']=='accepted') 
    {
     echo "<p>Статус - <b>Подготовлен</b> ";
     if(defined("IN_ADMIN")  || defined("IN_SUPERVISOR"))
      {
      ?>
       <a title="Изменить статус" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><img src='img/s_process.png' width='16' height='16'> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='inprocess') 
    {
     echo "<p>Статус - <b>Проходит экспертизу</b>";
     if(defined("IN_ADMIN") || defined("IN_SUPERVISOR"))
      {
      ?>
       <a title="Изменить статус" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><img src='img/s_process.png' width='16' height='16'> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='finalized') 
    {
     echo "<p>Статус - <b>Экспертиза завершена</b> Итоговый балл: ".round($member['maxball'],2)." ";
     if(defined("IN_ADMIN") || (defined("IN_USER") && $member['userid']==USER_ID))
      {
      ?>
       <a title="Изменить статус проекта - опубликовать проект" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><img src='img/s_process.png' width='16' height='16'> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='published')
    { 
     echo "<p>Статус - <b>Проект опубликован</b> ";
     echo "<a href='view/".$member['id']."' title='Прямая ссылка' target='_blank'>Ссылка на проект</a> "; 
     if(defined("IN_ADMIN") || (defined("IN_USER") && $member['userid']==USER_ID))
      {
      ?>
       <a title="Изменить статус проекта - отменить публикацию" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><img src='img/s_process.png' width='16' height='16'> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }              
   echo "</div>"; 
   
   $lastid = $member['id']; 
  }
  
  echo "</div></div><p></p>";
                       
 }  
 else
 {
//      print "<HTML><HEAD>\n";
//      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=newprojects'>\n";
//      print "</HEAD></HTML>\n";
 }
 }
  include "bottomadmin.php";
} else die;  
?>