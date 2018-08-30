<?php

  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }
  
  include "config.php";

?>

<!DOCTYPE html>
<html lang="ru"> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Создание адаптивных и стандартных тестов онлайн и расширенный анализ результатов">
    <meta name="keywords" content="тестирование, онлайн тестирование, адаптивный тест, адаптивное тестирование, online test, online тестирование, анализ результатов тестирования, освоение тем теста, решаемость заданий, психологическое тестирование, психологический тест" /> 
    <meta name="copyright" content="Oleg Utkin" /> 
    <meta name="author" content="Oleg Utkin" />
    <meta name='yandex-verification' content='6dcc51c08b1fd8e9' />
    <meta name="google-site-verification" content="L7sugM88OlBgf0Duo_Kr_hwhNo0OCyzTY4BB9u-Q_hA" />
    <meta property="og:image" content="http://testlife.org/img/testlife.png" />
    <title>Test Life</title>
    <link rel="icon" href="ico/favicon.ico">
    <title>Test Life</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <link href="css/customadmin.css?v=<?=$version?>" rel="stylesheet">
    <link href="lms/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" >
  </head>
<body>
      
<?php
 include "bootstrapsocial.php";
?>

<div id="spinner"></div>

<div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" style="margin-top: 4px;" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Меню</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a title="TestLife version <?=$version?>" class="navbar-brand" href="http://testlife.org"><img src="img/testlife.png" height="12"></a>
            </div>
            <div class="navbar-collapse collapse">
             <ul class="nav navbar-top-links navbar-right" style="background-color: #f8f8f8;">
                        <li>
                            <a href="javascript:;" onclick="$('#tlLoginForm').modal('show');">Вход</a>
                        </li>
             </ul>
            </div>
            <div class="navbar-default sidebar" role="navigation" style="margin-top: -10px;">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <?
                         $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages WHERE news=0 ORDER BY id;");
                         while($member = mysqli_fetch_array($sql))
                          echo "<li><a href='h&id=".$member['id']."'>".$member['name']."</a></li>";
                         mysqli_free_result($sql);
                        ?>
                    </ul>
                </div>
            </div>        
       </nav>

<div id="page-wrapper">
            
<?php
  
  $id = $_GET['id'];
  if (empty($id)) $id=1;
  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages WHERE id=".$id);
  $member = mysqli_fetch_array($sql);
?>
            <div class="row" style="margin-top: 40px;">
                <div class="col-lg-12">
                   <div class="panel panel-default">
                        <div class="panel-heading">
                        <?=$member['name']?>
                        </div>
                        <div class="panel-body">
                        <?=$member['content']?>
                        </div>
                   </div>              
                </div>
            </div>
      </div>
    </div>
<?
  mysqli_free_result($sql);
?>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu.min.js"></script>
    <script src="js/sbadmin.js"></script>
    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
    <script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter27317486 = new Ya.Metrika({id:27317486, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/27317486" style="position:absolute; left:-9999px;" alt="" /></div></noscript>                        
    <script>
     (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
     (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
     m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
     })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
     ga('create', 'UA-53631745-2', 'auto');
     ga('send', 'pageview');
    </script>
  </body>
</html>
           