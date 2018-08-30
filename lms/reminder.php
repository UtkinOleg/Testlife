<?php

         if ($_SERVER['REQUEST_URI'] != "/profile")
         {
          if (empty(USER_EMAIL))
          {
          ?>
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Внимание!</strong> В вашем профиле не заполнено поле - электронная почта. <strong><a href="profile">Перейти в профиль</a></strong>
          </div>
          <?
          }
         }

if(defined("IN_SUPERVISOR") and USER_EXPERT_KIM==0)
{ 

  $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM usergroups WHERE userid='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($countg); 

  if ($count_gs==0)
  {
   echo '<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Не созданы группы пользователей!</strong> <a href="ug">Группы пользователей</a> - это списки участников зачетного тестирования. Обычно группой пользователей является список учеников, группа студентов и т.д. Участники идентифицируются в системе только по адресу электронной почты.&nbsp;<a href="h&id=2"><i title="Подробнее..." class="fa fa-question-circle fa-fw"></i></a>
          </div>';
  }

  $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM knowledge WHERE userid='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $count_gs = $cntgs['count(*)'];
  mysqli_free_result($countg); 
  if ($count_gs==0)
  {
   echo '<div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Не созданы области знаний!</strong> Области знаний - это дисциплины, разделы или темы. К любой области знаний могут принадлежать одна или несколько групп вопросов и тестов. Область знаний может содержать подобласти. Например, раздел содержит темы, темы - подтемы и т.д. <a onclick="dialogOpen(&quot;eknows&amp;m=a&amp;p=0&quot;,500,360)" href="javascript:;">Создать новую область</a>&nbsp;<a href="h&id=1"><i title="Подробнее..." class="fa fa-question-circle fa-fw"></i></a>
          </div>';
  }

  if (SUPERVISOR_REST==0)
  {
          ?>
          <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         		<strong>Внимание!</strong> У Вас закончились сеансы тестирования. Просмотр новых результатов тестирования будет недоступен.
          </div>
          <?
  }
 }
?>