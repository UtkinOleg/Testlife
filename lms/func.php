<?php
  function check_user_in_system($mysqli, $email)
  {
   if (!empty($email))
   {
   $user = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, usertype FROM users WHERE email='".$email."' LIMIT 1;");
   $userdata = mysqli_fetch_array($user);
   $userid = $userdata['id'];  
   $usertype = $userdata['usertype'];
   mysqli_free_result($user);   
   
   $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM prousers WHERE email='".$email."' AND userid=0;");
   $tot2cnt = mysqli_fetch_array($tot2);
   $countu = $tot2cnt['count(*)'];  
   mysqli_free_result($tot2);   
   
   $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM proexperts WHERE email='".$email."' AND expertid=0;");
   $tot2cnt = mysqli_fetch_array($tot2);
   $counte = $tot2cnt['count(*)'];  
   mysqli_free_result($tot2);   
   
   mysqli_query($mysqli,"START TRANSACTION;");
   if ($countu>0) 
    {
       $prousr = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM prousers WHERE email='".$email."' AND userid='0'");
       while ($member = mysqli_fetch_array($prousr))
         mysqli_query($mysqli,"UPDATE prousers SET userid='".$userid."' WHERE id='".$member['id']."'");
       mysqli_free_result($prousr);   
    }                     
   if ($counte>0) 
    {
       $proex = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM proexperts WHERE email='".$email."' AND expertid=0;");
       while ($member = mysqli_fetch_array($proex))
         mysqli_query($mysqli,"UPDATE proexperts SET expertid='".$userid."' WHERE id='".$member['id']."'");
       mysqli_free_result($proex);   
       if ($usertype=='user')
       {
        mysqli_query($mysqli,"UPDATE users SET usertype='expert' WHERE id='".$userid."'");
       }

    }               
   mysqli_query($mysqli,"COMMIT");
   }       
  }

  function msghead($fio, $site)
  {
    $s='<body style="margin:0; padding:0;">
   <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background-color:#F8F8F8;" align="center"><tr>
     <td style="margin:0; padding:0;">
     <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;background-color:#497787;" align="center"><tr>
      <td height="10"/></tr>
      <tr><td align="center">
      <h1 style="margin-top:10px;margin-bottom:10px;">
        <a href="'.$site.'" title="Экспертная система оценки проектов" target="_blank">
         <img src="'.$site.'/img/logoexpert.gif" alt="Экспертная система оценки проектов" style="border:none"/></a>
      </h1>
     </td></tr>
    </table>
   </td></tr>
  </table>
   <table cellpadding="0" cellspacing="0" width="500" style="border-collapse:collapse;background-color:#FFF;" align="center"><tr>
   <tr><td> 
      <table cellpadding="0" cellspacing="0" width="500" style="padding: 0 1em; font: 14px / 1.4 \'Helvetica\', \'Arial\', sans-serif;color:#000;border-collapse:collapse;margin:0">
      <tr>
       <td align="center" colspan="3" valign="top">
        <br><p style="font-size:1.5em;">Здравствуйте '.$fio.'!</p><br>
       </td>
      </tr>
      <tr>
       <td align="justify" colspan="3" valign="bottom">';
    return $s;
  }
  
  function msgtail($site)
  {
   $s='<p style="font-size:12px;">Не отвечайте на данное сообщение.</p>
        <hr>
        <p>С уважением, команда разработчиков сервиса <a href="'.$site.'" title="Экспертная система оценки проектов" target="_blank">expert03.ru</a></p>
       </td>
      </tr>
      </table>
   </td></tr>
   </table>
   </body>';
   return $s;
  }
  
  function ProfileDone($userid)
  {
       $ptot2 = mysql_query("SELECT email, job FROM users WHERE id='".$userid."' LIMIT 1");
       if (!$ptot2) puterror("Ошибка при обращении к базе данных");
       $ptotal2 = mysql_fetch_array($ptot2);
       $pemail = $ptotal2['email'];       
       $pjob = $ptotal2['job'];  
       mysql_free_result($ptot2); 
       if (empty($pemail) or empty($pjob))
         return false;
       else
         return true;
  }

  function isUrl($val)  
  {  
        if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $val, $matches))  
        {  
            return true;  
        }  
        return false;  
  }  
  
  function maintab($mysqli, $paid, $name, $test, $payment, $showheader=false)
  {
    if ($showheader)
     echo "<p align='center'><h1 class='z1'>".$name."</h1></p>";

   if (LOWSUPERVISOR) // Для бесплатного супервизора - три участника
   {
  ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Тариф бесплатный.</strong> Можно создать только одну модель с ограниченными функциями: максимум два параметра в шаблоне проекта, один вычисляемый показатель, максимум два критерия в экспертном листе, один эксперт в системе, один тест и максимум три участника (тестируемых). Также закрыт доступ к расширенной аналитике по экспертизе проектов и тестированию. Сменить на тариф <a title="Сменить на тариф Ограниченный" id="limit" href="javascript:;"><strong>Ограниченный</strong></a> или сменить на тариф <a title="Сменить на тариф Базовый" id="base" href="javascript:;"><strong>Базовый</strong></a></p>
            	</div>
           </div><p></p>    
  <?
   }
   else
   {
    // Проверим есть ли супервизор в списке ограниченных
    $tot2 = mysqli_query($mysqli,"SELECT count(*) FROM limitsupervisor WHERE proarrid='".$paid."' AND userid='".USER_ID."'");
    $tot2cnt = mysqli_fetch_array($tot2);
    $countlim = $tot2cnt['count(*)'];  
    mysqli_free_result($tot2);                         
    if ($countlim > 0)
    {
     // ограниченный супервизор - двадцать участников
  ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Для данной модели действуют ограничения:</strong> Максимум пять экспертов и двадцать участников. Отключено голосование, открытая экспертиза и платный конкурс. Сменить на тариф <a title="Сменить на тариф Базовый" id="base" href="javascript:;"><strong>Базовый</strong></a></p>
            	</div>
           </div><p></p>    
  <?
    }
   }

    $totl = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT photoname, openexpert FROM projectarray WHERE id='".$paid."' LIMIT 1;");
    $totall = mysqli_fetch_array($totl);
    $photoname = $totall['photoname'];  // Картинка
    $openexpert = $totall['openexpert'];
    mysqli_free_result($totl);

    $totl = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE proarrid='".$paid."'");
    $totall = mysqli_fetch_array($totl);
    $countp = $totall['count(*)'];  // Количество проектов
    mysqli_free_result($totl);

    $totl = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM poptions WHERE proarrid='".$paid."'");
    $totall = mysqli_fetch_array($totl);
    $countpo = $totall['count(*)'];  // Количество параметров шаблона проекта
    mysqli_free_result($totl);

    $totl = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablon WHERE proarrid='".$paid."'");
    $totall = mysqli_fetch_array($totl);
    $countps = $totall['count(*)'];  // Количество критериев
    mysqli_free_result($totl);

    $totl = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb as s, projects as p WHERE p.id=s.memberid AND p.proarrid='".$paid."'");
    $totall = mysqli_fetch_array($totl);
    $countl = $totall['count(*)'];   // Количество листов
    mysqli_free_result($totl);
    
    $tot3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM prousers WHERE proarrid='".$paid."'");
    $tot3cnt = mysqli_fetch_array($tot3);
    $countu = $tot3cnt['count(*)'];   // Количество участников
    mysqli_free_result($tot3);

    $tot3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE proarrid='".$paid."'");
    $tot3cnt = mysqli_fetch_array($tot3);
    $countt = $tot3cnt['count(*)'];   // Количество test
    mysqli_free_result($tot3);

    $tot3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM proexperts WHERE proarrid='".$paid."'");
    $tot3cnt = mysqli_fetch_array($tot3);
    $counte = $tot3cnt['count(*)'];   // Количество экспертов
    mysqli_free_result($tot3);

     
    ?>
  
  <script type="text/javascript">

	$(document).ready(function() {
      var countp = <? echo $countp; ?>;
      var countl = <? echo $countl; ?>;
      var countpo = <? echo $countpo; ?>;    
      $( "#model<? echo $paid?>" ).button();
      $( "#pop<? echo $paid?>" ).button();
      $( "#pind<? echo $paid?>" ).button();
      if (countpo < 2)
       $('#pind<? echo $paid?>').button( "option", "disabled", true ); 
      $( "#shab<? echo $paid?>" ).button();
      $( "#proj<? echo $paid?>" ).button();
      $( "#lists<? echo $paid?>" ).button();
      $( "#expert<? echo $paid?>" ).button();
      <?if ($openexpert>0){?>
      $('#expert<? echo $paid?>').button( "option", "disabled", true ); 
      <?}?>
      $( "#user<? echo $paid?>" ).button();
      $( "#test<? echo $paid?>" ).button();
      $( "#createtest<? echo $paid?>" ).button();
      $( "#createshablon<? echo $paid?>" ).button();
      $( "#createlist<? echo $paid?>" ).button();
      if (countp > 0)
       $('#createshablon<? echo $paid?>').button( "option", "disabled", true ); 
      if (countl > 0)
       $('#createlist<? echo $paid?>').button( "option", "disabled", true );  
      $( "#oplata<? echo $paid?>" ).button();
      $( "#del<? echo $paid?>" ).button();
      $("#createtest<? echo $paid?>").click(function() {
				$.fancybox.open({
					href : 'createtest&paid=<? echo $paid?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});      
      $("#createshablon<? echo $paid?>").click(function() {
				$.fancybox.open({
					href : 'createshablon&mode=project&paid=<? echo $paid?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
          fitToView : true,
          autoSize : false,
          modal : true,
          showCloseButton : false,
					padding : 5
				});
			});      
      $("#createlist<? echo $paid?>").click(function() {
				$.fancybox.open({
					href : 'createshablon&mode=list&paid=<? echo $paid?>',
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : 700,
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


    if (!empty($photoname))
     {      
       if (stristr($photoname,'http') === FALSE)    
           echo "<div class='menu_glide_img'><img title='".$name."' src='uploads/pavatars/".$paid.$photoname."' height='45' class='leftimg' style='padding:10px;'></div>"; 
          else
           echo "<div class='menu_glide_img'><img title='".$name."' src='".$photoname."' height='45' class='leftimg' style='padding:10px;'><div>"; 
     }

    echo "<div id='toolbar' style='text-align:left; background:#fff; padding:5px;' class='ui-widget-header ui-corner-all'>";
    echo "<a id='model".$paid."' href='editproarr&id=".$paid."' class=link title='Настройка параметров модели'><i class='fa fa-cogs fa-lg'></i> ".$name."</a>";
    echo "<p></p>";
    echo "<a id='createshablon".$paid."' href='javascript:;' title='Создать шаблон'><i class='fa fa-magic fa-lg'></i>&nbsp;<i class='fa fa-cog fa-lg'></i></a>&nbsp;";
    echo "<a id='pop".$paid."' href='poptions&paid=".$paid."' title='Настройка параметров шаблона проекта'><i class='fa fa-cog fa-lg'></i> ".$countpo."</a>&nbsp;"; 
    echo "<a id='createlist".$paid."' href='javascript:;' title='Создать экспертный лист'><i class='fa fa-magic fa-lg'></i>&nbsp;<i class='fa fa-thumbs-up fa-lg'></i></a>&nbsp;";
    echo "<a id='shab".$paid."' href='shablons&paid=".$paid."' title='Настройка критериев экспертных листов'><i class='fa fa-thumbs-up fa-lg'></i> ".$countps."</a>&nbsp;"; 
    echo "<a id='pind".$paid."' href='pindicator&paid=".$paid."' title='Настройка вычисляемых показателей'><i class='fa fa-cube fa-lg'></i>&nbsp;</a>";
 //   echo "<p></p>";
    echo "&nbsp;<a id='user".$paid."' href='prousers&paid=".$paid."' class=link title='Определение (добавление) участников'><i class='fa fa-user fa-lg'></i> ".$countu."</a>";
    echo "  <a id='proj".$paid."' title='Просмотр проектов' href='projects&paid=".$paid."'><i class='fa fa-book fa-lg'></i> ".$countp."</a>";
    echo "  <a id='expert".$paid."' href='proexperts&paid=".$paid."' class=link title='Определение (добавление) экспертов'><i class='fa fa-user-md fa-lg'></i> ".$counte."</a>";
    echo "  <a id='lists".$paid."' title='Просмотр введенных экспертных листов' href='lists&paid=".$paid."'><i class='fa fa-thumbs-o-up fa-lg'></i> ".$countl."</a>";

    if ($test==1) {
     echo "  <a id='createtest".$paid."' href='javascript:;' class=link title='Создать тест'><i class='fa fa-magic fa-lg'></i>&nbsp;<i class='fa fa-question-circle fa-lg'></i></a>";
     echo "  <a id='test".$paid."' href='testoptions&paid=".$paid."' class=link title='Список тестов модели'><i class='fa fa-question-circle fa-lg'></i> ".$countt."</a>";
    }
    
    if ($payment>0) 
     {
      $tot4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT SUM(summa) FROM money WHERE proarrid='".$paid."'");
      $sum = mysqli_fetch_array($tot4, MYSQLI_NUM);
      $sum0 = $sum[0];
      mysqli_free_result($tot4);
      echo "  <a id='oplata".$paid."' href='oplata&paid=".$paid."' class=link title='Просмотр поступлений и ручное разрешение доступа при оплате проектов'><i class='fa fa-dollar fa-lg'></i> ".$sum0." руб.</a>";
     }
    
    if ($countp==0 and $countl==0 and $countt==0) {
    ?>&nbsp;<a id='del<?echo $paid?>' href="#" class=link onClick="DelWindow(<? echo $paid;?> ,0,'delproarr','parray','шаблон')" title="Удалить модель"><i class='fa fa-trash fa-lg'></i>&nbsp;</a>
    <? } 
    
    //echo "</b></p>";
    echo "</div><p></p>";
  }
  
  function viewlist($mysqli, $paid, $listid, $listball, $exlistid)
  {
    echo"<table class=bodytable border='0' width='100%' height='100%' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
    
    $res2=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablongroups WHERE proarrid='".$paid."' AND exlistid='".$exlistid."' ORDER BY id");
    while($group = mysqli_fetch_array($res2))
    { 
    echo"<tr>";
        echo"<td>";
            echo"<p><b>".$group['name']."</b></p></td>";
    echo"</tr>";
    
    $subtotal = 0; 
    $res3=mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablon WHERE proarrid='".$paid."' AND groupid='".$group['id']."' ORDER BY id");
    while($param = mysqli_fetch_array($res3))
    { 
     echo"<tr>";
     $query4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM leafs WHERE shablonid='".$param['id']."' AND shablondbid='".$listid."'");
     $r4 = mysqli_fetch_array($query4);
     echo"<td><font face='Tahoma,Arial' size='-1'>".$param['name']."</font></td>";
     echo"<td align='center'><font face='Tahoma,Arial' size='-1'>".$r4['ball']."</font></td>";
     echo"</tr>";
     $subtotal = $subtotal + $r4['ball']; 
     mysqli_free_result($query4);
    } 
     echo"<tr><td><p><b>Итого по группе:</b></p></td>";
     echo"<td align='center'><p><b>".$subtotal."</b></p></td></tr>";
    }  
     if ($listball>0) 
     { 
      echo"<tr><td><p><b>Итого:</b></p></td>";
      echo"<td align='center'><p><b>".$listball."</b></p></td></tr>";
     }
     echo "</table>";
  
  }
  
  function viewp($mysqli, $pid, $upload_dir)   
  {
   $gst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projects WHERE id='".$pid."' LIMIT 1");
   if (!$gst) puterror("Ошибка при обращении к базе данных");
   $member = mysqli_fetch_array($gst);

   $paid = $member['proarrid'];

   $pa1 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT defaultshablon FROM projectarray WHERE id='".$paid."' LIMIT 1");
   $paa1 = mysqli_fetch_array($pa1);
   $daf = $paa1['defaultshablon'];

   $btot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM blockcontentnames WHERE proarrid='".$paid."'");
   if (!$btot) puterror("Ошибка при обращении к базе данных");
   $totbcnt = mysqli_fetch_array($btot);
   $countb = $totbcnt['count(*)'];

   $slidercounter=0;
   
  if (!empty($member)) 
  {
  
  if ($countb>0) 
  {
  // Заголовок ---------------------------------------------------
  if (isUrl($member['info']))
  {
   if (preg_match("/http:/i", $member['info'])>0)
    $v.="<p align='center' style='font-size:18px;'><a href='".$member['info']."' target='_blank'>".$member['info']."</a></p>";   
   else
    $v.="<p align='center' style='font-size:18px;'><a href='http://".$member['info']."' target='_blank'>".$member['info']."</a></p>";   
  }
  else
   $v.="<p align='center' style='font-size:18px;'>".$member['info']."</p>";   
  }
  // Табы ----------------------------------------------------------------
  $v.= "<script type='text/javascript'>";  
  $v.= "$(function() {";
  $v.= "$( '#tabs' ).tabs({";
  $v.= "beforeLoad: function( event, ui ) {";
  $v.= "ui.jqXHR.error(function() {";
  $v.= "ui.panel.html('Загрузка содержания...');";
  $v.= "      });";
  $v.= "    }";
  $v.= "   });";
  $v.= " });";
  $v.= "</script>";
  $v.= "<div id='tabs'>";
  if ($countb>0)
  {
   $bgst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM blockcontentnames WHERE proarrid='".$paid."' ORDER BY id");
   if ($bgst) 
   {
    $v.= "<ul><li><a href='viewtab.php?id=".$pid."&paid=".$paid."&tabid=0'>".$daf."</a></li>";
    while($block = mysqli_fetch_array($bgst))
    {
     $multiid = $block['id'];
     $v.= "<li><a href='viewtab.php?id=".$pid."&paid=".$paid."&tabid=".$multiid."' title='".$block['info']."'>".$block['name']."</a></li>";
    }
    $v.= "</ul>"; 
   }
  }
  else
   {
    if (empty($daf))
     $v.= "<ul><li><a href='viewtab.php?id=".$pid."&paid=".$paid."&tabid=0'>".$member['info']."</a></li></ul>";
    else
     $v.= "<ul><li><a href='viewtab.php?id=".$pid."&paid=".$paid."&tabid=0'>".$daf."</a></li></ul>";
   }
  
  // Покажем вычисляемые показатели
  $v.= "<p><div id='menu_glide' class='menu_glide'><table width='98%' align='center' border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM pindicator WHERE proarrid='".$member['proarrid']."' ORDER BY id");
  if (!$res3) puterror("Ошибка при обращении к базе данных");
  while($param = mysqli_fetch_array($res3))
   { 
      $v.="<tr><td><p>".$param['name'].":</p></td></tr>";

      $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['poptionid1']."'");
      $param4 = mysqli_fetch_array($res4);
      $ind1 = $param4['content'];

      $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM projectdata WHERE projectid='".$member['id']."' AND optionsid='".$param['poptionid2']."'");
      $param4 = mysqli_fetch_array($res4);
      $ind2 = $param4['content'];
      
      if ($param['operation'] == 'mul')
       $res = $ind1 * $ind2;
      else
      if ($param['operation'] == 'div') 
      {
          if ($ind2>0)
           $res = round(($ind1 / $ind2) * 100,2);
          else
           $res = 0;
      }
      else
      if ($param['operation'] == 'sum') 
        $res = $ind1 + $ind2;
      else
      if ($param['operation'] == 'sub') 
        $res = $ind1 - $ind2;
        
     $v.="<tr><td>".$res."</td></tr>";
   }

  $v.="<tr><td align='center'><p>Дата создания проекта: <b>".data_convert ($member['regdate'], 1, 0, 0)."</b> ";   

  $v.=" Статус проекта: ";   
  if ($member['status']=='created') 
    {
     $v.="<b>создание и изменение</b></p>";
    }
    else
    if ($member['status']=='accepted') 
    {
     $v.="<b>подготовлен к экспертизе</b></p>";
    }
    else
    if ($member['status']=='inprocess') 
    {
     $v.= "<b>проходит экспертизу</b></p>";
    }
    else
    if ($member['status']=='finalized') 
    {
     $v.= "<b>экспертная оценка проекта завершена.<b></p><p class=help>Итоговый балл: <b>".round($member['maxball'],2)."</b></p>";
    }
    else
    if ($member['status']=='published') 
     $v.= "<b>опубликован в сети<b></p>";

   $v.= "<p>
   <a href='http://expert03.ru' target='_blank'>Экспертная система оценки проектов</a>
   </p></td></tr>";
   $v.= "</table><div></p>";

  
    if ($member['status']=='finalized' or $member['status']=='published') 
    {
      $v.= "<p><div id='menu_glide' class='menu_glide'>
        <table width='100%' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
      $res5 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablondb WHERE memberid='".$member['id']."' ORDER BY puttime");
      if (!$res5) puterror("Ошибка при обращении к базе данных");
      while($param5 = mysqli_fetch_array($res5))
      { 
       if (!empty($param5['info']))
        $v.= "<tr><td><p class=zag2><b>Рецензия или комментарий от ".data_convert ($param5['puttime'], 1, 1, 0).":</b> ".$param5['info']."</p></td></tr>";
      } 
      $v.= "</table></div></p>";
    }
  

  $res3cnt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM comments WHERE projectid='".$member['id']."'");
  $param3cnt = mysqli_fetch_array($res3cnt);
  $count = $param3cnt['count(*)'];
  if ($count>0) 
  {
  $v.= "<p><table width='100%' class='bodytable' border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
//  $v.= "<tr class='tableheader'><td><p class=help>Замечания и комментарии к проекту от экспертов</p></td></tr>";   
  
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM comments WHERE projectid='".$member['id']."' ORDER BY cdate DESC");
  while($param3 = mysqli_fetch_array($res3))
   { 
      $res4 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT userfio FROM users WHERE id='".$param3['expertid']."'");
      $param4 = mysqli_fetch_array($res4);
      
      $v.= "<tr><td><hr><p><b>Эксперт ".$param4['userfio']." от ".data_convert ( $param3['cdate'], 1, 1, 0)." оставил комментарий (замечание):</b></p>
      <p>".$param3['content']."</p></td></tr>";   

      // Установим статус - комментарий прочтен
      //if ($param3['readed']==0) {
      //  $query = "UPDATE projects SET readed='1' WHERE id=".$param3["id"];
      //  mysql_query($query);
      //}

   } 

   $v.= "</table></p>";
  }
  } 
   return $v;
  }


  function viewc($mysqli, $pid, $upload_dir)
  {
  

  $tableheader = "class=tableheaderhide";
  $v= "<p align='center'>";

  $res3cnt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM shablondb WHERE memberid='".$pid."' AND LENGTH(info)>0");
  $param3cnt = mysqli_fetch_array($res3cnt);
  $count = $param3cnt['count(*)'];
  if ($count>0) 
  {
  $v.= "<div id='menu_glide' class='menu_glide'><table width='100%' class=bodytable border='0' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>";
  
  $res3 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM shablondb WHERE memberid='".$pid."' AND LENGTH(info)>0");
  while($param3 = mysqli_fetch_array($res3))
   { 
      $v.= "<tr><td><p><b>".data_convert ( $param3['puttime'], 1, 0, 0).":</b></p>
      <p>".$param3['info']."</p><hr></td></tr>";   
   } 
   $v.= "</table></div>";
  }
   $v.= "</p>";

   return $v;
  }
  
  function puterror($message)
  {
    echo("<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></head><body><p align='center'>$message</p></body></html>");
    exit();
  }

  function data_convert($data, $year, $time, $second){
  $res = "";
  $part = explode(" " , $data);
  $ymd = explode ("-", $part[0]);
  $hms = explode (":", $part[1]);
  if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
  if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
  return $res;
  }

function update_cache($sql)
{
require_once('cache.php');
$cache = new Cache();
$filename = md5($sql) . '_' . strlen($sql) . '.tmp';
$gst2 = mysql_query($sql);
if(!$gst2) die;

if ($sql == 'SELECT id,userfio,email,usertype FROM users ORDER BY userfio')
{
 while($member = mysql_fetch_array($gst2))
     $data[] = array('id' => $member['id'], 'userfio' => $member['userfio'], 
                     'usertype' => $member['usertype'], 'email' => $member['email']);
}
else
if ($sql == 'SELECT a.id, a.name FROM projectarray as a WHERE a.closed=0 ORDER BY a.id DESC')
{
 while($member = mysql_fetch_array($gst2))
     $data[] = array('id' => $member['id'], 'name' => $member['name']);
}

$cache->delete($filename);
$cache->write($filename, $data);
}
  
function writelog($msg)
{     
     $ip = getenv ("REMOTE_ADDR");
     if (defined("USER_ID"))
     {
      $userid = USER_ID;
      $userfio = USER_FIO;
      $query = "INSERT INTO logs VALUES (0,
                                        NOW(),
                                        $userid,
                                        '$msg',
                                        '$ip','$userfio');";
     }
     else
     {
      $query = "INSERT INTO logs VALUES (0,
                                        NOW(),
                                        0,
                                        '$msg',
                                        '$ip','');";
     } 
    
    if(!mysql_query($query))
    puterror("Ошибка при обращении к базе данных.");
     
}
  
function generate_password($number)
 
   {
 
     $arr = array('a','b','c','d','e','f',
 
                  'g','h','i','j','k','l',
 
                  'm','n','o','p','r','s',
 
                  't','u','v','x','y','z',
 
                  'A','B','C','D','E','F',
 
                  'G','H','I','J','K','L',
 
                  'M','N','O','P','R','S',
 
                  'T','U','V','X','Y','Z',
 
                  '1','2','3','4','5','6',
 
                  '7','8','9','0');
 
     // Генерируем пароль
 
     $pass = "";
 
     for($i = 0; $i < $number; $i++)
 
     {
 
       // Вычисляем случайный индекс массива
 
       $index = rand(0, count($arr) - 1);
 
       $pass .= $arr[$index];
 
     }
 
     return $pass;
 
   }
   
function dt2sql($dt) {
     //определяем какой разделитель использован в дате (любое символ, кроме цифр и пробела)
     preg_match("/([^0-9 ])/",$dt,$m);
     if(!isset($m[1])) return FALSE;
     //экранируем в разделитель т.к. он может являться управляющим символом регулярных выражений
     $s = preg_quote($m[1],"/");
     //разбиваем строку на день, месяц и год    
     preg_match("/([\d]{1,2})".$s."([\d]{1,2})".$s."([\d]{2,4})/",$dt,$m);
	     //выводим дату в формате mySQL
	     return isset($m[1]) ? y24($m[3])."-".a0($m[2])."-".a0($m[1]) : FALSE;    
	 }
	
//дополнительная функция, ставящая 0 перед цифрой
function a0($v) {    
	     return preg_match("/^\d$/",$v) ? "0".$v : $v;
}    
	
	 //преобразует год 09 в 2009. В качестве параметра $c задается префикс = век+1
function y24($v,$c = 20) {
	     return preg_match("/^\d{2}$/",$v) ? $c.$v : $v;
}    

      
?>