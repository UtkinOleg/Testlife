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
            if (!empty($post['photoname']))
             $json['picurl'][] = $site."/file_thumb_real.php?id=".$post['id']."&w=130&h=130"; 
            else
             $json['picurl'][] = '';      
            $json['comment'][] = strip_tags($post['comment'], '<p><a>');  
            $json['name'][] = $post['name'];  
            $json['topdate'][] = " с ".date("d-m-Y", strtotime($post['startdate']))." по ".date("d-m-Y", strtotime($post['stopdate'])); 
            $json['id'][] = $post['id'];  
        
$json['script'][] = "<script>"
."$(document).ready(function() {"
."  $('#top".$post['id']."').click(function() {"
."				$.fancybox.open({"
."					href : 'report2&mode=0&paid=".$post['id']."',"
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