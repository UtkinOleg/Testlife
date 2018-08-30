<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) {  
// Устанавливаем соединение с базой данных

include "config.php";

$error = "";
$action = "";
$title=$titlepage="Новая настройка проекта";

include "topadmin.php";

// Возвращаем значение переменной action, переданной в урле
$action = $_POST["action"];
// Если оно не пусто - добавляем сообщение в базу данных
if (!empty($action)) 
{
  
  $paid = $_POST["paid"];

  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  // Проверяем правильность ввода информации в поля формы
  if (empty($_POST["name"])) 
  {
    $action = ""; 
    $error = $error."<LI>Вы не ввели наимнование.\n";
  }

  if (!empty($action)) 
  {
    $name = $_POST["name"];
    $oldname = $name;
    $content = $_POST["content"];
    $files = $_POST["files"];
    $youtube = $_POST["youtube"];
    $link = $_POST["link"];
    $filetype = $_POST["filetype"];
    $fileformat = $_POST["fileformat"];
    $typetext = $_POST["typetext"];
    $contentcnt = $_POST["contentcnt"];


    for ($i=0; $i<$contentcnt; $i++) {
     if ($contentcnt>1)
     {
      $s = " ".$i+1;
      $name = $oldname." ".$s;
     } 
    
    mysql_query("LOCK TABLES poptions WRITE");
    mysql_query("SET AUTOCOMMIT = 0");
    // Запрос к базе данных на добавление сообщения
    $query = "INSERT INTO poptions VALUES (0,
                                        '$name',
                                        '$content',
                                        '$files',
                                        $paid,
                                        '$typetext',
                                        '$youtube',
                                        '$filetype',
                                        '$fileformat','','$link'
                                        );";

    if(!mysql_query($query))
    {
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      // Выводим сообщение об ошибке в случае неудачи
      echo "<a href='poptions&paid=".$paid."'>Вернуться</a>";
      echo("<P> Ошибка при добавлении параметра</P>");
      echo("<P> $query</P>");
      exit();
    }
   }
   
      mysql_query("COMMIT");
      mysql_query("UNLOCK TABLES");
      // Возвращаемся на главную страницу если всё прошло удачно
      print "<HTML><HEAD>\n";
      print "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=poptions&paid=".$paid."'>\n";
      print "</HEAD></HTML>\n";
      exit();
    
  }  
  
}
}

if (empty($action)) 
{

  $paid = $_GET["paid"];
  $gst3 = mysql_query("SELECT ownerid FROM projectarray WHERE id='".$paid."'");
  if (!$gst3) puterror("Ошибка при обращении к базе данных");
  $projectarray = mysql_fetch_array($gst3);

   if ((defined("IN_SUPERVISOR") and $projectarray['ownerid'] == USER_ID) or defined("IN_ADMIN")) 
   {
  
?>
<form action=addpoption method=post>
<input type=hidden name=action value=post>
<input type=hidden name=paid value=<? echo $paid; ?>>
<p align='center'><table class=bodytable border="0" cellpadding=3 cellspacing=3 bordercolorlight=gray bordercolordark=white>
    <tr>
        <td><p class=ptd>Количество параметров</p></td>
        <td><select name='contentcnt'><option value='1' selected>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
        <option value='6'>6</option>
        <option value='7'>7</option>
        <option value='8'>8</option>
        <option value='9'>9</option>
        <option value='10'>10</option>
        <option value='11'>11</option>
        <option value='12'>12</option>
        </select>
    </tr><tr>
        <td witdh='400'><p class=ptd><b><em class=em>Наименование *</em></b></td>
        <td witdh='400'><input type=text name='name' size=35></td>
    </tr><tr>
        <td><p class=ptd>Содержит описание</p></td>
        <td><select name='content'><option value='yes'>Да</option><option value='no'>Нет</option></select>
    </tr><tr>
        <td><p class=ptd>Содержит прикрепленные файлы</p></td>
        <td><select name='files'><option value='yes'>Да</option><option value='no'>Нет</option></select>
    </tr><tr>
        <td><p class=ptd>Тип прикрепленных файлов</p></td>
        <td><select name='filetype'><option value='file'>Файл</option><option value='foto'>Картинка</option></select>
    </tr><tr>
        <td><p class=ptd>Формат загрузчика прикрепленных файлов</p></td>
        <td><select name='fileformat'><option value='simple'>Простой (один файл)</option><option value='ajax'>Составной на основе HTML5</option></select>
    </tr><tr>
        <td><p class=ptd>Содержит ID на видео YouTube</p></td>
        <td><select name='youtube'><option value='yes'>Да</option><option value='no'>Нет</option></select>
    </tr><tr>
        <td><p class=ptd>Содержит ссылку</p></td>
        <td><select name='link'><option value='yes'>Да</option><option value='no'>Нет</option></select>
    </tr><tr>
        <td><p class=ptd>Тип содержания</p></td>
        <td><select name='typetext'><option value='textarea'>Область</option><option value='text'>Строка</option></select>
    </tr><tr>
        <td colspan="3" witdh='400'>
            <input type="submit" value="Добавить">&nbsp;&nbsp;&nbsp;
            <input type="button" name="close" value="Назад" onclick="history.back()"> 
        </td>
    </tr>           
</table>
</form>

<?
  include "bottomadmin.php";
  // Выводим сообщение об ошибке
  if (!empty($error)) 
  {
    print "<P><font color=green>Во время добавления записи произошли следующие ошибки: </font></P>\n";
    print "<UL>\n";
    print $error;
    print "</UL>\n";
  }
}
}
} else die;
?>
