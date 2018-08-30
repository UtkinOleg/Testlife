  /*   
  *  JSON functions 
  *  Oleg Utkin (siberia-soft@yandex.ru)
  */
  
  function dialogOpen(phref, pwidth, pheight) {
				if (pwidth==0)
         pwidth = document.documentElement.clientWidth;
				if (pheight==0)
         pheight = document.documentElement.clientHeight;
        $.fancybox.open({
					href : phref,
					type : 'iframe',
          width : pwidth,
          height : pheight,
          fitToView : true,
          autoSize : false,          
          modal : true,
          showCloseButton : false,
					padding : 5
				});
  }
  
  function Resume() {
    $('#defaultCountdown').countdown('resume');
  } 

  function getquest(id, mode) 
  {
   if (id!=null)
   {
    $("#spinner").fadeIn("slow");
    $.post('getquest.json',{kid:id},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
        $('#questions').html(obj.content);        
        $('#tests').html(obj.content2);        
        $('#knowname').html(obj.knowname);        
        $('#knowcontent').html(obj.knowcontent); 
        $('#spinner').fadeOut("slow");
        if (mode=='q') 
         $('#qtTab li:eq(0) a').tab('show');
        else
        if (mode=='t') 
         $('#qtTab li:eq(1) a').tab('show');
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
   } 
  }

  function getexpertquest(id, mode) 
  {
   if (id!=null)
   {
    $("#spinner").fadeIn("slow");
    $.post('getexpertquest.json',{kid:id},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
        $('#questions').html(obj.content);        
        $('#knowname').html(obj.knowname);        
        $('#knowcontent').html(obj.knowcontent); 
        $('#spinner').fadeOut("slow");
        $('#qtTab li:eq(0) a').tab('show');
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
   } 
  }

  function activetests() 
  {
    $("#spinner").fadeIn("slow");
    $.post('getactivetests.json',{},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#tests').empty();        
       $('#tests').append(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getusergroups(parent) 
  {
    $("#spinner").fadeIn("slow");
    $.post('getusergroups.json',{parentid:parent},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#usersgroups').html(obj.content);        
       if (parent==0)
        $('#usersgroupname').html('Группы пользователей для организации тестирования');
       else
       {
        dels = '';
        if(obj.del=='0')
         dels = '&nbsp;<a href="javascript:;" onclick="$(\'#DelUserhiddenInfoFolderParent\').val('+obj.folderparent+
         ');$(\'#DelUserhiddenInfoFolderId\').val('+parent+');$(\'#DelUserFolder\').modal(\'show\');"><i class="fa fa-trash fa-fw"></i></a>';
        
        $('#usersgroupname').html(obj.fname+'&nbsp;<a href="javascript:;" onclick="dialogOpen(\'eduserfolder&m=e&id='+parent+
        '\',500,200)"><i class="fa fa-cog fa-fw"></i></a>'+dels);        
       }
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getquests(grid,kkid) 
  {
    $("#spinner").fadeIn("slow");
    $.post('getquests.json',{gid:grid,kid:kkid},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#quests').html(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getexpertquests(grid,kkid) 
  {
    $("#spinner").fadeIn("slow");
    $.post('getexpertquests.json',{gid:grid,kid:kkid},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#quests').html(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getpsy(tid,kkid) 
  {
    $("#spinner").fadeIn("slow");
    $.post('getpsy.json',{id:tid,kid:kkid},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#psy').html(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function gethelppages() 
  {
    $("#spinner").fadeIn("slow");
    $.post('gethelppages.json',{},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#helppages').empty();        
       $('#helppages').append(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getscales()  
  {
    $("#spinner").fadeIn("slow");
    $.post('getscales.json',{},  
     function(data){  
      eval('var obj='+data);         
      if(obj.ok=='1')
      {
       $('#scales').html(obj.content);        
       $('#spinner').fadeOut("slow");
      } 
      else 
      {
       $('#spinner').fadeOut("slow");
       alert("Ошибка при получении данных.");
      }
    });  
  }

  function getusermsgs() 
  {
    $.post('getusermsgs.json',{},  
     function(data){  
      eval('var obj='+data);         
      $('#usermsgsoper').prop("onclick",null);        
      $('#usermsgs').empty();  
      if(obj.ok=='1')
       $('#usermsgs').append(obj.content);        
      else 
       $('#usermsgs').append('Ошибка при загрузке сообщений.');        
    }); 
  }

  function gettesttasks() 
  {
    $.post('gettesttasks.json',{},  
     function(data){  
      eval('var obj='+data);         
      $('#testtasks').empty();  
      if(obj.ok=='1')
       $('#testtasks').append(obj.content);        
      else 
       $('#testtasks').append('Ошибка при загрузке текущих сеансов.');        
    }); 
  }

  function formDelAllQ() {
     var postParams = {
          id: $('#DelAllQhiddenInfoId').val()
         }; 
     $('#DelAllQ').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delquests.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          if(obj.ok=='1') {
           $('#qlist'+$('#DelAllQhiddenInfoId').val()).empty();        
           $('#cqlist'+$('#DelAllQhiddenInfoId').val()).empty();        
           $('#spinner').fadeOut("slow");
          } 
          else
          { 
           $('#spinner').fadeOut("slow");
           myInfoMsgShow("Ошибка при удалении вопросов из группы!");
          }
       });
  }

  function formDelQuestion() {
     var postParams = {
          id: $('#DelQuestionhiddenInfoId').val(),
          grid: $('#DelQuestionhiddenInfoGrId').val()
         }; 
     $('#DelQuestion').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delquest.json", postParams, 
       function (data) {
          $('#spinner').fadeOut("slow");
          eval("var obj=" + data);
          if(obj.ok=='1') 
           $('#qlist'+$('#DelQuestionhiddenInfoId').val()).empty();  
          else
           myInfoMsgShow("Ошибка при удалении вопроса!");
       });
  }

  function formDelPsy() {
     var postParams = {
          id: $('#DelPsyhiddenInfoId').val(),
          tid: $('#DelPsyhiddenInfoTestId').val()
         }; 
     $('#DelPsy').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delpsy.json", postParams, 
       function (data) {
          $('#spinner').fadeOut("slow");
          eval("var obj=" + data);
          if(obj.ok=='1') 
           $('#qlist'+$('#DelPsyhiddenInfoId').val()).empty();  
          else
           myInfoMsgShow("Ошибка при удалении интерпретации!");
       });
  }

  function formDelTest() {
     var postParams = {
          id: $('#DelTesthiddenInfoId').val()
         }; 
     $('#DelTest').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("deltest.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
            getquest($('#DelTesthiddenInfoKid').val(),'t');
          else 
           myInfoMsgShow("Ошибка при удалении теста!");
       });
  }

  function formDelUserGroup() {
     var postParams = {
          id: $('#DelUserGrouphiddenInfoId').val()
         }; 
     $('#DelUserGroup').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delusergroup.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
            getusergroups($('#DelUserGrouphiddenInfoParent').val());
          else 
           myInfoMsgShow("Ошибка при удалении группы!");
       });
  }

  function formDelUserFolder() {
     var postParams = {
          id: $('#DelUserhiddenInfoFolderId').val()
         }; 
     $('#DelUserFolder').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("deluserfolder.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
            getusergroups($('#DelUserhiddenInfoFolderParent').val());
          else 
           myInfoMsgShow("Ошибка при удалении папки!");
       });
  }

  function formDelHelpPage() {
     var postParams = {
          id: $('#DelHelpPagehiddenInfoId').val()
         }; 
     $('#DelHelpPage').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delhelppage.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
           gethelppages();
          else 
           myInfoMsgShow("Ошибка при удалении страницы!");
       });
  }

  function formDelQGroup() {
     var postParams = {
          id: $('#DelQGrouphiddenInfoId').val()
         }; 
     $('#DelQGroup').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delgroup.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
            getquest($('#DelQGrouphiddenInfoKid').val(),'q');
          else 
           myInfoMsgShow("Ошибка при удалении группы вопросов!");
       });
  }

  function formExpertQGroup() {
     var postParams = {
          qgid: $('#QGroupId').val(),
          ugid: $('#UserGroupKIMId').val()
         }; 
     $('#ExpertQGroup').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("expertqgroup.json", postParams, 
       function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
           myInfoMsgShow("Запрос на экспертизу группы вопросов отправлен!");
          else 
           myInfoMsgShow("Ошибка при отправке запроса!");
       });
  }
  
  function formDelKnow() {
     var postParams = {
          kid: $('#hiddenInfoKnow').val()
         }; 
     $('#DelKnow').modal('hide'); 
     $("#spinner").fadeIn("slow"); 
     $.post("delknow.json", postParams, function (data) {
          eval("var obj=" + data);
          $('#spinner').fadeOut("slow");
          if(obj.ok=='1') 
           location.replace("qt");
          else 
           myInfoMsgShow("Ошибка при удаленни области знаний!");
     });
  }
  
  function myInfoMsgShow(info) {
     $('#myInfoMsgContent').html(info);
     $('#myInfoMsg').modal('show');  
  }

  