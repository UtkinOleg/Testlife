<?php
if (defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {
    include "config.php";
    include "func.php";
    spl_autoload_register(function ($class) {
        include 'class/' . $class . '.class.php';
    });

    // Возвращает количество подгрупп
    function GetCnt($mysqli, $kid)
    {
        $groups = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questgroups WHERE knowsid='" . $kid . "' LIMIT 1;");
        $groupsd = mysqli_fetch_array($groups);
        $cnt = $groupsd['count(*)'];
        mysqli_free_result($groups);
        return $cnt;
    }

    // Возвращает количество вопросов
    function GetQuestionCnt($mysqli, $groupid)
    {
        $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM questions WHERE qgroupid='" . $groupid . "' LIMIT 1;");
        $groupsd = mysqli_fetch_array($sql);
        $cnt = $groupsd['count(*)'];
        mysqli_free_result($groups);
        return $cnt;
    }

    // Пишет состояние для указанной подобласти знаний
    function StoreChild($mysqli, $mode, Knows $ks, $kid, $testid)
    {
        foreach ($ks->getKnows($kid) as $tmpknow) {

            if (defined("IN_ADMIN"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' ORDER BY id DESC;");
            else
                if (defined("IN_SUPERVISOR"))
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");

            while ($member = mysqli_fetch_array($sql)) {
                $groupid = $member['id'];
                $maxq = GetQuestionCnt($mysqli, $groupid);
                if ($maxq > 0) {
                    $questcount = $_POST["qg" . $groupid];
                    $rnd = $_POST["rnd" . $groupid];

                    if ($mode === 'e') {
                        $td = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='" . $testid . "' AND groupid='" . $groupid . "' LIMIT 1;");
                        $testdata = mysqli_fetch_array($td);
                        if (empty($testdata)) {
                            $query = "INSERT INTO testdata VALUES (0,
                                        $testid,
                                        $questcount,
                                        $rnd, 
                                        $groupid);";
                            mysqli_query($mysqli, $query);
                        } else {
                            $query = "UPDATE testdata SET qcount = '" . $questcount . "'
              , random = '" . $rnd . "' WHERE id=" . $testdata['id'];
                            mysqli_query($mysqli, $query);
                        }
                        mysqli_free_result($td);
                    }
                    if ($mode === 'a') {
                        $query = "INSERT INTO testdata VALUES (0,
                                        $testid,
                                        $questcount,
                                        $rnd, 
                                        $groupid);";
                        mysqli_query($mysqli, $query);
                    }
                }
            }
            mysqli_free_result($sql);

            if (GetCnt($mysqli, $tmpknow->getId()) > 0)
                StoreChild($mysqli, $mode, $ks, $tmpknow->getId(), $testid);
        }
    }

    // Возвращает количество вопросов для указанной подобласти знаний
    function GetActiveQFromChild($mysqli, $mode, Knows $ks, $kid, $testid, $testkind)
    {
        foreach ($ks->getKnows($kid) as $tmpknow) {

            if (defined("IN_ADMIN"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' ORDER BY id DESC;");
            else
                if (defined("IN_SUPERVISOR"))
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");

            $allq = 0;
            while ($member = mysqli_fetch_array($sql)) {
                $groupid = $member['id'];
                $maxq = GetQuestionCnt($mysqli, $groupid);
                if ($maxq > 0) {
                    $questcount = $_POST["qg" . $groupid];
                    $rnd = $_POST["rnd" . $groupid];
                    if ($testkind == 'standard')
                        $allq += $questcount;
                    else
                        if ($testkind == 'adaptive' and $rnd == 1)
                            $allq += $questcount;
                    if ($testkind == 'adaptive' and $questcount < 7 and $rnd == 1) {
                        $allq = 0;
                    }

                }
            }
            mysqli_free_result($sql);

            if (GetCnt($mysqli, $tmpknow->getId()) > 0)
                $allq += GetActiveQFromChild($mysqli, $mode, $ks, $tmpknow->getId(), $testid, $testkind);

        }
        return $allq;
    }

    // Возвращает HTML для указанной подобласти знаний
    function GetChildHTML($mysqli, $mode, Knows $ks, $kid, $testid, $testkind)
    {
        foreach ($ks->getKnows($kid) as $tmpknow) {

            if (defined("IN_ADMIN"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' ORDER BY id DESC;");
            else
                if (defined("IN_SUPERVISOR"))
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $tmpknow->getId() . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");


            while ($member = mysqli_fetch_array($sql)) {
                $qcount = 0;
                $tdr = true;
                $maxq = GetQuestionCnt($mysqli, $member['id']);
                if ($maxq > 0) {

                    if ($mode === 'e') {
                        $td = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='" . $testid . "' AND groupid='" . $member['id'] . "' LIMIT 1;");
                        $testdata = mysqli_fetch_array($td);
                        if (empty($testdata)) {
                            $qcount = 0;
                            $tdr = true;
                        } else {
                            $qcount = $testdata['qcount'];
                            $tdr = $testdata['random'];
                        }
                        mysqli_free_result($td);
                    }

                    echo "<tr><td width='300'><p><b>" . $tmpknow->getName() . ":</b> " . $member['name'] . "</p></td>";
                    echo "<td align='center'><p><i title='Баллов за вопрос' class='fa fa-calculator fa-lg'></i> " . $member['singleball'] . "</p></td>";
                    echo "<td align='center'><p><i title='Время ответа на вопрос (минут)' class='fa fa-clock-o fa-lg'></i> " . $member['singletime'] . "</p></td>";
                    echo "<td align='center'><p><i title='Дата создания группы' class='fa fa-calendar fa-lg'></i> " . data_convert($member['regdate'], 1, 0, 0) . "</p></td>";

                    if ($testkind == 'standard') {
                        echo "<td width='300' align='center'>";
                        echo "<p><div style='margin: 1px; font-size: 0.8em;' id='slideru" . $member['id'] . "'></div>
      <i title='Количество вопросов' class='fa fa-question fa-lg'></i> <label for='qg" . $member['id'] . "' id='lqg" . $member['id'] . "'>" . $qcount . "</label>
      <input type='hidden' id='qg" . $member['id'] . "' name='qg" . $member['id'] . "' value='" . $qcount . "'/></p>";
                        ?>
     <script>
        $(function() {
          $( "#slideru<?= $member['id'] ?>" ).slider({
           range: "min", value: <?= $qcount ?>, min: 0, max: <?= $maxq ?>, step: 1,
           slide: function( event, ui ) {
           $( "#qg<?= $member['id'] ?>" ).val(ui.value);
           $( "#lqg<?= $member['id'] ?>" ).text('' + ui.value);
           }
          });
        });
    	  $(document).ready(function() {
         $("#rndp<?= $member['id'] ?>").buttonset();
        });
     </script>          
        </td><td align="center">
          <div id="rndp<?= $member['id'] ?>">
           <?if ($mode === 'a') { ?>
                            <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>" checked="checked">
                            <label for="closed1_<?= $member['id']; ?>"><i title='Случайная выборка'
                                                                          class='fa fa-random fa-lg'></i></label>
                            <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>">
                            <label for="closed2_<?= $member['id']; ?>"><i title='Стандартная выборка'
                                                                          class='fa fa-sort-numeric-asc fa-lg'></i></label>
                        <? } else if ($mode === 'e') { ?>
                            <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>" <?= ($tdr ? 'checked="checked"' : '') ?>>
                            <label for="closed1_<?= $member['id']; ?>"><i title='Случайная выборка'
                                                                          class='fa fa-random fa-lg'></i></label>
                            <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>" <?= ($tdr ? '' : 'checked="checked"') ?>>
                            <label for="closed2_<?= $member['id']; ?>"><i title='Стандартная выборка'
                                                                          class='fa fa-sort-numeric-asc fa-lg'></i></label>
                        <?
                        }?>
          </div>        
        </td></tr>         
<?
                    } else
                        if ($testkind == 'adaptive') {
                            echo "<td align='center'><p><i title='Количество вопросов в группе' class='fa fa-question fa-lg'></i> " . $maxq . "</p></td>";
                            echo "<td width='300' align='center'>";
                            // Для адаптивного теста - максимум вопросов
                            echo "<input type='hidden' id='qg" . $member['id'] . "' name='qg" . $member['id'] . "' value='" . $maxq . "'/>";
                            // Параметр 'случайная выборка' используется в качестве выключателя
                            ?>
     <script>
    	  $(document).ready(function() {
         $("#rndp<?= $member['id'] ?>").buttonset();
        });
     </script>          
          <div id="rndp<?= $member['id'] ?>">
           <?if ($mode === 'a') { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" checked="checked">
                                <label for="closed1_<?= $member['id']; ?>">Активна</label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>">
                                <label for="closed2_<?= $member['id']; ?>">Неактивна</label>
                            <? } else if ($mode === 'e') { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" <?= ($tdr ? 'checked="checked"' : '') ?>>
                                <label for="closed1_<?= $member['id']; ?>">Активна</label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" <?= ($tdr ? '' : 'checked="checked"') ?>>
                                <label for="closed2_<?= $member['id']; ?>">Неактивна</label>
                            <?
                            }?>
          </div>        
        </td></tr>         
<?
                        }

                }
            }
            mysqli_free_result($sql);

            if (GetCnt($mysqli, $tmpknow->getId()) > 0)
                GetChildHTML($mysqli, $mode, $ks, $tmpknow->getId(), $testid, $testkind);
        }
    }

    require_once "header.php";

    // Инициализируем массив областей знаний
    if (defined("IN_ADMIN"))
        $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge ORDER BY id;");
    else
        $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM knowledge WHERE userid='" . USER_ID . "' ORDER BY id;");
    $knows = new Knows();
    while ($member = mysqli_fetch_array($sql))
        $knows->addKnow(new Know($member['id'],
            $member['name'],
            $member['content'],
            $member['parentid'],
            $member['userid']));
    mysqli_free_result($sql);

    ?>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.9em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
.button_disabled { background: #D1D4D8;  }.button_enabled {  } 
p {   font: 16px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner {   display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset {   display:block;  font-family:Arial;  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 155px; bottom : 45px;  position: absolute; overflow: auto;} 
#buttonsetm2 { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 70px; bottom : 45px;  position: absolute; overflow: auto;} 
</style>
<?

    $action = $_POST["action"];

    if ($action == 'stepfive') {
        $kid = $_POST["kid"];
        $id = $_POST["id"];
        $mode = $_POST["m"];

        $name = $_POST["name"];

        $testtype = $_POST["testtype"];
        $testkind = $_POST["testkind"];
        $attempt = $_POST["attempt"];
        $active = $_POST["active"];

        $content = $_POST["content"];

        $ext = $_POST["external"];
        $psy = $_POST["psy"];
        $expert = $_POST["expert"];
        $scale = $_POST["scale"];

        if (empty($attempt))
            $attempt = 0;

        // Проверим тест если идет активация теста
        if ($active == 1 or $ext == 1) {
            if (defined("IN_ADMIN"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' ORDER BY id DESC;");
            else
                if (defined("IN_SUPERVISOR"))
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");

            $allq = 0;
            $nonadaptive = false;
            while ($member = mysqli_fetch_array($sql)) {
                $groupid = $member['id'];
                $maxq = GetQuestionCnt($mysqli, $groupid);
                if ($maxq > 0) {
                    $questcount = $_POST["qg" . $groupid];
                    $rnd = $_POST["rnd" . $groupid];
                    if ($testkind == 'standard')
                        $allq += $questcount;
                    else
                        if ($testkind == 'adaptive' and $rnd == 1)
                            $allq += $questcount;
                    if ($testkind == 'adaptive' and $questcount < 7 and $rnd == 1) {
                        $nonadaptive = true;
                        $allq = 0;
                    }
                }
            }
            mysqli_free_result($sql);

            // Получим количество вопросов в подобластях
            if (GetCnt($mysqli, $kid) > 0)
                $allq += GetActiveQFromChild($mysqli, $mode, $knows, $kid, $testid, $testkind);

            // Если выборка пустая - деактивируем тест
            if ($allq == 0) {
                $active = 0;
                $ext = 0;
                $psy = 0;
            }

            // Если в выборке есть группы где меньше 7 вопросов - деактивируем тест
            if ($nonadaptive) {
                $active = 0;
                $ext = 0;
            }
        }

        // Зачетный не может быть внешним
        if ($testtype == 'pass' and $ext == 1)
            $ext = 0;

        if ($testkind == 'adaptive')
            $psy = 0;

        // Запишем параметры
        $userid = USER_ID;
        $token = md5(time() . $userid . $name);  // Уникальная сигнатура теста

        mysqli_query($mysqli, "START TRANSACTION;");
        if ($mode === 'a') {
            $query = "INSERT INTO testgroups VALUES (0,
                                        '$name',
                                        '$testkind',
                                        $kid, 
                                        '$testtype', 
                                        0, 
                                        $attempt,
                                        $userid,
                                        NOW(),
                                        '$token',
                                        $active,
                                        '$content',
                                        $ext,
                                        0,0,0,
                                        $psy, $expert, $scale);";
            if (mysqli_query($mysqli, $query))
                $testid = mysqli_insert_id($mysqli);
            else
                $testid = 0;
        } else
            if ($mode === 'e') {
                $testid = $id;
                $query = "UPDATE testgroups SET name = '" . $name . "'
            , testkind = '" . $testkind . "'
            , testtype = '" . $testtype . "'
            , attempt = " . $attempt . "
            , active = " . $active . "
            , content = '" . $content . "'
            , external = " . $ext . "
            , psy = " . $psy . "
            , expert = " . $expert . "
            , scale = " . $scale . "
           WHERE id=" . $testid;
                mysqli_query($mysqli, $query);
            }

        if (defined("IN_ADMIN"))
            $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' ORDER BY id DESC;");
        else
            if (defined("IN_SUPERVISOR"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");

        while ($member = mysqli_fetch_array($sql)) {
            $groupid = $member['id'];
            $maxq = GetQuestionCnt($mysqli, $groupid);
            if ($maxq > 0) {
                $questcount = $_POST["qg" . $groupid];
                $rnd = $_POST["rnd" . $groupid];

                if ($mode === 'e') {
                    $td = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='" . $testid . "' AND groupid='" . $groupid . "' LIMIT 1;");
                    $testdata = mysqli_fetch_array($td);
                    if (empty($testdata)) {
                        $query = "INSERT INTO testdata VALUES (0,
                                        $testid,
                                        $questcount,
                                        $rnd, 
                                        $groupid);";
                        mysqli_query($mysqli, $query);
                    } else {
                        $query = "UPDATE testdata SET qcount = '" . $questcount . "'
              , random = '" . $rnd . "' WHERE id=" . $testdata['id'];
                        mysqli_query($mysqli, $query);
                    }
                    mysqli_free_result($td);
                }
                if ($mode === 'a') {
                    $query = "INSERT INTO testdata VALUES (0,
                                        $testid,
                                        $questcount,
                                        $rnd, 
                                        $groupid);";
                    mysqli_query($mysqli, $query);
                }
            }
        }
        mysqli_free_result($sql);

        // Запишем состояние групп вопросов в подобластях
        if (GetCnt($mysqli, $kid) > 0)
            StoreChild($mysqli, $mode, $knows, $kid, $testid);

        // Сохраним транзакцию
        mysqli_query($mysqli, "COMMIT;");

        // Завершаем диалог
        echo '<script language="javascript">';
        echo 'parent.closeFancyboxAndRedirectToUrl(' . $kid . ',"t");';
        echo '</script>';
        exit();
    } else
        if ($action == 'stepfour') {
            $kid = $_POST["kid"];
            $mode = $_POST["m"];
            $name = $_POST["name"];
            $name = str_replace('"', '', $name);
            $name = str_replace("'", '', $name);

            $testtype = $_POST["testtype"];
            $testkind = $_POST["testkind"];
            $attempt = $_POST["attempt"];
            $active = $_POST["active"];
            $psy = $_POST["psy"];
            $ext = $_POST["external"];
            $expert = $_POST["expert"];
            $scale = $_POST["scale"];

            if (empty($ext))
                $ext = 0;
            $content = $_POST["content"];
            $content = htmlspecialchars_decode($content);

            if ($mode === 'e') {
                $id = $_POST["id"];
                $modename = 'Изменение теста - Шаг 2 - Установка параметров выборки вопросов';
            } else
                if ($mode === 'a')
                    $modename = 'Создание теста - Шаг 2 - Установка параметров выборки вопросов';
            if ($testkind == 'standard')
                $infotext = 'При помощи слайдера укажите количество вопросов, которое будет использоваться в тесте. Можно также установить параметр случайной выборки вопросов из группы. Вопросы из группы не будут использоваться в тесте, если количество вопросов равно нулю.';
            else
                if ($testkind == 'adaptive')
                    $infotext = 'Для адаптивного теста необходимо указать группы вопросов, которые будут использоваться.';
            ?>
<script>
 $(document).ready(function(){
    $(".ui-state-error").hide();
    $("#spinner").fadeOut("slow");
    $( "button" ).button();
    $('form').submit(function(){
     $('#next', $(this)).attr('disabled', 'disabled');
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
</head>
<body>
    <div id="spinner"></div>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="text-align: center; padding: 0 .7em;">                    
        <p>      
          <div id="info2"><?= $modename ?></div>
        </p>            	   
      </div>
    </div>
    <p></p>
    <div class="ui-widget">	
      <div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 0 .7em;">		
        <p>
          <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;">
          </span>	<?= $infotext ?>
        </p>	
      </div>
    </div>
    <div id="buttonsetm">  

  <form id="step_five" action="createtest" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="stepfive">
    <input type="hidden" name="name" value="<?= $name ?>">
    <input type="hidden" name="testtype" value="<?= $testtype ?>">
    <input type="hidden" name="testkind" value="<?= $testkind ?>">
    <input type="hidden" name="attempt" value="<?= $attempt ?>">
    <input type="hidden" name="active" value="<?= $active ?>">
    <input type="hidden" name="content" value="<?= $content ?>">
    <input type="hidden" name="kid" value="<?= $kid ?>">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="m" value="<?= $mode ?>">
    <input type="hidden" name="external" value="<?= $ext ?>">
    <input type="hidden" name="psy" value="<?= $psy ?>">
    <input type="hidden" name="expert" value="<?= $expert ?>">
    <input type="hidden" name="scale" value="<?= $scale ?>">

    <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
        <table border="0" width='98%' align='center' cellpadding=3 cellspacing=0>
<?

            if (defined("IN_ADMIN"))
                $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' ORDER BY id DESC;");
            else
                if (defined("IN_SUPERVISOR"))
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE knowsid='" . $kid . "' AND userid='" . USER_ID . "' ORDER BY id DESC;");

            while ($member = mysqli_fetch_array($sql)) {
                $qcount = 0;
                $tdr = true;
                $groupid = $member['id'];
                $maxq = GetQuestionCnt($mysqli, $groupid);
                if ($maxq > 0) {

                    if ($mode === 'e') {
                        $td = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='" . $id . "' AND groupid='" . $member['id'] . "' LIMIT 1;");
                        $testdata = mysqli_fetch_array($td);
                        if (empty($testdata)) {
                            $qcount = 0;
                            $tdr = true;
                        } else {
                            $qcount = $testdata['qcount'];
                            $tdr = $testdata['random'];
                        }
                        mysqli_free_result($td);
                    }

                    echo "<tr><td width='300'><p>" . $member['name'] . "</p></td>";
                    echo "<td align='center'><p><i title='Баллов за вопрос' class='fa fa-calculator fa-lg'></i> " . $member['singleball'] . "</p></td>";
                    echo "<td align='center'><p><i title='Время ответа на вопрос (минут)' class='fa fa-clock-o fa-lg'></i> " . $member['singletime'] . "</p></td>";
                    echo "<td align='center'><p><i title='Дата создания группы' class='fa fa-calendar fa-lg'></i> " . data_convert($member['regdate'], 1, 0, 0) . "</p></td>";

                    if ($testkind == 'standard') {
                        echo "<td width='300' align='center'>";
                        echo "<p><div style='margin: 1px; font-size: 0.8em;' id='slideru" . $member['id'] . "'></div>
      <i title='Количество вопросов' class='fa fa-question fa-lg'></i> <label for='qg" . $member['id'] . "' id='lqg" . $member['id'] . "'>" . $qcount . "</label>
      <input type='hidden' id='qg" . $member['id'] . "' name='qg" . $member['id'] . "' value='" . $qcount . "'/></p>";
                        ?>
     <script>
        $(function() {
          $( "#slideru<?= $member['id'] ?>" ).slider({
           range: "min", value: <?= $qcount ?>, min: 0, max: <?= $maxq ?>, step: 1,
           slide: function( event, ui ) {
           $( "#qg<?= $member['id'] ?>" ).val(ui.value);
           $( "#lqg<?= $member['id'] ?>" ).text('' + ui.value);
           }
          });
        });
    	  $(document).ready(function() {
         $("#rndp<?= $member['id'] ?>").buttonset();
        });
     </script>          
        </td><td align="center">
          <div id="rndp<?= $member['id'] ?>">
           <?if ($mode === 'a') { ?>
                            <? if ($psy) { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>">
                                <label for="closed1_<?= $member['id']; ?>"><i title='Случайная выборка'
                                                                              class='fa fa-random fa-lg'></i></label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" checked="checked">
                                <label for="closed2_<?= $member['id']; ?>"><i title='Стандартная выборка'
                                                                              class='fa fa-sort-numeric-asc fa-lg'></i></label>
                            <? } else { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" checked="checked">
                                <label for="closed1_<?= $member['id']; ?>"><i title='Случайная выборка'
                                                                              class='fa fa-random fa-lg'></i></label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>">
                                <label for="closed2_<?= $member['id']; ?>"><i title='Стандартная выборка'
                                                                              class='fa fa-sort-numeric-asc fa-lg'></i></label>
                            <? }
                        } else if ($mode === 'e') { ?>
                            <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>" <?= ($tdr ? 'checked="checked"' : '') ?>>
                            <label for="closed1_<?= $member['id']; ?>"><i title='Случайная выборка'
                                                                          class='fa fa-random fa-lg'></i></label>
                            <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                   name="rnd<?= $member['id']; ?>" <?= ($tdr ? '' : 'checked="checked"') ?>>
                            <label for="closed2_<?= $member['id']; ?>"><i title='Стандартная выборка'
                                                                          class='fa fa-sort-numeric-asc fa-lg'></i></label>
                        <?
                        }?>
          </div>        
        </td></tr>         
<?
                    } else
                        if ($testkind == 'adaptive') {
                            echo "<td align='center'><p><i title='Количество вопросов в группе' class='fa fa-question fa-lg'></i> " . $maxq . "</p></td>";
                            echo "<td width='300' align='center'>";
                            // Для адаптивного теста - максимум вопросов
                            echo "<input type='hidden' id='qg" . $member['id'] . "' name='qg" . $member['id'] . "' value='" . $maxq . "'/>";
                            // Параметр 'случайная выборка' используется в качестве выключателя
                            ?>
     <script>
    	  $(document).ready(function() {
         $("#rndp<?= $member['id'] ?>").buttonset();
        });
     </script>          
          <div id="rndp<?= $member['id'] ?>">
           <?if ($mode === 'a') { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" checked="checked">
                                <label for="closed1_<?= $member['id']; ?>">Активна</label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>">
                                <label for="closed2_<?= $member['id']; ?>">Неактивна</label>
                            <? } else if ($mode === 'e') { ?>
                                <input type="radio" value='1' id="closed1_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" <?= ($tdr ? 'checked="checked"' : '') ?>>
                                <label for="closed1_<?= $member['id']; ?>">Активна</label>
                                <input type="radio" value='0' id="closed2_<?= $member['id']; ?>"
                                       name="rnd<?= $member['id']; ?>" <?= ($tdr ? '' : 'checked="checked"') ?>>
                                <label for="closed2_<?= $member['id']; ?>">Неактивна</label>
                            <?
                            }?>
          </div>        
        </td></tr>         
<?
                        }

                }
            }
            mysqli_free_result($sql);
            // Покажем подгуппы
            if (GetCnt($mysqli, $kid) > 0)
                GetChildHTML($mysqli, $mode, $knows, $kid, $id, $testkind);

            ?>
          </table>
        </td></tr>
       </table>
     </form>
    </div>
    <div id="buttonset">  
      <button id="next" onclick="$('#step_five').submit();">
        <i class='fa fa-check fa-lg'></i> Готово
      </button>    
      <button id="close" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Отмена
      </button>  
      <button id="help" onclick="window.open('h&id=5');"><i class="fa fa-question fa-lg"></i> Помощь</button>
    </div>
</body>
</html>
<?
        } else
            if (empty($action)) {
                $kid = $_GET["kid"];
                $mode = $_GET["m"];
                if ($mode === 'e') {
                    $modename = 'Изменение теста - Шаг 1 - Установка параметров теста';
                    $id = $_GET["id"];
                    $sql = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id='" . $id . "' LIMIT 1;");
                    $member = mysqli_fetch_array($sql);
                }
                if ($mode === 'a') {
                    $modename = 'Создание теста - Шаг 1 - Установка параметров теста';
                }
                ?>
<script>
  $(function() {
    $(".ui-state-error").hide();
    $("#spinner").fadeOut("slow");
    $( "button" ).button();
    $( "#attempt" ).selectmenu({ width : 250 });
    $( "#scale" ).selectmenu({ width : 400 });
    $( "#testtype" ).buttonset();
    $( "#testkind" ).buttonset();
    $( "#active" ).buttonset();
    $( "#external" ).buttonset();
    $( "#psy" ).buttonset();
    $( "#expert" ).buttonset();

    <? if ($mode === 'e') {
                    if ($member['testtype'] === 'check') { ?>
                        $("#attempt").selectmenu("option", "disabled", true);
                        //      $("#psy").buttonset( "option", "disabled", true );
                    <?
                    }
                    if ($member['testkind'] === 'adaptive') { ?>
                        $("#psy").buttonset("option", "disabled", true);
                    <?
                    }
                    if ($member['testtype'] === 'pass') { ?>
                        $("#external").buttonset("option", "disabled", true);
                    <?
                    }
                }?>
  });
 $(document).ready(function(){
    $('form').submit(function(){
     $(".ui-state-error").hide();
     $("#err2").empty();
     var hasError = false;
     if($("#name").val()=='') {
            $("#name").focus();
            $("#err2").append('Укажите наименование теста');
            hasError = true;
     }
     if(hasError == true) {     
       $(".ui-state-error").show();
       return false; 
     }
     $('#next', $(this)).attr('disabled', 'disabled');
     $("#spinner").fadeIn("slow");
    });   
  });
</script>
</head>
<body>
    <div id="spinner"></div>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="text-align: center; padding: 0 .7em;">                    
        <p>      
          <div id="info2"><?= $modename ?></div>
        </p>            	   
      </div>
    </div>
    <p></p>
 <div id="buttonsetm2">  
    <div id="err1" class="ui-widget">            	
      <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">               
        <p>
          <div id="err2">
          </div>
        </p>            	
      </div>           
    </div>   
  <form id="step_four" action="createtest" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="stepfour">
    <input type="hidden" name="kid" value="<?= $kid ?>">
    <input type="hidden" name="m" value="<?= $mode ?>">
    <input type="hidden" name="id" value="<?= $id ?>">
     <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' bgcolor='#ffffff'>
      <tr><td>
          <p align='center'>
              <table border="0" width='98%' align='center' cellpadding=3 cellspacing=3>    
<?
                $goahead = 1;
                if (defined("IN_SUPERVISOR") and !defined("IN_ADMIN")) {
                    $tot = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testgroups WHERE ownerid='" . USER_ID . "' LIMIT 1;");
                    $totalt = mysqli_fetch_array($tot);
                    if (LOWSUPERVISOR and $totalt['count(*)'] > 0)
                        $goahead = 0;
                    mysqli_free_result($tot);
                }

                if ($goahead == 1) {
                    ?>
                    <tr>
                        <td width='30%'>
                            <p>Наименование теста:</p>
                        </td>
                        <td>
                            <input style="width:100%" type='text' id='name' name='name' value='<?= $member['name'] ?>'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Тест является:</p>
                        </td>
                        <td>
                            <div id="testtype">
                                <? if ($mode === 'a') { ?>
                                    <input type="radio" value='pass' id="testtype1" name="testtype" checked="checked"
                                           onclick='$("#external").buttonset( "option", "disabled", true ); $( "#attempt" ).selectmenu( "option", "disabled", false ); '>
                                    <label for="testtype1">Зачетным</label>
                                    <input type="radio" value='check' id="testtype2" name="testtype"
                                           onclick='$("#external").buttonset( "option", "disabled", false ); $( "#attempt" ).selectmenu( "option", "disabled", true ); '>
                                    <label for="testtype2">Проверочным</label>
                                <? } else if ($mode === 'e') { ?>
                                    <input type="radio" value='pass' id="testtype1"
                                           name="testtype" <? echo($member['testtype'] === 'pass' ? 'checked="checked"' : '') ?>
                                           onclick='$("#external").buttonset( "option", "disabled", true ); $( "#attempt" ).selectmenu( "option", "disabled", false ); '>
                                    <label for="testtype1">Зачетным</label>
                                    <input type="radio" value='check' id="testtype2"
                                           name="testtype" <? echo($member['testtype'] === 'check' ? 'checked="checked"' : '') ?>
                                           onclick='$("#external").buttonset( "option", "disabled", false ); $( "#attempt" ).selectmenu( "option", "disabled", true ); '>
                                    <label for="testtype2">Проверочным</label>
                                <?
                                }?>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Тип тестирования:</p>
                        </td>
                        <td>
                            <div id="testkind">
                                <? if ($mode === 'a') { ?>
                                    <input type="radio" value='standard' id="testkind1" name="testkind"
                                           checked="checked"
                                           onclick='$( "#psy" ).buttonset( "option", "disabled", false );'><label
                                        for="testkind1">Стандартное</label>
                                    <input type="radio" value='adaptive' id="testkind2" name="testkind"
                                           onclick='$( "#psy" ).buttonset( "option", "disabled", true );'><label
                                        for="testkind2">Адаптивное</label>
                                <? } else if ($mode === 'e') { ?>
                                    <input type="radio" value='standard' id="testkind1" name="testkind"
                                           onclick='$( "#psy" ).buttonset( "option", "disabled", false );' <? echo($member['testkind'] === 'standard' ? 'checked="checked"' : '') ?>>
                                    <label for="testkind1">Стандартное</label>
                                    <input type="radio" value='adaptive' id="testkind2" name="testkind"
                                           onclick='$( "#psy" ).buttonset( "option", "disabled", true );' <? echo($member['testkind'] === 'adaptive' ? 'checked="checked"' : '') ?>>
                                    <label for="testkind2">Адаптивное</label>
                                <?
                                }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Количество попыток тестирования:
                            </p></td>
                        <td>
                            <select id="attempt" name="attempt">
                                <? if ($mode === 'a') { ?>
                                    <option value='0' selected>Без ограничений
                                    </option>
                                    <option value='1'>Одна
                                    </option>
                                    <option value='2'>Две
                                    </option>
                                    <option value='3'>Три
                                    </option>
                                    <option value='5'>Пять
                                    </option>
                                <? } else if ($mode === 'e') { ?>
                                    <option value='0' <? echo($member['attempt'] == 0 ? 'selected' : '') ?>>Без
                                        ограничений
                                    </option>
                                    <option value='1' <? echo($member['attempt'] == 1 ? 'selected' : '') ?>>Одна
                                    </option>
                                    <option value='2' <? echo($member['attempt'] == 2 ? 'selected' : '') ?>>Две
                                    </option>
                                    <option value='3' <? echo($member['attempt'] == 3 ? 'selected' : '') ?>>Три
                                    </option>
                                    <option value='5' <? echo($member['attempt'] == 5 ? 'selected' : '') ?>>Пять
                                    </option>
                                <?
                                }?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>
                            <p>Шкала оценок:</p>
                        </td>
                        <td>
                            <select id="scale" name="scale">
                                <? if ($mode === 'a') { ?>
                                    <option value='0' selected>Стандартная
                                    </option>
                                    <?
                                    if (defined("IN_ADMIN"))
                                        $sqls = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales ORDER BY id;");
                                    else
                                        if (defined("IN_SUPERVISOR"))
                                            $sqls = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales WHERE ownerid='" . USER_ID . "' LIMIT 1;");
                                    while ($scales = mysqli_fetch_array($sqls)) {
                                        ?>
                                        <option value='<?= $scales['id'] ?>'><?= $scales['name'] ?></option>
                                    <?
                                    }
                                    mysqli_free_result($sqls);

                                } else if ($mode === 'e') { ?>
                                    <option value='0' <? echo($member['scale'] == 0 ? 'selected' : '') ?>>Стандартная
                                    </option>
                                    <?
                                    if (defined("IN_ADMIN"))
                                        $sqls = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales ORDER BY id;");
                                    else
                                        if (defined("IN_SUPERVISOR"))
                                            $sqls = mysqli_query($mysqli, "/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM scales WHERE ownerid='" . USER_ID . "' LIMIT 1;");
                                    while ($scales = mysqli_fetch_array($sqls)) {
                                        ?>
                                        <option
                                            value='<?= $scales['id'] ?>' <? echo($member['scale'] == $scales['id'] ? 'selected' : '')?>><?= $scales['name'] ?></option>
                                    <?
                                    }
                                    mysqli_free_result($sqls);

                                }?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>
                            <p>Психологический тест:</p>
                        </td>
                        <td>
                            <div id="psy">
                                <? if ($mode === 'a') { ?>
                                    <input type="radio" value='1' id="psy1" name="psy"><label for="psy1">Да</label>
                                    <input type="radio" value='0' id="psy2" name="psy" checked="checked"><label
                                        for="psy2">Нет</label>
                                <? } else if ($mode === 'e') { ?>
                                    <input type="radio" value='1' id="psy1"
                                           name="psy" <? echo($member['psy'] == 1 ? 'checked="checked"' : '') ?>><label
                                        for="psy1">Да</label>
                                    <input type="radio" value='0' id="psy2"
                                           name="psy" <? echo($member['psy'] == 0 ? 'checked="checked"' : '') ?>><label
                                        for="psy2">Нет</label>
                                <?
                                }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Тест доступен для участников:</p>
                        </td>
                        <td>
                            <div id="active">
                                <? if ($mode === 'a') { ?>
                                    <input type="radio" value='1' id="active1" name="active"><label
                                        for="active1">Да</label>
                                    <input type="radio" value='0' id="active2" name="active" checked="checked"><label
                                        for="active2">Нет</label>
                                <? } else if ($mode === 'e') { ?>
                                    <input type="radio" value='1' id="active1"
                                           name="active" <? echo($member['active'] == 1 ? 'checked="checked"' : '') ?>>
                                    <label for="active1">Да</label>
                                    <input type="radio" value='0' id="active2"
                                           name="active" <? echo($member['active'] == 0 ? 'checked="checked"' : '') ?>>
                                    <label for="active2">Нет</label>
                                <?
                                }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Использовать в тесте только вопросы, прошедшие экспертизу:</p>
                        </td>
                        <td>
                            <div id="expert">
                                <? if ($mode === 'a') { ?>
                                    <input type="radio" value='1' id="expert1" name="expert"><label
                                        for="expert1">Да</label>
                                    <input type="radio" value='0' id="expert2" name="expert" checked="checked"><label
                                        for="expert2">Нет</label>
                                <? } else if ($mode === 'e') { ?>
                                    <input type="radio" value='1' id="expert1"
                                           name="expert" <? echo($member['expert'] == 1 ? 'checked="checked"' : '') ?>>
                                    <label for="expert1">Да</label>
                                    <input type="radio" value='0' id="expert2"
                                           name="expert" <? echo($member['expert'] == 0 ? 'checked="checked"' : '') ?>>
                                    <label for="expert2">Нет</label>
                                <?
                                }?>
                            </div>
                        </td>
                    </tr>
                    <?if (defined("IN_ADMIN")) {
                        ?>
                        <tr>
                            <td>
                                <p>Тест доступен для Интернета:</p>
                            </td>
                            <td>
                                <div id="external">
                                    <? if ($mode === 'a') { ?>
                                        <input type="radio" value='1' id="external1" name="external"><label
                                            for="external1">Да</label>
                                        <input type="radio" value='0' id="external2" name="external" checked="checked">
                                        <label for="external2">Нет</label>
                                    <? } else if ($mode === 'e') { ?>
                                        <input type="radio" value='1' id="external1"
                                               name="external" <? echo($member['external'] == 1 ? 'checked="checked"' : '') ?>>
                                        <label for="external1">Да</label>
                                        <input type="radio" value='0' id="external2"
                                               name="external" <? echo($member['external'] == 0 ? 'checked="checked"' : '') ?>>
                                        <label for="external2">Нет</label>
                                    <?
                                    }?>
                                </div>
                            </td>
                        </tr>
                    <?
                    }?>
                    <tr>
                        <td><p>Информация:</p></td>
                        <td><textarea name='content' style='width:100%' rows='7'><?= $member['content'] ?></textarea>
                        </td>
                    </tr>
                <?
                } else {
                    if ($goahead == 0)
                        echo "<tr><td align='center'><p>Превышено количество доступных тестов. Создание теста запрещено.</p></td></tr>";
                }
                ?>
              </table>
          </p></td>
      </tr>
    </table>
  </form>
 </div>
 <div id="buttonset">  
      <? if ($goahead == 1) { ?>
                    <button id="next" onclick="$('#step_four').submit();">
                        <i class='fa fa-arrow-right fa-lg'></i> Далее
                    </button>
                <?
                }?>
      <button id="close" onclick="parent.closeFancybox();">
        <i class='fa fa-times fa-lg'></i> Отмена
      </button> 
      <button id="help" onclick="window.open('h&id=5');"><i class="fa fa-question fa-lg"></i> Помощь</button>
 </div>
</body>
</html>
<?
            }


} else die;
?>