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
    $usergroupid = $_POST["usergroup"];
    $usergroupfgosid = $_POST["usergroupfgos"];
    
    if (empty($usergroupid)) 
    {
     $usergroupid = 0;
    }
    if (empty($usergroupfgosid)) 
    {
     $usergroupfgosid = 0;
    }
     
    $content = htmlspecialchars_decode($_POST["content"]);
    if ($mode=='e')
    {
     $query = "UPDATE knowledge SET name = '".$name."'
            , content = '".$content."', usergroupid = ".$usergroupid.", usergroupfgosid = ".$usergroupfgosid." WHERE id=".$_POST["id"];
     mysqli_query($mysqli,$query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$_POST["id"].',"q");';
     echo '</script>';
     exit();
    }
    else
    if ($mode=='a')
    {
     $parent = $_POST["p"];
     $query = "INSERT INTO knowledge VALUES (0,
                                        '$name',
                                        '$content',
                                        $userid,
                                        $parent,
                                        $usergroupid,
                                        $usergroupfgosid);";
     mysqli_query($mysqli,$query);
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectTo("'.$site.'/qt");';
     echo '</script>';
     exit();
    }
}

if (empty($action)) 
{
 $mode = $_GET['m'];
 if ($mode=='e')
 {
  $modename = "Изменить область знаний";
  $id = $_GET['id'];
  $query = "SELECT * FROM knowledge WHERE id='".$id."' LIMIT 1;";
  $gst = mysqli_query($mysqli,$query);
  $member = mysqli_fetch_array($gst);
 }
 else
 if ($mode=='a')
 {
  $modename = "Добавить область знаний";
  $parent = $_GET['p'];
 }
 
require_once "header.php"; 
?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
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
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 500px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -250px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
<script>
 $(document).ready(function(){
    $("button").button();
    $( "#usergroup" ).selectmenu({ width: 400 });
    $( "#usergroupfgos" ).selectmenu({ width: 400 });
    $('#addk').submit(function()
    {
     var hasError = false; 
     var name = $("#name");
     if(name.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите наименование области знаний!');
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
<form id='addk' action='eknows' method='post'>
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
    <tr>
        <td>
         <p>Группа экспертов КИМ:</p>
        </td>
    </tr>
    <tr>
        <td>
               <select id="usergroup" name="usergroup">     
                <option value=""></option>   
<?
  
  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetChildFolders($mysqli, Folders $ks, $fid, $mid)
  {
    $ss = '';
    foreach($ks->getFolders($fid) as $tmpfolder) 
    {
     $ss .= "<option disabled='disabled'>".$tmpfolder->getName()."</option>";
     if (defined("IN_ADMIN")) 
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$tmpfolder->getId()."' AND usergrouptype='1' ORDER BY id DESC;");
     else
     if (defined("IN_SUPERVISOR"))
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$tmpfolder->getId()."' AND usergrouptype='1' AND userid='".USER_ID."' ORDER BY id DESC;");
     while($member2 = mysqli_fetch_array($sql))
     {
      if ($member2['id']==$mid)
       $sel = 'selected';
      else
       $sel = ''; 
      $ss .= '<option value="'.$member2['id'].'" '.$sel.'>' . $member2['name'] . '</option>';          
     }
     mysqli_free_result($sql); 
     
     $ss .= GetChildFolders($mysqli, $ks, $tmpfolder->getId(), $mid);
     //$ss .= "</optgroup>";
    }
    return $ss;
  }

  // Инициализация папок
  if (defined("IN_ADMIN"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders ORDER BY id;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE userid='".USER_ID."' ORDER BY id;");

  $folders = new Folders();
  
  while($member2 = mysqli_fetch_array($sql))
   $folders->addFolder(new Folder($member2['id'], 
                            $member2['name'], 
                            $member2['parentid'], 
                            $member2['userid']));
  mysqli_free_result($sql);
  
  echo GetChildFolders($mysqli, $folders, 0, $member['usergroupid']);

  if (defined("IN_ADMIN")) 
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='1' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='1' AND userid='".USER_ID."' ORDER BY id DESC;");
  while($member2 = mysqli_fetch_array($sql))
  {
    if ($member2['id']==$member['usergroupid'])
     $sel = 'selected';
    else
     $sel = ''; 
    echo '<option value="'.$member2['id'].'" '.$sel.'>'.$member2['name'].'</option>';          
  }
  mysqli_free_result($sql); 

?>
               </select>            
        </td>
    </tr>
    <? if (USER_EXTMODE) {?> 
    <tr>
        <td>
         <p>Группа экспертов проверки заданий ФГОС:</p>
        </td>
    </tr>
    <tr>
        <td>
               <select id="usergroupfgos" name="usergroupfgos">     
                <option value=""></option>   
<?
  echo GetChildFolders($mysqli, $folders, 0, $member['usergroupfgosid']);

  if (defined("IN_ADMIN")) 
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='1' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
      $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='0' AND usergrouptype='1' AND userid='".USER_ID."' ORDER BY id DESC;");
  while($member2 = mysqli_fetch_array($sql))
  {
    if ($member2['id']==$member['usergroupfgosid'])
     $sel = 'selected';
    else
     $sel = ''; 
    echo '<option value="'.$member2['id'].'" '.$sel.'>'.$member2['name'].'</option>';          
  }
  mysqli_free_result($sql); 

?>
               </select>            
        </td>
    </tr>
    <?}?>
    <tr><td><p>Содержание:</p></td></tr>
    <tr><td><textarea name='content' style='width:100%' rows='5'><?=$member['content'] ?></textarea></td></tr>

</table>
</form>
</div>
<div id="buttonset">
            <button id="ok" onclick="$('#addk').submit();"><?=$modename ?></button> 
            <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
            <button id="help" onclick="window.open('h&id=1');"><i class="fa fa-question fa-lg"></i> Помощь</button>
</div>
</td></tr></table>
</body></html>
<?php
}} else die;
?>
