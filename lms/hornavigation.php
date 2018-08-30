<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
{ 
?>
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
<?} else
if(defined("IN_USER"))
{?>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<?}?>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" style="margin-top: 4px;" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Меню</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a title="TestLife version <?=$version?>" class="navbar-brand" href="<?=$site?>"><img src="img/testlife.png" height="12"></a>
            </div>
            <div class="navbar-collapse collapse">


<?
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR"))
{ 

  $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed='0' LIMIT 1;");
  $param = mysqli_fetch_array($sql); 
  $count = $param['count(*)'];
  mysqli_free_result($sql);

  if ($count>0)
    $cols = '<span id="msgcount" class="count">'.$count.'</span>';
  else
    $cols = '';

  $date3 = date("d.m.Y");
  preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})/",$date3,$z);
  $day=$z[1][0];
  $month=$z[2][0];
  $year=$z[3][0];
  $ts_now = (mktime(0, 0, 0, $month, $day, $year));
  $coltasks = 0;
  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE active=1 AND testtype='pass' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE active=1 AND testtype='pass' AND ownerid='".USER_ID."' ORDER BY id DESC;");
  
  while($member = mysqli_fetch_array($sql))
  {
    $sql2 = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergrp WHERE testid='".$member['id']."' ORDER BY id");
    while($usergrp = mysqli_fetch_array($sql2))
    {
       $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails WHERE usergroupid='".$usergrp['usergroupid']."' LIMIT 1;");
       $cntusers = mysqli_fetch_array($countu);
       $count_users = $cntusers['count(*)'];
       mysqli_free_result($countu); 
       $acusers += $count_users;
       
       $date1 = $usergrp['startdate'];
       $date2 = $usergrp['stopdate'];
       $arr1 = explode(" ", $date1);
       $arr2 = explode(" ", $date2);  
       $arrdate1 = explode("-", $arr1[0]);
       $arrdate2 = explode("-", $arr2[0]);
       $timestamp1 = (mktime(0, 0, 0, $arrdate1[1],  $arrdate1[2],  $arrdate1[0]));
       $timestamp2 = (mktime(0, 0, 0, $arrdate2[1],  $arrdate2[2],  $arrdate2[0]));
       
       if ($count_users > 0 and $ts_now >= $timestamp1 and $ts_now <= $timestamp2) 
        $coltasks++;
    }
    mysqli_free_result($sql2);
  }
  mysqli_free_result($sql);
  if ($coltasks>0)
    $coltasks = '<span id="taskcount" class="count2">'.$coltasks.'</span>';
  else
    $coltasks = '';

?>

            <ul class="nav navbar-top-links navbar-right" style="background-color: #f8f8f8;">
                <li class="dropdown">
                    <a id="usermsgsoper" title="Сообщения" class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="getusermsgs()">
                        <i class="fa fa-envelope fa-fw"></i><?=$cols?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" id="usermsgs">
                     <img src="img/ajax-loader.gif"> 
                    </ul>                                                                 
                </li>
<? if(defined("IN_SUPERVISOR")) 
{?>
                <li class="dropdown">
                    <a id="usermsgsoper" title="Активность участников" class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="gettesttasks()">
                        <i class="fa fa-users fa-fw"></i><?=$coltasks?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" id="testtasks">
                     <img src="img/ajax-loader.gif"> 
                    </ul>                                                                 
                </li>
                <li class="dropdown">
                    <a id="testscounter" title="Количество доступных сеансов" class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-dashboard fa-fw"></i><span id="testcount" class="count3"><?=SUPERVISOR_REST?></span> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li style="padding: 5px;">
                                <div>
                                  Осталось сеансов тестирования: <strong><?=SUPERVISOR_REST?> <i class="fa fa-dashboard fa-fw"></i></strong>
                                </div>
                        </li>
                        <li class="divider"></li>
                        <li style="padding: 5px;">
                                <div>
                                    Купить <strong>10 <i class="fa fa-dashboard fa-fw"></i></strong> сеансов 
                                    <span class="pull-right text-muted">
                                        <em><?=$resultprice*10?> руб.</em>
                                    </span>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&yamoney-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*10?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&any-card-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*10?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>                               
                                </div>
                        </li>
                        <li style="padding: 5px;">
                                <div>
                                    Купить <strong>25 <i class="fa fa-dashboard fa-fw"></i></strong> сеансов 
                                    <span class="pull-right text-muted">
                                        <em><?=$resultprice*25?> руб.</em>
                                    </span>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&yamoney-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*25?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&any-card-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*25?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>                               
                                </div>
                        </li>
                        <li style="padding: 5px;">
                                <div>
                                    Купить <strong>50 <i class="fa fa-dashboard fa-fw"></i></strong> сеансов 
                                    <span class="pull-right text-muted">
                                        <em><?=$resultprice*50?> руб.</em>
                                    </span>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&yamoney-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*50?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&any-card-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*50?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>                               
                                </div>
                        </li>
                        <li style="padding: 5px;">
                                <div>
                                    Купить <strong>100 <i class="fa fa-dashboard fa-fw"></i></strong> сеансов 
                                    <span class="pull-right text-muted">
                                        <em><?=$resultprice*100?> руб.</em>
                                    </span>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&yamoney-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*100?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>
                                    <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/small.xml?account=41001249202406&quickpay=small&any-card-payment-type=on&button-text=02&button-size=s&button-color=orange&targets=%D0%A1%D0%B5%D0%B0%D0%BD%D1%81%D1%8B+%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F&default-sum=<?=$resultprice*100?>&fio=on&mail=on&successURL=http%3A%2F%2Ftestlife.org" width="100" height="25"></iframe>                               
                                </div>
                        </li>
                    </ul>                                                                 
                </li>
                <li class="dropdown">
                    <a title="Помощь" class="dropdown-toggle" data-toggle="dropdown" href="#"">
                        <i class="fa fa-question-circle fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                    <?
                         echo "<li style='padding: 5px;'><div><a href='h&t=n'><i class='fa fa-newspaper-o fa-fw'></i>&nbsp;Последние изменения</a></div></li>";
                         $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM helppages WHERE news=0 ORDER BY id;");
                         while($member = mysqli_fetch_array($sql))
                          echo "<li style='padding: 5px;'><div><a href='h&id=".$member['id']."'><i class='fa fa-question fa-fw'></i>&nbsp;".$member['name']."</a>
                          </div></li>";
                         mysqli_free_result($sql);
                         echo '<li style="padding: 5px;"><div>
                         <a href="javascript:;" title="Задать вопрос" onclick="formShow(\'Задать вопрос\',\'Вопрос:\',\''.USER_FIO.'\',\''.USER_EMAIL.'\');"><i class="fa fa-comment fa-fw"></i>&nbsp;Остались вопросы?</a>
                         </div></li>';
                    ?>
                    </ul>                                                                 
                </li>
<?}?>
                <li><a href="profile" title="Профиль"><?=(empty ($img)) ? "<i class='fa fa-user fa-fw'></i>" : USER_PICT ?> <?=USER_FIO?></a>
                </li>
                <li><a href="logout" title="Выход"><i class="fa fa-sign-out fa-fw"></i></a>
                </li>
            </ul>
<?}
else
if(defined("IN_USER"))
{
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM msgs WHERE touser='".USER_ID."' AND readed='0' LIMIT 1;");
   $param = mysqli_fetch_array($sql); 
   $count = $param['count(*)'];
   mysqli_free_result($sql);
   if ($count>0)
    $cols = '<span id="msgcount" class="count">'.$count.'</span>';
   else
    $cols = '';
?>
            <ul class="nav navbar-top-links navbar-left" style="background-color: #f8f8f8;">
                        <li>
                            <a href="ts"><i class="fa fa-dashboard fa-fw"></i> Тесты</a>
                        </li>
                        <li>
                            <a href="vr"><i class="fa fa-line-chart fa-fw"></i> Мои результаты</a>
                        </li>
            </ul>
            <ul class="nav navbar-top-links navbar-right" style="background-color: #f8f8f8;">
                <li class="dropdown">
                    <a id="usermsgsoper" title="Сообщения" class="dropdown-toggle" data-toggle="dropdown" href="#" onclick="getusermsgs()">
                        <i class="fa fa-envelope fa-fw"></i><?=$cols?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages" id="usermsgs">
                     <img src="img/ajax-loader.gif"> 
                    </ul>                                                                 
                </li>
                <li><a href="profile" title="Профиль"><?=(empty ($img)) ? "<i class='fa fa-user fa-fw'></i>" : USER_PICT ?> <?=USER_FIO?></a>
                </li>
                <li><a href="logout" title="Выход"><i class="fa fa-sign-out fa-fw"></i></a>
                </li>
            </ul>
<?
}
?>