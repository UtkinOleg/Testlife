<?php   
 include "hornavigation.php"; 
 include "leftsidebar.php"; 
 if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {      
?>
                        <li>
                            <a href="qt"><i class="fa fa-wrench fa-fw fa-inverse" style="color:#5ED52D;"></i> Создание тестов<span class="fa arrow"></span></a>
                        </li>
                        <li>
                            <a href="scales"><i class="fa fa-arrows-h fa-fw fa-inverse" style="color:#5ED52D;"></i> Шкалы оценок</a>
                        </li>
                        <? if(defined("IN_ADMIN")) { ?>
                        <li>
                            <a href="help"><i class="fa fa-question fa-fw"></i> Страницы помощи</a>
                        </li>
                        <li>
                            <a href="supervisors"><i class="fa fa-child fa-fw"></i> Супервизоры</a>
                        </li>
                        <li>
                            <a href="supervisors?t=user"><i class="fa fa-child fa-fw"></i> Пользователи</a>
                        </li>
                        <? } 
                        if(USER_EXPERT_KIM) {?>
                        <li>
                            <a href="ex"><i class="fa fa-check-circle fa-fw fa-inverse" style="color:#FF2F66;"></i> Экспертиза заданий<span class="fa arrow"></span></a>
                        </li>
                        <?}?>
                    </ul>
                </div>
            </div>
          </div> 
        </nav>

<div class="modal fade" id="myModalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelQ" aria-hidden="true">    
  <div class="modal-dialog">       
    <div class="modal-content">           
      <div class="modal-header">               
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;       
        </button>               
        <h4 class="modal-title" id="myModalLabelQ"></h4>           
      </div>           
      <div class="modal-body">      
        <form role="form">           
          <div id="Nameformgroup" class="form-group">               
            <input type="text" class="form-control" id="InputName" placeholder="Имя">           
          </div>           
          <div id="Emailformgroup" class="form-group">               
            <input type="email" class="form-control" id="InputEmail" placeholder="Email">           
          </div>           
          <input type="hidden" id="hiddenInfo" value="">           
          <div id="Infoformgroup" class="form-group">               
            <label for="InputInfo" id="LabelInfo">          
            </label>     
            <textarea class="form-control" rows="5" id="InputInfo"></textarea>            
          </div>      
        </form>           
      </div>           
      <div class="modal-footer">               
        <button type="button" class="btn btn-primary" onclick="formSend();">Отправить       
        </button>               
        <button type="button" class="btn btn-primary" onclick="$('#myModalMsg').modal('hide');">Закрыть       
        </button>           
      </div>       
    </div>   
  </div>
</div>      

<div class="modal fade" id="myHelpMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelHelp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabelHelp">Сообщение</h4>
      </div>
      <div id="myHelpMsgContent" class="modal-body">
      </div>
    </div>
  </div>
</div>

<div id="page-wrapper">
<? } else 
 if(defined("IN_USER")) {
?>
          </div>
        </nav>
        <div id="page-wrapper-user">
<?}?>