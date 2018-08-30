<?php
if(defined("IN_ADMIN")) 
{  
  include "config.php";

  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }

  if (defined("IN_ADMIN")) 
   $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM helppages LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($countg); 

  if ($count_gs>0)
  {
  $s= "<div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='100'><i title='Дата создания' class='fa fa-calendar fa-lg'></i></td>
          </thead>
          <tbody>";

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages ORDER BY id;");

  $i=0;
  while($member = mysqli_fetch_array($sql))
  {
    if ($member['news'])
     $news = '<i class="fa fa-newspaper-o fa-fw"></i>';
    else
     $news = '<i class="fa fa-question fa-fw"></i>';
     
    $s.= "<tr><td witdh='30'><p>".++$i."&nbsp;".$news."</p></td>"; 
    $s.= "<td width='300'>
    <p><a href='h&id=".$member['id']."' target='_blank'>" .$member['name']. "</a> <a onclick='dialogOpen(\"edhelppage&m=e&id=".$member['id']."\",0,0)' href='javascript:;' title='Редактировать страницу'><i class='fa fa-cog fa-lg'></i></a>";
    
    if ($count_res==0) {
      $s.= '&nbsp;<a href="javascript:;" onclick="$(\'#DelHelpPagehiddenInfoId\').val('.$member['id'].');$(\'#DelHelpPage\').modal(\'show\');" title="Удалить страницу"><i class="fa fa-trash fa-lg"></i></a>';
    }
    
    $s.= '</p></td>';

    $s.=  "<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s.= "</tr>";
  }
  mysqli_free_result($sql);

  $s.= "</tbody></table></div>";
  }

  $json['content'] = $s;  
  $json['ok'] = '1';  
  
} else 
   $json['ok'] = '0'; 
echo json_encode($json);
?>
