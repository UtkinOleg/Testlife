<?php
if(!defined("IN_ADMIN")) die;  
  // �������� ���������� � ����� ������
  include "config.php";
  include "func.php";
  // ��������� SQL-������
  $query = "DELETE FROM users
            WHERE id=".$_GET["id"];
  // �������  $id
  if(mysql_query($query))
  {
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=experts&start=".$_GET["start"]."'>\n";
      print "</HEAD></HTML>\n";
  }
  else puterror("������ ��� ��������� � ���� ������");
?>