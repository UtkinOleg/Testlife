<?
include "config.php";

$offset = intval($_POST['offset']);  


$sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM news WHERE pagetype='news' AND published=1 ORDER BY ndate DESC LIMIT $offset, 3;");

if(mysqli_num_rows($sql)>0) { 
        while($post = mysqli_fetch_assoc($sql)){  
            foreach($post AS $n=>$m)
            { 
             $post[$n] = $m; 
            }
            if (!empty($post['content']))
             $post['content'] = htmlspecialchars_decode($post['content']);     
            if (!empty($post['content2']))
             $post['content2'] = '2';
            if (!empty($post['docurl']))
             $json['docurl'][] = $post['docurl']; 
            else
             $json['docurl'][] = '';      
            if (!empty($post['picurl']))
             $json['picurl'][] = $post['picurl']; 
            else
             $json['picurl'][] = '';      
            $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$post['userid']."'");
            $param4 = mysqli_fetch_array($res4);
            $json['content'][] = strip_tags($post['content'], '<p><a>');  
            $json['name'][] = $post['name'];  
            $json['userfio'][] = $param4['userfio'];       
            $json['newsdate'][] = date("d-m-Y", strtotime($post['ndate'])); //data_convert ($post['ndate'], 1, 0, 0);
            $json['id'][] = $post['id'];  
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