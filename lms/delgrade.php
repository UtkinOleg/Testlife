<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
  // �������� ���������� � ����� ������
  include "config.php";
  include "func.php";

  $paid = $_GET["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("������ ��� ��������� � ���� ������");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // ��������� SQL-������
  $query = "DELETE FROM grades
            WHERE id=".$_GET["id"];
  // ������� ��������� � ��������� ������ $id
  if(mysql_query($query))
  {
      // ����� �������� �������� ��������� ��������� �
      // ����������� ����������������� �������� �����
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=grades&paid=".$_GET["paid"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  // � ������ ������� ������� ��������� �� ������
  else puterror("������ ��� ��������� � ���� ������");
  }
} else die;  
?>