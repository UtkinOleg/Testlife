<?
  if((defined("IN_USER") or defined("IN_EXPERT")) and !defined("IN_SUPERVISOR") and !defined("IN_ADMIN")) {  
  include "config.php";
  include "func.php";


// Участник или эксперт хочет стать супервизором
require_once "header.php"; 
?>
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget { font-family: Verdana,Arial,sans-serif; font-size: 0.7em;}
.button_disabled { background: #D1D4D8;  }.button_enabled {  } 
p {   font: 16px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset {   display:block;  font-family:Arial;  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
</style>
<?
      
$action = "";
$action = $_POST["action"];

if ($action=='stepone')
{
    // Обновим свойства пользователя до супервизора с ограниченными правами
    mysql_query("LOCK TABLES users WRITE");
    mysql_query("START TRANSACTION;");
    mysql_query("UPDATE users SET usertype='supervisor', pacount=1, qcount=1 WHERE id=".USER_ID);
    mysql_query("COMMIT");
    mysql_query("UNLOCK TABLES");
    // Обновим статусы пользователя
    define("IN_SUPERVISOR", TRUE); 
    if (!defined("IN_EXPERT")) define("IN_EXPERT", TRUE); 
    define("LOWSUPERVISOR", TRUE);
    define("USER_STATUS", "супервизор"); 
    // переходим к созданию ограниченной модели
    echo '<script language="javascript">';
    echo 'parent.createModel();';
    echo '</script>';
    exit();
}
else
if (empty($action)) 
{
?>
<script>
  $(function() {
    $( "#next" ).button();
    $( "#close" ).button();
  });
 $(document).ready(function(){
    $('#step_one').submit(function(){
     $('#next', $(this)).attr('disabled', 'disabled');
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
</head>
<body>
  <form id="step_one" action="iwant" method="post">
    <input type="hidden" name="action" value="stepone">
    <div id="spinner">
    </div>
    <h3 class='ui-widget-header ui-corner-all' align="center">
      <p>Стать супервизором бесплатно
      </p></h3>
    <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
          <p align='center'>
            <div id="menu_glide" class="menu_glide" style="margin-top:40px;">
              <table class=bodytable border="0" width='95%' height='100%' align='center' cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
                <tr><td>
                    <p>Став супервизором, Вы можете самостоятельно организовать новый онлайн конкурс, внешнюю экспертизу различных проектов или онлайн тестирование.
                    </p>
                    <p>Бесплатный тариф позволяет создать только одну модель с ограниченными функциями: максимум два параметра в шаблоне проекта, один вычисляемый показатель, максимум два критерия в экспертном листе, один эксперт в системе, один тест и максимум три участника (тестируемых). Также закрыт доступ к расширенной аналитике по экспертизе проектов и тестированию.
                    </p>
                    <p>Перед тем как создавать модель, необходимо ознакомится с <a href="page&id=57" target="_blank">основными функциями сервиса.</a>
                    </p>
                    <p>
                    </p>
                    <p>Если Вы готовы попробовать - нажмите "
                      <a href="javascript:;" onclick="$('#step_one').submit();">Далее</a>".
                    </p></td>
                </tr>
              </table>
            </div>
          </p></td>
      </tr>
    </table>
    <div id="buttonset">  
      <button style="font-size: 1em;" id="next" onclick="$('#step_one').submit();">
        <i class='fa fa-arrow-right fa-lg'></i> Далее
      </button>    
      <a style="font-size: 1em;" id="close" href="javascript:;" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Отмена
      </a>  
    </div>
  </form>
</body>
</html>
<?
}
  
  
} else die;
?>