<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  $title=$titlepage="Настройки проекта";
  
  $helppage='';
  // Выводим шапку страницы
  include "topadmin.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
    maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);

?>

<script type="text/javascript">
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
   $(document).ready(function() {
			$('.fancybox').fancybox();
    	$("#fancybox-view").click(function() {
				$.fancybox.open({
					href : 'editproject&mode=add&nomenu=yes&paid=<? echo $paid; ?>',
					type : 'iframe',
          width : 1000,
					padding : 5
				});
			});
    	$("#addpoption").click(function() {
				$.fancybox.open({
					href : 'addpoption2&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 700,
          height : 450,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    	$("#addfromfile").click(function() {
				$.fancybox.open({
					href : 'addpoptionfromfile&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 700,
          height : 450,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      $("#addmulti").click(function() {
				$.fancybox.open({
					href : 'addmulti&paid=<? echo $paid ?>',
					type : 'iframe',
          width : 500,
          height : 320,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
    }); 
    
 $(function() {
  $( "#tabs" ).tabs({
   active: <? if (empty($_GET['tab'])) echo '0'; else echo $_GET['tab']; ?>
  });
 });
</script>    
<?

  $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
  $tot2cnt = mysql_fetch_array($tot2);
  $countpr = $tot2cnt['count(*)'];

  if ($countpr>0){  

?>

            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Редактирование шаблона запрещено! В системе созданы проекты.</p>
            	</div>
            </div><p></p> 
 <? } ?>           

<div id="tabs">
  <ul>
    <li><a href="#Tab1">Разделы шаблона</a></li>
    <li><a href="#Tab2">Параметры шаблона</a></li>
  </ul>
<div id="Tab1">

<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> В разделах шаблона определяются наименования разделов или общих параметров проекта. Разбивать шаблон проекта на разделы необходимо в случае достаточно большого набора параметров в проекте или в случае явного разделения проекта на разделы или темы. В случае небольшого количества параметров в проекте можно не добавлять разделы.</p>
  </div>
</div>
<?
  
  echo "<p align='center'>";
  if ($countpr==0){  

      ?>
       <a title="Добавить раздел мультишаблона" id="addmulti" href="javascript:;"><i class='fa fa-cogs fa-lg'></i> Добавить раздел</a></p>
      <?
  }
  $tot = mysql_query("SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."'");
  $gst = mysql_query("SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst || !$tot) puterror("Ошибка при обращении к базе данных");

  $tot2cnt = mysql_fetch_array($tot);
  $countkg = $tot2cnt['count(*)'];
  
  if ($countkg>0) {
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" align="center" border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
          <tr>
              <td align='center' witdh='50'><p>1</p></td>
              <td><p><? if (empty($projectarray['defaultshablon'])) echo "По умолчанию"; else echo $projectarray['defaultshablon'];?></p></td>
          </tr>   
     <?         

  $i=1;
  while($member = mysql_fetch_array($gst))
  {

?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editmulti<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'editmulti&paid=<? echo $paid ?>&id=<? echo $member['id'] ?>',
					type : 'iframe',
          width : 600,
          height : 320,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?  

    echo "<tr><td align='center'><p>".++$i."</p></td>";
    if ($countpr==0){  
     ?>
     <td><a title="Редактировать раздел мультишаблона" id="editmulti<? echo $member['id'] ?>" href="javascript:;"><? echo $member['name']; ?> <i class='fa fa-pencil fa-lg'></i></a>
     &nbsp;
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delmulti','poptions','раздел мультишаблона')" title="Удалить раздел мультишаблона"><i class='fa fa-trash fa-lg'></i></a></p>
     <?
     echo "</td>";
    }
    else
    {
     echo "<td><p>".$member['name']."</p></td>";
    }
  }
    echo "</table></div>";
  }  
    echo "</p></div>";
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

  echo"<div id='Tab2'>";

  ?>
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> В параметрах шаблона определяются непосредственно составляющие элементы проекта. Под элементами понимаются строки, текстовые блоки, файлы, внешние ссылки, видеоролики и т.д. Элементы проекта могут быть принадлежать какому-либо разделу шаблона. Также в проекте могут присутствовать вычисляемые показатели необходимые для экспертизы. Если в системе на основе созданного шаблона будет создан хотябы один проект, то доступ изменению настроек шаблона будет заблокирован.</p>
	</div>
</div>
  <?

  $tot3 = mysql_query("SELECT count(*) FROM poptions WHERE proarrid='".$paid."'");
  $tot3cnt = mysql_fetch_array($tot3);
  $countpo = $tot3cnt['count(*)'];

  if ($countpr==0)
  {  
  
  echo"<p align='center'>";
  if (LOWSUPERVISOR) 
  {
   if ($countpo < 2) {
    echo"<b><a id='addpoption' href='javascript:;'><i class='fa fa-cog fa-lg'></i>&nbsp;
    Добавить параметр шаблона</a></b>";
   }
  }
  else
   echo"<b><a id='addpoption' href='javascript:;'><i class='fa fa-cog fa-lg'></i>&nbsp;
   Добавить параметр шаблона</a></b>";

  if (!LOWSUPERVISOR) 
   echo" | <a id='addfromfile' href='javascript:;'><i class='fa fa-file-code-o fa-lg'></i>&nbsp;
   Добавить параметры из файла XML</a>";
  
  if ($countpo>0)
    {
    echo " | <i class='fa fa-search fa-lg'></i>&nbsp;<a id='fancybox-view' href='javascript:;'>Просмотр формы проекта</a>";
    ?>
    &nbsp;|&nbsp;<a href="#" onClick="DelWindow(<? echo $paid;?>,0,'delpoptions','','')" title="Удалить все параметры шаблона проекта"><i class='fa fa-trash fa-lg'></i> Удалить все</a></p>
    <?
    }
  }
  
  echo "<p align='center'>";

  if ($countpo>0){  


  $gst = mysql_query("SELECT * FROM poptions WHERE proarrid='".$paid."' ORDER BY id");
  if (!$gst) puterror("Ошибка при обращении к базе данных");
  $tableheader = "class=tableheaderhide";
    ?>
     <div id='menu_glide' class='menu_glide'>
      <table width="100%" align="center" border="0" cellpadding=3 cellspacing=0 bordercolorlight=gray bordercolordark=white>
          <tr <? echo $tableheader ?> >
              <td witdh='50'><p class=help>№</p></td>
              <td><p class=help>Наименование</p></td>
              <td><p class=help>Пояснение</p></td>
              <td><p class=help>Тип параметра</p></td>
              <td><p class=help>Раздел</p></td>
          </tr>   
     <?         

  $i=0;
  while($member = mysql_fetch_array($gst))
  {
   $id = $member['id'];
?>

<script type="text/javascript">

		$(document).ready(function() {

    	$("#editpoption<? echo $id ?>").click(function() {
				$.fancybox.open({
					href : 'editpoption&paid=<? echo $paid ?>&id=<? echo $id ?>',
					type : 'iframe',
          width : 650,
          height : 500,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
			
      });
</script>

<?  
    echo "<tr><td witdh='50'><p>".++$i."</p></td>";
    if ($countpr==0){  

     echo "<td><p><a class='menu' id='editpoption".$id."' href='javascript:;'' title='Редактировать параметр шаблона'>".$member['name']." <i class='fa fa-pencil fa-lg'></i></a>";
     ?>
     &nbsp;
     <a href="#" onClick="DelWindowPaid(<? echo $member['id'];?> ,<? echo $paid;?>,'delpoption','poptions','параметр')" title="Удалить параметр шаблона проекта"><i class='fa fa-trash fa-lg'></i></a>
     <?
     echo "</p></td>"; 
    }
    else
     echo "<td><p>".$member['name']."</p></td>";
    echo "<td><p>".$member['doptext']."</p></td>";

    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Строка</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'textarea')
         echo "<td align='center'><p>Несколько строк</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'no' and $member['link'] == 'yes' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Внешняя ссылка</p></td>";
    else     
    if ($member['content'] == 'yes' and $member['files'] == 'no' and $member['youtube'] == 'yes' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Ссылка на ролик Youtube</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Файл</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'simple' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Фотография (картинка)</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'file' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Несколько файлов</p></td>";
    else     
    if ($member['content'] == 'no' and $member['files'] == 'yes' and $member['youtube'] == 'no' and $member['link'] == 'no' and 
        $member['filetype'] == 'foto' and $member['fileformat'] == 'ajax' and $member['typetext'] == 'text')
         echo "<td align='center'><p>Несколько фотографий (картинок)</p></td>";

    if ($member['multiid']>0)
    {
    $gstex = mysql_query("SELECT name FROM blockcontentnames WHERE id='".$member['multiid']."'");
    $memberex = mysql_fetch_array($gstex);
    echo "<td align='center'><p>".$memberex['name']."</p></td>";
    }
    else 
    {
     echo "<td align='center'><p>";
     if (empty($projectarray['defaultshablon'])) 
      echo "По умолчанию"; 
     else 
      echo $projectarray['defaultshablon'];
     echo "</p></td>";
    }
    
  }
  echo "</table></div>";
  }
  echo "</p></div>";
  include "bottomadmin.php";
}} else die;  
?>