<?php
  include "config.php";
  include "func.php";

  $id = $_GET['id'];

  $query = "SELECT * FROM users WHERE id='".$id."'";
  $gst = mysql_query($query);
  if ($gst) 
   $member = mysql_fetch_array($gst);
  else 
   puterror("Ошибка при обращении к базе данных");
  if ($member['usertype']=='expert') 
   $s = 'Эксперт';
  else if ($member['usertype']=='user') 
   $s='Участник';
  else if ($member['usertype']=='supervisor')
   $s='Супервизор';
  else if ($member['usertype']=='admin')
   $s='Администратор';
   

  $title = $s." ".$member['userfio'];
  $titlepage=$title;  
  include "topadmin.php";
  ?>

<script type="text/javascript">

		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
  
   function closeFancybox(){
    $.fancybox.close();
   }    
		  
		$(document).ready(function() {

    	$("#msg").click(function() {
				$.fancybox.open({
					href : 'msg&id=<? echo $id ?>',
					type : 'iframe',
          width : 650,
          height : 440,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      
    });  
</script>    
<? 
  
  echo "<table width='100%'' border='0' cellpadding=0 cellspacing=0>";
  echo "<tr><td>";
  echo "<div id='menu_glide' class='menu_glide'>
  <table align='center' bgcolor='#FAFAFA' border='0' cellpadding='3' cellspacing='3' width='60%'>";
  echo "<tr valign='top' align='center'><td valign='top'>";

        if (!empty($member['photoname'])) 
        {
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$photo_upload_dir.$member['id'].$member['photoname']."' height='50'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='50'></div>"; 
        } 
        else  
         echo "<i class='fa fa-user fa-3x'></i>";
  echo "</td></tr>";
  echo "<tr valign='top' align='left'><td valign='top'>";
  echo "<p>Место работы: ".$member['job']."</p>";
  echo "<p>Должность: ".$member['person']."</p>";
  echo "<hr><p>".$member['info']."</p>";
  if (USER_REGISTERED)
   echo "<p align='center'><a href='javascript:;'' id='msg' title='Отправить сообщение'><i class='fa fa-envelope fa-lg'></a></p>";
  echo "</td></tr></table></div></td></tr></table>";

  include "bottomadmin.php";

?>