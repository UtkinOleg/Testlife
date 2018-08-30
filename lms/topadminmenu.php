<script>
$(document).ready(function(){
    $( document ).tooltip({
      items: "[data-menu]",
      position: {
          my: "left top",
          at: "right+10 top+5"
      },
      content: function() {
        var element = $( this );
        if ( element.is( "[data-menu]" ) ) {
          return element.attr( "title" );
        }
      }   
    });  
    
    $(window).scroll(function () {
      if ($(this).scrollTop() > 0) {$('#scroller').fadeIn();} else {$('#scroller').fadeOut();}
    });
    
    $('#scroller').click(function () {
      $('body,html').animate({scrollTop: 0}, 500); return false;
    });
 });
</script>
<style>
    label { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 0.7em; margin: .6em 0; }
    .ui-dialog .ui-state-error { padding: .3em;}
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .validateTips2 { border: 1px solid transparent; padding: 0.3em; }
    .validateRegTips { border: 1px solid transparent; padding: 0.3em; }
    .validateForgotTips { border: 1px solid transparent; padding: 0.3em; }
    .ui-widget { font-size: 75%; }  
    .ui-widget-header { background: #497787 50% 50% repeat-x; }
    .ui-dialog .ui-dialog-title { color: #FFFFFF; }
    #menu_glide h3 { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #FFFFFF; margin: 0; padding: 0.4em; text-align: center; }
</style>

<? if (USER_REGISTERED) { ?>

<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
 <style>
.badge {
  font-family: Verdana,Arial,sans-serif;
  display: block;
  height: 1em;
  line-height: 1em;
  padding: 3px 7px;
  font-size: 14px;
  font-weight: 700;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  background: #497787 url("lms/scripts/jquery-ui/images/ui-bg_inset-soft_75_497787_1x100.png") 50% 50% repeat-x;
}

.smenu, .smenu-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    list-style-type: none;
    margin: 0;
    padding: 0;
    background: #f7f7f7;
    z-index:10;  
    overflow:hidden;
    box-shadow: 2px 0 18px rgba(0, 0, 0, 0.26);
}
.smenu li a{
  display: block;
  text-indent: -500em;
  height: 3em;
  width: 3em;
  line-height: 3em;
  text-align:center;
  color: #497787;
  position: relative;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  transition: background 0.1s ease-in-out;
}
.smenu li a:before {
  font-family: FontAwesome;
  speak: none;
  text-indent: 0em;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  font-size: 1.4em;
}
.smenu li a.results:before {             
  content: "\f080";
}
.smenu li a.profile:before {
  content: "\f007";
}
.smenu li a.tests:before {
  content: "\f128";
}
.smenu li a.messages:before {
  content: "\f003";
}
.smenu li a.exit:before {
  content: "\f090";
}
.smenu li a.home:before {
  content: "\f0c9";
}
.smenu li a.expert:before {
  content: "\f087";
}
.smenu li a.supervisor:before {
  content: "\f185";
}
.smenu li a.supervisor2:before {
  content: "\f12e";
}
.smenu li a.admin:before {
  content: "\f013";
}
.smenu-bar li a:hover,
.smenu li a:hover {
  background: #497787;
  color: #fff;
}
.smenu-bar{
    overflow:hidden;
    left:3em;
    z-index:5;
    width:0;
    height:0;
    transition: all 0.1s ease-in-out;
}
.smenu-bar li a{
  display: block;
  height: 3em;
  line-height: 3em;
  text-align:center;
  color: #497787;
  text-decoration:none;  
  position: relative;
  font-family:verdana;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  transition: background 0.1s ease-in-out;
}

.smenu-bar li:first-child a{
    height: 3em;
    background: #497787 url("lms/scripts/jquery-ui/images/ui-bg_inset-soft_75_497787_1x100.png") 50% 50% repeat-x;
    color: #f7f7f7;
    pointer-events: none;
    cursor: default;
} 

.para{
    color:#033f72;
    padding-left:100px;
    font-size:3em;
    margin-bottom:20px;
}

.open{
    width:10em;
    height:100%;
}

@media all and (max-width: 500px) {
    .container{
        margin-top:100px;
    }
    .smenu{
        height:5em;
        width:100%;
    }
    .smenu li{
        display:inline-block;
        float:left;
    }
    .smenu-bar li a{
        width:100%;
    }
    .smenu-bar{
        width:100%;
        left:0;
        height:0;
    }
    .open{
        width:100%;
        height:auto;
    }
    .para{
    padding-left:5px;
}  
}
@media screen and (max-height: 34em){
  .smenu li,
  .smenu-bar,
  .badge {
    font-size:70%;
  }
}
@media screen and (max-height: 34em) and (max-width: 500px){
  .smenu,
  .badge {
        height:2em;
    }
}
</style>
<script type="text/javascript" src="lms/scripts/ssmenu.js"></script> 
</head>
<body style='background-color:#e6e6e6;'>
<?
       $ctot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM projects WHERE userid='".USER_ID."'");
       $ctotal = mysqli_fetch_array($ctot);
       $count = $ctotal['count(*)'];
       mysqli_free_result($ctot);
?>
    <ul class="smenu">
      <li><a title="Тестирование" href="tests" class="tests" data-menu="">Тестирование</a></li>
      <li><a title="Мои результаты" href="results" class="results" data-menu="">Мои результаты</a></li>
      <? if ($count>0){ ?>
       <span title="Результатов - <? echo $count;?>" class="badge" data-menu=""><?=$count?></span>
      <?}?>
      <? if (defined("IN_SUPERVISOR")) { ?>
       <li><a href="#" id="button-supervisor2" class="menu-button supervisor2">Тестирование</a></li>
      <?
       $ctot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE ownerid='".USER_ID."'");
       $ctotal = mysqli_fetch_array($ctot);
       $count = $ctotal['count(*)'];
       mysqli_free_result($ctot);
       if ($count>0){
      ?>
       <span data-menu="" title="Создано тестов - <? echo $count;?>" class="badge"><? echo $count;?></span>
      <?}?>
      <?}?>
      <? if (defined("IN_ADMIN")) { ?>
       <li><a href="#" id="button-admin" class="menu-button admin">Администратор</a></li>
      <?}?>
      <li data-menu="" title="Профиль"><a href="welcome" class="profile">Мой профиль</a></li>
      <li data-menu="" title="Сообщения"><a href="usermsgs" class="messages">Сообщения</a></li>
      <li data-menu="" title="Выход"><a href="logout" class="exit">Выход</a></li>
    </ul>

<?     
 if (defined("IN_SUPERVISOR")) { ?>

<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
		});
   function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
   }    
   function closeFancybox(){
    $.fancybox.close();
   }    
</script>                        

    <ul id="supervisor-menu2" class="smenu-bar">    
        <li><a href="#" id="button-supervisor-h2" class="menu-button"><i class='fa fa-puzzle-piece fa-lg'></i> Создатель</a></li>
        <li data-menu="" title="Создание тестов, вопросов, определение областей знаний"><a href="knows">Конструктор</a></li>
    <?
     if(defined("IN_ADMIN")) 
       $ctot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM singleresult as s;");
     else
       $ctot = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups as t, singleresult as s WHERE s.testid=t.id AND t.ownerid=".USER_ID.";");
     $stotal = mysqli_fetch_array($ctot);
     $scount = $stotal['count(*)'];
     mysqli_free_result($ctot);
     if ($scount>0){
    ?>    
        <li data-menu="" title="Просмотр результатов тестирования"><a href="viewtestresults">Результаты</a></li>
    <? } ?>    
    </ul> 
<? } 

if (defined("IN_ADMIN"))
 { ?>
    <ul id="admin-menu" class="smenu-bar">
        <li><a href="#" id="button-admin-h" class="menu-button"><i class='fa fa-gear fa-spin fa-lg'></i> Администратор</a></li>
        <li><a href="members">Пользователи</a></li>
        <li><a href="experts">Эксперты</a></li>
        <li><a href="experts&s=1">Супервизоры</a></li>
        <li><a href="logs">Журнал</a></li>
        <li><a href="admshab&mode=project">П.шаблоны</a></li>
        <li><a href="admshab&mode=list">К.шаблоны</a></li>
        <li><a href="admlimit">Ограниченные</a></li>
        <li><a href="newses">Страницы</a></li>
    </ul> 
<? } ?>
  
<?} 

if (USER_REGISTERED) 
 {
  echo '<div id="pagewidth" style="background-color:#e6e6e6; margin-top: 0px;"><div id="wrapper" class="clearfix">';
  echo '<div id="maincol" style="left:3em; background-color:#e6e6e6; width:96%;">';

         $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT email, job FROM users WHERE id='".USER_ID."' LIMIT 1");
         if (!$tot2) puterror("Ошибка при обращении к базе данных");
         $total2 = mysqli_fetch_array($tot2);
         $email = $total2['email'];       
         $job = $total2['job'];       
         mysqli_free_result($tot2); 
         if ($_SERVER['REQUEST_URI'] != "/edituser")
         {

          if (empty($email))
          {
          ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
              		<strong>Внимание:</strong> В вашем профиле не заполнено поле - электронная почта. <strong><a href="edituser">Перейти в профиль</a></strong></p>
            	</div>
           </div>    
          <?
          }
         }

  $tot2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed='0'");
  if (!$tot2) puterror("Ошибка при обращении к базе данных");
  $total2 = mysqli_fetch_array($tot2);
  $count2 = $total2['count(*)'];
  mysqli_free_result($tot2);
  
         if ($count2>0 and $_SERVER['REQUEST_URI'] != "/usermsgs")
         {

          ?>
          <div class="ui-widget" style="margin-top: 5px;">
            	<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
            		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
              		Пришли новые сообщения - <? echo $count2; ?> <strong><a href="usermsgs">Прочитать</a></strong></p>
            	</div>
           </div>    
          <?
         }

 }
else
 {
  echo '<div id="pagewidth"><div id="wrapper" class="clearfix">';
  echo '<div id="maincol">';
 }
// echo "<p></p>";
 echo "<h1 class=z1>".$titlepage."</h1>";
?>



