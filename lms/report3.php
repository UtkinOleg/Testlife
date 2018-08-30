<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
   {  
   
   // Получаем соединение с базой данных
  include "config.php";
  include "func.php";
  
  $mode = $_GET["mode"];

  $selpaid = $_GET["paid"];
  if (empty($selpaid)) die;

  // Найдем оценку проекта
  $res5=mysql_query("SELECT ownerid, openproject, ocenka, name FROM projectarray WHERE id='".$selpaid."' LIMIT 1;");
  if(!$res5) puterror("Ошибка 3 при получении данных.");
  $proarray = mysql_fetch_array($res5);
  $openproject = $proarray['openproject'];
  $ocenka = $proarray['ocenka'];
  $paname = $proarray["name"];
  
  // Посмотрим открытый ли проект
  if ($openproject==1 || defined("IN_ADMIN") || defined("IN_SUPERVISOR")) 
  {

  $title=$titlepage="Анализ критериев оценки проекта &#8220;".$paname."&#8221;";

  include "topadmin.php";

  if((defined("IN_SUPERVISOR") and $proarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
  {       


  ?>
      <script type="text/javascript" src="scripts/jquery.tableSort.js"></script>

<div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Для сортировки критериев по значимости - нажмите в таблице Рейтинг (сумма) или Рейтинг (%)</p>
            	</div>
            </div><p></p>
      <p>
      <table align='center' width='100%' id='example1_table' class='table' border="0" cellpadding=5 cellspacing=0>
         <thead>
          <tr class="tableheader" align="center">
              <th><p><a class='bold' style="color:#fff;" href="javascript:$('#example1_table').sortTable({onCol: 1, keepRelationships: true, sortType: 'numeric'})">№</a></p></th>
              <th><p><font face="Tahoma,Arial" size="-1" style="color:#fff;">Наименование критерия</font></p></th>
              <th><p><font face="Tahoma,Arial" size="-1" style="color:#fff;">Группа</font></p></th>
              <th><p><a class='bold' style="color:#fff;" href="javascript:$('#example1_table').sortTable({onCol: 4, keepRelationships: true, sortType: 'numeric'})">Рейтинг (сумма)</a></p></th>
              <th><p><a class='bold' style="color:#fff;" href="javascript:$('#example1_table').sortTable({onCol: 5, keepRelationships: true, sortType: 'numeric'})">Рейтинг (%)</a></p></th></tr>
         </thead> 
         <tbody>    
  <?           
   
    $res2=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$selpaid."' ORDER BY id");
    if (!$res2) puterror("Ошибка при обращении к базе данных");
    $subtotal = 0; 
    while($group = mysql_fetch_array($res2))
    { 
     $res3=mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' AND groupid='".$group['id']."' ORDER BY id");
     if (!$res3) puterror("Ошибка при обращении к базе данных");
     while($param = mysql_fetch_array($res3))
     { 
      $lst = mysql_query("SELECT * FROM shablondb ORDER BY ball DESC");
      if (!$lst) puterror("Ошибка при обращении к базе данных");
      while($list = mysql_fetch_array($lst))
      {
       // Покажем проект только если он входит в выбранный шаблон
       $res1=mysql_query("SELECT * FROM projects WHERE id='".$list['memberid']."'");
       $r1 = mysql_fetch_array($res1);
       $paid = $r1['proarrid'];
       if ($selpaid == $paid) {
        $query4=mysql_query("SELECT * FROM leafs WHERE shablonid='".$param['id']."' AND shablondbid='".$list['id']."'");
        $r4 = mysql_fetch_array($query4);
        $subtotal+=$r4['ball']; 
       } 
      }
     } 
    }

    $i=1;
    $res2=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$selpaid."' ORDER BY id");
    if (!$res2) puterror("Ошибка при обращении к базе данных");
    $i=1;
    while($group = mysql_fetch_array($res2))
    { 
     $res3=mysql_query("SELECT * FROM shablon WHERE proarrid='".$selpaid."' AND groupid='".$group['id']."' ORDER BY id");
     if (!$res3) puterror("Ошибка при обращении к базе данных");
     while($param = mysql_fetch_array($res3))
     { 
      echo"<tr bgcolor='#F0F0F0'>";
      echo"<td align='center'>".$i."</td><td><font face='Tahoma,Arial' size='-1'>".$param['name']."</font></td>";
      echo"<td><font face='Tahoma,Arial' size='-1'>".$group['name']."</font></td>";
      $summak = 0;
      $lst = mysql_query("SELECT * FROM shablondb ORDER BY ball DESC");
      if (!$lst) puterror("Ошибка при обращении к базе данных");
      while($list = mysql_fetch_array($lst))
      {
       // Покажем проект только если он входит в выбранный шаблон
       $res1=mysql_query("SELECT * FROM projects WHERE id='".$list['memberid']."'");
       $r1 = mysql_fetch_array($res1);
       $paid = $r1['proarrid'];
       if ($selpaid == $paid) {
        $query4=mysql_query("SELECT * FROM leafs WHERE shablonid='".$param['id']."' AND shablondbid='".$list['id']."'");
        $r4 = mysql_fetch_array($query4);
        $summak+=$r4['ball']; 
       } 
      }
      echo"<td align='center'>".$summak."</td>";
      echo"<td align='center'>".round($summak/$subtotal*100,2)." %</td>";
      echo"</tr>";
      $i+=1;
     } 
   
    }  
  echo "</tbody></table></p>";
  
  include "bottomadmin.php";
  }
 } 
}
else die; 
?>

