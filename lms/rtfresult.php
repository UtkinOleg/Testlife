<?php
if(!defined("USER_REGISTERED")) die;

  include "config.php";
  require_once "resultblocker.php";

  function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
  }

  function str2url($str) {
    $str = rus2translit($str);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = trim($str, "-");
    return $str;
  }
  
  $testid = $_GET["tid"];
  $begindate = $_GET["bdate"];
  $enddate = $_GET["edate"];
  $groupid = $_GET["grid"];
  $folderid = $_GET["frid"];
  $folder_parent_id = $_GET["frpid"];

  $begindate1 = $_GET["bdate"];
  $enddate1 = $_GET["edate"];

  if(defined("IN_SUPERVISOR"))
   $sum0 = getTestCount($mysqli);

  if (!empty($testid))
  {
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM testgroups WHERE signature = '".$testid."' LIMIT 1;");
   $test = mysqli_fetch_array($sql);
   $testid = $test['id'];
   $testname = $test['name'];
   mysqli_free_result($sql);
  }
  
  if (!empty($begindate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $begindate.' 00:00:00'); // Начало суток
   $begindate = $DateTime1->format('Y-m-d H:i:s');
  }
  
  if (!empty($enddate))
  {
   $DateTime1 = DateTime::createFromFormat('d.m.Y H:i:s', $enddate.' 23:59:59');  // Конец суток
   $enddate = $DateTime1->format('Y-m-d H:i:s');
  }

  if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
  {
   $userid = $_POST["uid"];
  }
  else
  {
   if (defined("IN_USER"))
   {
    $userid = USER_ID;
   }
   else
   {
    die;
   }
  }
   
  $selector = "";
                                                                              
  if(defined("IN_ADMIN"))
  {
   if (!empty($folderid) and empty($groupid) and empty($folder_parent_id))
   {
     $selector = ", usergroups as g, useremails as e, users as u WHERE g.folderid='".$folderid."' AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   } 
   else
   if (empty($folderid) and empty($groupid) and !empty($folder_parent_id))
   {
     $selector = ", folders as f, usergroups as g, useremails as e, users as u WHERE f.parentid='".$folder_parent_id."' AND g.folderid=f.id AND e.usergroupid=g.id AND e.email=u.email AND s.userid=u.id";
   }
  } 
  
  if (!empty($groupid) and empty($folderid) and empty($folder_parent_id))
  {
     $selector = ", useremails as e, users as u WHERE e.usergroupid='".$groupid."' AND e.email=u.email AND s.userid=u.id";
  }
  
  if (!empty($testid))
  {
    if (strlen($selector)>0)
      $selector .= " AND s.testid='".$testid."'";
    else
      $selector = " WHERE s.testid='".$testid."'";
  }
  
  if (!empty($begindate) and !empty($enddate))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
    else
     $selector = " WHERE s.resdate>='".$begindate."' AND s.resdate<='".$enddate."'";
   } 
  
  if (!empty($userid))
   {
    if (strlen($selector)>0)
     $selector .= " AND s.userid='".$userid."'";
    else
     $selector = " WHERE s.userid='".$userid."'";
   } 
  
  $order1 = "ORDER BY s.id DESC;";
  
  if(defined("IN_ADMIN"))
   $q = "SELECT s.* FROM singleresult as s".$selector." ".$order1; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector == "")
    $q = "SELECT s.* FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
   else
    $q = "SELECT s.* FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID." ".$order1; 
  }
  else
  if(defined("IN_USER"))
  {
   if ($selector == "")
    $q = "SELECT s.* FROM singleresult as s WHERE s.userid=".USER_ID." ".$order1; 
   else
    $q = "SELECT s.* FROM singleresult as s".$selector." AND s.userid=".USER_ID." ".$order1; 
  }
  
  if(defined("IN_ADMIN"))
   $q1 = "SELECT count(*) FROM singleresult as s".$selector." LIMIT 1;"; 
  else
  if(defined("IN_SUPERVISOR"))
  {
   if ($selector == "")
    $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID." LIMIT 1;"; 
   else
    $q1 = "SELECT count(*) FROM testgroups as t, singleresult as s".$selector." AND s.testid=t.id AND t.ownerid=".USER_ID." LIMIT 1;"; 
  }
  else
  if(defined("IN_USER"))
  {
   if ($selector == "")
    $q1 = "SELECT count(*) FROM singleresult as s WHERE s.userid=".USER_ID." LIMIT 1;"; 
   else
    $q1 = "SELECT count(*) FROM singleresult as s".$selector." AND s.userid=".USER_ID." LIMIT 1;"; 
  }

  $rows = array();
  $tot = mysqli_query($mysqli,$q1);
  $total = mysqli_fetch_array($tot);
  $counter = $total['count(*)'];
  mysqli_free_result($tot);
  
 
  $res = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . $q);

  $dir = dirname(__FILE__);
  require_once $dir . '/lib/PHPRtfLite.php';

  PHPRtfLite::registerAutoloader();

  $rtfs = new PHPRtfLite();

  $sect = $rtfs->addSection();
  $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
  $sect->writeText('<i>Результаты тестирования <b>'.$testname.' c '.data_convert($begindate1, 1, 0, 0).' по '.data_convert($enddate1, 1, 0, 0).'</b></i>',
  new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));
  $sect->writeText(' ', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));

$table = $sect->addTable();
$table->addRows(1);
$table->addColumnsList(array(1,4,2,2,2,2,2,2,2,2));

$cell = $table->getCell(1,1);
$cell->writeText("№");
$cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
$cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

$cell = $table->getCell(1,2);
$cell->writeText("Участник");
$cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
$cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,3);
 $cell->writeText("Группа");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,4);
 $cell->writeText("Тест");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,5);
 $cell->writeText("Всего вопросов");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,6);
 $cell->writeText("Правильно отвечено");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,7);
 $cell->writeText("Баллов получено");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,8);
 $cell->writeText("Уровень знаний");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,9);
 $cell->writeText("Оценка");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

 $cell = $table->getCell(1,10);
 $cell->writeText("Дата и время");
 $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
 $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

$borderTop = new PHPRtfLite_Border($rtfs);
$borderTop->setBorderTop(new PHPRtfLite_Border_Format(1, '#000'));
$table->setBorderForCellRange($borderTop, 1, 1, 1, $columnCount);

$borderBottom = new PHPRtfLite_Border($rtfs);
$borderBottom->setBorderBottom(new PHPRtfLite_Border_Format(1, '#000'));
$table->setBorderForCellRange($borderBottom, $rowCount, 1, $rowCount, $columnCount);
 
  $i=0;
  $allrightball = 0;
  $allball = 0;
  if ($counter>0)
  {

  while($member = mysqli_fetch_array($res))
  {
    $table->addRow(1);
    $i++;
    $cell = $table->getCell($i+1, 1);
    $cell->writeText($i);
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

    $test = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='".$member['testid']."' LIMIT 1;");
    $testdata = mysqli_fetch_array($test);
    $testtype = $testdata['testtype'];
    $testsignature = $testdata['signature'];
    $testname = $testdata['name'];
    mysqli_free_result($test);

    if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
    {
     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM users WHERE id='".$member['userid']."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     $fromuseremail = $fromuser['email'];
     $uname = $fromuser['userfio'];
     $fromuserid = $fromuser['id'];
     mysqli_free_result($from);

     $from = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT g.id, g.name FROM useremails as e, usergroups as g WHERE g.id=e.usergroupid AND e.email='".$fromuseremail."' LIMIT 1;");
     $fromuser = mysqli_fetch_array($from);
     $fromgroupid = $fromuser['id'];
     $fromgroupname = $fromuser['name'];
     mysqli_free_result($from);
    
     $cell = $table->getCell($i+1, 2);
     $cell->writeText($uname.' ('.$fromuseremail.')');
     $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
     $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
     
     $cell = $table->getCell($i+1, 3);
     $cell->writeText($fromgroupname);
     $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
     $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
    }
    
    $cell = $table->getCell($i+1, 4);
    $cell->writeText($testname);
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
    
    $cell = $table->getCell($i+1, 5);
    $cell->writeText($member['allq']);
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

    $blocked = false;
    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
      $blocked = isBlocked($mysqli, $sum0, $member['id']);

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
      {
       $cell = $table->getCell($i+1, 6);
       $cell->writeText('0');
       $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
       $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
      }
      else  
      {
       $cell = $table->getCell($i+1, 6);
       $cell->writeText($member['rightq']);
       $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
       $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
      }    
    }
    else
    {
       $cell = $table->getCell($i+1, 6);
       $cell->writeText($member['rightq']);
       $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
       $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
    }

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
      {
        $cell = $table->getCell($i+1, 7);
        $cell->writeText('-');
        $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
      }
      else  
      {    
        $cell = $table->getCell($i+1, 7);
        $cell->writeText($member['rightball']." из ".$member['allball']);
        $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $allrightball += $member['rightball'];
        $allball += $member['allball'];
      }
        
    }
    else
    {
        $cell = $table->getCell($i+1, 7);
        $cell->writeText($member['rightball']." из ".$member['allball']);
        $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $allrightball += $member['rightball'];
        $allball += $member['allball'];
    }

    if(defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
    {
      if ($blocked)
        $percent = 0;
      else  
        $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    }
    else
     $percent = (int) floor($member['rightball'] / $member['allball'] * 100);
    
    $cell = $table->getCell($i+1, 8);
    $cell->writeText($percent."%");
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

    if ($percent<45) $ocenka = 2;
    else
    if ($percent>=45 and $percent<=69) $ocenka = 3;
    else
    if ($percent>=70 and $percent<=85) $ocenka = 4;
    else
    $ocenka = 5;
    $cell = $table->getCell($i+1, 9);
    $cell->writeText($ocenka);
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

    $cell = $table->getCell($i+1, 10);
    $cell->writeText(data_convert ($member['resdate'], 1, 1, 0));
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

  }                                 
 
  $percent = (int) floor($allrightball / $allball * 100);

  $table->addRow(1);
  $cell = $table->getCell($i+2, 1);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

  $cell = $table->getCell($i+2, 2);
  $cell->writeText('Итоги');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

  $cell = $table->getCell($i+2, 3);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
  $cell = $table->getCell($i+2, 4);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
  $cell = $table->getCell($i+2, 5);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
  $cell = $table->getCell($i+2, 6);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

  $cell = $table->getCell($i+2, 7);
  $cell->writeText($allrightball." из ".$allball);
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
  $cell = $table->getCell($i+2, 8);
  $cell->writeText($percent."%");
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

    if ($percent<45) $ocenka = 2;
    else
    if ($percent>=45 and $percent<=69) $ocenka = 3;
    else
    if ($percent>=70 and $percent<=85) $ocenka = 4;
    else
    $ocenka = 5;
    $cell = $table->getCell($i+2, 9);
    $cell->writeText($ocenka);
    $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
    $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
  
  
  $cell = $table->getCell($i+2, 10);
  $cell->writeText('');
  $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
  $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

  mysqli_free_result($res);
}

header('Content-Type: application/octet-stream');

if (!empty($testid))
 $filename = str2url("results_".$testname).".rtf";
else
 $filename = str2url("result").".rtf";

header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
header('Content-Transfer-Encoding: binary');

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$rtfs->save('php://output');

