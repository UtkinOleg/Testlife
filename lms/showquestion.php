<?php
  function _encode($string) {
    $converter = array(
        '1' => 'a',   '0' => 'b',   '2' => 'v',
        '3' => 'g',   '5' => 'd',   '4' => 'e',
        '7' => 'e',   '8' => 'h',  '9' => 'z',
    );
    return strtr($string, $converter);
  }

  function _encode2($string) {
    $converter = array(
        '1' => 'g',   '0' => 'd',   '2' => 'w',
        '3' => 'q',   '5' => 'b',   '4' => 'k',
        '7' => 't',   '8' => 'u',  '9' => 'c',
    );
    return strtr($string, $converter);
  }
  
  function _decode($string) {
    $converter = array(
        'a' => '1',   'b' => '0',   'v' => '2',
        'g' => '3',   'd' => '5',   'e' => '4',
        'e' => '7',   'h' => '8',  'z' => '9',
    );
    return strtr($string, $converter);
  }

  function _decode2($string) {
    $converter = array(
        'g' => '1',   'd' => '0',   'w' => '2',
        'q' => '3',   'b' => '5',   'k' => '4',
        't' => '7',   'u' => '8',  'c' => '9',
    );
    return strtr($string, $converter);
  }

  function WriteAnswer($mysqli, $ansqid, $prefix, $token, $multi, $strseq, $stracc1, $stracc2, $strkbd)
  {
   if ($ansqid!=0) 
   {
   $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$ansqid."' ORDER BY id LIMIT 1;");
   $quest = mysqli_fetch_array($qq);
   if (!empty($quest))
   {
          $groupid = $quest['qgroupid'];
          if ($quest['qtype']=='accord') // Соответствия
          {
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           mysqli_query($mysqli,"START TRANSACTION;");
           $qid = $quest['id'];
           
             if ($allcount==0)
             {
              for($k = 0, $size = count($stracc1); $k < $size; ++$k) {
               $value = substr($stracc1[$k],7);
               $value = _decode($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpaccord1".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               return false;
              }
             }
             else
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($stracc1[$k],7);
               $value = _decode($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpaccord1".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                return false;
              }
              mysqli_free_result($ans);
             }
            
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           
             if ($allcount==0)
             {
              for($k = 0, $size = count($stracc2); $k < $size; ++$k) {
               $value = substr($stracc2[$k],7);
               $value = _decode2($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpaccord2".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               return false;
              }
             }
             else                              
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($stracc2[$k],7);
               $value = _decode2($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpaccord2".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                return false;
              }
              mysqli_free_result($ans);
             }
            

            mysqli_query($mysqli,"COMMIT;");
          }
          else
          if ($quest['qtype']=='sequence') // Последовательность
          {
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);
           
           mysqli_query($mysqli,"START TRANSACTION;");
           $qid = $quest['id'];
             if ($allcount==0)
             {
              for($k = 0, $size = count($strseq); $k < $size; ++$k) {
               $value = substr($strseq[$k],6);
               $value = _decode($value);
               if (!mysqli_query($mysqli,"INSERT INTO tmpsequence".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $value);"))
               return false;
              }
             }
             else
             {
              $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
              $k=0;
              while($answer = mysqli_fetch_array($ans))
              {
               $value = substr($strseq[$k],6);
               $value = _decode($value);
               $k++;
               if (!mysqli_query($mysqli,"UPDATE tmpsequence".$prefix." SET answerid=".$value." WHERE id='".$answer['id']."' AND questionid='".$qid."' AND signature='".$token."'"))
                return false;
              }
              mysqli_free_result($ans);
             }
            mysqli_query($mysqli,"COMMIT;");
          }
          else
          if ($quest['qtype']=='multichoice')
          {
           $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM answers WHERE questionid='".$quest['id']."' ORDER BY id");
           $k=0;
           mysqli_query($mysqli,"START TRANSACTION;");
           while($answer = mysqli_fetch_array($ans))
           {
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpmultianswer".$prefix." WHERE questionid='".$quest['id']."' AND answerid='".$answer['id']."' AND signature='".$token."' LIMIT 1;");
             $total = mysqli_fetch_array($tmpanswer);
             $allcount = $total['count(*)'];
             $qid = $quest['id'];
             $aid = $answer['id'];
             $value = $multi[$k];
             $k++;
             if ($allcount==0)
             {
              if (!mysqli_query($mysqli,"INSERT INTO tmpmultianswer".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        $aid,
                                        $value);"))
               return false;
             }
             else
              if (!mysqli_query($mysqli,"UPDATE tmpmultianswer".$prefix." SET value=".$value." WHERE questionid='".$qid."' AND answerid='".$aid."' AND signature='".$token."'"))
               return false;
             mysqli_free_result($tmpanswer);
           }
           mysqli_query($mysqli,"COMMIT");
           mysqli_free_result($ans);
          }
          else
          if ($quest['qtype']=='shortanswer')
          {
             mysqli_query($mysqli,"START TRANSACTION;");
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpshortanswer".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
             $total = mysqli_fetch_array($tmpanswer);
             $allcount = $total['count(*)'];
             $qid = $quest['id'];
             if ($allcount==0)
             {
              if (!mysqli_query($mysqli,"INSERT INTO tmpshortanswer".$prefix." VALUES (0,
                                        '$token',
                                        $qid,
                                        '$strkbd');"))
               return false;
             }
             else
              if (!mysqli_query($mysqli,"UPDATE tmpshortanswer".$prefix." SET value='".$strkbd."' WHERE questionid='".$qid."' AND signature='".$token."'"))
               return false;
             mysqli_free_result($tmpanswer);
             mysqli_query($mysqli,"COMMIT");
          }
   }   
   mysql_free_result($qq);
   return true;
   }
   else
   return false;
  }

  function IsRightAnswer($mysqli, $ansqid, $token, $prefix)
  {
     $rightanswer = null; 
     $answerexist = false;

     $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT qtype FROM questions WHERE id='".$ansqid."' LIMIT 1;");
     if ($qq)
     {
       $quest = mysqli_fetch_array($qq);
       $questtype = $quest['qtype'];
       if ($questtype=='accord')  // Просканируем соответствия
       {
         // Получим правильный ответ
         $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM answers WHERE questionid='".$ansqid."' ORDER BY id;");
         $rightanswers = array(); 
         while($answer = mysqli_fetch_array($ans))
          $rightanswers[] = $answer['id'];
         mysqli_free_result($ans);   

         $ball = 0;
         $kball = count($rightanswers);
         // Сравним с эталоном 
         $accord1 = array(); 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord1".$prefix." WHERE questionid='".$ansqid."' AND signature='".$token."' ORDER BY id;");
         while($useranswer = mysqli_fetch_array($userans))
          $accord1[] = $useranswer['answerid'];
         mysqli_free_result($userans);   

         $accord2 = array(); 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord2".$prefix." WHERE questionid='".$ansqid."' AND signature='".$token."' ORDER BY id;");
         while($useranswer = mysqli_fetch_array($userans))
          $accord2[] = $useranswer['answerid'];
         mysqli_free_result($userans);   
         
         for($i = 0; $i < $kball; $i++) {
           for($ii = 0; $ii < $kball; $ii++) {
             if ($accord1[$ii] == $rightanswers[$i] and $accord2[$ii] == $rightanswers[$i])
              $ball++;
           }
         }
         $answerexist = count($accord1)>0;

       }
       else
       if ($questtype=='sequence')
       {
         // Получим правильный ответ
         $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM answers WHERE questionid='".$ansqid."' ORDER BY id;");
         $rightanswers = array(); 
         while($answer = mysqli_fetch_array($ans))
          $rightanswers[] = $answer['id'];
         mysqli_free_result($ans);   
         $ball = 0;
         $kball = count($rightanswers);
         // Сравним с эталоном 
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpsequence".$prefix." WHERE questionid='".$ansqid."' AND signature='".$token."' ORDER BY id;");
         if ($userans)
         {
          $answerexist = true;
          $i=0;
          while($useranswer = mysqli_fetch_array($userans))
          {
           $aid = $useranswer['answerid'];
           if (empty($aid))
            $aid = 0;
           if ($aid == $rightanswers[$i])
            $ball++;
           $i++; 
          }
         }
         mysqli_free_result($userans);   
       }
       else
       if ($questtype=='multichoice')
       {
       $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$ansqid."' ORDER BY id");
       $ball=0;
       $kball=0;
       while($answer = mysqli_fetch_array($ans))
        {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM tmpmultianswer".$prefix." WHERE questionid='".$ansqid."' AND answerid='".$answer['id']."' AND signature='".$token."' LIMIT 1;");
         if ($userans)
         {
          $answerexist = true;
          if ($answer['ball']>0) 
           $kball++;
          $useranswer = mysqli_fetch_array($userans);
          $ku = $useranswer['value'];
          if (empty($ku))
           $ku = 0;
          if ($ku and $answer['ball']>0) 
           $ball++;
          if ($ku and $answer['ball']==0) 
           $ball--;
         }
         mysqli_free_result($userans);   
        }
        mysqli_free_result($ans); 
       } 
       else
       if ($questtype=='shortanswer')
       {
         $userans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpshortanswer".$prefix." WHERE questionid='".$ansqid."' AND signature='".$token."' ORDER BY id LIMIT 1;");
         $useranswer = mysqli_fetch_array($userans);
         $kustr = trim(mb_strtolower($useranswer['value'],'UTF-8'));
        
         if ($userans)
         {
          
          $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$ansqid."' ORDER BY id");
          $ball=0;
          $kball=0;
          while($answer = mysqli_fetch_array($ans))
          {
            $answerexist = true;
            if ($answer['ball']>0) 
             $kball++;
            $ansk = trim(mb_strtolower($answer['name'],'UTF-8'));
            if (mb_ereg_match($ansk,$kustr))
            //if ($kustr === $ansk) 
             $ball++;
          }
          mysqli_free_result($ans); 
         }
         mysqli_free_result($userans);   
       }

       
       if ($answerexist)
       {
        if ($questtype=='multichoice' or $questtype=='sequence' or $questtype=='accord')
        {
         if ($ball==$kball and $ball>0) 
         { 
          // Ну вот и правильный ответ
          $rightanswer = true;
         }
        }
        else
        if ($questtype=='shortanswer')
        {
         if ($ball>0) 
         { 
          // Ну вот и правильный ответ
          $rightanswer = true;
         }
        }
        else
         $rightanswer = false;
       }
      }
      mysqli_free_result($qq); 

      return $rightanswer;
 }

 /*
 *   v.1.0.2 Новый интерфейс простмотра
 *   
 */

 function showquestion($mysqli,$questid,$prefix,$token,$num) {
  $s="";
  $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questions WHERE id='".$questid."' ORDER BY id LIMIT 1;");
  $quest = mysqli_fetch_array($qq);
  if (!empty($quest))
        {
        $qgid = $quest['qgroupid'];
        
        $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM questgroups WHERE id='".$qgid."' LIMIT 1");
        $questgroup = mysqli_fetch_array($qg);
        $s.="<p align='center' style='font-size: 1em;'>Тема: ".$questgroup['name']."</p>
        <p style='font-size: 1.1em;'>№".$num.".&nbsp;<b>".$quest['content']."</b></p>";
        mysql_free_result($qg);
       
          if ($quest['qtype']=='multichoice')
          {
           
           $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$quest['id']."' ORDER BY id;");
           $anscnt = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM answers WHERE questionid='".$quest['id']."' AND ball=1 LIMIT 1;");
           $answercnt = mysqli_fetch_array($anscnt);
           $answer_count = $answercnt['count(*)'];
           mysqli_free_result($anscnt);
           if ($answer_count==1)
           {
             $s.="
<script>
function oncheck(n)
 {
   var cnt=$('#allcheck').val();
   for (var i = 1; i <= cnt; i++) {
    if (i==n)
    {
     $('#check'+n).val('1');
     $('#labelcheck'+n).html('<span class=\"ui-button-text\"><i class=\"fa fa-check fa-2x\"></i></span>');
    }
    else
    {
     $('#check'+i).val('0');
     $('#labelcheck'+i).html('<span class=\"ui-button-text\"><i class=\"fa fa-check fa-2x icon-invisible\"></i></span>');
    }
   }
 }
</script>";
             $a=0;
             while($answer = mysqli_fetch_array($ans))
             {
             $a++;
             $s.="
<script>
 $(function(){
     $('#zcheck'+".$a.").button();
 });
</script>";
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpmultianswer".$prefix." WHERE questionid='".$quest['id']."' AND answerid='".$answer['id']."' AND signature='".$token."' ORDER BY id LIMIT 1;");
             $curanswer = mysqli_fetch_array($tmpanswer);
             if (!empty($curanswer))
             {
              if ($curanswer['value']==1)
               $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='1'><input type='checkbox' id='zcheck".$a."' checked onclick='oncheck(".$a.")'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x'></i></label> ".$answer['name']."</p>";
              else
               $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='0'><input type='checkbox' id='zcheck".$a."' onclick='oncheck(".$a.")'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x icon-invisible'></i></label> ".$answer['name']."</p>";
             }
             else
              $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='0'><input type='checkbox' id='zcheck".$a."' onclick='oncheck(".$a.")'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x icon-invisible'></i></label> ".$answer['name']."</p>";
             mysqli_free_result($tmpanswer);
             }
             mysqli_free_result($ans);
             $s.="<input type='hidden' id='allcheck' value='".$a."'>";
           }
           else
           {
           $a=0;
           while($answer = mysqli_fetch_array($ans))
           {
             $a++;
             $s.="
<script>
 $(function(){
     $('#zcheck'+".$a.").button();
 });
 function oncheck".$a."()
 {
   ball = document.getElementById('zcheck'+".$a.").checked;
   if (ball)       
   {
     $('#check'+".$a.").val('1');
     $('#labelcheck'+".$a.").html('<span class=\"ui-button-text\"><i class=\"fa fa-check fa-2x\"></i></span>');
   }
   else
   {
     $('#check'+".$a.").val('0');
     $('#labelcheck'+".$a.").html('<span class=\"ui-button-text\"><i class=\"fa fa-check fa-2x icon-invisible\"></i></span>');
   }
 }
</script>";
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpmultianswer".$prefix." WHERE questionid='".$quest['id']."' AND answerid='".$answer['id']."' AND signature='".$token."' ORDER BY id LIMIT 1;");
             $curanswer = mysqli_fetch_array($tmpanswer);
             if (!empty($curanswer))
             {
              if ($curanswer['value']==1)
               $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='1'><input type='checkbox' id='zcheck".$a."' checked onclick='oncheck".$a."()'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x'></i></label> ".$answer['name']."</p>";
              else
               $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='0'><input type='checkbox' id='zcheck".$a."' onclick='oncheck".$a."()'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x icon-invisible'></i></label> ".$answer['name']."</p>";
             }
             else
              $s.="<p style='font-size: 1em;'><input type='hidden' id='check".$a."' value='0'><input type='checkbox' id='zcheck".$a."' onclick='oncheck".$a."()'><label id='labelcheck".$a."' style='font-size: 0.7em; background:#fff;' for='zcheck".$a."'><i class='fa fa-check fa-2x icon-invisible'></i></label> ".$answer['name']."</p>";
             mysqli_free_result($tmpanswer);
           }
           mysqli_free_result($ans);
           $s.="<input type='hidden' id='allcheck' value='".$a."'>";
          }
          }
          else
          if ($quest['qtype']=='shortanswer')
          {
             $s.="<script>\$(function() { \$('#kbd').focus(); });</script>";
             $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT value FROM tmpshortanswer".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id LIMIT 1;");
             $curanswer = mysqli_fetch_array($tmpanswer);
             $s.="<p>Введите ответ с клавиатуры:<p>";
             $s.="<p><input value='".$curanswer['value']."' type='text' id='kbd' style='box-shadow: inset 0 1px 1px rgba(0,0,0,.075); transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s; border-radius: 4px; border: 1px solid #000; font-size: 1.5em; width:99%;'></p>";
             mysqli_free_result($tmpanswer);
          }
          else
          if ($quest['qtype']=='accord') // Соответствия
          {
           $s.="<script>\$(function() { 
             \$( '#accord1' ).sortable({ revert: true, scrollSpeed: 100,
             stop: function( event, ui ) {  
              \$( '#accdata1' ).val(Base64.encode(\$('#accord1').sortable('serialize'))); }
             }); 
             \$( '#accord2' ).sortable({ revert: true, scrollSpeed: 100,
             stop: function( event, ui ) {  
              \$( '#accdata2' ).val(Base64.encode(\$('#accord2').sortable('serialize'))); }
             }); 
             \$( 'ul, li' ).disableSelection();
             \$( '#accdata1' ).val(Base64.encode(\$('#accord1').sortable('serialize')));
             \$( '#accdata2' ).val(Base64.encode(\$('#accord2').sortable('serialize')));
             });</script>";

           $s .= '<input type="hidden" id="accdata1" name="accdata1" value="">';
           $s .= '<input type="hidden" id="accdata2" name="accdata2" value="">';
           $s.='<p>При помощи указателя (удерживая элемент левой клавишей "мыши") установите соответствия элементов друг другу:</p>';
           
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);

           if ($allcount>0)
           {
            $s.='<div class="table-responsive"><table class="table"><tr><td width="50%"><div class="accord"><ul id="accord1">';
            $tmpsequence = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord1".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
            while($curanswer = mysqli_fetch_array($tmpsequence))
            { 
             $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, name FROM answers WHERE id='".$curanswer['answerid']."' LIMIT 1;");
             $answer = mysqli_fetch_array($ans);
             $pieces = explode("=", $answer['name']);
             $name = $pieces[0];
             $aid = _encode($answer['id']);
             $s .= '<li id="set1_'.$aid.'" class="ui-state-default">'.$name.'</li>';
             mysqli_free_result($ans);
            }
            mysqli_free_result($tmpsequence);
            $s .= '</ul></div></td><td width="50%">';
           }
           else
           {
            // Соответсвия без ответа - случайная выборка
            $s.='<div class="table-responsive"><table class="table"><tr><td width="50%"><div class="accord"><ul id="accord1">';
            $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$quest['id']."' ORDER BY RAND();");
            while($answer = mysqli_fetch_array($ans))
            {
             $pieces = explode("=", $answer['name']);
             $name = $pieces[0];
             $aid = _encode($answer['id']);
             $s .= '<li id="set1_'.$aid.'" class="ui-state-default">'.$name.'</li>';
            }
            mysqli_free_result($ans);
            $s .= '</ul></div></td><td width="50%">';
           } 
           
           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);

           if ($allcount>0)
           {
            $s.='<div class="accord"><ul id="accord2">';
            $tmpsequence = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpaccord2".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
            while($curanswer = mysqli_fetch_array($tmpsequence))
            { 
             $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id, name FROM answers WHERE id='".$curanswer['answerid']."' LIMIT 1;");
             $answer = mysqli_fetch_array($ans);
             $pieces = explode("=", $answer['name']);
             $name = $pieces[1];
             $aid = _encode2($answer['id']);
             $s .= '<li id="set2_'.$aid.'" class="ui-state-default">'.$name.'</li>';
             mysqli_free_result($ans);
            }
            mysqli_free_result($tmpsequence);
            $s .= '</ul></div></td></tr></table></div>';
           }
           else
           {
            $s.='<div class="accord"><ul id="accord2">';
            $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$quest['id']."' ORDER BY RAND();");
            while($answer = mysqli_fetch_array($ans))
            {
             $pieces = explode("=", $answer['name']);
             $name = $pieces[1];
             $aid = _encode2($answer['id']);
             $s .= '<li id="set2_'.$aid.'" class="ui-state-default">'.$name.'</li>';
            }
            mysqli_free_result($ans);
            $s .= '</ul></div></td></tr></table></div>';
           }
          }
          else
          if ($quest['qtype']=='sequence') // Последовательность
          {
           $s.="<script>\$(function() { \$( '#sequence' ).sortable({ revert: true, scrollSpeed: 100,
             stop: function( event, ui ) {  
              \$( '#seqdata' ).val(Base64.encode(\$('#sequence').sortable('serialize'))); }
             }); 
             \$( 'ul, li' ).disableSelection();
             \$( '#seqdata' ).val(Base64.encode(\$('#sequence').sortable('serialize')));
             });</script>";

           $s.='<p>При помощи указателя (удерживая элемент левой клавишей "мыши") установите элементы в правильной последовательности:</p>';
           $s.='<div class="sequence"><ul id="sequence">';

           $tmpanswer = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' LIMIT 1;");
           $total = mysqli_fetch_array($tmpanswer);
           $allcount = $total['count(*)'];
           mysqli_free_result($tmpanswer);

           if ($allcount>0)
           {
            $tmpsequence = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT answerid FROM tmpsequence".$prefix." WHERE questionid='".$quest['id']."' AND signature='".$token."' ORDER BY id;");
            while($curanswer = mysqli_fetch_array($tmpsequence))
            {
             $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM answers WHERE id='".$curanswer['answerid']."' LIMIT 1;");
             $answer = mysqli_fetch_array($ans);
             $s .= '<li id="set_'._encode($curanswer['answerid']).'" class="ui-state-default">'.$answer['name'].'</li>';
             mysqli_free_result($ans);
            }
            mysqli_free_result($tmpsequence);
            $s .= '</ul></div>';
            $s .= '<input type="hidden" id="seqdata" name="seqdata" value="">';
           }
           else
           {
            // Последоватеотнсть без ответа - случайная выборка
            $ans = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM answers WHERE questionid='".$quest['id']."' ORDER BY RAND()");
            while($answer = mysqli_fetch_array($ans))
             $s .= '<li id="set_'._encode($answer['id']).'" class="ui-state-default">'.$answer['name'].'</li>';
            mysqli_free_result($ans);
            $s .= '</ul></div>';
            $s .= '<input type="hidden" id="seqdata" name="seqdata" value="">';
           }
          }
          
          $s.="<input type='hidden' id='ansqid' value='".$quest['id']."'>";
         } else $s.="error";
  mysqli_free_result($qq);
  return $s;
 }
?>