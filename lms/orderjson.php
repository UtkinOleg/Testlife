<?
include "config.php";

$paid = intval($_POST['paid']);  
$userid = intval($_POST['userid']);  


$gst3 = mysql_query("SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1");
if ($gst3) 
{
$member = mysql_fetch_array($gst3);

// сумма заказа
$out_summ = $member['paysumma'];

// описание заказа
$inv_desc = "Услуга по размещению проекта ".$member['name'];

$mrh_login = "Expert03";
$mrh_pass1 = "jgkfnf1";
  
// номер заказа - id пользователя
   mysql_query("LOCK TABLES orders WRITE");
   mysql_query("SET AUTOCOMMIT = 0");
   $query = "INSERT INTO orders VALUES (0,$userid,0)";
   if(mysql_query($query)) 
    $inv_id = mysql_insert_id();
   else
    $inv_id = 0;
   mysql_query("COMMIT");
   mysql_query("UNLOCK TABLES");


// тип товара - номер модели
$shp_item = $paid;

// предлагаемая валюта платежа
$in_curr = "";

// язык
// language
$culture = "ru";

// кодировка
$encoding = "utf-8";

// формирование подписи
$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");

$json['order'] = "https://merchant.roboxchange.com/Handler/MrchSumPreview.ashx?".
      "MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id&IncCurrLabel=$in_curr".
      "&Desc=$inv_desc&SignatureValue=$crc&Shp_item=$shp_item".
      "&Culture=$culture&Encoding=$encoding";


echo json_encode($json);
}
?>  