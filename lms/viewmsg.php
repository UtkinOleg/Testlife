<?php
if(defined("USER_REGISTERED")) 
{  
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $mid = $_GET["id"];
  $toid = $_GET["to"];
  $title=$titlepage="Чтение сообщения";
  $helppage='';

  $query = "UPDATE msgs SET readed = '1' WHERE id=".$mid;
  if(!mysql_query($query))
    puterror("Ошибка при обращении к базе данных");

  include "topadmin.php";
  
  $tableheader = "class=tableheaderhide";
  $from = mysql_query("SELECT * FROM users WHERE id='".$toid."' LIMIT 1");
  $fromuser = mysql_fetch_array($from);
  $msg = mysql_query("SELECT * FROM msgs WHERE id='".$mid."' LIMIT 1");
  $msguser = mysql_fetch_array($msg);
  
?>

<script type="text/javascript">

		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
  
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    window.location = url;
    window.location.reload();
   }    
		  
		$(document).ready(function() {

    	$("#msg").click(function() {
				$.fancybox.open({
					href : 'msg&id=<? echo $toid ?>',
					type : 'iframe',
          width : 650,
          height : 450,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      
    });  
</script>    
<?

  echo "<div id='menu_glide' class='menu_glide'><table width='100%' align='center' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  echo "<tr><td width='300'><p>Тема: ".$msguser['title']."</td></tr>";
  echo "<tr><td width='300'><p>От: ".$fromuser['userfio']."</td></tr>";
  echo "<tr><td width='300'><p>Дата: ".data_convert ($msguser['msgdate'], 1, 0, 0)."</td></tr>";
  echo "<tr class='tableheaderhide'><td><p class=help>Сообщение</p></td></tr>";   
  echo "<tr><td width='300'><p>".$msguser['body']."</td></tr>";
  echo "<tr align='center'><td width='100'><a id='msg' href='javascript:;'>Ответить</a></td></tr>"; 
  echo "</table></div></p>";
  include "bottomadmin.php";
} else die;  
?>