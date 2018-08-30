<?php
  // Получаем соединение с базой данных
  include "config.php";
  include "func.php";

  $r = $_GET['r'];
  if (empty($r)) 
   $r=0; 
  if ($r==0) 
   $title=$titlepage="Новые сообщения";
  else
   $title=$titlepage="Прочитанные сообщения";
  include "topadmin.php";

 if (defined("IN_USER"))
  {

  ?>
  
<script>

		$(document).ready(function() {
			$('.fancybox').fancybox();
      
    	$("#admin").click(function() {
				$.fancybox.open({
					href : 'msg&id=14',
					type : 'iframe',
          width : 650,
          height : 450,
          fitToView : false,
          autoSize : false,          
					padding : 5
				});
			});
      
		});
  
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    window.location = url;
    window.location.reload();
   }    

   function closeFancybox(){
    $.fancybox.close();
   }    
		  
  function getmsg(id,uid){
   $("#spinner").fadeIn("slow");
   $.post('getmsgjson',{msgid:id, userid:uid},  
    function(data){  
      eval('var obj='+data);         
      $("#spinner").fadeOut("slow");
      if(obj.ok=='1')
      {
       $('#msg'+id).empty();        
       $('#msg'+id).append(obj.content);        
      } 
      else 
       alert("Ошибка при получении сообщения.");
    }); 
  } 

</script>  
  
  <div id="spinner"></div>  
<!--  <p align='center'><a id='admin' href='javascript:;'><i class='fa fa-envelope-square fa-lg'></i>&nbsp;Сообщение администратору</a></p>
-->
<?  
  
  $tot2 = mysql_query("SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed='".$r."'");
  if (!$tot2) puterror("Ошибка при обращении к базе данных");
  $total2 = mysql_fetch_array($tot2);
  $count2 = $total2['count(*)'];
  if ($count2>0) {

  ?>
  <table width="80%" align="center" border="0">
   <tr><td>
   <div id='menu_glide' class='ui-widget-content ui-corner-all'>
   <table width="100%" align="center" border="0" cellpadding=5 cellspacing=5 bordercolorlight=gray bordercolordark=white>
   <tr><td>
  <?

   $msgs = mysql_query("SELECT * FROM msgs WHERE touser='".USER_ID."' AND readed='".$r."' ORDER BY msgdate DESC");
   while ($param = mysql_fetch_array($msgs)) 
    {
    $from = mysql_query("SELECT * FROM users WHERE id='".$param['fromuser']."' LIMIT 1");
    $fromuser = mysql_fetch_array($from);
    
    if (!empty($fromuser['id'])) {
  ?>
   <script>
		$(document).ready(function() {

    	$("#postmsg<? echo $param['id'] ?>").click(function() {
				$.fancybox.open({
					href : 'msg&id=<? echo $fromuser['id'] ?>',
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
  <? } 

    echo "<p>&nbsp;&nbsp;&nbsp;<a onclick=\"getmsg(".$param['id'].",'".$fromuser['id']."');\" href='javascript:;' title='Прочитать'>";
    echo "<i class='fa fa-envelope fa-lg'></i>&nbsp;".$param['title']." [".data_convert ($param['msgdate'], 1, 0, 0);
    echo "] от ".$fromuser['userfio']."</a>";
    if (!empty($fromuser['id'])) 
     echo "&nbsp;<a title='Ответить' href='javascript:;' id='postmsg".$param['id']."'><i class='fa fa-mail-reply fa-lg'></i></a></p><div id='msg".$param['id']."'></div>";
    mysql_free_result($from);
   }
   mysql_free_result($msgs);
  ?>
   </td></tr></table></div></td></tr></table>
  <? 
  } 
  else
    {
?>
            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Нет новых сообщений.</p>
            	</div>
            </div><p></p> 
 <? 
     }
    
  
  } 
  include "bottomadmin.php";
?>