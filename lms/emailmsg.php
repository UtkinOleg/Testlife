<?php
  
  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }
  
  function msghead($fio, $site)
  {
    $s='<body style="margin:0; padding:0;">
   <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background-color:#F8F8F8;" align="center"><tr>
     <td style="margin:0; padding:0;">
     <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;" align="center"><tr>
      <td height="10"/></tr>
      <tr><td align="center">
      <h1 style="margin-top:10px;margin-bottom:10px;">
        <a href="'.$site.'" title="Создание тестов онлайн" target="_blank"><img src="'.$site.'/img/testlife.png"></a>
      </h1>
     </td></tr>
    </table>
   </td></tr>
  </table>
   <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;background-color:#FFF;" align="center"><tr>
   <tr><td> 
      <table cellpadding="0" cellspacing="0" width="500" style="padding: 0 1em; font: 14px / 1.4 \'Helvetica\', \'Arial\', sans-serif;color:#000;border-collapse:collapse;margin:0">
      <tr>
       <td align="center" colspan="3" valign="top">
        <br><p style="font-size:1.5em;">Здравствуйте '.$fio.'!</p><br>
       </td>
      </tr>
      <tr>
       <td align="justify" colspan="3" valign="bottom">';
    return $s;
  }
  
  function msgtail($site)
  {
   $s='<p>Это информационное сообщение - отвечать на него не нужно.</p>
        <hr>
        <p>С уважением, <a href="'.$site.'" target="_blank">TestLife</a></p>
       </td>
      </tr>
      </table>
   </td></tr>
   </table>
   </body>';
   return $s;
  }

?>