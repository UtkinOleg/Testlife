<?php

include "config.php";

ini_set('display_errors', 1);
error_reporting(E_ALL); // E_ALL

$dir = dirname(__FILE__);
require_once $dir . '/lib/Classes/PHPRtfLite/lib/PHPRtfLite.php';

// register PHPRtfLite class loader
PHPRtfLite::registerAutoloader();

$rtf = new PHPRtfLite();
$sect = $rtf->addSection();
$sect->writeText('<i><b>Протокол тестирования</b></i>.', new PHPRtfLite_Font(12), new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER));



header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment;filename="testprotocol.rtf"');
header('Cache-Control: max-age=0');
header('Content-Transfer-Encoding: binary');

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

// save rtf document
$rtf->save('php://output');




?>