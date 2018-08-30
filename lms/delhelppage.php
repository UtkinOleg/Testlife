<?php
if(defined("IN_ADMIN")) {  
   include "config.php";
   $id = $_POST["id"];

    mysqli_query($mysqli,"START TRANSACTION;");
    $query = "DELETE FROM helppages WHERE id=".$id;
    mysqli_query($mysqli, $query);
    mysqli_query($mysqli,"COMMIT;");
    $json['ok'] = '1';  
} else 
   $json['ok'] = '0';  
echo json_encode($json); 
