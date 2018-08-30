<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {

include "config.php";
include "func.php";

$action = "";
$action = $_POST["action"];
if (!empty($action)) 
{
    $mode = $_POST["m"];
    $userid = USER_ID;
    $name = $_POST["name"];
    $content = htmlspecialchars_decode($_POST["content"]);
    if ($mode=='e')
    {
     $query = "UPDATE folders SET name = '".$name."' WHERE id=".$_POST["id"];
     mysqli_query($mysqli,$query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$_POST["id"].');';
     echo '</script>';
     exit();
    }
    else
    if ($mode=='a')
    {
     $parent = $_POST["p"];
     $query = "INSERT INTO folders VALUES (0,
                                        '$name',
                                        $userid,
                                        $parent);";
     mysqli_query($mysqli,$query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$parent.');';
     echo '</script>';
     exit();
    }
}

if (empty($action)) 
{
 $mode = $_GET['m'];
 if ($mode=='e')
 {
  $modename = "Изменить папку";
  $id = $_GET['id'];
  $query = "SELECT * FROM folders WHERE id='".$id."' LIMIT 1;";
  $gst = mysqli_query($mysqli,$query);
  $member = mysqli_fetch_array($gst);
 }
 else
 if ($mode=='a')
 {
  $modename = "Добавить папку";
  $parent = $_GET['p'];
 }
 
require_once "header.php"; 
?>
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
</style>
<script>
  $(function() {
    $( "#ok" ).button();
    $( "#close" ).button();
  });
 $(document).ready(function(){
    $('#addk').submit(function()
    {
     var hasError = false; 
     var name = $("#name");
     if(name.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите наименование!');
            name.focus();
            hasError = true;
     }
     if(hasError == true) {
       $("#info1").removeClass("ui-state-highlight");
       $("#info1").addClass("ui-state-error");
       return false; 
     }
     else
     {
       $('#ok', $(this)).attr('disabled', 'disabled');
       return true; 
     }
    });   
  });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align="center">
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2"><?=$modename?></div>    
        </p>            	   
      </div>
    </div>
<p></p>
<form id='addk' action='eduserfolder' method='post'>
<input type='hidden' name='id' value='<?=$id?>'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='m' value='<?=$mode?>'>
<input type='hidden' name='p' value='<?=$parent?>'>
<table width="99%" align="center" border="0" cellpadding=3 cellspacing=3>
    <tr>
        <td><p>Наименование *:</p></td>
    </tr>
    <tr>
        <td><input type='text' id='name' name='name' style='width:100%' value='<?=$member['name']; ?>'></td>
    </tr>
</table></div>
</form>
<p></p>
<table width="99%" align="center" border="0" cellpadding=3 cellspacing=3>
    <tr>
        <td align="center">
            <button id="ok" onclick="$('#addk').submit();"><?=$modename ?></button> 
            <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
        </td>
    </tr>           
</table>
</td></tr></table>
</body></html>
<?php
}} else die;
?>
