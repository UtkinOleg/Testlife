<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$error = "";
$action = "";
// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{

   if (defined("IN_SUPERVISOR") or defined("IN_ADMIN")) 
   {
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели наименование группы.";
  }
  if (empty($_POST["singleball"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели балльную стоимость.";
  }
  if (empty($_POST["singletime"])) 
  {
    $action = ""; 
    $error = $error." Вы не ввели время ответа на один вопрос.";
  }

  if (!empty($action)) 
  {
    $kid = $_POST["kid"];
    $name = $_POST["name"];
    $singleball = $_POST["singleball"];
    $singletime = $_POST["singletime"];
    $comment = htmlspecialchars($_POST["comment"], ENT_QUOTES); 
    $knowsid = $_POST["knowsid"];
    $query = "UPDATE questgroups SET name = '".$name."'
            , singleball = '".$singleball."', singletime = '".$singletime."'
            , comment = '".$comment."' , knowsid = '".$knowsid."'
           WHERE id=".$_POST["id"];
    mysqli_query($mysqli,$query);
    echo '<script language="javascript">';
    echo 'parent.closeFancyboxAndRedirectToUrl("'.$site.'/knows&kid='.$kid.'");';
    echo '</script>';
    exit();
  }  
  else  
  {
      // Выводим сообщение об ошибке в случае неудачи
      echo '<script language="javascript">';
      echo 'alert("Ошибки:'.$error.'");
      parent.closeFancyboxAndRedirectToUrl("'.$site.'/knows&kid='.$kid.'");';
      echo '</script>';
      exit();
  }  
}
}

if (empty($action)) 
{

 $kid = $_GET['kid'];
 $id = $_GET['id'];
 $query = "SELECT * FROM questgroups WHERE id = $id LIMIT 1";
 $qg = mysqli_query($mysqli,$query);
 if ($qg) $member = mysqli_fetch_array($qg);
 else puterror("Ошибка при обращении к базе данных");
 
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
        $( "#close" ).button();
        $( "#knowsid" ).selectmenu();
        $( "#singleball" ).spinner({ min: 1 });
        $( "#singletime" ).spinner({ min: 1 });
    });
 $(document).ready(function(){
    $('form').submit(function()
    {
     var hasError = false; 
     $(".iferror").hide();
     var name = $("#name");
     var singleball = $("#singleball");
     var singletime = $("#singletime");
     if(name.val()=='') {
            name.after('<span class="iferror"><strong>Введите наименование!</strong></span>');
            name.focus();
            hasError = true;
     }
     if(singleball.val()==0) {
            singleball.after('<span class="iferror"><strong>Введите балльную стоимость вопроса!</strong></span>');
            singleball.focus();
            hasError = true;
     }
     if(singletime.val()==0) {
            singletime.after('<span class="iferror"><strong>Введите время ответа на вопрос!</strong></span>');
            singletime.focus();
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
<h3 class='ui-widget-header ui-corner-all' align="center"><p>Изменить группу вопросов</p></h3><center>
<form action='editquestgroup' method='post'>
<table border="0" width='100%' height='100%' cellpadding=3 cellspacing=3>
<tr><td>
<input type='hidden' name='action' value='post'>
<input type='hidden' name='id' value='<? echo $id; ?>'>
<input type='hidden' name='kid' value=<? echo $kid; ?>>
<p align='center'>
<div id="menu_glide" class="menu_glide">
<table width='100%' class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td witdh='400'><p class=ptd><b><em class=em>Наименование группы вопросов *</em></b></p></td>
        <td witdh='400'><input type=text id=name name=name style='width:100%;' value='<? echo $member['name'] ?>'></td>
    </tr><tr>
        <td witdh='400'><p class=ptd><b><em class=em>Балльная стоимость одного вопроса (баллы) *</em></b></p></td>
        <td witdh='400'><input name=singleball id=singleball size=5 readonly='1' value='<? echo $member['singleball'] ?>'></td>
    </tr><tr>
        <td witdh='400'><p class=ptd><b><em class=em>Время ответа на один вопрос (минут) *</em></b></p></td>
        <td witdh='400'><input name=singletime id=singletime size=5 readonly='1' value='<? echo $member['singletime'] ?>'></td>
    </tr>
    <tr>
        <td><p class=ptd>Область знаний для группы</p></td><td><select id="knowsid" name="knowsid" title="Область знаний для группы">
        <? 
          if ($member['knowsid']==0)
            echo "<option selected value='0'>Нет</option>";
          if (defined("IN_ADMIN"))
           $know = mysqli_query($mysqli,"SELECT * FROM knowledge ORDER BY id;");
          else
           $know = mysqli_query($mysqli,"SELECT * FROM knowledge WHERE userid='".USER_ID."' ORDER BY id;");
          while($knowmember = mysqli_fetch_array($know))
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
    <p class=ptd>Дополнительная информация:</p></td>
    <td><textarea name='comment' style='width:100%' rows='5'><? echo $member['comment']; ?></textarea>
    </td>
    </tr>
</table>
</div></p>
</td></tr>
    <tr align="center">
        <td>
            <input id="ok" type="submit" value="Изменить группу"> 
            <button id="close" onclick="parent.closeFancybox();">Отмена</button>  
        </td>
    </tr>           
</table>
</form>
</td></tr></table>
</body></html>
<?
}} else die;
?>
