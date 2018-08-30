<?
include "config.php";


$offset = intval($_POST['offset']);  


$sql = mysql_query("SELECT * FROM projectarray WHERE openproject=1 ORDER BY id DESC LIMIT $offset, 20;");


if(mysql_num_rows($sql)>0) { 

  $s="";
  while($member = mysql_fetch_array($sql))
  {
    
    if ($member['openexpert']>0)
     $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."' AND (status='published' OR status='inprocess' OR status='accepted')");
    else
     $tot2 = mysql_query("SELECT count(*) FROM projects WHERE proarrid='".$member['id']."' AND status='published'");
    $tot2cnt = mysql_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];

    if ($count2>0) 
    {

$s.="<script>"
."$(document).ready(function() {"
."$('#public".$member['id']."').click(function() {"
."				$.fancybox.open({"
."					href : 'opened&paname=".htmlspecialchars($member['name'])."&paid=".$member['id']."',"
."					type : 'iframe',"
."          width : 1000,"
."					padding : 5"
."				});"
."			});"
."    });"  
."</script>";

    $s.= "<div class='menu_glide_tops' itemscope itemtype='http://expert03.ru/opened&paname=".htmlspecialchars($member['name'])."&paid=".$member['id']."'>";
    $s.= "<table border='0'>";
    $s.= "<tr><td>";

     $s.= "<a id='public".$member['id']."' href='javascript:;'>";
    if (!empty($member['photoname']))
     {      
       if (stristr($member['photoname'],'http') === FALSE)
           $s.= "<div class='menu_glide_img'><img itemprop='photo' src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           $s.= "<div class='menu_glide_img'><img itemprop='photo' src='".$member['photoname']."' height='100'  class='leftimg'><div>"; 
     }
     
    $s.= "<p><h3><font face='Tahoma,Arial'>".$member['name']."</font></h3></p>";
    if ($count2>0) 
     $s.= "</a>";

    $s.= "<p>".$member['comment']."</p>
    <p><font size='-1'>Активность с <b>".date("d-m-Y", strtotime($member['startdate']))."</b> по 
    <b>".date("d-m-Y", strtotime($member['stopdate']))."</b>";
    $s.= " | Всего <b>".$count2."</b> опубликованных проект(ов)";

    if ($member['openexpert']>0)
     $s.= " | Открытая экспертиза</font></p>";
    else
    {
     $tot3 = mysql_query("SELECT count(*) FROM proexperts WHERE proarrid='".$member['id']."'");
     $tot3cnt = mysql_fetch_array($tot3);
     $count3 = $tot3cnt['count(*)'];
     $s.= " | <b>".$count3."</b> эксперт(ов)</font></p>";
    }
   $s.= "</td></tr>"; 
   $s.= "</table></div>";
   }
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