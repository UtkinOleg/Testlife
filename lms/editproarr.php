<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
include "func.php";

$error = "";
$action = "";
$title=$titlepage="Изменение параметров модели";

include "topadmin.php";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
 
  $paid = $_POST["paid"];
 
  $gst3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
 
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."<p>Вы не ввели наименование модели.</p>";
  }

  if (empty($_POST["startdate"])) 
  {
    $action = ""; 
    $error = $error."<p>Вы не ввели дату начала действия модели проекта.</p>";
  }

  if (empty($_POST["stopdate"])) 
  {
    $action = ""; 
    $error = $error."<p>Вы не ввели дату окончания действия модели проекта.</p>";
  }

  if (empty($_POST["checkdate1"])) 
  {
    $action = ""; 
    $error = $error."<p>Вы не ввели дату начала экспертизы.</p>";
  }

  if (empty($_POST["checkdate2"])) 
  {
    $action = ""; 
    $error = $error."<p>Вы не ввели дату окончания экспертизы.</p>";
  }

  
  $startdate = $_POST["startdate"];
  if(!preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$startdate,$i))
  {
    $action = ""; 
    $error = $error."<p>Вы ввели некорректно дату начала действия модели проекта.</p>";
  }

  $stopdate = $_POST["stopdate"];
  if(!preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$stopdate,$ii))
  {
    $action = ""; 
    $error = $error."<p>Вы ввели некорректно дату окончания действия модели проекта.</p>";
  }

  $checkdate1 = $_POST["checkdate1"];
  if(!preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$checkdate1,$ci))
  {
    $action = ""; 
    $error = $error."<p>Вы ввели некорректно дату начала экспертизы.</p>";
  }

  $checkdate2 = $_POST["checkdate2"];
  if(!preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$checkdate2,$cii))
  {
    $action = ""; 
    $error = $error."<p>Вы ввели некорректно дату окончания экспертизы.</p>";
  }
  
  $day=$i[1][0];
  $month=$i[2][0];
  $year=$i[3][0];
  $timestampstart = (mktime(0, 0, 0, $month, $day, $year));
  if(!checkdate($month,$day,$year))
  {
   $action=""; 
   $error = $error."<p>Вы ввели некорректно дату начала действия модели проекта.</p>";
  }

  $day2=$ii[1][0];
  $month2=$ii[2][0];
  $year2=$ii[3][0];
  $timestampstop = (mktime(0, 0, 0, $month2, $day2, $year2));
  if(!checkdate($month2,$day2,$year2))
  {
   $error = $error."<p>Вы ввели некорректно дату окончания действия модели проекта.</p>";
   $action=""; 
  }

  $cday=$ci[1][0];
  $cmonth=$ci[2][0];
  $cyear=$ci[3][0];
  $timestampcheck1 = (mktime(0, 0, 0, $cmonth, $cday, $cyear));
  if(!checkdate($cmonth,$cday,$cyear))
  {
   $error = $error."<p>Вы ввели некорректно дату начала экспертизы.</p>";
   $action=""; 
  }

  $cday2=$cii[1][0];
  $cmonth2=$cii[2][0];
  $cyear2=$cii[3][0];
  $timestampcheck2 = (mktime(0, 0, 0, $cmonth2, $cday2, $cyear2));
  if(!checkdate($cmonth2,$cday2,$cyear2))
  {
   $error = $error."<p>Вы ввели некорректно дату окончания экспертизы.</p>";
   $action=""; 
  }
  
  if ($timestampstart > $timestampstop)
  {
   $error = $error."<p>Дата начала действия модели больше дата окончания действия модели проекта. Скорректируйте даты модели проекта.</p>";
   $action=""; 
  }

  if ($timestampcheck1 > $timestampcheck2)
  {
   $error = $error."<p>Дата начала экспертизы больше дата окончания экспертизы проектов. Скорректируйте даты модели проекта.</p>";
   $action=""; 
  }

  if ($timestampcheck1 >= $timestampstart and $timestampcheck1 <= $timestampcheck2)
  {} else
  {
   $error = $error."<p>Дата начала экспертизы выходит за границы временного диапазона. Скорректируйте даты модели проекта.</p>";
   $action=""; 
  }

  if ($timestampcheck2 >= $timestampcheck1 and $timestampcheck2 <= $timestampstop)
  {} else
  {
   $error = $error."<p>Дата окончания экспертизы выходит за границы временного диапазона. Скорректируйте даты модели проекта.</p>";
   $action=""; 
  }
  
  if($_FILES["photo"]["name"]!=""){ 
          $origfilename = $_FILES["photo"]["name"]; 
          $filename = explode(".", $_FILES["photo"]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          for($x=0;$x<count($photo_file_types_array);$x++){ 
            if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
            } 
          } 
          if($file_ext_allow){ 
            if($_FILES["photo"]["size"]>100000){ 
              $error=$error."<p>Картинка: ".$origfilename." превышает размер 100 кбайт</p>";
              $action=""; 
            } 
          }else{ 
            $error=$error."<p>Картинка: ".$origfilename." не поддерживается.</p>"; 
            $action=""; 
          } 
    } 
  
  if (empty($action)) 
  {
    ?>
<script>
$(function() {
    $( "#error-message" ).dialog({
      width: 500,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      },
      close: function() {
        location.replace("<? echo $site; ?>/editproarr&id=<? echo $paid; ?>");
      }      
    });
  });
  </script>
  <div id="error-message" title="Ошибки">
     <? echo $error ?>
  </div>
   
    <?
    exit();
  }

     require_once ('lib/transliteration.inc');
     
     $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1;");
     if(!$res3) puterror("Ошибка 3 при изменении проекта.");
     $param = mysqli_fetch_array($res3);
         
     if($_FILES["photo"]["name"]!=""){ 
      $filedata = $_FILES["photo"]["name"]; 
      $realfiledata = transliteration_clean_filename($_FILES["photo"]["name"],"ru");
      $filesize = $_FILES["photo"]["size"]; 

      // Удалим файл - есди пользователь заменил его
      if (!empty($param['photoname'])) { 
       unlink($pa_upload_dir.$paid.$param['photoname']);
      }
     }
     else
     {
      $filedata = ""; 
      $realfiledata = "";
      $filesize = 0; 
     }

    if (!empty($param['photoname']) && !empty($_FILES["photo"]["name"])) { 
     $query2 = "UPDATE projectarray SET photoname = '".$realfiledata."' 
           WHERE id='".$paid."'";
     
     if(!mysqli_query($mysqli, $query2))
     {
      puterror("Ошибка 2 при изменении проекта.\n");
      break; 
     }
     
     } else {
    if (empty($param['photoname']) && !empty($_FILES["photo"]["name"])) { 
      $query2 = "UPDATE projectarray SET photoname = '".$realfiledata."' 
           WHERE id='".$paid."'";
      if(!mysqli_query($mysqli, $query2))
     {
      puterror("Ошибка 2 при изменении проекта.\n");
      break; 
     }

     } 
    }


    if($_FILES["photo"]["name"]!="")
    { 
          $origfilename = $_FILES["photo"]["name"]; 
          $filename = explode(".", $_FILES["photo"]["name"]); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          for($x=0;$x<count($photo_file_types_array);$x++){ 
            if($filenameext==$photo_file_types_array[$x]){ 
              $file_ext_allow = TRUE; 
            } 
          } 

          if($file_ext_allow)
          { 
            if($_FILES["photo"]["size"]<100000)
            { 
              move_uploaded_file($_FILES["photo"]["tmp_name"], $pa_upload_dir.$paid.$realfiledata); 
            } 
          } 
      } 

  
    $name = $_POST["name"];
    $startdate=$year."-".$month."-".$day;
    $stopdate=$year2."-".$month2."-".$day2;
    $checkdate1=$cyear."-".$cmonth."-".$cday;
    $checkdate2=$cyear2."-".$cmonth2."-".$cday2;
    $projectcount = $_POST["projectcount"];
    if (empty($projectcount)) 
      $projectcount = 1;
    $payment = $_POST["payment"];
    if (empty($payment)) 
      $payment = 0;
    $secretkey = generate_password(7);
    $expertkey = generate_password(7);
    $addcomment = $_POST["addcomment"];
    $paysumma = $_POST["paysumma"];
    if (empty($paysumma)) 
      $paysumma = 0;
    $moderatorverify = $_POST["moderatorverify"];
    $expertmailer = $_POST["expertmailer"];
    $comment = $_POST["comment"];
    $ocenka = $_POST["ocenka"];
    $testblock = $_POST["testblock"];
    
    if (empty($ocenka)) 
     $ocenka = 100;
    
    
    $fromid = USER_ID;
    $openexpert = $_POST["openexpert"];
    if (empty($openexpert)) 
      $openexpert = 0;
    $openproject = $_POST["openproject"];
    $knowsid = 0; // $_POST["knowsid"];
    $nowindow = $_POST["nowindow"];
    $enableinet = $_POST["enableinet"];
    if (empty($enableinet)) 
      $enableinet = 0;
    $adminproject = $_POST["adminproject"];
    if (empty($adminproject)) 
      $adminproject = 0;
    $closed = $_POST["closed"];
    $exlistname = $_POST["exlistname"];
    $defaultshablon = $_POST["defaultshablon"];
    $fotosize = $_POST["fotosize"];
    if (empty($fotosize)) 
      $fotosize = 1;
    $filesize = $_POST["filesize"];
    if (empty($filesize)) 
      $filesize = 1;
    $nodownload = $_POST["nodownload"];

    if ($openexpert>0)
      $openexperturl = md5("opened ".$name);
//            , secretkey = '".$secretkey."' 
//            , expertkey = '".$expertkey."' 

    mysqli_query($mysqli, "SET AUTOCOMMIT = 0");
    $query = "UPDATE projectarray SET name = '".$name."'
            , startdate = '".$startdate."' 
            , stopdate = '".$stopdate."' 
            , projectcount = ".$projectcount." 
            , addcomment = ".$addcomment." 
            , payment = ".$payment." 
            , paysumma = '".$paysumma."'
            , moderatorverify = ".$moderatorverify." 
            , expertmailer = ".$expertmailer."
            , comment = '".$comment."'
            , ocenka = ".$ocenka."
            , openexpert = ".$openexpert."
            , openproject = ".$openproject."
            , knowledge_id = ".$knowsid."
            , nowindow = ".$nowindow."
            , enableinet = ".$enableinet."
            , adminproject = ".$adminproject."
            , testblock = ".$testblock."
            , closed = ".$closed."
            , exlistname = '".$exlistname."'
            , checkdate1 = '".$checkdate1."' 
            , checkdate2 = '".$checkdate2."' 
            , defaultshablon = '".$defaultshablon."'
            , fotosize = ".$fotosize."
            , filesize = ".$filesize."
            , nodownload = ".$nodownload."
            , openexperturl = '".$openexperturl."'
           WHERE id='".$paid."'";

    if(mysqli_query($mysqli, $query))
    {
      mysqli_query($mysqli, "COMMIT");

      if ($enable_cache) update_cache('SELECT a.id, a.name FROM projectarray as a WHERE a.closed=0 ORDER BY a.id DESC');
      
      // Возвращаемся на главную страницу если всё прошло удачно
      print "<HTML><HEAD>\n";
      if ($closed==1)
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=parray&arc=1'>\n";
      else
       print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=parray'>\n";
      print "</HEAD></HTML>\n";
      exit();
    }
    else
    {
      mysqli_query($mysqli, "COMMIT");
      echo "<a href='parray'>Вернуться</a>";
      echo("<p>Ошибка при изменении шаблона.</p>");
      exit();
    }
    
 } 
}

if (empty($action)) 
{
  
  $paid = $_GET['id'];

  $gst3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectarray WHERE id='".$paid."' LIMIT 1;");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysqli_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
   
    maintab($mysqli, $paid, $projectarray['name'], $projectarray['testblock'], $projectarray['payment']);

    // Проверим есть ли супервизор в списке ограниченных
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM limitsupervisor WHERE proarrid='".$paid."' AND userid='".USER_ID."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $countlim = $tot2cnt['count(*)'];  
    mysqli_free_result($tot2);                         
   
?>

<style type="text/css">
.ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{font-size:inherit;}
div.ui-datepicker{font-size:12px;width:inherit;height:inherit;}
.ui-datepicker-title span{font-size:14px;}
.disabled {
   background: #D1D4D8;
   pointer-events: none;
   cursor: default;
}
</style>
<script>
  $(function() {
    
    $.datepicker.regional['ru'] = { 
      closeText: 'Закрыть',  prevText: '&#x3c;Пред',  nextText: 'След&#x3e;',  currentText: 'Сегодня',  monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',  'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],  monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',  'Июл','Авг','Сен','Окт','Ноя','Дек'],  dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],  dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],  dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],  dateFormat: 'dd.mm.yy',  firstDay: 1,  isRTL: false  };     
    $.datepicker.setDefaults($.datepicker.regional['ru']);

//    $('#model<? echo $paid ?>').addClass('disabled');

    $( "#ok" ).button();
    $( "#tabs" ).tabs();
    $( "#addcomment" ).buttonset();
    $( "#moderatorverify" ).buttonset();
    $( "#expertmailer" ).buttonset();
    $( "#openexpert" ).buttonset();
    $( "#openproject" ).buttonset();
    $( "#nowindow" ).buttonset();
    $( "#enableinet" ).buttonset();
    $( "#adminproject" ).buttonset();
    $( "#testblock" ).buttonset();
    $( "#closed" ).buttonset();
    $( "#payment" ).buttonset();
    $( "#fotosize" ).buttonset();
    $( "#filesize" ).buttonset();
    $( "#nodownload" ).buttonset();
    $( "#knowsid" ).selectmenu();
    $( "#projectcount" ).spinner({ min: 1 });
    $( "#paysumma" ).spinner({ min: 0 });
    $( "#ocenka" ).spinner({ min: 1 });
    
    $( "#startdate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#stopdate" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#stopdate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#startdate" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

    $( "#checkdate1" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#startdate" ).datepicker( "option", "maxDate", selectedDate );
        $( "#checkdate2" ).datepicker( "option", "minDate", selectedDate );
      }
      
    });

    $( "#checkdate2" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      dateFormat: "dd.mm.yy",
      onClose: function( selectedDate ) {
        $( "#stopdate" ).datepicker( "option", "minDate", selectedDate );
        $( "#checkdate1" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
    
  });

$(document).ready(function(){

    function checkRegexp(n, t) {
        return t.test(n.val())
    }
   
    function testDate(str) {
     str2=str.split(".");
     if(str2.length!=3){return false;}
     str2=str2[2] +'-'+ str2[1]+'-'+ str2[0];
     if (new Date(str2)=='Invalid Date')
      return false;
     else   
      return true;
    }

    function getDate(str) {
     str2=str.split(".");
     if(str2.length!=3){return false;}
     str2=str2[2] +'-'+ str2[1]+'-'+ str2[0];
     if (new Date(str2)=='Invalid Date')
      return false;
     else   
      return new Date(str2);
    }

    $('form').submit(function()
    {
     var hasError = false; 
     $(".iferror").hide();
     var name = $("#name");
     var startdate = $("#startdate");
     var stopdate = $("#stopdate");
     var checkdate1 = $("#checkdate1");
     var checkdate2 = $("#checkdate2");
     if(name.val()=='') {
            $("#tabs").tabs("option", "active", 0);
            $("#tabs").tabs("refresh");
            name.after('<span class="iferror"><strong>Введите наименование модели!</strong></span>');
            name.focus();
            hasError = true;
     }
     if(testDate(startdate.val())==false) {
            $("#tabs").tabs("option", "active", 1);
            $("#tabs").tabs("refresh");
            startdate.after('<span class="iferror"><strong>Введена некорректная дата!</strong></span>');
            startdate.focus();
            hasError = true;
     }
     if(testDate(stopdate.val())==false) {
            $("#tabs").tabs("option", "active", 1);
            $("#tabs").tabs("refresh");
            stopdate.after('<span class="iferror"><strong>Введена некорректная дата!</strong></span>');
            stopdate.focus();
            hasError = true;
     }
     if(testDate(checkdate1.val())==false) {
            $("#tabs").tabs("option", "active", 1);
            $("#tabs").tabs("refresh");
            checkdate1.after('<span class="iferror"><strong>Введена некорректная дата!</strong></span>');
            checkdate1.focus();
            hasError = true;
     }
     if(testDate(checkdate2.val())==false) {
            $("#tabs").tabs("option", "active", 1);
            $("#tabs").tabs("refresh");
            checkdate2.after('<span class="iferror"><strong>Введена некорректная дата!</strong></span>');
            checkdate2.focus();
            hasError = true;
     }
     var photo = $("#photo");
     if (photo.val()!='') {
     var photo2 = photo.val().search(/^.*\.(?:jpg|jpeg|png|gif)\s*$/ig);
     if(photo2!=0){
            $("#tabs").tabs("option", "active", 0);
            $("#tabs").tabs("refresh");
            photo.after('<span class="iferror"><strong>Недопустимое расширение имени файла!</strong></span>');
            photo.focus();
            hasError = true;
     }
     }
     if(hasError == true) {
       return false; 
     }
     else
     {
       $('input[type=submit]', $(this)).attr('disabled', 'disabled');
       $('input[type=button]', $(this)).attr('disabled', 'disabled');
       $("#spinner").fadeIn("slow");
       return true; 
     }
    });   
});

</script>
  <?
  $tot2 = mysqli_query($mysqli, "SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
  $tot2cnt = mysqli_fetch_array($tot2);
  $countpr = $tot2cnt['count(*)'];
  if ($countpr>0){  
?>

            <div class="ui-widget">
            	<div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание!</strong> Для выбранной модели в системе созданы проекты.</p>
            	</div>
            </div><p></p> 
 <? } ?>        

<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Для сведения!</strong> Общие настройки модели проекта позволяют определить большое количество параметров. Вы можете настраивать функционал модели на этапе экспертизы, определять временные интервалы действия модели, сделать размещение проектов в системе платным, добавить к модели сервис голосования и т.д. Желательно все настройки модели проекта производить до того момента, как в системе появится первый проект. <a href='page&id=57' target='_blank'>Подробнее.</a></p>
	</div>
</div>

<p></p>
<form action="editproarr" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="post">
<input type="hidden" name="paid" value="<? echo $_GET['id'];?>">

<div id="tabs">
  <ul>
    <li><a href="#Tab1">Общие</a></li>
    <li><a href="#Tab2">Период</a></li>
    <li><a href="#Tab3">Экспертиза</a></li>
    <li><a href="#Tab4">Проект</a></li>
    <li><a href="#Tab5">Файлы</a></li>
    <li><a href="#Tab6">Сервисы</a></li>
    <li><a href="#Tab7">Архивация</a></li>
  </ul>

<div id='Tab1'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr>
        <td width="400"><p class=ptd><b><em class=em>Наименование модели проекта (шаблона) *:</em></b></td>
        <td><input type="text" id="name" name="name" style="width:100%" value="<? echo $projectarray['name'];?>" title="Наименованием модели проекта определяется наименования в рейтингах, экспертных сообществах, открытых проектах и т.д."></td>
    </tr>
    <tr>
        <td><p class=ptd>Картинка модели:</p>
        <p><? 
        if (!empty($projectarray['photoname'])) 
        {
          if (stristr($projectarray['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$projectarray['id'].$projectarray['photoname']."' height='100'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$projectarray['photoname']."' height='100'><div>"; 
        }  
          ?></p>
       </td><td>
        <input type="file" id="photo" name="photo" style="width:100%"/><p class=ptd>Размер картинки не должен превышать 100кб.</p></td>
    </tr>
    <tr>
        <td><p class=ptd>Наименование экспертного листа по умолчанию:</td>
        <td><input type="text" name="exlistname" style="width:100%" value="<? echo $projectarray['exlistname']; ?>" title="Наименование экспертного листа требуется в случае если модель состоит из нескольких независимых экспертиз (этапов)."></td>
    </tr>
    <tr>
        <td><p class=ptd>Наименование первого раздела мультишаблона по умолчанию:</td>
        <td><input type="text" name="defaultshablon" style="width:100%" value="<? echo $projectarray['defaultshablon']; ?>" title="Наименование первого раздела мультишаблона требуется если шаблон модели состоит из нескольких разделов (мультишаблон)"></td>
    </tr>

        <? 
/*    echo'<tr>
        <td><p class=ptd><b><em class=em>Область знаний *:</em></b></td>
        <td><select name="knowsid" id="knowsid" title="Область знаний определяет принадлежность модели к определенной категории.">
        ';
         $know = mysqli_query($mysqli, "SELECT * FROM knowledge ORDER BY name");
          while($knowmember = mysqli_fetch_array($know))
          {
           if ($projectarray['knowledge_id']==$knowmember['id'])
            echo "<option value='".$knowmember['id']."' selected>".$knowmember['name']."</option>";
           else
            echo "<option value='".$knowmember['id']."'>".$knowmember['name']."</option>";
          }
        echo'</select></td>
    </tr>'; */
        ?>

    <tr>
     <td>
      <p class=ptd>Дополнительная информация о модели:</p></td>
      <td><textarea name="comment" style='width:100%' rows='7' title="Дополнительная информация о модели выводится в окне участника при создании проекта, в открытых рейтингах и т.д."><? echo $projectarray['comment']; ?></textarea>
    </td>
    </tr>
</table>
</div>    

<div id='Tab2'>
<table border="0" cellpadding=2 cellspacing=2>
    <tr>
        <td><p class=ptd><b><em class=em>Даты начала действия модели проекта (дата начала размещения проектов) и окончания действия модели проекта *:</em></b></td>
    </tr>
    <tr>
        <td>
         <input type='text' id="startdate" name="startdate" value='<? echo data_convert ($projectarray['startdate'], 1, 0, 0); ?>'> <i class='fa fa-question-circle  fa-lg' title="Дата начала действия модели проекта - дата начала размещения проектов или начала тестирования участников."></i>
         <input type='text' id="stopdate" name='stopdate' value='<? echo data_convert ($projectarray['stopdate'], 1, 0, 0); ?>'> <i class='fa fa-question-circle  fa-lg' title="Дата окончания действия модели проекта - дата окончания экспертизы и подведения итогов. Дату окончания можно при необходимости изменять, если не все эксперты успели закончить экспертизу."></i>
        </td>
    </tr>
    
    <tr>
        <td><p class=ptd><b><em class=em>Дата окончания размещения проектов (дата начала экспертизы) и дата окончания экспертизы*:</em></b></td>
    </tr>
    <tr>
        <td>
          <input type='text' id="checkdate1" name="checkdate1" value='<? echo data_convert ($projectarray['checkdate1'], 1, 0, 0); ?>'> <i class='fa fa-question-circle  fa-lg' title="Дата окончания размещения проектов является датой начала экспертизы. Должна быть позже даты начала действия модели проекта, но раньше даты окончания экспертизы."></i>
          <input type='text' id="checkdate2" name="checkdate2" value='<? echo data_convert ($projectarray['checkdate2'], 1, 0, 0); ?>'> <i class='fa fa-question-circle  fa-lg' title="Дата окончания экспертизы должна быть позже даты начала экспертизы, но раньше или совпадать с датой окончания действия модели проекта."></i>
        </td>
    </tr>
</table>
</div>    

<div id='Tab3'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr>
        <td width="50%"><p class=ptd><em class=em>Итоговая оценка экспертизы проекта:</em></td>
        <? if (LOWSUPERVISOR) {?>
         <script>
          $(function() {
           $( "#ocenka" ).spinner( "option", "disabled", true );
          });
         </script>
        <td><input type=text id=ocenka name=ocenka size=15 value='<? echo $projectarray['ocenka']; ?>' readonly='1'> <i class='fa fa-question-circle  fa-lg' title="Максимальный балл для расчета рейтинга при экспертизе проектов"></i></td>
        <?}else{?>
        <td><input type=text id=ocenka name=ocenka size=15 value='<? echo $projectarray['ocenka']; ?>' readonly="1"> <i class='fa fa-question-circle  fa-lg' title="Максимальный балл для расчета рейтинга при экспертизе проектов"></i></td>
        <?}?> 
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Эксперты могут оставлять замечания и комментарии в проектах:</em></td>
        <td>
        <div id="addcomment">
        <? if ($projectarray['addcomment']==1) { ?>
          <input type="radio" value='1' id="addcomment1" name="addcomment" checked="checked"><label for="addcomment1">Да</label>       
          <input type="radio" value='0' id="addcomment2" name="addcomment"><label for="addcomment2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="addcomment1" name="addcomment"><label for="addcomment1">Да</label>       
          <input type="radio" value='0' id="addcomment2" name="addcomment" checked="checked"><label for="addcomment2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="Разрешить или запретить замечания и комментарии в проектах для экспертов"></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Отправка готового проекта на проверку модератору (создателю модели проекта):</em></td>
        <td>
        <div id="moderatorverify">
        <? if ($projectarray['moderatorverify']==1) { ?>
          <input type="radio" value='1' id="moderatorverify1" name="moderatorverify" checked="checked"><label for="moderatorverify1">Да</label>       
          <input type="radio" value='0' id="moderatorverify2" name="moderatorverify"><label for="moderatorverify2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="moderatorverify1" name="moderatorverify"><label for="moderatorverify1">Да</label>       
          <input type="radio" value='0' id="moderatorverify2" name="moderatorverify" checked="checked"><label for="moderatorverify2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="Да - активировать алгоритм предварительной проверки подготовленных проектов модератором, Нет - подготовленные проекты направляются на экспертизу без предварительной проверки"></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Отправка уведомлений экспертам по электронной почте при изменении проекта:</em></td>
        <td>
        <div id="expertmailer">
        <? if ($projectarray['expertmailer']==1) { ?>
          <input type="radio" value='1' id="expertmailer1" name="expertmailer" checked="checked"><label for="expertmailer1">Да</label>       
          <input type="radio" value='0' id="expertmailer2" name="expertmailer"><label for="expertmailer2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="expertmailer1" name="expertmailer"><label for="expertmailer1">Да</label>       
          <input type="radio" value='0' id="expertmailer2" name="expertmailer" checked="checked"><label for="expertmailer2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - при большом количестве проектов на почту экспертов может приходить очень большое количество сообщений от системы."></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Открытая экспертиза:</em></td>
        <td>
        <div id="openexpert">
        <? if (LOWSUPERVISOR or $countlim>0) {?>
          <input type="radio" value='1' id="openexpert1" name="openexpert" disabled><label for="openexpert1">Да</label>       
          <input type="radio" value='0' id="openexpert2" name="openexpert" checked="checked" disabled><label for="openexpert2">Нет</label>       
        <?} else {?>
        <? if ($projectarray['openexpert']==1) { ?>
          <input type="radio" value='1' id="openexpert1" name="openexpert" checked="checked"><label for="openexpert1">Да</label>      
          <input type="radio" value='0' id="openexpert2" name="openexpert"><label for="openexpert2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="openexpert1" name="openexpert"><label for="openexpert1">Да</label>       
          <input type="radio" value='0' id="openexpert2" name="openexpert" checked="checked"><label for="openexpert2">Нет</label>       
         <? } ?>
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - эксперты могут оценивать проекты без обязательной регистрации и установки статуса эксперта (обезличенная экспертиза - все желающие могут оценить проекты по открытой ссылке). Нет - включается режим обычной экспертизы."></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Отображать содержимое проекта в экспертном листе:</em></td>
        <td>
        <div id="nowindow">
        <? if ($projectarray['nowindow']==1) { ?>
          <input type="radio" value='1' id="nowindow1" name="nowindow" checked="checked"><label for="nowindow1">Да</label>       
          <input type="radio" value='0' id="nowindow2" name="nowindow"><label for="nowindow2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="nowindow1" name="nowindow"><label for="nowindow1">Да</label>       
          <input type="radio" value='0' id="nowindow2" name="nowindow" checked="checked"><label for="nowindow2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - при экспертизе будет сразу показан проект. Если проект очень большой (содержит много файлов, текста), размещение его в экспертном листе будет очень тяжело восприниматься экспетом, так как все файлы проекта будут загружены в экспертном листе."></i>
        </div>
        </td>
    </tr>
</table>
</div>    

<div id='Tab4'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr>
        <td width="50%"><p class=ptd><em class=em>Сколько проектов может создать участник:</em></td>
        <? if (LOWSUPERVISOR or $countlim>0) {?>
         <script>
          $(function() {
           $( "#projectcount" ).spinner( "option", "disabled", true );
          });
         </script>
        <td><input type="text" name="projectcount" id="projectcount" size="5" readonly='1' value="<? echo $projectarray['projectcount'];?>"> <i class='fa fa-question-circle  fa-lg' title="Максимальное количество проектов, которое может создать участник в рамках модели"></i></td>
        <?} else {?>
        <td><input type="text" name="projectcount" id="projectcount" size="5" readonly="1" value="<? echo $projectarray['projectcount'];?>"> <i class='fa fa-question-circle  fa-lg' title="Максимальное количество проектов, которое может создать участник в рамках модели"></i></td>
        <?}?>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Проект платный:</em></td>
        <td>
        <div id="payment">
        <? if (LOWSUPERVISOR or $countlim>0) {?>
          <input type="radio" value='1' id="payment1" name="payment" disabled><label for="payment1">Да</label>       
          <input type="radio" value='0' id="payment2" name="payment" checked="checked" disabled><label for="payment2">Нет</label>       
        <?} else {?>
        <? if ($projectarray['payment']==1) { ?>
          <input type="radio" value='1' id="payment1" name="payment" checked="checked"><label for="payment1">Да</label>       
          <input type="radio" value='0' id="payment2" name="payment"><label for="payment2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="payment1" name="payment"><label for="payment1">Да</label>       
          <input type="radio" value='0' id="payment2" name="payment" checked="checked"><label for="payment2">Нет</label>       
         <? } ?>
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - размещение проекта для участника становится платным (подключается сервис платежной системы)"></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Стоимость оплаты за размещение проекта для одного участника (руб.):</em></td>
        <? if (LOWSUPERVISOR or $countlim>0) {?>
         <script>
          $(function() {
           $( "#paysumma" ).spinner( "option", "disabled", true );
          });
         </script>
        <td><input type=text id=paysumma name=paysumma size=5 readonly='1' value='<? echo $projectarray['paysumma']; ?>'> <i class='fa fa-question-circle  fa-lg' title="Стоимость оплаты за размещение проекта для одного участника в случае, если размещение проекта является платным"></i></td>
         <? } else
         { ?>
        <td><input type=text id=paysumma name=paysumma size=5 readonly="1" value='<? echo $projectarray['paysumma']; ?>'> <i class='fa fa-question-circle  fa-lg' title="Стоимость оплаты за размещение проекта для одного участника в случае, если размещение проекта является платным"></i></td>
         <? } ?>
    </tr>

    <tr>
        <td><p class=ptd><em class=em>Закрытый проект:</em></td>
        <td>
        <div id="adminproject">
        <? if (LOWSUPERVISOR) {?>
          <input type="radio" value='1' id="adminproject1" name="adminproject" disabled><label for="adminproject1">Да</label>       
          <input type="radio" value='0' id="adminproject2" name="adminproject" checked="checked" disabled><label for="adminproject2">Нет</label>       
        <?} else {?>
        <? if ($projectarray['adminproject']==1) { ?>
          <input type="radio" value='1' id="adminproject1" name="adminproject" checked="checked"><label for="adminproject1">Да</label>       
          <input type="radio" value='0' id="adminproject2" name="adminproject"><label for="adminproject2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="adminproject1" name="adminproject"><label for="adminproject1">Да</label>       
          <input type="radio" value='0' id="adminproject2" name="adminproject" checked="checked"><label for="adminproject2">Нет</label>       
         <? } ?>
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - включится блокировка возможности создания проекта для участника. Только супервизор сможет создавать новые проекты."></i>
        </div>
        </td>
    </tr>

    <tr>
        <td><p class=ptd><em class=em>Открытая модель проекта:</em></td>
        <td>
        <div id="openproject">
        <? if ($projectarray['openproject']==1) { ?>
          <input type="radio" value='1' id="openproject1" name="openproject" checked="checked"><label for="openproject1">Да</label>       
          <input type="radio" value='0' id="openproject2" name="openproject"><label for="openproject2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="openproject1" name="openproject"><label for="openproject1">Да</label>       
          <input type="radio" value='0' id="openproject2" name="openproject" checked="checked"><label for="openproject2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle fa-lg' title="В случае активации режима - проекты участников будут опубликованы в открытых рейтингах с возможностью публикации проекта."></i>
        </div>
        </td>
    </tr>
</table>
</div>    

<div id='Tab5'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr>
        <td width="50%"><p class=ptd><em class=em>Запрет скачивания файлов проекта на этапе экспертизы:</em></td>
        <td>
        <div id="nodownload">
        <? if ($projectarray['nodownload']==1) { ?>
          <input type="radio" value='1' id="nodownload1" name="nodownload" checked="checked"><label for="nodownload1">Да</label>       
          <input type="radio" value='0' id="nodownload2" name="nodownload"><label for="nodownload2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="nodownload1" name="nodownload"><label for="nodownload1">Да</label>       
          <input type="radio" value='0' id="nodownload2" name="nodownload" checked="checked"><label for="nodownload2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle fa-lg' title="Запретить скачивание файлов проекта на этапе экспертизы. Параметр повышает уровень конфиденциальности экспертизы."></i>
        </div>
        </td>
    </tr>


    <tr>
        <td><p class=ptd><em class=em>Максимальный размер загружаемой фотографии:</em></td>
        <td>
        <div id="fotosize">
        <? if (LOWSUPERVISOR) {?>
          <input type="radio" value='1' id="fotosize1" name="fotosize" checked disabled>
          <label for="fotosize1">1 Мб</label>       
          <input type="radio" value='2' id="fotosize2" name="fotosize" disabled>
          <label for="fotosize2">2 Мб</label>       
        <?}else{?>
          <input type="radio" value='1' id="fotosize1" name="fotosize" <? if ($projectarray['fotosize']==1) echo "checked='checked'" ?>>
          <label for="fotosize1">1 Мб</label>       
          <input type="radio" value='2' id="fotosize2" name="fotosize" <? if ($projectarray['fotosize']==2) echo "checked='checked'" ?>>
          <label for="fotosize2">2 Мб</label>       
        <?}?>
          &nbsp;<i class='fa fa-question-circle fa-lg' title="Параметр определяет максимальный размер любой фотографии загружаемой при создании проекта."></i>
        </div>
        </td>
    </tr>

    <tr>
        <td><p class=ptd><em class=em>Максимальный размер загружаемого файла:</em></td>
        <td>
        <div id="filesize">
        <? if (LOWSUPERVISOR) {?>
          <input type="radio" value='1' id="filesize1" name="filesize" checked disabled>
          <label for="filesize1">1 Мб</label>       
          <input type="radio" value='2' id="filesize2" name="filesize" disabled>
          <label for="filesize2">2 Мб</label>       
          <input type="radio" value='3' id="filesize3" name="filesize" disabled>
          <label for="filesize3">3 Мб</label>       
          <input type="radio" value='5' id="filesize5" name="filesize" disabled>
          <label for="filesize5">5 Мб</label>       
        <?}else{?>
          <input type="radio" value='1' id="filesize1" name="filesize" <? if ($projectarray['filesize']==1) echo "checked='checked'" ?>>
          <label for="filesize1">1 Мб</label>       
          <input type="radio" value='2' id="filesize2" name="filesize" <? if ($projectarray['filesize']==2) echo "checked='checked'" ?>>
          <label for="filesize2">2 Мб</label>       
          <input type="radio" value='3' id="filesize3" name="filesize" <? if ($projectarray['filesize']==3) echo "checked='checked'" ?>>
          <label for="filesize3">3 Мб</label>       
          <input type="radio" value='5' id="filesize5" name="filesize" <? if ($projectarray['filesize']==5) echo "checked='checked'" ?>>
          <label for="filesize5">5 Мб</label>       
        <?}?>
          &nbsp;<i class='fa fa-question-circle fa-lg' title="Параметр определяет максимальный размер любого файла загружаемого при создании проекта."></i>
        </div>
        </td>
    </tr>
</table>
</div>    


<div id='Tab6'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr>
        <td><p class=ptd><em class=em>Включить тестирование:</em></td>
        <td>
        <div id="testblock">
        <? if ($projectarray['testblock']==1) { ?>
          <input type="radio" value='1' id="testblock1" name="testblock" checked="checked"><label for="testblock1">Да</label>       
          <input type="radio" value='0' id="testblock2" name="testblock"><label for="testblock2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="testblock1" name="testblock"><label for="testblock1">Да</label>       
          <input type="radio" value='0' id="testblock2" name="testblock" checked="checked"><label for="testblock2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle fa-lg' title="В случае активации режима - участник должен будет пройти тестирование перед тем, как разместить проект или эксперт также должен будет пройти тестирование перед экспертизой проектов."></i>
        </div>
        </td>
    </tr>
    <tr>
        <td><p class=ptd><em class=em>Включить голосование:</em></td>
        <td>
        <div id="enableinet">
        <? if (LOWSUPERVISOR or $countlim>0) {?>
          <input type="radio" value='1' id="enableinet1" name="enableinet" disabled><label for="enableinet1">Да</label>       
          <input type="radio" value='0' id="enableinet2" name="enableinet" checked="checked" disabled><label for="enableinet2">Нет</label>       
        <?} else {?>
        <? if ($projectarray['enableinet']==1) { ?>
          <input type="radio" value='1' id="enableinet1" name="enableinet" checked="checked"><label for="enableinet1">Да</label>       
          <input type="radio" value='0' id="enableinet2" name="enableinet"><label for="enableinet2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="enableinet1" name="enableinet"><label for="enableinet1">Да</label>       
          <input type="radio" value='0' id="enableinet2" name="enableinet" checked="checked"><label for="enableinet2">Нет</label>       
         <? } ?>
         <? } ?>
        &nbsp;<i class='fa fa-question-circle  fa-lg' title="В случае активации режима - будет включен сервис интернет-голосования для проекта"></i>
        </div>
        </td>
    </tr>
</table>
</div>    

<div id='Tab7'>
<table border="0" width="100%" cellpadding=2 cellspacing=2>
    <tr align="center">
        <td width="100%">
        <div id="closed">
        <? if ($projectarray['closed']==1) { ?>
          <input type="radio" value='1' id="closed1" name="closed" checked="checked"><label for="closed1">Да</label>       
          <input type="radio" value='0' id="closed2" name="closed"><label for="closed2">Нет</label>       
         <? } else
         { ?>
          <input type="radio" value='1' id="closed1" name="closed"><label for="closed1">Да</label>       
          <input type="radio" value='0' id="closed2" name="closed" checked="checked"><label for="closed2">Нет</label>       
         <? } ?>
        &nbsp;<i class='fa fa-question-circle fa-lg' title="Архивация закрывает модель для изменения и блокирует экспертизу."></i>
        </div>
        </td>
    </tr>
</table>
</div>    

</div>

<table width="100%" border="0" cellpadding=2 cellspacing=2>
    <tr align="center">
        <td colspan="3">
            <p></p>
            <input id="ok" type="submit" value="Изменить модель">
        </td>
    </tr>           
</table>
</form>



<div id="spinner"></div>
<?php
  }
  include "bottomadmin.php";
}
} else die;
?>
