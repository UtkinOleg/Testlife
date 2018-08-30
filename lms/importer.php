<?php

function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}

function str2url($str) {
    $str = rus2translit($str);
    $str = strtolower($str);
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    $str = trim($str, "-");
    return $str;
}

function read_docx_file($filename, $qgid, $xmlupload_dir) {

    $error = "";

    // Проверяем правильность ввода информации в поля формы
    if (empty($xmlfile['name'])) 
    {
     $error = " Файл не найден.";
    }

    if($xmlfile['name']!="")
    { 
     $filedata = $xmlfile['name']; 
     $filename = explode(".", $filedata); 
     $filenameext = $filename[count($filename)-1]; 
     $realfiledata = str2url($filename[0]).".".$filenameext;
     $filesize = $xmlfile["size"]; 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if($xmlfile["name"]!="")
   { 
          $origfilename = $xmlfile["name"]; 
          $filename = explode(".", $origfilename); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          if($filenameext=='docx') 
              $file_ext_allow = TRUE; 

          if($file_ext_allow){ 
            if($xmlfile["size"] < 1048576){ 
              if(!move_uploaded_file($xmlfile["tmp_name"], $xmlupload_dir.$qgid.$realfiledata)){ 
              } 
            } 
          } 
    }   
  

    $striped_content = '';
    $content = '';

    if(!$xmlupload_dir.$qgid.$realfiledata || !file_exists($xmlupload_dir.$qgid.$realfiledata)) return false;

    $zip = zip_open($xmlupload_dir.$qgid.$realfiledata);

    if (!$zip || is_numeric($zip)) return false;

    while ($zip_entry = zip_read($zip)) {

        if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

        if (zip_entry_name($zip_entry) != "word/document.xml") continue;

        $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

        zip_entry_close($zip_entry);
    }// end while

    zip_close($zip);

    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
    $content = str_replace('</w:r></w:p>', "\r\n", $content);
    $striped_content = strip_tags($content);
    return str_replace("\r\n", "<br>", $striped_content);
}

function read_doc_file($xmlfile, $qgid, $xmlupload_dir ) {
    $error = "";
    require_once ('../lib/transliteration.inc');

    // Проверяем правильность ввода информации в поля формы
    if (empty($xmlfile['name'])) 
    {
     $error = " Файл не найден.";
    }

    if($xmlfile['name']!="")
    { 
     $filedata = $xmlfile['name']; 
     $realfiledata = transliteration_clean_filename($xmlfile["name"],"ru");
     $filesize = $xmlfile["size"]; 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if($xmlfile["name"]!="")
   { 
          $origfilename = $xmlfile["name"]; 
          $filename = explode(".", $origfilename); 
          $filenameext = $filename[count($filename)-1]; 
          unset($filename[count($filename)-1]); 
          $filename = implode(".", $filename); 
          $filename = substr($filename, 0, 15).".".$filenameext; 
          $file_ext_allow = FALSE; 
          if($filenameext=='doc') 
              $file_ext_allow = TRUE; 

          if($file_ext_allow){ 
            if($xmlfile["size"] < 1048576){ 
              if(!move_uploaded_file($xmlfile["tmp_name"], $xmlupload_dir.$qgid.$realfiledata)){ 
              } 
            } 
          } 
    }   
  
   if (file_exists($xmlupload_dir.$qgid.$realfiledata)) {
   
    $fileHandle = fopen($xmlupload_dir.$qgid.$realfiledata, "r");
    $word_text = @fread($fileHandle, filesize($xmlupload_dir.$qgid.$realfiledata));
    $line = "";
    $tam = filesize($xmlupload_dir.$qgid.$realfiledata);
    $nulos = 0;
    $caracteres = 0;
    for($i=1536; $i<$tam; $i++)
    {
        $line .= $word_text[$i];

        if( $word_text[$i] == 0)
        {
            $nulos++;
        }
        else
        {
            $nulos=0;
            $caracteres++;
        }

        if( $nulos>1996)
        {   
            break;  
        }
    }

    //echo $caracteres;

    $lines = explode(chr(0x0D),$line);
    //$outtext = "<pre>";

    $outtext = "";
    foreach($lines as $thisline)
    {
        $tam = strlen($thisline);
        if( !$tam )
        {
            continue;
        }

        $new_line = ""; 
        for($i=0; $i<$tam; $i++)
        {
            $onechar = $thisline[$i];
            if( $onechar > chr(240) )
            {
                continue;
            }

            if( $onechar >= chr(0x20) )
            {
                $caracteres++;
                $new_line .= $onechar;
            }

            if( $onechar == chr(0x14) )
            {
                $new_line .= "</a>";
            }

            if( $onechar == chr(0x07) )
            {
                $new_line .= "\t";
                if( isset($thisline[$i+1]) )
                {
                    if( $thisline[$i+1] == chr(0x07) )
                    {
                        $new_line .= "\n";
                    }
                }
            }
        }
        //troca por hiperlink
        $new_line = str_replace("HYPERLINK" ,"<a href=",$new_line); 
        $new_line = str_replace("\o" ,">",$new_line); 
        $new_line .= "\n";

        //link de imagens
        $new_line = str_replace("INCLUDEPICTURE" ,"<br><img src=",$new_line); 
        $new_line = str_replace("\*" ,"><br>",$new_line); 
        $new_line = str_replace("MERGEFORMATINET" ,"",$new_line); 


        $outtext .= nl2br($new_line);
    }   
        return $outtext;
        
   /*     if(($fh = fopen($xmlupload_dir.$qgid.$realfiledata, 'r')) !== false ) 
        {
           $headers = fread($fh, 0xA00);

           // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
           $n1 = ( ord($headers[0x21C]) - 1 );

           // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
           $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

           // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
           $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

           // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
           $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

           // Total length of text in the document
           $textLength = ($n1 + $n2 + $n3 + $n4);

           $extracted_plaintext = fread($fh, $textLength);

           // simple print character stream without new lines
           fclose($fh);
           // if you want to see your paragraphs in a new line, do this
           return nl2br($extracted_plaintext);
           // need more spacing after each paragraph use another nl2br
        } */
    }   
   
  }

  function ImportXML($mysqli,$xmlfile,$qgid,$xmlupload_dir)
  {
    
    $error = "";

    // Проверяем правильность ввода информации в поля формы
    if (empty($xmlfile['name'])) 
    {
     $error = " Файл не найден.";
    }

    if($xmlfile['name']!="")
    { 
     $filedata = $xmlfile['name']; 
     $filename = explode(".", $filedata); 
     $filenameext = strtolower($filename[count($filename)-1]); 
     $realfiledata = str2url($filename[0]).".".$filenameext;
     $filesize = $xmlfile["size"]; 
     $file_ext_allow = FALSE; 
     if($filenameext=='xml') 
      $file_ext_allow = TRUE; 
     if($file_ext_allow){ 
            if($filesize < 104576){ 
              if(!move_uploaded_file($xmlfile["tmp_name"], $xmlupload_dir.$qgid.$realfiledata)){ 
                $error = $error." ".$origfilename." не был загружен в каталог сервера."; 
              } 
            }else{ 
              $error=$error." ".$origfilename." (".$filesize." байт) превышает установленный размер файла."; 
            } 
          }else{ 
            $error=$error." ".$origfilename." не поддерживается."; 
     } 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if (file_exists($xmlupload_dir.$qgid.$realfiledata)) {
    $xml = simplexml_load_file($xmlupload_dir.$qgid.$realfiledata);
    $i=0;
    $cntq=0;
    $qtarray = array ('multichoice', 'shortanswer');
    foreach ($xml->xpath('//question') as $question) 
    {
     $cntq++;
     $qtype = $question['type'];
     
     if (!in_array($qtype, $qtarray))
      $qtype = 'multichoice';
      
     // Запишем вопрос в базу
     if (!empty($question->name->text))
     {
      
      $questionnametext = $question->name->text;
      $questionquestiontexttext = $question->questiontext->text;
      
      mysqli_query($mysqli,"START TRANSACTION;");
      
      $query = "INSERT INTO questions VALUES (0,
      '$questionnametext',
      '$questionquestiontexttext',
      $qgid,
      '$qtype',
      '')";
  
      if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении вопроса ".$questionnametext;
      }
      
      $questionid = mysqli_insert_id($mysqli);

      // Запишем ответы в базу
      if ($questionid>0)
      foreach ($question->answer as $answer) 
      {
       $answertext = trim($answer->text);
       $ball = $answer['fraction'];
       $query = "INSERT INTO answers VALUES (0,
       '$answertext',
       $questionid,
       $ball)";
  
       if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
       }
      }
     }   
    }
    mysqli_query($mysqli,"COMMIT");

    
    } 
    else 
    {
     $error=$error.' Не удалось открыть файл '.$xmlupload_dir.$qgid.$realfiledata;
    } 
   
   return $error;  
  }

  function ImportTXT($mysqli,$xmlfile,$qgid,$xmlupload_dir)
  {
    
    $error = "";

    // Проверяем правильность ввода информации в поля формы
    if (empty($xmlfile['name'])) 
    {
     $error = " Файл не найден.";
    }

    if($xmlfile['name']!="")
    { 
     $filedata = $xmlfile['name']; 
     $filename = explode(".", $filedata); 
     $filenameext = strtolower($filename[count($filename)-1]); 
     $realfiledata = str2url($filename[0]).".".$filenameext;
     $filesize = $xmlfile["size"]; 
     $file_ext_allow = FALSE; 
     if($filenameext=='txt') 
      $file_ext_allow = TRUE; 
     if($file_ext_allow){ 
            if($filesize < 104576){ 
              if(!move_uploaded_file($xmlfile["tmp_name"], $xmlupload_dir.$qgid.$realfiledata)){ 
                $error = $error." ".$origfilename." не был загружен в каталог сервера."; 
              } 
            }else{ 
              $error=$error." ".$origfilename." (".$filesize." байт) превышает установленный размер файла."; 
            } 
          }else{ 
            $error=$error." ".$origfilename." не поддерживается."; 
     } 
    }
    else
    {
     $filedata = ""; 
     $realfiledata = "";
     $filesize = 0; 
    }
  
   if (file_exists($xmlupload_dir.$qgid.$realfiledata)) 
   {

    $f = fopen($xmlupload_dir.$qgid.$realfiledata, "r");
    $i=0;
    $q="";
    $allq="";
    $putq=0;

    $ghead = "";
    $h="";
    $a = array();
    $v = array();
    $cntq = 0;
    while(!feof($f)) 
    { 
     $s = fgets($f);
     
//     if (mb_detect_encoding($s, 'UTF-8')!='UTF-8')
     $s = mb_convert_encoding ($s,'UTF-8','Windows-1251');
     
     if ($s[0]=='?')
     {
       $allq = $q;
       $ghead = $h;
       $putq = 1;
       $q = substr($s, 1);
       $q = htmlspecialchars($q, ENT_QUOTES);
       $h = $q;
       $cntq++;
     }
     else
     if ($s[0]=='+')  // Закрытая форма правильный ответ
     {
       $qtype = 'multichoice';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
       $v[] = 1;
     }
     else
     if ($s[0]=='-')  // Закрытая форма неправильный ответ
     {
       $qtype = 'multichoice';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
       $v[] = 0;
     }
     else
     if ($s[0]=='=')  // Открытая форма правильный ответ
     {
       $qtype = 'shortanswer';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
     }
     else
     if ($s[0]=='#')  // Правильная последоватеотнсть
     {
       $qtype = 'sequence';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
     }
     else
     if ($s[0]=='&')  // Соответствия
     {
       $qtype = 'accord';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
     }
     else // Продолжение предыдущего вопроса
     {
       // $s = htmlspecialchars($s, ENT_QUOTES);
       $q .= " ".$s;
     }
     
     if ($putq==1 and $allq!="" and count($a)>0)
     {
      
      mysqli_query($mysqli,"START TRANSACTION;");
      $query = "INSERT INTO questions VALUES (0,
      '$ghead',
      '$allq',
      $qgid,
      '$qtype',
      '')";
  
      if(!mysqli_query($mysqli,$query)) {
        $error=$error." Ошибка при добавлении вопроса ".$allq;
      }
      $questionid = mysqli_insert_id($mysqli);
      // Запишем ответы в базу
      if ($questionid>0)
      {
        for ($i = 0; $i < count($a); $i++) { 
         $answertext = trim($a[$i]);
         if ($qtype == 'multichoice') 
          $ball = $v[$i];
         else
          $ball = 1; 
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
      }
      mysqli_query($mysqli,"COMMIT");
      $putq = 0;
      $allq = "";
      $a = array();
      $v = array();
    }


    }
   
    $allq = $q;
    $ghead = $h;
    $putq = 1;
    // Запишем последний вопрос
    if ($putq==1 and $allq!="" and count($a)>0)
     {
      
      mysqli_query($mysqli,"START TRANSACTION;");
      $query = "INSERT INTO questions VALUES (0,
      '$ghead',
      '$allq',
      $qgid,
      '$qtype',
      '')";
  
      if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении вопроса ".$allq;
      }
      
      $questionid = mysqli_insert_id($mysqli);

      // Запишем ответы в базу
      if ($questionid>0)
      {
        for ($i = 0; $i < count($a); $i++) { 
         $answertext = trim($a[$i]);
         if ($qtype == 'multichoice') 
          $ball = $v[$i];
         else
          $ball = 1; 
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
      }
      mysqli_query($mysqli,"COMMIT");
    }
   
	  fclose($f);
    } else {
              $error=$error.' Не удалось открыть файл '.$xmlupload_dir.$qgid.$realfiledata;
   }   
   return $error;  
  }

  function ImportTXTContent($mysqli,$txtcontent,$qgid)
  {
    
    $error = "";
    $i=0;
    $q="";
    $allq="";
    $putq=0;
    $lena=0;
    $lenkbd=0;
    $ghead = "";
    $h="";
    $a = array();
    $v = array();
    $kbd = array();
    $cntq=0;

    $arr = explode("<br />", $txtcontent);
    
    foreach( $arr as $s ) { 
     
//    if (mb_detect_encoding($s, 'UTF-8')!='UTF-8')
//     $s = mb_convert_encoding ($s,'UTF-8','ASCII');
//     echo $s;
     
     
     if ($s[0]=='?')
     {
       $allq = $q;
       $ghead = $h;
       $putq = 1;
       $q = substr($s, 1);
       $q = htmlspecialchars($q, ENT_QUOTES);
       $h = $q;
       $cntq++;
     }
     else
     if ($s[0]=='+')  // Закрытая форма правильный ответ
     {
       $qtype = 'multichoice';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
       $v[] = 1;
       $lena++;
     }
     else
     if ($s[0]=='-')  // Закрытая форма неправильный ответ
     {
       $qtype = 'multichoice';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $a[] = $ta;
       $v[] = 0;
       $lena++;
     }
     else
     if ($s[0]=='=')  // Открытая форма правильный ответ
     {
       $qtype = 'shortanswer';
       $ta = substr($s, 1);
       $ta = htmlspecialchars($ta, ENT_QUOTES);
       $kbd[] = $ta;
       $lenkbd++;
     }
     else // Продолжение предыдущего вопроса
     {
       // $s = htmlspecialchars($s, ENT_QUOTES);
       $q .= " ".$s;
     }
     if ($putq==1 and $allq!="")
     {
      if ($cntq>5 and LOWSUPERVISOR) break;
      
      mysqli_query($mysqli,"START TRANSACTION;");
      $query = "INSERT INTO questions VALUES (0,
      '$ghead',
      '$allq',
      $qgid,
      '$qtype',
      '')";
  
      if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении вопроса ".$allq;
      }
      
      $questionid = mysqli_insert_id($mysqli);

      // Запишем ответы в базу
      if ($questionid>0)
      {
       if ($qtype == 'multichoice')
       {
        for ($i = 0; $i < $lena; $i++) { 
         $answertext = $a[$i];
         $ball = $v[$i];
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
       }
       else
       if ($qtype == 'shortanswer')
       {
        for ($i = 0; $i < $lenkbd; $i++) { 
         $answertext = $kbd[$i];
         $ball = 1;
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
       }
      }
      mysqli_query($mysqli,"COMMIT");
      $lena = 0;
      $lenkbd =0; 
      $putq = 0;
      $allq = "";
      $a = array();
      $v = array();
      $kbd = array();
    }


    }
   
    $allq = $q;
    $ghead = $h;
    $putq = 1;
    // Запишем последний вопрос
    if ($putq==1 and $allq!="")
     {
      
      mysqli_query($mysqli,"START TRANSACTION;");
      $query = "INSERT INTO questions VALUES (0,
      '$ghead',
      '$allq',
      $qgid,
      '$qtype',
      '')";
  
      if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении вопроса ".$allq;
      }
      
      $questionid = mysql_insert_id($mysqli);

      // Запишем ответы в базу
      if ($questionid>0)
      {
       if ($qtype == 'multichoice')
       {
        for ($i = 0; $i < $lena; $i++) { 
         $answertext = $a[$i];
         $ball = $v[$i];
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
       }
       else
       if ($qtype == 'shortanswer')
       {
        for ($i = 0; $i < $lenkbd; $i++) { 
         $answertext = $kbd[$i];
         $ball = 1;
         $query = "INSERT INTO answers VALUES (0,
         '$answertext',
         $questionid,
         $ball)";
         if(!mysqli_query($mysqli,$query)) {
              $error=$error." Ошибка при добавлении ответа ".$answertext;
         }
        }
       }
      }
      mysqli_query($mysqli,"COMMIT");
    }
   
   
   return $error;  
  }
