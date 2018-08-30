<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  

include "config.php";

$action = $_POST["action"];

if (!empty($action)) 
{

    $kid = $_POST["kid"];
    $mode = $_POST["m"];

    $name = $_POST["name"];
    $name = str_replace('"','',$name);
    $name = str_replace("'",'',$name);

    $singleball = $_POST["singleball"];
    $singletime = $_POST["singletime"];
    $author = USER_ID;
    $comment = htmlspecialchars_decode($_POST["comment"]); 
    $knowsid = $_POST["knowsid"];
    if ($mode=='a') 
     $query = "INSERT INTO questgroups VALUES (0,
                                        '$name',
                                        $singleball,
                                        $singletime,
                                        NOW(),
                                        $author,
                                        '$comment', 
                                        $knowsid);";
    else
    if ($mode=='e')
    {
      if (empty($knowsid))
       $query = "UPDATE questgroups SET name = '".$name."'
            , singleball = '".$singleball."', singletime = '".$singletime."'
            , comment = '".$comment."' WHERE id=".$_POST["id"];
      else
       $query = "UPDATE questgroups SET name = '".$name."'
            , singleball = '".$singleball."', singletime = '".$singletime."'
            , comment = '".$comment."' , knowsid = '".$knowsid."'
           WHERE id=".$_POST["id"];
    }
    mysqli_query($mysqli,"START TRANSACTION;");
    mysqli_query($mysqli,$query);
    mysqli_query($mysqli,"COMMIT;");
    
    echo '<script language="javascript">';
    echo 'parent.closeFancyboxAndRedirectToUrl('.$kid.',"q");';
    echo '</script>';
    exit();
}
else
if (empty($action)) 
{
   
  $kid = $_GET["kid"];
  $mode = $_GET["m"];
  $id = $_GET['id'];

  require_once "header.php"; 

/*  $goahead = 1;
  if (defined("IN_SUPERVISOR") and !defined("IN_ADMIN"))
  {
   $tot = mysqli_query($mysqli,"SELECT count(*) FROM questgroups WHERE userid='".USER_ID."' ORDER BY id");
   $totalqg = mysqli_fetch_array($tot);
   if (LOWSUPERVISOR and $totalqg['count(*)']>0)
     $goahead = 0;
   mysqli_free_result($tot);
  }
  
  if ($goahead==0) {
      echo '<script language="javascript">';
      echo 'alert("Количество групп вопросов ограничено.");
      parent.closeFancyboxAndRedirectToUrl('.$kid.',"q");';
      echo '</script>';
      exit();
  } */

  if ($mode=='e')
  {
   $modename = 'Изменить группу вопросов';
   $query = "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id = $id LIMIT 1";
   $qg = mysqli_query($mysqli,$query);
   $member = mysqli_fetch_array($qg);
   
   $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata as d, singleresult as s WHERE s.testid=d.testid AND d.groupid='".$id."' LIMIT 1;");
   $cnttests = mysqli_fetch_array($counttest);
   $count_res = $cnttests['count(*)'];
   mysqli_free_result($counttest); 
  }
  else
  if ($mode=='a')
  {
   $modename = 'Добавить группу вопросов';
  }
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
</style>
<script>
   $(document).ready(function(){
    $( "button" ).button();
    $( "#knowsid" ).selectmenu();
    $( "#singleball" ).spinner({ min: 1 });
    $( "#singletime" ).spinner({ min: 1 });
    <?if ($count_res>0) {?>$("#knowsid").selectmenu( "option", "disabled", true );<?}?>
    $('#addgr').submit(function()
    {
     var hasError = false; 
     var name = $("#name");
     var singleball = $("#singleball");
     var singletime = $("#singletime");
     if(name.val()=='') {
            $("#info2").empty();
            $("#info2").append('Введите наименование группы вопросов!');
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
<table border="0" width='100%' height='100%' cellpadding=3 cellspacing=3>
<tr><td>
<p align='center'>
<form id="addgr" action="addquestgroup" method="post">
<input type=hidden name=action value=post>
<input type=hidden name=kid value="<?=$kid?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="m" value="<?=$mode?>">
 <table width='100%' border="0" cellpadding=3 cellspacing=3>
    <tr>
        <td witdh='400'><p>Наименование группы вопросов *:</p></td>
        <td witdh='400'><input type=text id=name name=name style='width:100%;' value="<? if ($mode=='a') echo "Новая группа"; else echo $member['name']; ?>"></td>
    </tr><tr>
        <td witdh='400'><p>Балльная стоимость одного вопроса (баллы) *:</p></td>
        <td witdh='400'><input name=singleball id=singleball size=5 readonly='1' value='<? if ($mode=='a') echo "1"; else echo $member['singleball']; ?>'></td>
    </tr><tr>
        <td witdh='400'><p>Время ответа на один вопрос (минут) *:</P></td>
        <td witdh='400'><input name=singletime id=singletime readonly='1' size=5 value='<? if ($mode=='a') echo "1"; else echo $member['singletime']; ?>'></td>
    </tr>
    <tr>
        <td><p>Область знаний для группы:</p></td><td>
        <select name="knowsid" id="knowsid" title="Область знаний для группы">
        <? 
          if (defined("IN_ADMIN"))
           $know = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge ORDER BY id;");
          else
           $know = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY id;");
          while($knowmember = mysqli_fetch_array($know))
            if ($mode=='a')
            {
             if ($kid==$knowmember['id'])
              echo "<option selected value='".$knowmember['id']."'>".$knowmember['name']."</option>";
             else
              echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
            }
            else
            if ($mode=='e')
            {
             if ($member['knowsid']==$knowmember['id'])
              echo "<option selected value='".$knowmember['id']."'>".$knowmember['name']."</option>";
             else 
              echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
            }
        ?>
        </select></td>
    </tr>    
    <tr><td>
    <p>Дополнительная информация:</p></td>
    <td><textarea name=comment style='width:100%' rows='5'><? if ($mode=='e') echo $member['comment']; ?></textarea>
    </td>
    </tr>
</table>
</form>
<p></p>
</td></tr>
    <tr align="center">
        <td>
            <button id="ok" onclick="$('#addgr').submit();">Сохранить группу</button> 
            <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
            <button id="help" onclick="window.open('h&id=2');"><i class="fa fa-question fa-lg"></i> Помощь</button>
        </td>
    </tr>           
</table>
</td></tr></table>
</body></html>
<?
}
} else die;
?>
