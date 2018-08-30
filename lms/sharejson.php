<?
include "config.php";


$id = intval($_POST['project']);  

$secret = "hubble in space";

$s= "<a target='_blank' href='editproject&sl=".md5($id.$secret)."'>Ссылка на редактирование</a>"; 
$json['content'] = htmlspecialchars_decode($s);  
$json['ok'] = '1';  

echo json_encode($json);
?>  