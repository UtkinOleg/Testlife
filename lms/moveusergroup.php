<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

  include "config.php";

  $action = $_POST["action"];

if (!empty($action)) 
{
     $id = $_POST["id"];
     $folderid = $_POST["folder"];
     $parentid = $_POST["p"];
   
     mysqli_query($mysqli,"START TRANSACTION;");
     $query = "UPDATE usergroups SET folderid = '".$folderid."'
            WHERE id=".$id;
     mysqli_query($mysqli,$query);
     mysqli_query($mysqli,"COMMIT;");

     
     echo '<script language="javascript">';
     echo 'parent.closeFancyboxAndRedirectToUrl('.$parentid.');';
     echo '</script>';
     exit();
   
}
else
if (empty($action)) 
{
  $id = $_GET["id"];
  $parentid = $_GET["p"];
  
  require_once "header.php"; 
?>
<script type="text/javascript">
 jQuery(document).ready(function() {
   $("button").button();
   $("#folder").selectmenu({ width: 400 });
   $('#move').submit(function()
   {
     return true;
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
#buttonset { display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 600px;   height: 45px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 55px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
</head><body>
<div id="spinner"></div>
<table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'><tr><td align='center'>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2">Перенос группы пользователей</div>    
        </p>            	   
      </div>
    </div>
    <p></p>  
<div id="buttonsetm">
 <form id="move" action="moveusergroup" method="post">
  <input type='hidden' name='action' value='post'>
  <input type="hidden" name="id" value="<?=$id?>">
  <input type="hidden" name="p" value="<?=$parentid?>">
   <table border="0" width='90%' cellpadding=0 cellspacing=0>
    <tr>
        <td width="30%">
         <p>Папка:</p>
        </td>
        <td>
               <select id="folder" name="folder">     
                <option value="0"></option>   
<?
  
  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetChildFolders($mysqli, Folders $ks, $fid)
  {
    $ss = '';
    foreach($ks->getFolders($fid) as $tmpfolder) 
    {
     $ss .= "<option value='".$tmpfolder->getId()."'>".$tmpfolder->getName()."</option>";
     $ss .= GetChildFolders($mysqli, $ks, $tmpfolder->getId());
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
  
  while($member = mysqli_fetch_array($sql))
   $folders->addFolder(new Folder($member['id'], 
                            $member['name'], 
                            $member['parentid'], 
                            $member['userid']));
  mysqli_free_result($sql);
  echo GetChildFolders($mysqli, $folders, 0);
?>
               </select>            
        </td>
    </tr>
  </table>
  </form>
 </div>
 <div id="buttonset">
            <button id='ok' onclick="$('#move').submit();">Перенести группу</button>
            <button id="close" onclick="parent.closeFancybox();">Отмена</button> 
 </div>
</body></html>
<?
} 
} else die; 

?>