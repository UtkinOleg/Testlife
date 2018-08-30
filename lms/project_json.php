<?
include "config.php";

$offset = intval($_POST['offset']);  


$sql = mysql_query("SELECT * FROM news WHERE pagetype='news' AND published=1 ORDER BY ndate DESC LIMIT $offset, 5;");


if(mysql_num_rows($sql)>0) { 
        while($post = mysql_fetch_assoc($sql)){  
            foreach($post AS $n=>$m)
            { 
             $post[$n] = $m; 
            }
            if (!empty($post['content']))
             $post['content'] = htmlspecialchars_decode($post['content']);
            if (!empty($post['content2']))
             $post['content2'] = '2';
            if (!empty($post['docurl']))
             $json['docurl'][] = urlencode($post['docurl']); 
            else
             $json['docurl'][] = '';      
            $res4=mysql_query("SELECT userfio FROM users WHERE id='".$post['userid']."'");
            $param4 = mysql_fetch_array($res4);
            $json['more'][] = $post;  
            $json['userfio'][] = $param4['userfio'];       
            $json['newsdate'][] = date("d-m-Y", strtotime($post['ndate'])); 
        }   
         if(count($json['more']))  { 
             $json['ok'] = '1';  
         } else {  
             $json['ok'] = '0'; 
         }      
        } else { 
           $json['ok']='3'; 
        }    
echo json_encode($json);
?>  