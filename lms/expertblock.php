<?         

/*         echo "<div id='menu_glide' class='ui-widget-content ui-corner-all'>
         <h3 class='ui-widget-header ui-corner-all'>Стать экспертом</h3>
         <table border='0' cellpadding='1' cellspacing='1' width='220'>
         <tr valign='top' align='left'><td valign='top' width='220'>";
         
         echo "<form method='POST' action='toexpert'>";
         echo "<p align='center'><font face='Tahoma,Arial'size='-1'>Введите ключ:</font> <input type='password' name='key' size='7'></p>
         <input type='hidden' name='userid' value='".USER_ID."'>";
 	       echo "<p align='center'><input type='submit' name='ok' value='Стать экспертом'></p>";
         echo "</form>";
 	       echo "</td></tr></table></div><p></p>";
*/

         echo "<div id='menu_glide' class='ui-widget-content ui-corner-all'>
         <h3 class='ui-widget-header ui-corner-all'>Организовать конкурс</h3>
         <table border='0' cellpadding='1' cellspacing='1' width='220'>
         <tr valign='top' align='left'><td valign='top' width='220'>";
         
         echo "<form action='robokassa2' method='post'>";
         echo "<p align='center'><font face='Tahoma,Arial'size='-1'>Наименование конкурса:</font></p> 
         <p align='center'><input type='text' name='n' id='n' size='30' style='font-size: 80%;'></p>";
         echo "<p align='center'><label for='param1' id='lparam1'><font face='Tahoma,Arial' size='-1'>Предполагаемое количество участников: 30</font></label><input type='hidden' id='param1' name='param1'/></p>";
         ?>
         <script>
          $(document).ready(function() {

          $( "#param1" ).val(30);
          $( "#param2" ).val(10);
          $('.fancybox').fancybox();
          
          $("#oferta2").click(function() {
    		   	 	$.fancybox.open({
		    	  		href : 'page_fb&id=47',
				      	type : 'iframe',
                width : 1000,
				      	padding : 10
    				});
	    		 });

          $("#howto").click(function() {
    		   	 	$.fancybox.open({
		    	  		href : 'page_fb&id=49',
				      	type : 'iframe',
                width : 1000,
				      	padding : 10
    				});
	    		 });

           $('#ok').click(function() { 
            $(".iferror").hide();
            var hasError = false;
            if(document.getElementById('n').value=='') {
              $("#n").after('<span class="iferror">Введите наименование конкурса </span>');
             hasError = true;
             }
            if(!document.getElementById('oferta').checked) {
              $("#oferta").after('<span class="iferror">Требуется согласие с условиями договора-оферты! </span>');
             hasError = true;
             }
             if(hasError == true) { return false; }
            });
          });   
                
          $(function() {
          $( "#slideru" ).slider({
           value:30, min: 10, max: 500, step: 10,
           slide: function( event, ui ) {
           $( "#param1" ).val(ui.value);
           $( "#lparam1" ).text('Предполагаемое количество участников: ' + ui.value);
           var sum1 = ui.value * 50 + $( "#slidere" ).slider( "value" )*100;
           $( "#paramsum" ).val( sum1 );
           $( "#lparamsum" ).text(sum1);
           }
          });
          $( "#slidere" ).slider({
           value:10, min: 10, max: 120, step: 10,
          slide: function( event, ui ) {
           $( "#param2" ).val(ui.value);
           $( "#lparam2" ).text('Предполагаемое количество экспертов: ' + ui.value);
           var sum2 = ui.value * 100 + $( "#slideru" ).slider( "value" )*50;
           $( "#paramsum" ).val( sum2 );
           $( "#lparamsum" ).text(sum2);
           }
          });
          $( "#paramsum" ).val( $( "#slidere" ).slider( "value" )*100 + $( "#slideru" ).slider( "value" )*50 );
          });
         </script>
         <div style="margin: 10px;" id="slideru"></div>
         <p align='center'><label for='param2' id='lparam2'><font face='Tahoma,Arial' size='-1' >Предполагаемое количество экспертов: 10</font></label>
         <input type='hidden' id='param2' name='param2'/></p>
         <div style="margin: 10px;" id="slidere"></div>
         <?         
         echo "<p align='center'><font face='Tahoma,Arial' size='-1'>Сумма оплаты: </font><b><label for='paramsum' id='lparamsum' style='font-size: 24px; color: #497787;'>2500</label></b> руб.</font></p>
         <input type='hidden' id='paramsum' name='paramsum' />";
         echo "<p align='center'><font face='Tahoma,Arial' size='-1'><input type='checkbox' id='oferta' name='oferta'>Я согласен(согласна) с условиями <a id='oferta2' href='javascript:;'>договора-оферты.</a></font></p>";
 	       echo "<p align='center'><button type='submit' name='ok' id='ok' style='font-size: 1em;'>Создать новый конкурс</button></p>";
         echo "<p align='center'><font face='Tahoma,Arial' size='-1'><a id='howto' href='javascript:;'>Как это работает?</a></font></p>";
 	       echo "</form></td></tr></table></div><p></p>";

?>