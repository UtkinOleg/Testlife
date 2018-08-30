<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных
include "config.php";
require_once "header.php"; 
$grid = $_GET['id'];
$kid = $_GET['kid'];
?>
<link rel="stylesheet" href="lms/scripts/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="lms/scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>
<link rel="stylesheet" type="text/css" href="lms/scripts/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
<script type="text/javascript" src="lms/scripts/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget {
 font-family: Verdana,Arial,sans-serif;
 font-size: 0.8em;
}
.ui-state-highlight {
 border: 2px solid #838EFA;
}
.ui-state-error {
 border: 2px solid #cd0a0a;
}
p { font: 14px / 1.4 'Helvetica', 'Arial', sans-serif; } 
#spinner { display: none;   position: fixed; 	top: 50%; 	left: 50%; 	margin-top: -22px; 	margin-left: -22px; 	background-position: 0 -108px; 	opacity: 0.8; 	cursor: pointer; 	z-index: 8060;   width: 44px; 	height: 44px; 	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;   border-radius:7px; } 
#buttonset { display:block;  font-family:Arial;  text-align: center;   width: 600px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -300px; } 
#buttonsetm { display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 60px; bottom : 50px;  position: absolute; overflow: auto;} 
</style>
<script>

  var update = false;

  function closeFancyboxAndRedirectToUrl(url){
    $.fancybox.close();
    location.replace(url);
  }    

  function closeFancybox(){
    $.fancybox.close();
  } 

  function closeDlg (k, q)
  {
   if (update)
    parent.closeFancyboxAndRedirectToUrl(k, q);
   else 
    parent.closeFancybox();
  }   

  function editqm(kid, id, qid) {
				$.fancybox.open({
					href : 'addquestmanual&m=e&qid='+qid+'&kid='+kid+'&id='+id,
					type : 'iframe',
          width : document.documentElement.clientWidth,
          height : document.documentElement.clientHeight,
          fitToView : true,
          autoSize : false,          
          modal : true,
          showCloseButton : false,
					padding : 5
				});
  }

  function delquest(questid, groupid) {
					$('<div/>', {'title': 'Удаление вопроса'}).dialog({
						autoOpen: true,
						modal: true,
            width: 440,
						buttons: {
							"Да": function() {
								$(this).dialog("close");
                $("#spinner").fadeIn("slow");
                $.post('delquest.json',{grid:groupid, id:questid},  
                 function(data){  
                  eval('var obj='+data);         
                  if(obj.ok=='1') {
                   $('#q1list'+questid).empty();  
                   $('#q2list'+questid).empty();  
                   $('#q3list'+questid).empty();  
                   $('#q4list'+questid).empty();  
                   $('#q5list'+questid).empty();  
                   $('#q6list'+questid).empty();  
                  }       
                  $('#spinner').fadeOut("slow");
                  update = true;
                 }); 
							},
							"Нет": function() {
								$(this).dialog("close");
							}
						}
					}).html('<p>Вы действительно хотите удалить вопрос из группы?</p>');
  }
  
  $(document).ready(function(){
   	$('.fancybox').fancybox();
    $( "#close" ).button();
  });

</script>
</head><body>
<div id="spinner"></div>
<?php
   if (defined("IN_ADMIN")) 
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM questgroups WHERE id='".$grid."' LIMIT 1;");
    else
   if (defined("IN_SUPERVISOR"))
    $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM questgroups WHERE id='".$grid."' AND userid='".USER_ID."' LIMIT 1;");
   $qgname = mysqli_fetch_array($sql);
   $qgroupname = $qgname['name'];
   mysqli_free_result($sql); 
   if (!empty($qgroupname))
   {
?>
    <div class="ui-widget">            	   
      <div id='info1' class="ui-state-highlight ui-corner-all" style="text-align: center; padding: 0 .7em; font: 14px / 1.4 'Helvetica', 'Arial', sans-serif;">                    
        <p>      
          <div id="info2">Список вопросов группы <strong><?=$qgroupname?></strong></div>    
        </p>            	   
      </div>
    </div>
    <div id="buttonsetm">
        <table width='99%' align='center' border="0" cellpadding=3 cellspacing=0>
            <tbody>
      <? 
      
       $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM testdata WHERE groupid='".$grid."'");
   //   $counttest = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(sr.id) FROM testdata as td, singleresult as sr WHERE td.testid=sr.testid AND td.groupid='".$grid."' LIMIT 1;");
       $cnttests = mysqli_fetch_array($counttest);
       $count_res = $cnttests['count(*)'];
       mysqli_free_result($counttest); 
      
       $qst = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE qgroupid='".$grid."' ORDER BY id");
       $i=0;
       while($member = mysqli_fetch_array($qst))
       {
        if ($i % 2 == 0)
         echo "<tr style='background-color:#f7f7f7;'><td width='50'><div id='q1list".$member['id']."'><p>".++$i;
        else
         echo "<tr><td width='50'><div id='q1list".$member['id']."'><p>".++$i;
        if (defined("IN_ADMIN")) echo" [".$member['id']."]";
        echo "</p></div></td>";

        if ($member['qtype']=='multichoice')
         $s = "<i title='Закрытый' class='fa fa-check-square-o fa-lg'></i>";
        else 
        if ($member['qtype']=='shortanswer')
         $s = "<i title='Открытый' class='fa fa-square-o fa-lg'></i>";
        else 
        if ($member['qtype']=='sequence')
         $s = "<i title='Последовательнсть' class='fa fa-reorder fa-lg'></i>";
        else 
        if ($member['qtype']=='accord')
         $s = "<i title='Соответствия' class='fa fa-random fa-lg'></i>";

        echo "<td width='50'><div id='q2list".$member['id']."'>".$s."</div></td>";

        echo "<td width='400'><div id='q3list".$member['id']."'><p><font size='-1'>".$member['content']."</font></div></td>";

        echo "<td width='400'><div id='q4list".$member['id']."'>";
        
        $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$member['id']."' ORDER BY id");
         while($answer = mysqli_fetch_array($ans))
         {
           if ($member['qtype']=='accord')
           {
             $pieces = explode("=", $answer['name']);
             $name = $pieces[0];
             $name2 = $pieces[1];
             echo "<p>".$name." <i class='fa fa-arrows-h fa-lg'></i> ".$name2."</p>";
           }
           else
           {
           if ($answer['ball']>0)
            echo "<p><i class='fa fa-check fa-lg'></i> ".$answer['name']."</p>";
           else
            echo "<p>".$answer['name']."</p>";
           } 
         }
        mysqli_free_result($ans); 
        echo "</div></td>";
        
        if ($count_res==0)
         echo '<td width="30" align="center"><div id="q5list'.$member['id'].'">
         <a onclick="editqm('.$kid.','.$grid.','.$member['id'].')" href="javascript:;"><i title="Изменить вопрос" class="fa fa-cog fa-lg"></i></a>
         </div></td><td width="30" align="center"><div id="q6list'.$member['id'].'">
         <a href="javascript:;" onClick="delquest('.$member['id'].','.$grid.')" title="Удалить вопрос?"><i class="fa fa-trash fa-lg"></i></a>
        </div></td></tr>';
        else
         echo '<td width="30" align="center"></td><td width="30" align="center"></td></tr></div>';
       }
      mysqli_free_result($gst); 
      ?>
   </tbody>          
  </table>
 </div>
 <?}?>
 <div id="buttonset">  
      <button style="font-size: 1em;" id="close" onclick="closeDlg(<?=$kid?>,'q')">
        <i class='fa fa-times fa-lg'></i> Закрыть
      </button>  
 </div>
</body></html>
<?
} else die;
?>