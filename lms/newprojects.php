<?php
 if(!defined("USER_REGISTERED")) die;  
 
 include "config.php";
 include "func.php";

 $title = "Создание нового проекта";
 $titlepage=$title;  

 include "topadmin.php";

 $tableheader = "class=tableheaderhide";

     ?>
     
<script>
   $(document).ready(function() {
			$('.fancybox').fancybox();
 	 });
   
  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    

  function showresult(signature,tid){
    $.fancybox.close();
		$.fancybox.open({
					href : 'testresults&tid='+tid+'&sign='+signature+'&url=newprojects',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          showCloseButton : false,
          modal : true,
					padding : 5
				});
   }

  function closeFancybox(){
    $.fancybox.close();
   }
</script>
<style type="text/css">
.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
}
.ui-widget { font-family: Verdana,Arial,sans-serif; font-size: 0.7em;}
p {
  font: 16px / 1.4 'Helvetica', 'Arial', sans-serif;
}   
</style>
           
     <?

// Проверим заполненность профиля
if (ProfileDone(USER_ID))  
{

$kk=0;
$zz=0;

$date3 = date("d.m.Y");
preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$i);
$day=$i[1][0];
$month=$i[2][0];
$year=$i[3][0];
$timestamp3 = (mktime(0, 0, 0, $month, $day, $year));


$gst = mysqli_query($mysqli,"SELECT p.* FROM projectarray as p, prousers as u WHERE u.proarrid=p.id AND u.userid='".USER_ID."' AND p.closed='0' ORDER BY p.id");
if (!$gst) 
 puterror("Ошибка при обращении к базе данных");
 

 while($member = mysqli_fetch_array($gst))
  {
    // Начнем сравнение дат
    $date1 = $member['startdate'];
    $date2 = $member['checkdate1'];

    $arr1 = explode(" ", $date1);
    $arr2 = explode(" ", $date2);  
    $arrdate1 = explode("-", $arr1[0]);
    $arrdate2 = explode("-", $arr2[0]);
    $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
    $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));

  
    if ($timestamp3 >= $timestamp1 and $timestamp3 <= $timestamp2) 
    {
    
     if ($member['adminproject']==0)
     {
     
     $payed = true;
     if ($member['payment']>0) 
     {
      
      // Проверим на оплату
      $paysumma = $member['paysumma'];
      $sum1 = mysqli_query($mysqli,"SELECT * FROM money WHERE proarrid='".$member['id']."'");
      if (!$sum1) puterror("Ошибка при обращении к базе данных");
      $usumma = 0; 
      while ($s1 = mysqli_fetch_array($sum1))
      {
       $order1 = mysqli_query($mysqli,"SELECT * FROM orders WHERE id='".$s1['orderid']."' LIMIT 1");
       if (!$order1) puterror("Ошибка при обращении к базе данных");
       $o1 = mysqli_fetch_array($order1);
       if ($o1['userid']==USER_ID) 
        $usumma += $s1['summa'];
      }
      if ($usumma < $paysumma) {
       $payed = false;
       $paylink = "robokassa&p=".$member['id'];
      } 
     }
     
     // Сколько пользователь создал проектов
     $gst1 = mysqli_query($mysqli,"SELECT count(*) FROM projects WHERE userid='".USER_ID."' AND proarrid='".$member['id']."'");
     if (!$gst1) puterror("Ошибка при обращении к базе данных");
     $total3 = mysqli_fetch_array($gst1);
     $count3 = $total3['count(*)'];
     
      if ($count3<$member['projectcount'])
      {
      
       
      // Добавлено тестирование
      $goahead = 1;
      $paid = $member['id'];
      if ($member['testblock']>0) 
      {
        // Проверим результат, если результат есть и порог пройден - допутим к созданию проекта
        $userid = USER_ID;
        // Найдем тест, который должен пройти участник
        $test = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE testfor='member' AND enable='1' AND proarrid='".$paid."' ORDER BY id LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        // echo $testdata['id'];
        $res = mysqli_query($mysqli,"SELECT * FROM singleresult WHERE userid='".$userid."' AND testid='".$testdata['id']."' ORDER BY id;");
        if (!$res) 
          puterror("Ошибка при обращении к базе данных");
        $maxuserball=0;
        $attempts=0;
        // Просканируем попытки участника
        while($resdata = mysqli_fetch_array($res))
         {
           $attempts++;
           $percent = (int) floor($resdata['rightball'] / $resdata['allball'] * 100);
           if ($percent > $maxuserball)
            $maxuserball = $percent;
         } 
        mysqli_free_result($res); 
        //echo $testdata['maxball'];
        // Нашли максимальный балл
        if ($maxuserball < $testdata['maxball'])
          {
           // Участник не может проходить дальше - нужно пройти тест
           $goahead = 0;
           $testname = $testdata['name'];
           $testsign = $testdata['signature'];
           $testball = $testdata['maxball'];
           $testattempt = $testdata['attempt'];
           
    ?>
    
<script type="text/javascript">
		$(document).ready(function() {
    	$("#viewtest<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewtest&s=<? echo $testsign ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
      });
</script>
    
    <?
           
          }
        mysqli_free_result($test); 
      }


      if ($goahead==0 and $member['testblock']>0)
        {
        // Если нет - предложим тест
        $zz++;
        $test = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE testfor='member' AND enable='1' AND proarrid='".$paid."' LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        $cntatt = $testattempt - $attempts;
        echo "<div class='menu_glide_tops'>";
        echo "<table border='0'>";
        echo "<tr><td>";

        if ($testattempt>0 and $cntatt<=0)
        {
  ?>
           <div class="ui-widget">
	            <div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Попытки пройти тест "<? echo $testname; ?>" закончились. Размещение проекта "<? echo $member['name']; ?>" запрещено.</p>
            	</div>
            </div>
   <?
        }
        else
        {
        

        if (!empty($member['photoname']))
         {      
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100' class='leftimg'><div>"; 
         }
        echo "<p>Перед размещением проекта '".$member['name']."' необходимо пройти тест.</p>";

      ?>
 <script>
  $(function() {
    $( "#viewtest<? echo $member['id']; ?>" ).button();
  });
</script>
      <?

        if ($testattempt==0)
         echo "<p><a style='font-size:1em;' id='viewtest".$member['id']."' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a></p>";
        else
        {
         if ($cntatt>0)
           echo "<p><a style='font-size:1em;' id='viewtest".$member['id']."' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a> Осталось попыток: ".$cntatt."</p>";
        }
        
        echo "<p><font face='Tahoma,Arial' size=-1>Для успешного прохождения теста и получения возможности создания проекта, необходимо набрать не менее ".$testball."% баллов.</font></p>";
        
        }
        echo "<p><font face='Tahoma,Arial' size=-1>".$member['comment']."</font></p>";
        echo "<p><font face='Tahoma,Arial' size=-1>Сроки размещения проектов: с ".data_convert ($member['startdate'], 1, 0, 0)." по ".data_convert ($member['checkdate1'], 1, 0, 0)."</font></p>";
        if ($member['payment']>0) 
         echo "<p><font face='Tahoma,Arial' size=-1>Стоимость размещения проекта: ".$member['paysumma']." руб. </font></p>";
        echo "</td></tr>"; 
        echo "</table></div>";
        mysqli_free_result($test); 
      }
      else
      // Покажем меню созданиия проекта
      if ($goahead==1)
      {
      $zz++;
      echo "<div class='menu_glide_tops'>";
      echo "<table border='0'>";
      echo "<tr><td>";
      
      if (!empty($member['photoname']))
       {      
       if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'  class='leftimg'><div>"; 
       }

  $paid = $member['id'];
  $btot = mysqli_query($mysqli,"SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."'");
  $bgst = mysqli_query($mysqli,"SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
  if (!$bgst || !$btot) puterror("Ошибка при обращении к базе данных");

  $btot2cnt = mysqli_fetch_array($btot);
  $bcountkg = $btot2cnt['count(*)'];

     if ($bcountkg==0)
     {
     
     
      echo "<p>Создать новый проект:</p>";
      if ($member['payment']==0)
       echo "<p><h2><a title='Создать новый проект' href='".$site."/editproject&mode=add&paid=".$member['id']."'>";
      else
       { if ($payed)
           echo "<p><h2><a title='Создать новый проект' href='".$site."/editproject&mode=add&paid=".$member['id']."'>";
         else
           echo "<p><h2><a title='Создать новый проект' href='#'>";
       }
     }
     else
     {
      echo "<p>Создать новый проект:</p>";

      if ($member['payment']==0)
       echo "<p><h2><a title='Создать новый раздел проекта' href='".$site."/editproject&mode=add&paid=".$member['id']."&multi=0'>";
      else
       { if ($payed)
          echo "<p><h2><a title='Создать новый раздел проекта' href='".$site."/editproject&mode=add&paid=".$member['id']."&multi=0'>";
         else
          echo "<p><h2><a title='Создать новый раздел проекта' href='#'>";
       }

     }
      
      $df = $member['defaultshablon'];
      $bi=1;
      if (!empty($df)) 
      {
       if ($bcountkg==0)
        echo "Раздел проекта : ".$df;
       else
        echo "Раздел проекта №".$bi.": ".$df;
      }
      else 
      {
        if ($bcountkg==0)
         echo $member['name'];
        else
         echo "Раздел проекта №".$bi;
      }
      echo "</a></h2></p>";

  
  if ($bcountkg>0) {
    $rr=1;
    while($bmember = mysqli_fetch_array($bgst))
    {
     if ($member['payment']==0)
       echo "<p><h2><a title='Создать новый раздел проекта - ".$bmember['info']."' href='".$site."/editproject&mode=add&paid=".$member['id'].
       "&multi=".$rr++."'>Раздел проекта №".++$bi.": ".$bmember['name']."</a></h2></p>";
      else
       { if ($payed)
          echo "<p><h2><a title='Создать новый раздел проекта - ".$bmember['info']."' href='".$site."/editproject&mode=add&paid=".$member['id'].
         "&multi=".$rr++."'>Раздел проекта №".++$bi.": ".$bmember['name']."</a></h2></p>";
         else
         {
           echo "<p><h2><a title='Создать новый раздел проекта - ".$bmember['info']."' href='#'>
           Раздел проекта №".++$bi.": ".$bmember['name']."</a></h2></p>";
         }
       }  
    }
  }    
      echo "<p><font face='Tahoma,Arial' size=-1>".$member['comment']."</font></p>";
      echo "<p><font face='Tahoma,Arial' size=-1>Сроки размещения проектов: с ".data_convert ($member['startdate'], 1, 0, 0)." по ".data_convert ($member['checkdate1'], 1, 0, 0)."</font></p>";
      if (($member['paysumma']>0) and (!$payed)) 
      {
       echo "<p><font face='Tahoma,Arial' size=-1>Стоимость услуги размещения проекта: ".$member['paysumma']." руб.</p>";
       
?>

<script type="text/javascript">
$(document).ready(function() {
 
    $('#submit<? echo $paid ?>').click(function() { 
 
        $(".iferror").hide();
        var hasError = false;
 
        if(!document.getElementById('oferta<? echo $paid ?>').checked) {
            $("#oferta<? echo $paid ?>").after('<span class="iferror">Требуется согласие с условиями договора-оферты! </span>');
            hasError = true;
        }
        if(!document.getElementById('personal<? echo $paid ?>').checked) {
            $("#personal<? echo $paid ?>").after('<span class="iferror">Требуется согласие с условиями соглашения о персональных данных! </span>');
            hasError = true;
        }
         
        if(hasError == true) { return false; }
 
    });
});
</script>
       
<?       
       echo "<form action='robokassa' method='post'>
       <p><label><input type='checkbox' id='oferta".$paid."' name='oferta".$paid."'>Я согласен(согласна) с условиями <a href='docs/dogoferta.rtf' target='_blank'>договора-оферты.</a></label></p>";
       echo "<p><label><input type='checkbox' id='personal".$paid."' name='personal".$paid."'>Я согласен(согласна) с условиями <a href='docs/personal.rtf' target='_blank'>соглашения о персональных данных.</a></label></p>";
       echo "<input type='hidden' name='p' value='".$paid."'>";
       echo "<p><input type='submit' id='submit".$paid."' style='font-size: 120%;' value='Продолжить оплату'></p></form>";
       //echo "<p>Оплачивая услугу, Вы соглашаетесь с условиями <a href='docs/dogoferta.rtf' target='_blank'>договора-оферты</a>. <a href='".$paylink."' title='Оплата услуги размещения проекта'>Перейти к оплате.</a></p>";
      
      }
       echo "</td></tr>"; 
       echo "</table></div>";
      }
      
      }
     }
     else
     // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     
     if ($member['adminproject']==1 and (defined("IN_ADMIN") or defined("IN_SUPERVISOR")))
     {
      // Добавлено тестирование
      $goahead = 1;
      $paid = $member['id'];
      if ($member['testblock']>0) 
      {
        // Проверим результат, если результат есть и порог пройден - допутим к созданию проекта
        $userid = USER_ID;
        // Найдем тест, который должен пройти участник
        $test = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE testfor='member' AND enable='1' AND proarrid='".$paid."' ORDER BY id LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        $res = mysqli_query($mysqli,"SELECT * FROM singleresult WHERE userid='".$userid."' AND testid='".$testdata['id']."' ORDER BY id;");
        if (!$res) 
          puterror("Ошибка при обращении к базе данных");
        $maxuserball=0;
        $attempts=0;
        // Просканируем попытки участника
        while($resdata = mysqli_fetch_array($res))
         {
           $attempts++;
           $percent = (int) floor($resdata['rightball'] / $resdata['allball'] * 100);
           if ($percent > $maxuserball)
            $maxuserball = $percent;
         } 
        mysqli_free_result($res); 
        // Нашли максимальный балл
        if ($maxuserball < $testdata['maxball'])
          {
           // Участник не может проходить дальше - нужно пройти тест
           $goahead = 0;
           $testname = $testdata['name'];
           $testid = $testdata['id'];
           $testball = $testdata['maxball'];
           $testattempt = $testdata['attempt'];
           
    ?>
    
<script type="text/javascript">
		$(document).ready(function() {
    	$("#viewtest<?php echo $member['id']; ?>").click(function() {
				$.fancybox.open({
					href : 'viewtest&paid=<? echo $paid ?>&id=<? echo $testid ?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});
      });
</script>
    
    <?
           
          }
        mysqli_free_result($test); 
      }


      if ($goahead==0 and $member['testblock']>0)
        {
        // Если нет - предложим тест
        $zz++;
        $test = mysqli_query($mysqli,"SELECT * FROM testgroups WHERE testfor='member' AND enable='1' AND proarrid='".$paid."' LIMIT 1;");
        if (!$test) 
          puterror("Ошибка при обращении к базе данных");
        $testdata = mysqli_fetch_array($test);
        $cntatt = $testattempt - $attempts;
        echo "<div class='menu_glide_tops'>";
        echo "<table border='0'>";
        echo "<tr><td>";

        if ($testattempt>0 and $cntatt<=0)
        {
  ?>
           <div class="ui-widget">
	            <div class="ui-state-error ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Попытки пройти тест "<? echo $testname; ?>" закончились. Размещение проекта "<? echo $member['name']; ?>" запрещено.</p>
            	</div>
            </div>
   <?
        }
        else
        {
        

        if (!empty($member['photoname']))
         {      
          if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'  class='leftimg'><div>"; 
         }
        echo "<p>Перед размещением проекта '".$member['name']."' необходимо пройти тест.</p>";

      ?>
 <script>
  $(function() {
    $( "#viewtest<? echo $member['id']; ?>" ).button();
  });
</script>
      <?

        if ($testattempt==0)
         echo "<p><a style='font-size:1em;' id='viewtest".$member['id']."' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a></p>";
        else
        {
         if ($cntatt>0)
           echo "<p><a style='font-size:1em;' id='viewtest".$member['id']."' href='javascript:;'><i class='fa fa-question fa-lg'></i>&nbsp;".$testname."</a> Осталось попыток: ".$cntatt."</p>";
        }
        
        echo "<p><font face='Tahoma,Arial' size=-1>Для успешного прохождения теста и получения возможности создания проекта, необходимо набрать не менее ".$testball."% баллов.</font></p>";
        
        }
        echo "<p><font face='Tahoma,Arial' size=-1>".$member['comment']."</font></p>";
        echo "<p><font face='Tahoma,Arial' size=-1>Сроки размещения проектов: с ".data_convert ($member['startdate'], 1, 0, 0)." по ".data_convert ($member['checkdate1'], 1, 0, 0)."</font></p>";
        if ($member['payment']>0) 
         echo "<p><font face='Tahoma,Arial' size=-1>Стоимость размещения проекта: ".$member['paysumma']." руб. </font></p>";
        echo "</td></tr>"; 
        echo "</table></div>";
        mysqli_free_result($test); 
      }
      else
      // Покажем меню созданиия проекта
      if ($goahead==1)
      {
      $zz++;
      echo "<div class='menu_glide_tops'>";
      echo "<table border='0'>";
      echo "<tr><td>";

      if (!empty($member['photoname']))
       {      
       if (stristr($member['photoname'],'http') === FALSE)
           echo "<div class='menu_glide_img'><img src='".$pa_upload_dir.$member['id'].$member['photoname']."' height='100'  class='leftimg'></div>"; 
          else
           echo "<div class='menu_glide_img'><img src='".$member['photoname']."' height='100'  class='leftimg'><div>"; 
       }

  $paid = $member['id'];
  $btot = mysqli_query($mysqli,"SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."'");
  $bgst = mysqli_query($mysqli,"SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
  if (!$bgst || !$btot) puterror("Ошибка при обращении к базе данных");

  $btot2cnt = mysqli_fetch_array($btot);
  $bcountkg = $btot2cnt['count(*)'];

     if ($bcountkg==0)
     {
      ?>
 <script>
  $(function() {
    $( "#link<? echo $member['id']; ?>" ).button();
  });
</script>
      <?
     
      echo "<p>Создать новый проект:</p>";
      echo "<p><h2><a id='link".$member['id']."' title='Создать новый проект' href='".$site."/editproject&mode=add&paid=".$member['id']."'>";
     }
     else
     {

      ?>
 <script>
  $(function() {
    $( "#sublink0<? echo $member['id']; ?>" ).button();
  });
</script>
      <?

      echo "<p>Создать новый проект:</p>";
      echo "<p><h2><a id='sublink0".$member['id']."' title='Создать новый раздел проекта' href='".$site."/editproject&mode=add&paid=".$member['id']."&multi=0'>";
     }
      
      $df = $member['defaultshablon'];
      $bi=1;
      if (!empty($df)) 
       echo "Раздел проекта №".$bi.": ".$df;
      else 
       {
        if ($bcountkg==0)
         echo $member['name'];
        else
         echo "Раздел проекта №".$bi;
       }
      echo "</a></h2></p>";

  
  if ($bcountkg>0) {
    $rr=1;
    echo "<p>";
    while($bmember = mysqli_fetch_array($bgst))
    {

      ?>
 <script>
  $(function() {
    $( "#sublink<? echo $rr.$member['id']; ?>" ).button();
  });
</script>
      <?

      echo " <h2 style='display:inline-block;'><a id='sublink".$rr.$member['id']."' title='Создать новый раздел проекта' href='".$site."/editproject&mode=add&paid=".$member['id'].
      "&multi=".$rr++."'>Раздел проекта №".++$bi.": ".$bmember['name']."</a></h2>";
    }
    echo "</p>";
    
  }    
      echo "<p><font face='Tahoma,Arial' size=-1>".$member['comment']."</font></p>";
      echo "<p><font face='Tahoma,Arial' size=-1>Сроки размещения проектов: с ".data_convert ($member['startdate'], 1, 0, 0)." по ".data_convert ($member['checkdate1'], 1, 0, 0)."</font></p>";
      if ($member['payment']>0) 
       echo "<p><font face='Tahoma,Arial' size=-1>Стоимость размещения проекта: ".$member['paysumma']." руб. </font></p>";

      echo "</td></tr>"; 
      echo "</table></div>";
     }
     
     }

    }
    
  }



  if ($zz==0)
  {
  ?>
            <div class="ui-widget">
	            <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; padding: 0 .7em;">
               <p align="center">Нет новых проектов.</p>
            	</div>
            </div><p></p>
   <?
  }
}
  include "bottomadmin.php";
?>
