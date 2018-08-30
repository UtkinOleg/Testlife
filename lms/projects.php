<?php
  if(defined("IN_USER") or defined("IN_ADMIN")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $all = $_GET["all"];
  $paid = $_GET["paid"];

  if (!empty($paid))
  {
    $allowchange = false;
    $pa1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM projectarray WHERE id='".$paid."' LIMIT 1");
    $paa1 = mysqli_fetch_array($pa1);
    $paname = $paa1['name'];
    if ((defined("IN_SUPERVISOR") and $paa1['ownerid'] == USER_ID)) 
     $allowchange = true;
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
  
  // Проверка на супервизора
  if (empty($paid))
  {
  if (defined("IN_SUPERVISOR")) 
  {
   if (LOWSUPERVISOR)
   {
      $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE userid='".USER_ID."'");
      $total2 = mysqli_fetch_array($tot2);
      $countp = $total2['count(*)'];
      mysqli_free_result($tot2);
      $tot = mysqli_query($mysqli,"SELECT count(*) FROM projectarray WHERE ownerid='".USER_ID."'");
      $total = mysqli_fetch_array($tot);
      $countz = $total['count(*)'];
      mysqli_free_result($tot);
      if ($countz==0 and $countp==0)
      {
       print "<HTML><HEAD>\n";
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=welcome&iwant=1'>\n";
       print "</HEAD></HTML>\n";
       exit();
      }
      else
      {
/*       print "<HTML><HEAD>\n";
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=parray'>\n";
       print "</HEAD></HTML>\n";
       exit();  */
      }
   }
   else
   {
/*      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=parray'>\n";
      print "</HEAD></HTML>\n";
      exit();  */
   }
  }
  }
  
  $helppage='';

  // Выводим шапку страницы
  include "topadmin.php";


?>

<script type="text/javascript">
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    window.location = url;
    window.location.reload();
   }    

   function closeFancybox(){
    $.fancybox.close();
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

  $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE userid='".USER_ID."' AND status='created'");
  $total2 = mysqli_fetch_array($tot2);
  $countcreated = $total2['count(*)'];
  mysqli_free_result($tot2);
  if ($countcreated==1 and empty($paid)) 
  {
?>           
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Вы создали свой первый проект. Вы можете его <strong><i class='fa fa-pencil fa-lg'></i> Изменить</strong> или <strong><i class='fa fa-trash fa-lg'></i> Удалить</strong>. Если проект подготовлен к экспертизе - нажмите кнопку <strong><i class="fa fa-cog fa-lg"></i> Изменить статус</strong> и укажите пункт <strong>Подготовлен к экспертизе</strong>. Только в этом случае эксперты смогут оценить Ваш проект.</p>
  </div>
</div><p></p>
<?
  }

  $sort = $_GET["sort"];
  if (empty($sort)) 
   $sort='id';

  if (empty($paid))
   $pa = mysqli_query($mysqli,"SELECT * FROM projectarray ORDER BY id DESC");
  else
   $pa = mysqli_query($mysqli,"SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");

  $pai=0;
  $pc=0;
  while($paa = mysqli_fetch_array($pa))
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
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE proarrid='".$paid2."'");
   else 
   {
    if (empty($paid))
     $tot = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."'");
    else
     $tot = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
    }
  }
  else
  {
   if (empty($paid))
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."'");
   else
    $tot = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
  } 

  if (!$tot) puterror("Ошибка при обращении к базе данных");
  
  if(defined("IN_ADMIN"))
  {
   if ($all==1)
    $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid2."' ORDER BY ".$sort." ASC");
   else
   {
    if (empty($paid))
     $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."' ORDER BY ".$sort." ASC");
    else 
     $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid."' ORDER BY ".$sort." ASC");
   }
  } 
  else
  {
    if (empty($paid))
     $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid2."' AND userid='".USER_ID."' ORDER BY ".$sort." ASC");
    else 
     $gst = mysqli_query($mysqli,"SELECT * FROM projects WHERE proarrid='".$paid."' ORDER BY ".$sort." ASC");
  } 
   
  if (!$gst) puterror("Ошибка при обращении к базе данных");

  $total = mysqli_fetch_array($tot);
  $count = $total['count(*)'];

  if ($count>0) 
  {
   $ccount = $count;
   $pc+=$count;
   $tableheader = "class=tableheaderhide";
    ?>
<script>
  $(function() {
    $( "#accordion<? echo $pai; ?>" ).accordion({
      heightStyle: "content",
      collapsible: true
    });
  });
</script>
  
<div id='menu_glide' class='ui-widget-content ui-corner-all'>
<h3 class='ui-widget-header ui-corner-all' style='font-size: 14px; margin: 10px; padding: 1em; text-align: left; background: #497787 url("scripts/jquery-ui/images/ui-bg_inset-soft_75_497787_1x100.png") 50% 50% repeat-x;'>

<?
    if (!empty($pap))
     {      
       if (stristr($pap,'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$paid2.$pap."' height='30' style='margin-top: -7px;' class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$pap."' height='30' style='margin-top: -7px;' class='leftimg'><div>"; 
     }

?>
<? echo $pan ?> - <? echo $count ?> </h3>
<div id="accordion<? echo $pai ?>">

 <?         

  while($member = mysqli_fetch_array($gst))
  {
    switch ($member['status']) 
       { 
        case 'created' : $st = '<i class="fa fa-pencil fa-2x"></i>'; break;
        case 'accepted' : $st = '<i class="fa fa-paperclip fa-2x"></i>'; break;
        case 'inprocess' : $st = '<i class="fa fa-thumbs-o-up fa-2x"></i>'; break;
        case 'finalized' : $st = '<i class="fa fa-check-square-o fa-2x"></i>'; break;
        case 'published' : $st = '<i class="fa fa-cloud-upload fa-2x"></i>'; break;
       } 
    switch ($member['status']) 
       { 
        case 'created' : $st2 = '<i class="fa fa-pencil fa-lg"></i>'; break;
        case 'accepted' : $st2 = '<i class="fa fa-paperclip fa-lg"></i>'; break;
        case 'inprocess' : $st2 = '<i class="fa fa-thumbs-o-up fa-lg"></i>'; break;
        case 'finalized' : $st2 = '<i class="fa fa-check-square-o fa-lg"></i>'; break;
        case 'published' : $st2 = '<i class="fa fa-cloud-upload fa-lg"></i>'; break;
       } 

/*    switch ($member['status']) 
       { 
        case 'created' : echo '<h1 title="Проходит создание">'; break;
        case 'accepted' : echo '<h1 title="Проект подготовлен">'; break;
        case 'inprocess' : echo '<h1 title="Проект проходит экспертизу">'; break;
        case 'finalized' : echo '<h1 title="Экспертиза проекта завершена">'; break;
        case 'published' : echo '<h1 title="Проект опубликован в сети">'; break;
       } */
    echo '<h1>';
    echo $st." ".$i." (ID".$member['id'].") <b>".$member['info']."</b> [".data_convert ($member['regdate'], 1, 0, 0)."]";
    echo "</h1><div>";
    
    
    if(defined("IN_ADMIN"))
    {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$member['userid']."' LIMIT 1");
     if (!$from) puterror("Ошибка при обращении к базе данных");
     $fromuser = mysqli_fetch_array($from);
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
  $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  while($param = mysqli_fetch_array($res3))
   { 
    $res4=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysqli_fetch_array($res4);
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

      
		});
</script>

    <p><a target="_blank" href="viewproject3&id=<? echo $member['id'] ?>"><i class='fa fa-search fa-lg'></i> Просмотр проекта <strong><? echo $member['info']; ?></strong></a>
    
    <?
    $lastinfo = $member['info'];
     
 /*   $from3 = mysqli_query($mysqli,"SELECT count(*) FROM comments WHERE projectid='".$member['id']."' AND readed='0'");
    if (!$from3) puterror("Ошибка при обращении к базе данных");
    $newcomments = mysqli_fetch_array($from3);
    if ($newcomments['count(*)']>0)
     echo "&nbsp;<img title='Есть комментарии к проекту' src='img/b_newtbl.png'";   */

   echo "</p>";
    

  $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM poptions WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  $viewphoto = false; 
  while($param = mysqli_fetch_array($res3))
   { 
    $res4=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['id']."'");
    $param4 = mysqli_fetch_array($res4);

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
        <hr><p><a title="Оставить комментарий" id="comment<? echo $member['id'] ?>" href="javascript:;"><i class='fa fa-comment-o fa-lg'></i> Оставить комментарий</a></p>
       <?  
       
  $res3cnt=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM comments WHERE projectid='".$member['id']."'");
  $param3cnt = mysqli_fetch_array($res3cnt);
  $ccount = $param3cnt['count(*)'];
  if ($ccount>0) 
  {
  $v= "";   
  
  $cres3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM comments WHERE projectid='".$member['id']."' ORDER BY cdate DESC");
  while($cparam3 = mysqli_fetch_array($cres3))
   { 
      $cres4=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$cparam3['expertid']."' LIMIT 1");
      $cparam4 = mysqli_fetch_array($cres4);
      $v.= "<p><i class='fa fa-comments fa-lg'></i> <b>Эксперт ".$cparam4['userfio']." от ".data_convert ( $cparam3['cdate'], 1, 1, 0).":</b>
      ".$cparam3['content']."</p>";   
   } 

   echo $v;
  }
       
       }
    }
    
    if ($member['status']=='created')
    {
       ?>                                                                                                                                                               
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#editproject<? echo $member['id'] ?>").button();
      $("#delproject<? echo $member['id'] ?>").button();
		});
    </script>
       <?
     if (defined("IN_ADMIN")) 
     {
      echo "<hr><p><a id='editproject".$member['id']."' href='editproject&id=".$member['id']."'><i class='fa fa-pencil fa-lg'></i> Изменить проект</a>"; 
      echo "&nbsp;<a id='delproject".$member['id']."' href='#' onClick='DelProjectWindow(".$member['id'].",".$member['id'].");'><i class='fa fa-trash fa-lg'></i> Удалить проект</a>&nbsp;"; 
     } 
     else
     if ($member['userid']==USER_ID)
     {
      echo "<hr><p><a id='editproject".$member['id']."' href='editproject&id=".$member['id']."'><i class='fa fa-pencil fa-lg'></i> Изменить проект</a>"; 
      echo "&nbsp;<a id='delproject".$member['id']."' href='#' onClick='DelProjectWindow(".$member['id'].",".$member['id'].");'><i class='fa fa-trash fa-lg'></i> Удалить проект</a>&nbsp;"; 
     }
    }
    
    if ($member['status']=='created') 
    {
     $iscreate = 1;
     echo "<b> ".$st2." Создание проекта</b> ";
      {
      if ($member['userid']==USER_ID || defined("IN_ADMIN") || (defined("IN_SUPERVISOR") && $allowchange)) 
       {
       ?>                                                                                                                                                               
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#fancybox-chs<? echo $member['id'] ?>").button();
		});
    </script>
    <a title="Изменить статус проекта - подготовлен к экспертизе" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Изменить статус</a></p>
       <?
       }
      }
    }
    else
    if ($member['status']=='accepted') 
    {
     echo "<b> ".$st2." Подготовлен</b> ";
     if(defined("IN_ADMIN") || (defined("IN_SUPERVISOR") && $allowchange))
      {
      ?>
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#fancybox-chs<? echo $member['id'] ?>").button();
		});
    </script>
    <a title="Изменить статус" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='inprocess') 
    {
     echo "<b> ".$st2." Проходит экспертизу</b>";
     if(defined("IN_ADMIN") || (defined("IN_SUPERVISOR") && $allowchange))
      {
      ?>
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#fancybox-chs<? echo $member['id'] ?>").button();
		});
    </script>
    <a title="Изменить статус" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='finalized') 
    {
     echo "<b> ".$st2." Экспертиза завершена</b> Итоговый балл: ".round($member['maxball'],2)." ";
     if(defined("IN_ADMIN") || (defined("IN_USER") && $member['userid']==USER_ID))
      {
      ?>
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#fancybox-chs<? echo $member['id'] ?>").button();
		});
    </script>
    <a title="Изменить статус проекта - опубликовать проект" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Изменить статус</a></p>
      <?
      }
     else 
      echo "</p>";
    }
    else
    if ($member['status']=='published')
    { 
     echo "<b> ".$st2." Проект опубликован</b> ";
     echo "<a href='view/".$member['id']."' title='Прямая ссылка' target='_blank'>Ссылка на проект</a> "; 
     if(defined("IN_ADMIN") || (defined("IN_USER") && $member['userid']==USER_ID))
      {
      ?>
    <script type="text/javascript">
	 	$(document).ready(function() {
      $("#fancybox-chs<? echo $member['id'] ?>").button();
		});
    </script>
    <a title="Изменить статус проекта - отменить публикацию" id="fancybox-chs<? echo $member['id'] ?>" href="javascript:;"><i class="fa fa-cog fa-lg"></i> Изменить статус</a></p>
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
 }

 if ($pc==0)
 {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=newprojects'>\n";
      print "</HEAD></HTML>\n";
 }
 
  include "bottomadmin.php";
} else die;  
?>