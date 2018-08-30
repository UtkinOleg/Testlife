<?
 
$inv_id = $_REQUEST["InvId"];
$s = "Вы отказались от оплаты. ID# $inv_id\n";
echo '<script language="javascript">';
echo 'alert('.$s.');';
echo '</script>';
print "<HTML><HEAD>\n";
print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=ts'>\n";
print "</HEAD></HTML>\n";

?>
