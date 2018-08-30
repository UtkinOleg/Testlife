<?
include "config.php";


$offset = intval($_POST['offset']);  


$sql = mysql_query("SELECT * FROM projectarray WHERE openproject=1 ORDER BY id DESC LIMIT $offset, 5;");


if(mysql_num_rows($sql)>0) { 

  $s="";
  while($member = mysql_fetch_array($sql))
  {

$s.="<script>"
."$(document).ready(function() {"
."  $('#top".$member['id']."').click(function() {"
."				$.fancybox.open({"
."					href : 'report2&mode=0&paid=".$member['id']."',"
."					type : 'iframe',"
."          width : 1000,"
."					padding : 5"
."				});"
."			});"
."  $('#etop".$member['id']."').click(function() {"
."				$.fancybox.open({"
."					href : 'report2&mode=0&paid=".$member['id']."',"
."					type : 'iframe',"
."          width : 1000,"
."					padding : 5"
."				});"
."			});"
."    });"  
."</script>";

    $s.= "<div class='menu_glide_tops' itemscope itemtype='http://expert03.ru/report2&mode=0&paid=".$member['id']."'>";
    $s.= "<table border='0'>";
    $s.= "<tr><td>";

    $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."'");
    $tot2cnt = mysql_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];

    if (!empty($member['photoname']))
     {      
     
        	$s.= "<div class='ch-item'>";				
					$s.= "<div class='ch-info'>";
					$s.= "<div class='ch-info-front ch-img' style='background-image: url(http://expert03.ru/file_thumb_real.php?id=".$member['id']."&w=130&h=130)'></div>";
					$s.= "<div class='ch-info-back'>";
					$s.= "<h3>".date("d-m-Y", strtotime($member['startdate']))."<br>".date("d-m-Y", strtotime($member['stopdate']))."</h3>";
          if ($count2>0)
          { 
           if ($count2==1)
            $prn = "проект";
           else 
           if ($count2<5)
            $prn = "проекта";
           else 
           $prn = "проектов";
              
		 	 		 $s.= "<p>".$count2." ".$prn."<a id='top".$member['id']."' href='javascript:;'>Рейтинг</a></p>";
					}
          else
		 	 		 $s.= "<p>Нет проектов</p>";
          $s.= "</div></div></div>";	

     }
     
    if ($count2>0) 
     $s.= "<a id='etop".$member['id']."' href='javascript:;'>";
    $s.= "<p><h3><font face='Tahoma,Arial'><span itemprop='description'>".$member['name']." (рейтинг проектов)</span></font></h3></p>";
    if ($count2>0) 
     $s.= "</a>";

    $s.= "<p>".$member['comment']."</p>";
    $selpaid = $member['id'];
    $exlistname = $member['exlistname'];
    if (!empty($exlistname))
     $exlistname = "(".$exlistname.")";
    
  /*  if ($member['openexpert']>0)
    {

     // Проверим на дату начала и окончания экспертизы
     $date3 = date("d.m.Y");
     preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$ik);
     $day=$ik[1][0];
     $month=$ik[2][0];
     $year=$ik[3][0];
     $timestamp3 = (mktime(0, 0, 0, $month, $day, $year));
     $date1 = $member['checkdate1'];
     $date2 = $member['checkdate2'];
     $arr1 = explode(" ", $date1);
     $arr2 = explode(" ", $date2);  
     $arrdate1 = explode("-", $arr1[0]);
     $arrdate2 = explode("-", $arr2[0]);
     $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
     $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
     if ($timestamp3 >= $timestamp1 and $timestamp3 <= $timestamp2)
      {
       $s.= "<p align='center'><input type='button' style='font-size:120%;' name='addlist' value='Открытая экспертиза ".$exlistname."' onclick='document.location=\"addlist&paid=".$selpaid."&ext=0&sl=".$member['openexperturl']."\"'></p>";
       $ex = mysql_query("SELECT * FROM expertcontentnames WHERE proarrid='".$selpaid."' ORDER BY id");
       if (!$ex) puterror("Ошибка при обращении к базе данных");
       while($exmember = mysql_fetch_array($ex))
        $s.= "<p align='center'><input type='button' style='font-size:120%;' name='addlist' value='Открытая экспертиза (".$exmember['name'].")' onclick='document.location=addlist&paid=".$selpaid."&ext=0&exlist=".$exmember['id']."&sl=".$member['openexperturl']."></p>";
      }
      else
      {
        if ($timestamp3 > $timestamp2)
         $s.= "<p><b>Открытая экспертиза проектов завершена.</b></p>";
        else
         $s.= "<p><b>Дата начала проведения открытой экспертизы проектов: ".date("d-m-Y", strtotime($member['checkdate1']))."</b></p>";
      }
      
     }  */
         
/*    
    <p align='center'><font size='-2'>Активность с <b>".date("d-m-Y", strtotime($member['startdate']))."</b> по 
    <b>".date("d-m-Y", strtotime($member['stopdate']))."</b>";
    $s.= " | Всего <b>".$count2."</b> проект(ов)";

    $tot3 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$member['id']."'");
    $tot3cnt = mysql_fetch_array($tot3);
    $count3 = $tot3cnt['count(*)'];
    $s.= " | <b>".$count3."</b> эксперт(ов)</font></p>";
*/

   $s.= "</td></tr>"; 
   $s.= "</table></div>";

  }
  $json['content'] = htmlspecialchars_decode($s);  

         if(!empty($json['content']))  { 
             $json['ok'] = '1';  
         } else {  
             $json['ok'] = '0'; 
         }      
        } else { 
           $json['ok']='3'; 
        }    
echo json_encode($json);
?>  