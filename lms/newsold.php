<?
  include "config.php";
  include "func.php";
  $title = "Новости";
  $titlepage="";  
  include "topadmin.php";

if (!USER_REGISTERED) { 

?>
<script>
	$(function() {
		var offset = $("#leftcol").offset();  
    var height = 565 - $("#leftcol").height();
    $( "#ok" ).button();
    $( "#newsbutton" ).button();
    $( "#topsbutton" ).button();
    $( "#publicbutton" ).button();
		$(window).scroll(function() {
			if ($(window).scrollTop() < height) {
				$("#leftcol").stop().animate({marginTop: $(window).scrollTop()});
			}
			else
      if ($(window).scrollTop() >= height) {
				$("#leftcol").stop().animate({marginTop: height});
			}
			else {
        $("#leftcol").stop().animate({marginTop: 0});
      };
    });
	});
</script>
<div id="pagewidth" style="margin-top: 10px;">
 <div id="leftcol">
   <? include "expertblock.php"; ?>
 </div>
 
 <div id="maincol2">
 <div id="container">
  <iframe id="prezi" width="100%" height="600" scrolling="no" frameborder="0" src="http://expert03.ru/prezi.php">
  </iframe>
 </div>
 </div>
</div>

<? } ?>
<script type="text/javascript"> 
  jQuery(document).ready(function() {  
      news();  
<? if (!USER_REGISTERED) { ?>
      document.getElementById("prezi").focus();
      tops();  
      publics();  
<? } ?>
$('.fancybox').fancybox();
  }); 
 
  var n; 
  var t;
  var p;

  function news(){    
  $("#spinner").fadeIn("slow");
  if(n==undefined) { 
     n=0; 
  } else { 
     n=n+3; 
  } 
  $.post('newsjson.php',{offset:n}, 
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1'){ 
        for(var i = 0; i <= obj.more.length; i++){                             
           var s='<div class="menu_glide_tops" itemscope itemtype="http://expert03.ru/page&id='+obj.more[i].id+'">'+
           '<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0">' +
           '<tr valign="top" align="left"><td valign="top" style="text-align: justify;"><h1 class="ztitle">' + obj.newsdate[i] + ': ' + obj.more[i].name + '</h1></td><tr>' +
           '<tr valign="top" align="left"><td valign="top" style="text-align: justify;">';
           if (obj.more[i].picurl.length > 0)
            s+='<div class="menu_glide_img"><img itemprop="photo" src="' + obj.more[i].picurl+ '" width="140" class="leftimg"></div>'; 
           s+=obj.more[i].content;
           if (obj.more[i].content2.length > 0)  
           {
					  s+="<p><a href='page&id="+obj.more[i].id+"'>Подробнее</a></p>";
           }      
           s+='</td></tr></table></div>';
           $('#newsresult').append(s);        
        }
      } 
      else 
      if(obj.ok=='3') { 
      $('#newsbutton').addClass('button_disabled').attr('disabled', true); 
      }                
    }); 
  $("#spinner").fadeOut("slow");
  }  

<? if (!USER_REGISTERED) { ?>
  function tops(){    
  $("#spinner").fadeIn("slow");
  if(t==undefined) { 
     t=0; 
  } else { 
     t=t+5; 
  } 
  $.post('topsjson2.php',{offset:t}, 
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
       $('#topsresult').append(obj.content);        
      else 
      if(obj.ok=='3') { 
       $('#topsbutton').addClass('button_disabled').attr('disabled', true); 
      }                
    }); 
  $("#spinner").fadeOut("slow");
  }  

  function publics(){    
  $("#spinner").fadeIn("slow");
  if(p==undefined) { 
     p=0; 
  } else { 
     p=p+20; 
  } 
  $.post('publicjson.php',{offset:p}, 
    function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
       $('#publicresult').append(obj.content);        
      else 
      if(obj.ok=='3') { 
       $('#publicbutton').addClass('button_disabled').attr('disabled', true); 
      }                
    }); 
  $("#spinner").fadeOut("slow");
  }  
<? } ?>
</script>

<div id="spinner"></div>

<? if (USER_REGISTERED) { ?>
<div id="container">
    <h1 class="ztitle2" align="center">Новости</h1>
    <div id="newsresult"></div> 
    <center><button onclick="news();" id="newsbutton" style="font-size: 1em;">Ещё новости</button></center> 
</div>
<? } else { ?>
<div id="container">
  <div id="sidebar1">
    <h1 class="ztitle2" align="center">Новости</h1>
    <div id="newsresult"></div> 
    <center><button onclick="news();" id="newsbutton" style="font-size: 1em;">Ещё новости</button></center> 
  </div>
  <div id="sidebar2">
    <h1 class="ztitle2" align="center">Рейтинги проектов</h1>
    <div id="topsresult"></div> 
    <center><button onclick="tops();" id="topsbutton" style="font-size: 1em;">Ещё рейтинги проектов</button></center> 
  </div> 
</div>

<script src="scripts/slider.js"></script>
<script>
$(function () {
      $("#slides1").responsiveSlides({
        auto: true,
        pagination: true,
        nav: true,
        fade: 500,
        maxwidth: 900,
        timeout: 14E3
      });
    });
</script>

    <h1 class="ztitle" align="center">Как создать проект и отправить его на экспертизу?</h1>
    <div class="rslides_container">
      <ul id="slides1" class="rslides">
        <li>
          <img src="img/user1.gif" alt="" />
          <h1 class="caption ztitle">После регистрации в системе необходимо выбрать пункт меню - Создать проект.</h1>
        </li>
        <li>
          <img src="img/user2.gif" alt="" />
          <h1 class="caption ztitle">Заполнить наименование проекта и требуемые значения. Если требуется прикрепить один или несколько файлов, а самих файлов пока нет, можно их добавить позже.</h1>
        </li>
        <li>
          <img src="img/user3.gif" alt="" />
          <h1 class="caption ztitle">После создания проекта в левом углу меню появится новый пункт - Мои проекты. Проекты можно редактировать или удалять пока они имеют статус - Создание.</h1>
        </li>
        <li>
          <img src="img/user4.gif" alt="" />
          <h1 class="caption ztitle">Выбираем пункт - Мои проекты. В списке появится созданный проект.</h1>
        </li>
        <li>
          <img src="img/user5.gif" alt="" />
          <h1 class="caption ztitle">Выбрав пункт - 'Изменить проект', в проект можно добавить новые файлы или изменить содержимое. К редактированию проекта можно возвращаться в любое время. Также проект можно удалить.</h1>
        </li>
        <li>
          <img src="img/user6-0.gif" alt="" />
          <h1 class="caption ztitle">После окончательной подготовки проекта, его можно отправить на экспертизу. Для этого необходимо изменить его статус. Выбираем пункт 'Изменить статус'.</h1>
        </li>
        <li>
          <img src="img/user6.gif" alt="" />
          <h1 class="caption ztitle">Новый статус проекта - 'подготовлен к экспертизе' скажет экспертам, что необходимо приступать к экспертизе и выставлять оценки. Система также предупреждает, что после изменения статуса проект нельзя будет редактировать и удалять.</h1>
        </li>
        <li>
          <img src="img/user7.gif" alt="" />
          <h1 class="caption ztitle">Наблюдать процесс экспертизы проекта можно в меню 'Рейтинги'.</h1>
        </li>
        <li>
          <img src="img/user8.gif" alt="" />
          <h1 class="caption ztitle">Любой пользователь может в режиме реального времени наблюдать за процессом экспертизы через открытые рейтинги системы.</h1>
        </li>
      </ul>
    </div>

<div id="container">
 <h1 class="ztitle2" align="center">Опубликованные проекты</h1>
 <div id="publicresult"></div> 
 <center><button onclick="publics();" id="publicbutton" style="font-size: 1em;">Ещё опубликованные проекты</button></center> 
</div>
<p></p>
<div id="container">
 <h1 class="ztitle2" align="center">О сервисе</h1>
<?
  $n = mysql_query("SELECT * FROM news WHERE id='45'");
  if (!$n) puterror("Ошибка при обращении к базе данных");
  $nmember = mysql_fetch_array($n);
  echo "<div class='menu_glide_tops' id='about'><table  width='100%'' border='0' cellpadding=0 cellspacing=0>";
  echo "<tr valign='top' align='left'><td valign='top'>";

  if (!empty($nmember['picurl']))
   echo "<img src=".$nmember['picurl']." width='140' class='leftimg'>"; 

  if ($nmember['pagetype']=='page')
   echo htmlspecialchars_decode($nmember['content']);
  else
   echo htmlspecialchars_decode($nmember['content2']);

  include "social.php";
  echo "</td></tr></table></div>";

?>
</div>
<p></p>
<? include "diskus.php"; ?>
<p></p>

<div id="container" style="text-align: center;">
<object type="application/x-shockwave-flash" data="http://promo.cloudmouse.com/promo/banner-728x90-1.swf?link1=http://cloudmouse.com/%3Fi=2308" width="728" height="90">Облачные VPS сервера от 5$ под ваши проекты</object>
</div>

<?  } ?>  <p></p>
<?
  include "bottomadmin.php";
?>