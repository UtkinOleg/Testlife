<?php
if(defined("IN_ADMIN")) {

include "config.php";
include "func.php";

$action = "";
$action = $_POST["action"];
if (!empty($action)) 
{
    $pmode = $_POST["pmode"];
    $mode = $_POST["mode"];
    $name = $_POST["name"];
    $paid = $_POST["paid"];
    $content = $_POST["content"];
    if ($mode=='edit')
    {
     $query = "UPDATE adminshab SET name = '".$name."'
            , content = '".$content."'
            , paid = '".$paid."'
             WHERE id=".$_POST["id"];
     mysql_query($query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/admshab&mode='.$pmode.'");';
     echo '</script>';
     exit();
    }
    else
    if ($mode=='add')
    {
     $query = "INSERT INTO adminshab VALUES (0,
                                        '$name',
                                        '$content',
                                        $paid, 
                                        '$pmode');";
     mysql_query($query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/admshab&mode='.$pmode.'");';
     echo '</script>';
     exit();
    }
}

if (empty($action)) 
{
 $mode = $_GET['mode'];
 $pmode = $_GET['pmode'];
 if ($mode=='edit')
 {
  $modename = "Изменить шаблон";
  $id = $_GET['id'];
  $query = "SELECT * FROM adminshab WHERE id='".$id."'";
  $gst = mysql_query($query);
  if ($gst)
   $member = mysql_fetch_array($gst);
  else 
   puterror("Ошибка при обращении к базе данных");
 }
 else
  $modename = "Добавить шаблон";
 
require_once "header.php"; 
?>
<style>
.ui-widget {
font-family: Verdana,Arial,sans-serif;
font-size: 0.7em;
}
.iferror {
	margin:0;
  color: #FF4565; 
  font-size: 0.7em;
  font-family: Verdana,Arial,sans-serif;
}
.error .iferror {
	display:block;
}
</style>
<script>
  $(function() {
    $( "#ok" ).button();
  });
 $(document).ready(function(){
    $('form').submit(function()
    {
     var hasError = false; 
     $(".iferror").hide();
     var name = $("#name");
     if(name.val()=='') {
            name.after('<span class="iferror"><strong>Введите наименование!</strong></span>');
            name.focus();
            hasError = true;
     }
     if(hasError == true) {
       return false; 
     }
     else
     {
       $('input[type=submit]', $(this)).attr('disabled', 'disabled');
       return true; 
     }
    });   
  });
</script>
</head><body>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td>
<h3 class='ui-widget-header ui-corner-all' align="center"><p><? echo $modename ?></p></h3>
<p align='center'>
<form action='editshab' method='post'>
<input type='hidden' name='id' value='<?php echo $id; ?>'>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='mode' value='<? echo $mode ?>'>
<input type='hidden' name='pmode' value='<? echo $pmode ?>'>

<p align='center'>
<table  width="100%" border="0" cellpadding=0 cellspacing=0 bordercolorlight=gray bordercolordark=white>
<tr><td>
<div id="menu_glide" class="menu_glide">
<table width="90%" align="center" class=bodytable border="0" cellpadding=0 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd><b><em class=em>Наименование:</em></b></td>
    </tr>
    <tr>
        <td><input type='text' id='name' name='name' style='width:100%' value='<? echo $member['name']; ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd>Модель</p></td></tr>
        <tr><td><select id="paid" name="paid">
        <? 
          if ($member['paid']==0)
            echo "<option selected value='0'>Нет</option>";

          $know = mysql_query("SELECT * FROM projectarray ORDER BY name;");
          while($knowmember = mysql_fetch_array($know))
            {
             if ($member['paid']==$knowmember['id'])
              echo "<option selected value='".$knowmember['id']."'>".$knowmember['name']."</option>";
             else 
              echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
            }
          mysql_free_result($know);  
        ?>
        </select></td>
    </tr>    
    <tr><td><p class=ptd>Содержание:</p></td></tr>
    <tr><td><textarea name='content' style='width:100%' rows='5'><? echo $member['content'] ?></textarea></td>
    </tr>
    <tr>
        <td align="center">
            <input id="ok" type="submit" value="<? echo $modename ?>">
        </td>
    </tr>           
</table></div>
</td></tr></table>
</form>
</p></td></tr></table>
</body></html>
<?php
}} else die;
?>
