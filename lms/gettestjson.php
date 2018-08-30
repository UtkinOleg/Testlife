<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
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

  $kid = $_POST["kid"];

  if (defined("IN_ADMIN")) 
   $gst = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE proarrid='".$kid."' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $gst = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE proarrid='".$kid."' AND userid='".USER_ID."' ORDER BY id DESC;");
  else
   die;

  $s2="
      <table align='center' width='100%' style='background-color: #FFFFFF;'' class='bodytable' border='0' cellpadding='3' cellspacing='0' bordercolorlight='gray' bordercolordark='white'>
          <tr class='tableheaderhide' style='color: #fff;'>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'><p>Тесты <a onclick='dialogOpen(\"createtest&kid=".$kid."\",0,0)' title='Создать новый тест' href='javascript:;'><i class='fa fa-plus fa-lg'></i></a></p></td>
              <td align='center' witdh='50'><i title='Итоговый балл' class='fa fa-calculator fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Время тестирования' class='fa fa-clock-o fa-lg'></i></td>
              <td align='center' witdh='200'><i title='Группы вопросов' class='fa fa-question-circle fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Просмотр теста' class='fa fa-play-circle fa-lg'></i></td>
              <td align='center' witdh='50'><i title='Результаты тестирования' class='fa fa-bar-chart fa-lg'></i></td>
              <td align='center' witdh='100'><i title='Дата создания теста' class='fa fa-calendar fa-lg'></i></td>
          </tr>";         

  $i=0;
  while($member = mysqli_fetch_array($gst))
  {

    $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult WHERE testid='".$member['id']."' LIMIT 1;");
    $cnttests = mysqli_fetch_array($counttest);
    $count_res = $cnttests['count(*)'];
    mysqli_free_result($counttest); 

    $s2.= "<tr><td witdh='30'><p>".++$i."</p></td>";
    $s2.= "<td width='300'>
    <p>".$member['name']." <a onclick='edittest(".$kid.",".$member['id'].")' href='javascript:;' title='Редактировать тест'><i class='fa fa-cog fa-lg'></i></a>";
    if ($count_res==0) {
      $s2.='&nbsp;<a href="javascript:;" onclick="deltest('.$kid.','.$member['id'].')" title="Удалить тест"><i class="fa fa-trash fa-lg"></i></a>';
    }
    $s2.= '</p></td>';

      $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='".$member['id']."' ORDER BY id");
      if (!$td) puterror("Ошибка при обращении к базе данных");
      $qc=0;
      $tt=0;
      $sumball=0;
      $b="";
      $grq = 0;
      while($testdata = mysqli_fetch_array($td))
      {
       $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name, singleball, singletime FROM questgroups WHERE id='".$testdata['groupid']."' LIMIT 1");
       $questgroup = mysqli_fetch_array($qg);
       if ($testdata['random']) 
        $c = "<b>".$questgroup['name']."</b>"; 
       else
        $c = $questgroup['name'];

       if ($count_res>0)
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> ".$c."
        - ".$testdata['qcount']." вопрос(ов)</p>";
       else
       if ($member['enable'])
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> ".$c."
        - ".$testdata['qcount']." вопрос(ов)</p>";
       else
        $b .= "<p><i class='fa fa-question-circle fa-lg'></i> <a title='Редактировать параметры группы в тесте' onclick='dialogOpen(\"editgroupintest&kid=".$kid."&id=".$testdata['id']."\",1000,400)' href='javascript:;'>".$c."</a>
        - ".$testdata['qcount']." вопрос(ов) <a href='#' onClick=\"DelWindowPaid(".$testdata['id']." ,".$paid.",'deltestdata','testoptions','группу в тесте')\" title='Удалить вопросы из теста'>
        <i class='fa fa-trash fa-lg'></i></a></p>";
       
       $qc += $testdata['qcount'];
       $tt += $questgroup['singletime']*$testdata['qcount'];
       $sumball += $questgroup['singleball']*$testdata['qcount'];
       $grq++;
      }
      mysqli_free_result($td); 
    
    $s2.="<td align='center'><p class=zag2>".$member['singleball']."</p>";
    $s2.="</td><td align='center'><p class=zag2>".$member['singletime']."</p></td>
    <td>";


    $s2.="<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s2.="</tr>";
  }
  mysqli_free_result($gst);
  $s2.="</table>";

  $json['content2'] = $s2;  
  
  if(!empty($json['content']))  { 
             $json['ok'] = '1';  
  } else {  
             $json['ok'] = '0'; 
  }      
  echo json_encode($json); 
} else die;
