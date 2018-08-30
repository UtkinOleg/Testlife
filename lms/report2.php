<?php
if(defined("IN_ADMIN")) 
{  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

//  ini_set('display_errors', 1);
//  error_reporting(E_ALL);  
  
  $mode = $_GET["mode"];
  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  $xls = $_GET["xls"];
  if (empty($xls)) $xls=0;

  // Найдем оценку проекта
  $res5=mysql_query("SELECT id, openproject, ocenka, name, photoname FROM projectarray WHERE id='".$selpaid."' LIMIT 1");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysql_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  $paname = $proarray["name"];

  $title=$titlepage="Итоговый рейтинг &#8220;".$paname."&#8221; по всем участникам";


  // Посмотрим открытый ли проект
  if ($openproject==1 || defined("IN_ADMIN") || defined("IN_SUPERVISOR")) {

if ($xls==1) {  
/** Include PHPExcel */
require_once dirname(__FILE__).'/lib/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Экспертная система оценки проектов")
							 ->setLastModifiedBy("Экспертная система оценки проектов")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory($title);
} 
else 
{              
 

//  include "topadmin.php";
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

    if (!empty($proarray['photoname']))
     {      
       if (stristr($proarray['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$proarray['id'].$proarray['photoname']."' height='80'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$proarray['photoname']."' height='80'><div>"; 
     } 
  
  echo "<font face='Tahoma, Arial' size='+1'>".$title."</font></p>";
}
  // Стартовая точка
  $start = $_GET["start"];
  if (empty($start)) $start = 0;
  $start = intval($start);
  if ($start < 0) $start = 0;

  // Запрашиваем общее число проектов
  if ($xls==1)
  {
   $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published')");
   $lst = mysql_query("SELECT * FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published') ORDER BY maxball DESC");
  }
  else
  {
   $tot = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published')");
   $lst = mysql_query("SELECT * FROM projects WHERE proarrid='".$selpaid."' AND (status='inprocess' OR status='finalized' OR status='published') ORDER BY maxball DESC LIMIT $start, $pnumber;");
  }
  if (!$lst || !$tot) puterror("Ошибка при обращении к базе данных");
  // При помощи цикла выбираем из базы данных
  // сообщения
  $n=$start;

  if ($xls==1) {  
    $exp_cnt = 2;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Место в рейтинге')
            ->setCellValue('B1', 'Наименование проекта');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(80);        
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
    {       
      $exp_cnt = 4;
      $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C1', 'Фамилия Имя Отчество участника')
            ->setCellValue('D1', 'Место работы');  
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);        
      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);        
    }        
  }
  else
  {
  ?>
     
     <div id='menu_glide' class='menu_glide'>
      <table class=bodytable style="table-layout:fixed" width="750" border="0" cellpadding=5 cellspacing=5 bordercolorlight=gray bordercolordark=white align=center>
          <tr class=tableheaderhide>
              <td width="50" align='center'><p>Место в рейтинге</p></td>
              <td width="500" style="overflow:hidden;" align='center'><p>Наименование проекта</p></td>
  <? if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
  { ?>
              <td width="100" style="overflow:hidden;" align='center'><p>Фамилия Имя Отчество участника</p></td>
              <td width="100" style="overflow:hidden;" align='center'><p>Место работы</p></td>
  <? }           
  }


  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {
   if ($mode==1) 
   {
    $lst2 = mysql_query("SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
    if (!$lst2) puterror("Ошибка при обращении к базе данных");
    while($list2 = mysql_fetch_array($lst2)) {
     $res2=mysql_query("SELECT * FROM users WHERE id='".$list2['expertid']."' LIMIT 1");
     $r2 = mysql_fetch_array($res2);
     if ($xls==1) {  
       $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(chr(++$exp_cnt+65).'1', "Эксперт ".$r2['userfio']);
     } 
     else
      echo "<td width='150' align='center'><p>Эксперт ".$r2['userfio']."</p></td>";
    }
   }
   else
   if ($mode==2) 
   {
    $gstk = mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' ORDER BY id");
    if (!$gstk) puterror("Ошибка при обращении к базе данных");
    while($list2 = mysql_fetch_array($gstk)) {
     if ($xls==1) {  
       $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(chr(++$exp_cnt+65).'1', $list2['name']);
     } 
     else
      echo "<td width='150' align='center'><p>".$list2['name']."</p></td>";
    }
   }
  }
   
  if ($xls==1) {  
   $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(chr(++$exp_cnt+65).'1', 'Сумма средних баллов (рейтинг)')
            ->setCellValue(chr(++$exp_cnt+65).'1', 'Средний балл по рейтингу')
            ->setCellValue(chr(++$exp_cnt+65).'1', 'Проведено экспертиз');
  } 
  else
  {
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Сумма средних баллов (рейтинг)</p></td>";
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Средний балл по рейтингу</p></td>";
   echo "<td width='100' style='overflow:hidden;' align='center'><p>Проведено экспертиз</p></td></tr>";
  }
  while($list = mysql_fetch_array($lst))
  {
    $n++;
    if ($xls==1) {  
     $exp_cnt = 2;
     $nn = $n+1;
     $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$nn, ''.$n);
    } 
    else
    {
     if ($n<11)
      echo "<tr bgcolor='#FFFFFF'>";
     else
      echo "<tr>";
     echo "<td align='center'><p class=zag2>".$n."</p></td>";
    }
    
if ($xls==0) {  
    $res3cnt=mysql_query("SELECT count(*) FROM shablondb WHERE memberid='".$list['id']."' AND LENGTH(info)>0");
    $param3cnt = mysql_fetch_array($res3cnt);
     
     if ($param3cnt['count(*)']>0)
     {

?> 
<script type="text/javascript">
		$(document).ready(function() {
    	$("#fancybox<?php echo $list['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewcomment3&id=<? echo $list['id'] ?>',
					type : 'iframe',
					padding : 5
				});
			});
		});
</script>
<?
      $commstr = "<font size='-2'>
      <a title='Комментарии экспертов' id='fancybox".$list['id']."' href='javascript:;'>
      Комментарии экспертов (".$param3cnt['count(*)'].")</a>
      </font>";
     } 
     else 
      $commstr = "";
}

    if ($xls==1) {  
     $nn = $n+1;
     $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$nn, 'Проект №'.$list['id'].'. '.$list['info']);
    } 
    else
    {
     if (isUrl($list['info']))
     {
      if (preg_match("/http:/i", $list['info'])>0)
       echo "<td width='500' style='overflow:hidden;'><p class=zag2><a href='".$list['info']."' target='_blank'>Проект №".$list['id'].". ".$list['info']."</a></p>".$commstr."</td>";
      else
       echo "<td width='500' style='overflow:hidden;'><p class=zag2><a href='http://".$list['info']."' target='_blank'>Проект №".$list['id'].". ".$list['info']."</a></p>".$commstr."</td>";
     }
 
     else
     {
      if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
       echo "<td width='500' style='overflow:hidden;'><p class=zag2><a href='editproject&id=".$list['id']."' target='_blank' title='Изменить проект'>№".$list['id'].". ".$list['info']."</a></p>".$commstr."</td>";
      else
       echo "<td width='500' style='overflow:hidden;'><p class=zag2>Проект №".$list['id'].". ".$list['info']."</p>".$commstr."</td>";
     }
    }
    
    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
    {
     $res2=mysql_query("SELECT * FROM users WHERE id='".$list['userid']."'");
     $r2 = mysql_fetch_array($res2);
    if ($xls==1) {  
     $exp_cnt = 4;
     $nn = $n+1;
     $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C'.$nn, $r2['userfio'])
            ->setCellValue('D'.$nn, $r2['job']);
    } 
    else
    {
     echo "<td width='100' style='overflow:hidden;'><p class=zag2>".$r2['userfio']."</p></td>";
     echo "<td width='100' style='overflow:hidden;'><p class=zag2>".$r2['job']."</p></td>";
    }
    }
    
    $lst3 = mysql_query("SELECT expertid FROM proexperts WHERE proarrid='".$selpaid."' ORDER BY expertid");
    if (!$lst3) puterror("Ошибка при обращении к базе данных");
    $i=0;
    $newprcent = 0;
    
    while($list3 = mysql_fetch_array($lst3))
     {
      $lst4 = mysql_query("SELECT * FROM shablondb WHERE userid='".$list3['expertid']."' AND memberid='".$list['id']."'");
      if (!$lst4) puterror("Ошибка при обращении к базе данных");
      $list4 = mysql_fetch_array($lst4);
      $yespercent = false;
      if ($list4['maxball']!=0) 
      {
       $percent = ($list4['ball'] / $list4['maxball']) * $ocenka;  
       $i++;
       $yespercent = true;
      }
      else
       $percent = 0;
      
      $newprcent = $newprcent + $percent; 
      if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
      {
       if ($mode==1) 
       {

    if ($xls==1) {  
       $nn = $n+1;
       if ($percent == 0)
       { 
        if ($yespercent)
         $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, $list4['ball']." из ".$list4['maxball']." (".round($percent,2).")");
        else
         $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, '-');
       }
       else     
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, $list4['ball']." из ".$list4['maxball']." (".round($percent,2).")");
    } 
    else
    {
       
       if ($percent == 0)
       { 
        if ($yespercent)
         echo "<td align='center' width='150'><p class=zag2>".$list4['ball']." из ".$list4['maxball']." (".round($percent,2).")</p></td>";
        else
         echo "<td align='center' width='150'>-</td>";
       }
       else 
        echo "<td align='center' width='150'><p class=zag2>".$list4['ball']." из ".$list4['maxball']." (".round($percent,2).")</p></td>";
       }
     }  
      } 
     }
    
     if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
      {
       if ($mode==2) 
       {
         $gstk = mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' ORDER BY id");
         if (!$gstk) puterror("Ошибка при обращении к базе данных");
         while($listg = mysql_fetch_array($gstk)) {
          $s1 = mysql_query("SELECT s.* FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$selpaid."' AND p.id='".$list['id']."'");
          if (!$s1) puterror("Ошибка при обращении к базе данных");
          $countb=0;
          while($list_s1 = mysql_fetch_array($s1)){
           $query4=mysql_query("SELECT SUM(ball) FROM leafs WHERE shablonid='".$listg['id']."' AND shablondbid='".$list_s1['id']."'");
           $r4 = mysql_fetch_array($query4);
           $countb += $r4['SUM(ball)'];
          }
    if ($xls==1) {  
        $nn = $n+1;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, ''.$countb);
    } 
    else
          echo "<td align='center' width='150'><p>".$countb."</p></td>";
         }
       }
      } 

    if ($xls==1) {  
     $nn = $n+1;
     if ($list['maxball']>0)
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, ''.round($list['maxball'],2));
     elseif ($newprcent>0)
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, ''.round($newprcent,2));
     else
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, '-');
    } 
    else
    {

    if ($list['maxball']>0)
     echo "<td align='center' width='100'><p class=zag2>".round($list['maxball'],2)."</p></td>";
    elseif ($newprcent>0)
     echo "<td align='center' width='100'><p class=zag2>".round($newprcent,2)."</p></td>";
    else
     echo "<td align='center' width='100'><p class=zag2>-</p></td>";

    }
    
    if ($i>0) 
    {
     if ($list['maxball']>0)
      $aball = $list['maxball'] / $i;
     elseif ($newprcent>0)
      $aball = $newprcent / $i;
     else 
      $aball = 0;
    }
    else
     $aball = 0;

    if ($xls==1) {  
     $nn = $n+1;
     if ($aball>0)
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, ''.round($aball,2));
     else
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, '-');
     if ($aball>0)
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, ''.$i);
     else
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(''.chr(++$exp_cnt+65).$nn, '-');
    } 
    else
    {

    if ($aball>0)
     echo "<td align='center' width='100'><p class=zag2>".round($aball,2)."</p></td>";
    else  
     echo "<td align='center' width='100'>-</td>";

    if ($aball>0)
     echo "<td align='center' width='100'><p class=zag2>".$i."</p></td>";
    else  
     echo "<td align='center' width='100'>-</td>";
    
    echo "</tr>";
    }

  }

  if ($xls==0)   
  {
     echo "</table></div>";
  
  // Выводим ссылки на предыдущие и следующие 
  $total = mysql_fetch_array($tot);
  $count = $total['count(*)'];
  if ($xls==0)   
   echo "<p align='center'>";
  $i=1;
  $start2 = 0;
  if ($count>$pnumber)
  while ($count > 0)
  {
    if ($start==$start2)
    {
       echo $i."&nbsp;";
    }
    else {
     ?>
     <input type="button" name="close" value="<? echo $i;?>" onclick="document.location='report2&mode=<? echo $mode; ?>&paid=<? echo $selpaid; ?>&start=<? echo $start2; ?>'">&nbsp;
     <?
    }
    $i++;
    $count = $count - $pnumber;
    $start2 = $start2 + $pnumber; 
  }

  echo "</p>";
  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
   echo "<p><a href='report2&mode=".$mode."&paid=".$selpaid."&xls=1'><img src='img/xls.gif' height='20'> Экспорт рейтинга в Microsoft Excel</a></p>";
  echo "<p><b>Пояснения к таблице итогового рейтинга.</b></p>";
  echo "<p>Итоговый рейтинг формируется в режиме реального времени в процессе оценки проектов экспертами. По мере того, как эксперты 
  заполняют экспертные листы, рейтинг автоматически пересчитывается. На оценку всех проектов отводится определенный срок. 
  После экспертизы всех проектов сформируется окончательный рейтинг.</p>";
  echo "<p>Итоговый рейтинг формируется по показателю <b>Сумма средних баллов</b>, который вычисляется на основании количества проведенных 
  экспертиз по данному проекту, полученному среднему баллу (обычно по стобалльной системе) по каждой экспертизе и суммарному итогу всех средних баллов.</p>";
  echo "<p>Показатель <b>Средний балл по рейтингу</b> вычисляется как отношение суммы средних баллов к количеству проведенных экспертиз.</p>"; 
  include "social.php";

  echo "</center><p></p></body></html>";
  }
  else
  {

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Report');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

  }
 } 
 
} else die; 
?>

