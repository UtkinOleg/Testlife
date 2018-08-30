<?
  $title = "Рейтинги открытых проектов";
  $titlepage=$title;  

  include "config.php";
  include "func.php";

  // Выводим шапку страницы
  include "topadmin.php";
  
?>
<script type="text/javascript"> 
  jQuery(document).ready(function() {  
      tops();  
			$('.fancybox').fancybox();
  }); 
 
  var t;

  function tops(){    
  $("#spinner").fadeIn("slow");
  if(t==undefined) { 
     t=0; 
  } else { 
     t=t+5; 
  } 
  $.post('topsjson2.php',{offset:t}, 
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
       $('#topsresult').append(obj.content);        
      else 
      if(obj.ok=='3') { 
       $('#topsbutton').addClass('button_disabled').attr('disabled', true); 
      }                
    }); 
  $("#spinner").fadeOut("slow");
  }  

</script>

  <div id="spinner"><img src="img/ajax-loader.gif"></div>
<p align='center'>
 <div id='container'>
  <div id="topsresult"></div> 
  <center><button onclick="tops();" id="topsbutton">Показать еще</button></center> 
 </div>
</p>
<?

  include "bottomadmin.php";
?>