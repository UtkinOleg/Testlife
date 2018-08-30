<?
include "config.php";
$offset = intval($_POST['offset']);  
$sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE openproject=1 ORDER BY id DESC LIMIT $offset, 3;");
if(mysqli_num_rows($sql)>0) { 
        while($post = mysqli_fetch_assoc($sql)){  
            
            foreach($post AS $n=>$m)
            { 
             $post[$n] = $m; 
            }

    if ($post['openexpert']>0)
     $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE proarrid='".$post['id']."' AND (status='published' OR status='inprocess' OR status='accepted')");
    else
     $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE proarrid='".$post['id']."' AND status='published'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $count2 = $tot2cnt['count(*)'];
    mysqli_free_result($tot2); 

            if (!empty($post['photoname']))
             $json['picurl'][] = $site."/file_thumb_real.php?id=".$post['id']."&w=130&h=130"; 
            else
             $json['picurl'][] = '';      
            $json['comment'][] = strip_tags($post['comment'], '<p><a>');  
            $json['name'][] = $post['name'];  
            $json['topdate'][] = " с ".date("d-m-Y", strtotime($post['startdate']))." по ".date("d-m-Y", strtotime($post['stopdate'])); 
            $json['id'][] = $post['id'];  
            $json['count'][] = $count2;  

    if ($count2>0) 
    {            
$json['script'][] = "<script>"
."$(document).ready(function() {"
."  $('#public".$post['id']."').click(function() {"
."				$.fancybox.open({"
."					href : 'opened&paname=".htmlspecialchars($post['name'])."&paid=".$post['id']."',"
."					type : 'iframe',"
."          width : document.documentElement.clientWidth,"
."          height : document.documentElement.clientHeight,"
."          fitToView : true,"
."					padding : 5"
."				});"
."			});"
."    });"  
."</script>";        
     }
     else
$json['script'][] = "";
     
        }   
         if(count($json['name']))  { 
             $json['ok'] = '1';  
         } else {  
             $json['ok'] = '0'; 
         }      
        } else { 
           $json['ok']='3'; 
        }    
echo json_encode($json);
?>  