<?
include "config.php";

$id = intval($_POST['id']);  

$sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM news WHERE id='".$id."' AND pagetype='news' AND published=1 LIMIT 1;");

if (mysqli_num_rows($sql)>0) { 
        while($post = mysqli_fetch_assoc($sql)){  
            foreach($post AS $n=>$m)
            { 
             $post[$n] = $m; 
            }
            if (!empty($post['content2']))
             $post['content2'] = htmlspecialchars_decode($post['content2']);  
            else    
             $post['content2'] = htmlspecialchars_decode($post['content']);  
            $json['content'][] = strip_tags($post['content2'], '<p><a>');  
        }   
         if(count($json['content']))  { 
             $json['ok'] = '1';  
         } else {  
             $json['ok'] = '0'; 
         }      
        } else { 
           $json['ok']='3'; 
        }    
mysqli_free_result($sql);        
echo json_encode($json);
?>  