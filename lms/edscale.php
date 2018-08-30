<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) { 
include "config.php";

$action = $_POST["action"];
if (!empty($action)) 
{


  $id = $_POST["id"];
  $mode = $_POST["m"];
  $cntparams = $_POST["cntparams"];
  $name = $_POST["scalename"];
  $name = str_replace('"','',$name);
  $name = str_replace("'",'',$name);
   
  if ($mode=='a')
  {
   mysqli_query($mysqli,"START TRANSACTION;");
   $query = "INSERT INTO scales VALUES (0,
      '$name',
      ".USER_ID.",
      NOW())";
  
   if(mysqli_query($mysqli,$query)) {
    $id = mysqli_insert_id($mysqli);
    // Запишем параметры
    if ($id>0)
    {
     for ($i = 1; $i <= $cntparams; $i++) {

      $param_name = htmlspecialchars($_POST["paramname".$i], ENT_QUOTES);
      $param_top = $_POST["paramtop".$i];
      $param_end = $_POST["paramend".$i];
      if (!empty($param_name))
      {
         $query = "INSERT INTO scaleparams VALUES (0,
         '$param_name',
         ".$param_top.",
         ".$param_end.",
         ".$id.")";
//      echo '<script language="javascript">';
//      echo 'console.log("'.$query.'");';   
//      echo '</script>';
         mysqli_query($mysqli,$query);
      }
    }
   }
  }
   
     mysqli_query($mysqli,"COMMIT");
     echo '<script language="javascript">';
     echo 'parent.closeFancybox();';
     echo '</script>';
     exit();
   }
   else
   if ($mode=='e')
   {
     // Сначала удалим старые 
     mysqli_query($mysqli,"START TRANSACTION;");
     mysqli_query($mysqli,"DELETE FROM scaleparams WHERE scaleid=".$id);
     mysqli_query($mysqli,"COMMIT");

     mysqli_query($mysqli,"START TRANSACTION;");
     // Добавим параметры
     for ($i = 1; $i <= $cntparams; $i++) 
     {
      $param_name = htmlspecialchars($_POST["paramname".$i], ENT_QUOTES);
      $param_top = $_POST["paramtop".$i];
      $param_end = $_POST["paramend".$i];
      if (!empty($param_name))
      {
         $query = "INSERT INTO scaleparams VALUES (0,
         '$param_name',
         $param_top,
         $param_end,
         $id)";
         mysqli_query($mysqli,$query);
      }
     }
     
     // Обновим 
     $query = "UPDATE scales SET name = '".$name."' WHERE id=".$id;
     mysqli_query($mysqli,$query);
     mysqli_query($mysqli,"COMMIT");
     
     echo '<script language="javascript">';
     echo 'parent.closeFancybox();';
     echo '</script>';
     exit();
   } 
    
}
else
if (empty($action)) 
{
  $mode = $_GET["m"];
  $id = $_GET["id"];
  
  if ($mode=='e')
  {
   $modename = '<i class="fa fa-arrows-h fa-fw"></i> Изменить шкалу оценок';
   if (defined("IN_ADMIN")) 
   {
       $query = "SELECT * FROM scales WHERE id='".$id."' LIMIT 1;";
   }
   else
   { 
       $query = "SELECT * FROM scales WHERE id='".$id."' AND ownerid='".USER_ID."' LIMIT 1;";
   }
   
   $sql = mysqli_query($mysqli,$query);
   $scale = mysqli_fetch_array($sql);
   $scalename = $scale['name'];
   mysqli_free_result($sql);
  }
  else
  if ($mode=='a')
  {
    $scalename = '';
    $modename = '<i class="fa fa-arrows-h fa-fw"></i> Новая шкала оценок';
  }
  
  require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<script type="text/javascript">

 $(function(){
   $("#spinner").fadeOut("slow");
   $("button").button();
   $("#slider-range").slider({
      range: true,
      min: 0,
      max: 100,
      values: [ 0, 100 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( ui.values[ 0 ] + "% - " + ui.values[ 1 ] + "%");
        $( "#amount1" ).val(ui.values[ 0 ]);
        $( "#amount2" ).val(ui.values[ 1 ]);
      }
    });
    
    $( "#amount" ).val( $( "#slider-range" ).slider( "values", 0 ) +
      "% - " + $( "#slider-range" ).slider( "values", 1 ) + "%");
    $( "#amount1" ).val(0);
    $( "#amount2" ).val(100);
      
    <? if ($mode=='e') { 
   
   $sql2 = mysqli_query($mysqli,"SELECT * FROM scaleparams WHERE scaleid='".$id."' ORDER BY id;");
   while($scpar = mysqli_fetch_array($sql2))
   {
   ?>
    scpar_name = "<?=trim(rtrim($scpar['name'], '\n\r'))?>";
    scpar_top = "<?=$scpar['top']?>";
    scpar_end = "<?=$scpar['end']?>";
    addscalemanual(scpar_name, scpar_top, scpar_end);
   <?}    
   mysqli_free_result($sql2);
   } ?>
  });       

 function checkRegexp(n, t) {
        return t.test(n);
 }

 function delscale(cnt)
 {
   $("#paramname"+cnt).val('');
   $("#paramtop"+cnt).val(0);
   $("#paramend"+cnt).val(0);
   $('#dparam'+cnt).empty();
 }

 function addparam()
 {
     var hasError = false; 
     var cnt = $("#cntparams").val();
     var name = $("#name").val();
     var ptop = $("#amount1").val();
     var pend = $("#amount2").val();
     
     if(name === '') {
            $("#info2").html('Введите наименование диапазона');
            hasError = true;    
     }
      
     if(hasError === true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       $("#name").focus();
     }
     else
     {

      cnt++;
      $('#hiddenparams').append('<input type="hidden" id="paramname'+cnt+'" name="paramname'+cnt+'" value="' + name + '">'); 
      $('#hiddenparams').append('<input type="hidden" id="paramtop'+cnt+'" name="paramtop'+cnt+'" value="' + ptop + '">'); 
      $('#hiddenparams').append('<input type="hidden" id="paramend'+cnt+'" name="paramend'+cnt+'" value="' + pend + '">'); 
      var s = '<div id="dparam'+cnt+'"><p>' + name + ': ' + ptop + '% - ' + pend + '%';
      s+='&nbsp;<button title="Удалить диапазон" id="delb'+cnt+'" onclick="delscale('+cnt+');"><i class="fa fa-minus-circle fa-lg"></i></button></p></div>';        
      $('#showparams').append(s);
      $("#delb"+cnt).button();
      $("#cntparams").val(cnt);
      
      $("#name").val('');
      $("#name").focus();
     
     }
     
 }

 function addscalemanual(name, ptop, pend)
 {
      var cnt = $("#cntparams").val();
      cnt++;
      $("#cntparams").val(cnt);

      $('#hiddenparams').append('<input type="hidden" id="paramname'+cnt+'" name="paramname'+cnt+'" value="' + name + '">'); 
      $('#hiddenparams').append('<input type="hidden" id="paramtop'+cnt+'" name="paramtop'+cnt+'" value="' + ptop + '">'); 
      $('#hiddenparams').append('<input type="hidden" id="paramend'+cnt+'" name="paramend'+cnt+'" value="' + pend + '">'); 
      var s = '<div id="dparam'+cnt+'"><p>' + name + ': ' + ptop + '% - ' + pend + '%';
      s+='&nbsp;<button title="Удалить диапазон" id="delb'+cnt+'" onclick="delscale('+cnt+');"><i class="fa fa-minus-circle fa-lg"></i></button></p></div>';        
      $('#showparams').append(s);
      $("#delb"+cnt).button();
      $("#name").val('');
      $("#name").focus();
     
 }
 
 jQuery(document).ready(function() {
    $('#save').submit(function()
    {
     if ($("#scalename").val() === '')
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Введите наименование шкалы');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#scalename").focus();
      return false;
     }   

     var cnt = $("#cntparams").val();
     if (cnt>0)
     {
      $('#ok', $(this)).attr('disabled', 'disabled');
      $("#spinner").fadeIn("slow");
      return true;
     } 
     else 
     {
      $("#info1").removeClass("ui-state-highlight");
      $("#info1").addClass("ui-state-error");
      $("#info2").empty();
      $("#info2").append('Укажите диапазоны шкалы оценок');
      $("#helpb").button();
      $('#helpb').addClass('button_disabled').attr('disabled', true);
      $("#email").focus();
      return false;
     }   
    });   
  });   

</script>
<style>
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.8em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
p { font: 14px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2"><?=$modename?></div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<div id="buttonsetm">
 <form id="save" action="edscale" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="id" value="<?=$id?>">
  <input type="hidden" name="m" value="<?=$mode?>">
  <input type='hidden' id='cntparams' name='cntparams' value='0'>
   <table border="0" width='90%' cellpadding=0 cellspacing=0>
    <tr>
        <td width="30%"><p>Наименование шкалы *:</p></td>
        <td><input type='text' id='scalename' name='scalename' style='width:100%' value='<?=$scalename?>'></td>
    </tr>
    <tr>
        <td width="30%"><p>Наименование диапазона *:</p></td>
        <td><input type='text' id='name' name='name' style='width:100%' value=''></td>
    </tr>
    <tr>
        <td width="30%">
         <p>Диапазон:</p>
        </td>
        <td>
          <p><label for="amount"></label>
          <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
          <input type="hidden" id="amount1" value="0">
          <input type="hidden" id="amount2" value="0">
          <div id="slider-range"></div></p>
        </td>
    </tr>
    <tr>    
     <td>
        <div id="hiddenparams"></div>
     </td>
     <td>
       <div id="showparams"></div>
     </td>
    </tr>
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id="add" onclick="addparam();"><i class="fa fa-arrow-h fa-lg"></i> Добавить</button>
            <button id='ok' onclick="$('#save').submit();" >Сохранить шкалу</button>
            <button id="close" onclick="parent.closeFancybox();">Отмена</button> 
            <button id="help" onclick="window.open('h&id=7');"><i class="fa fa-question fa-lg"></i> Помощь</button>
 </div>
</body></html>
<?
} 
} else die; 
