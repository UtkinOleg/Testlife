<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";
require_once "header.php"; 
?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget { 
font-family: Verdana,Arial,sans-serif; font-size: 0.9em;}
p { 
font: 16px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%;   height: 500px;   bottom : 50px;  position: absolute; overflow: auto;} 
</style>
<script>
  $(function() {
    $( "#close" ).button();
  });
</script>
</head>
<body>
    <h3 class='ui-widget-header ui-corner-all' align="center">
      <p>Просмотр экспертного листа
      </p></h3>
    <div id="buttonsetm">
    <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
          <p align='center'>
            <div id="menu_glide" class="menu_glide" style="margin-top:20px;">
              <table class=bodytable border="0" width='95%' height='100%' align='center' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
<?

 $paid = $_GET["paid"];
 echo "<tr><td>";
 $res2=mysql_query("SELECT * FROM shablongroups WHERE proarrid='".$paid."' ORDER BY id;");
 while($group = mysql_fetch_array($res2))
  { 
    echo"<tr>";
        echo"<td>";
            echo"<p><b>".$group['name']."</b></td><td></td>";
    echo"</tr>";
    
   $res3=mysql_query("SELECT * FROM shablon WHERE proarrid='".$paid."' AND groupid='".$group['id']."' ORDER BY id");
   while($param = mysql_fetch_array($res3))
   { 
    echo"<tr>";
        if ($param['complex']==1) {
         echo"<td><p class=ptd><b>".$param['name']."* (Составной критерий)</b></td>";
         echo"<td></td>";

         $cgst = mysql_query("SELECT * FROM shabloncomplex WHERE proarrid='".$paid."' AND shablonid='".$param['id']."' ORDER BY id");
         if (!$cgst) puterror("Ошибка при обращении к базе данных");
         while($cmember = mysql_fetch_array($cgst))
           {
             echo"<tr><td><p class=ptd><em class=em>".$cmember['name']."*</em></td>";
             $c+=1;
             $cid = $cmember['id'];
             $cgst2 = mysql_query("SELECT * FROM shabloncparams WHERE shabloncid='$cid' ORDER BY id");
             if (!$cgst2) puterror("Ошибка при обращении к базе данных");

         ?>
         <script>
          $(function() {
    $( "#complex<? echo $cid; ?>" ).selectmenu({ width: 80 });
          });
         </script> 
         <?
            
             echo"<td><select id='complex".$cid."'  name='complex".$cid."'><option value=''></option>";
             while($cmember2 = mysql_fetch_array($cgst2))
             {
               if ($cmember2['type']==1) $val = $cmember2['value'];
               else
               if ($cmember2['type']==-1) $val = - $cmember2['value'];
               else
               if ($cmember2['type']==0) $val = 0;
               echo"<option value='".$val."'>".$cmember2['paramname']."</option>";
             }
            echo"</select></td>";
            echo "</tr>";
            echo"<tr><td><hr></td><td><hr></td></tr>";
           }
        }
        else
        if ($param['digital']==1) {
         echo"<input type='hidden' name='paramtype".$param['id']."' value='digital'>";
         echo"<td><p class=ptd><em class=em>".$param['name']."* (Введите оценку. Максимальная - ".$param['maxball']." балл(ов))</em></td>";
         ?>
         <script>
          $(function() {
    $( "#spparam<? echo $param['id'];?>" ).spinner({
      min: 0,
      max: <? echo $param['maxball'];?>,
      spin: function( event, ui ) {
          $( "#param<? echo $param['id'];?>" ).val(ui.value);
      }
    });
          });
         </script> 
         <?
         if ($defparams == 1)
          echo "<td><input readonly='1' type=text id='spparam".$param['id']."' name='spparam".$param['id']."' size='5'><input type=hidden id='param".$param['id']."' name='param".$param['id']."' value='0'></td>";
         else
          echo "<td><input readonly='1' type=text id='spparam".$param['id']."' name='spparam".$param['id']."' size='5'><input type=hidden id='param".$param['id']."' name='param".$param['id']."' value='0'></td>";
        }
        else {
         echo"<input type='hidden' name='paramtype".$param['id']."' value='common'>";
         
         echo"<td><p class=ptd><em class=em>".$param['name']."*</em></td>";

         // +++++++++++++++++++++++++++++++++++++ Слайдер jquery UI (механизм без заполнения оценок) 10.11.13
         ?>
         <script>
          $(function() {
          $( "#slider<?echo $param['id'];?>" ).slider({
           value:0,
           min: 0,
           max: <?echo $param['maxball'];?>,
           step: 1,
          slide: function( event, ui ) {
           $( "#<?echo"param".$param['id'];?>" ).val(ui.value);
           }
          });
          $( "#<?echo"param".$param['id'];?>" ).val($( "#slider<?echo $param['id'];?>" ).slider( "value" ));
          });
         </script>
         <td width="200">
         <input type="hidden" name="maxparam<?echo $param['id'];?>" value="<?echo $param['maxball'];?>">
         <label for="<?echo"param".$param['id'];?>">Баллов: </label><input type="text" readonly="1" id="param<?echo $param['id'];?>" name="param<?echo $param['id'];?>" style="border: 0; margin: 2px; padding: 2px; font-weight: bold; font-size:14px;"/>
         <div id="slider<?echo $param['id'];?>"></div>
         </td>
         <?
         // ------------------------------------------- Слайдер jquery UI
       }
    echo"</tr>";
    echo"<tr><td><p><font face='Tahoma,Arial' size='-2'>".$param['info']."</font></p></td></tr>";
    echo"<tr><td><hr></td><td><hr></td></tr>";
   } 
  }       

?>
              </table>
            </div>
          </p></td>
      </tr>
    </table>
   </div>
   <div id="buttonset">  
      <button style="font-size: 1em;" id="close" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Закрыть
      </button>  
   </div>
  </form>
</body>
</html>
<?

} else die;
