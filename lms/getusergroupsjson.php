<?php
if(defined("IN_ADMIN") or defined("IN_SUPERVISOR")) 
{  
  require_once "config.php";

  spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
  });
  
  function GetChildFoldersCnt(Folders $ks, $folderid)
  {
    return count($ks->getFolders($folderid));
  }

  function GetChildFolders(Folders $ks, $folderid, &$parentid, &$folderscnt)
  {
   $ss = '';

   if ($folderid>0)
   {
     foreach($ks->getAll() as $tmpfolder) 
     {
      if ($tmpfolder->getId()==$folderid)
      {
       $parentid = $tmpfolder->getParent();
       $ss .= '<tr>';
       $ss .= "<td></td><td width='300'>
       <a href='javascript:;' onclick='getusergroups(".$parentid.")'><i class='fa fa-folder fa-fw'></i>...</a>
       </td><td></td><td></td>";
       $ss .= '</tr>';
       break;
      }
     }
   }
   
   $folderscnt = GetChildFoldersCnt($ks, $folderid);
   
   if ($folderscnt>0)
   {
    foreach($ks->getFolders($folderid) as $tmpfolder) 
     $ss .= "<tr><td></td><td width='300'>
     <a href='javascript:;' onclick='getusergroups(".$tmpfolder->getId().")'><i class='fa fa-folder fa-fw'></i> ".$tmpfolder->getName()."</a>
     </td><td></td><td></td></tr>";
   }
   return $ss;
  }


  function data_convert($data, $year, $time, $second){
   $res = "";
   $part = explode(" " , $data);
   $ymd = explode ("-", $part[0]);
   $hms = explode (":", $part[1]);
   if ($year == 1) {$res .= $ymd[2]; $res .= ".".$ymd[1]; $res .= ".".$ymd[0];}
   if ($time == 1) {$res .= " ".$hms[0]; $res .= ":".$hms[1]; if ($second == 1) $res .= ":".$hms[2];}
   return $res;
  }

  $folderid = $_POST["parentid"];
  
  // Инициализация папок
  if (defined("IN_ADMIN"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders ORDER BY id;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM folders WHERE userid='".USER_ID."' ORDER BY id;");

  $folders = new Folders();
  
  while($member = mysqli_fetch_array($sql))
  { 
    $folders->addFolder(new Folder($member['id'], 
                            $member['name'], 
                            $member['parentid'], 
                            $member['userid']));
  }
  mysqli_free_result($sql);
  
  $s = "<p><button type='button' class='btn btn-outline btn-primary btn-sm' onclick='dialogOpen(\"edusergroup&m=a&p=".$folderid."\",700,500)'><i class='fa fa-users fa-fw'></i> Новая группа</button>
       &nbsp;<button type='button' class='btn btn-outline btn-primary btn-sm' onclick='dialogOpen(\"eduserfolder&m=a&p=".$folderid."\",500,200)'><i class='fa fa-folder-open fa-fw'></i> Новая папка</button>
       </p>  
    <div class='table-responsive'>
          <table class='table'>
          <thead>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='30'></td>
              <td align='center' witdh='300'></td>
              <td align='center' witdh='50'><i title='Количество участников' class='fa fa-users'></i></td>
              <td align='center' witdh='50'><i title='Количество участников прошедших тестирование' class='fa fa-users'></i></td>
              <td align='center' witdh='100'><i title='Дата создания группы' class='fa fa-calendar'></i></td>
          </thead>
          <tbody>";

  $parentid = 0;
  $folderscnt = 0;
  $s .= GetChildFolders($folders, $folderid, $parentid, $folderscnt);

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$folderid."' ORDER BY id DESC;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM usergroups WHERE folderid='".$folderid."' AND userid='".USER_ID."' ORDER BY id DESC;");

  $i=0;
  $itog1 = 0;
  $itog2 = 0;
  while($member = mysqli_fetch_array($sql))
  {

    $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails as e, users as u, singleresult as s WHERE s.userid=u.id AND u.email=e.email AND e.usergroupid='".$member['id']."' LIMIT 1;");
    $cntusers = mysqli_fetch_array($countu);
    $count_res2 = $cntusers['count(*)'];
    mysqli_free_result($countu); 
 
    $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM useremails as e WHERE e.usergroupid='".$member['id']."' LIMIT 1;");
    $cntusers = mysqli_fetch_array($countu);
    $count_users = $cntusers['count(*)'];
    mysqli_free_result($countu); 
    
    $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM usergrp WHERE usergroupid='".$member['id']."' LIMIT 1;");
    $cntusers = mysqli_fetch_array($countu);
    $count_res = $cntusers['count(*)'];
    mysqli_free_result($countu); 

    $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM knowledge WHERE usergroupid='".$member['id']."' LIMIT 1;");
    $cntusers = mysqli_fetch_array($countu);
    $count_res += $cntusers['count(*)'];
    mysqli_free_result($countu); 
    
    $s.= "<tr><td witdh='30'><p>".++$i."</p></td>"; 

    if ($member['usergrouptype']==0)
     $s.= "<td witdh='30'><i title='Участники тестирования' class='fa fa-users'></i></td>"; 
    else
    if ($member['usergrouptype']==1)
     $s.= "<td witdh='30'><i title='Эксперты проверки КИМ' class='fa fa-pencil'></i></td>"; 
    else
    if ($member['usergrouptype']==2)
     $s.= "<td witdh='30'><i title='Эксперты выполнения ФГОС' class='fa fa-pencil-square'></i></td>"; 
    
    $s.= "<td width='300'>
    <p>" .$member['name']. " <a onclick='dialogOpen(\"edusergroup&p=".$folderid."&m=e&id=".$member['id']."\",700,500)' href='javascript:;' title='Редактировать группу'><i class='fa fa-cog fa-fw'></i></a>
    &nbsp;<a onclick='dialogOpen(\"moveusergroup&p=".$folderid."&id=".$member['id']."\",700,300)' href='javascript:;' title='Перенести группу в другую папку'><i class='fa fa-folder-o fa-fw'></i></a>";
    
    if ($count_res==0 and $count_res2==0) {
      $s.= '&nbsp;<a href="javascript:;" onclick="$(\'#DelUserGrouphiddenInfoParent\').val('.$folderid.');$(\'#DelUserGrouphiddenInfoId\').val('.$member['id'].');$(\'#DelUserGroup\').modal(\'show\');" title="Удалить группу"><i class="fa fa-trash fa-fw"></i></a>';
    }
    
    $s.= '</p></td>';
    $s.=  "<td align='center'><p>".$count_users."</p></td>";
    $itog1 += $count_users;

    $count_users_exist = 0;
    $sql2 = mysqli_query($mysqli,"SELECT * FROM useremails WHERE usergroupid='".$member['id']."' ORDER BY id;");
    while($email = mysqli_fetch_array($sql2))
    {
     $countu = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM users as u, singleresult as s WHERE s.userid=u.id AND u.email='".$email['email']."' LIMIT 1;");
     $cntusers = mysqli_fetch_array($countu);
     $count_exist_res = $cntusers['count(*)'];
     if ($count_exist_res>0) $count_users_exist++;
     mysqli_free_result($countu); 
    }
    mysqli_free_result($sql2); 
    $s.=  "<td align='center'><p>".$count_users_exist."</p></td>";
    $itog2 += $count_users_exist;
    
    $s.=  "<td align='center'><p>".data_convert ($member['regdate'], 1, 0, 0)."</p></td>";
    $s.= "</tr>";
  }
  mysqli_free_result($sql);

  $s.= "<tr>";
  $s.=  "<td></td>";
  $s.=  "<td></td>";
  $s.=  "<td></td>";
  $s.=  "<td align='center'><p>".$itog1."</p></td>";
  $s.=  "<td align='center'><p>".$itog2."</p></td>";
  $s.=  "<td></td>";
  $s.= "</tr>";

  $s.= "</tbody></table>";

  $countg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM folders WHERE id='".$folderid."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($countg);
  $fname = $cntgs['name'];
  mysqli_free_result($countg); 
  $json['fname'] = $fname;  

  if (defined("IN_ADMIN")) 
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM usergroups WHERE folderid='".$folderid."' LIMIT 1;");
  else
  if (defined("IN_SUPERVISOR"))
   $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT count(*) FROM usergroups WHERE folderid='".$folderid."' AND userid='".USER_ID."' LIMIT 1;");
  $cntgs = mysqli_fetch_array($sql);
  $cntgroups = $cntgs['count(*)'];
  mysqli_free_result($sql); 

  $json['del'] = $folderscnt + $cntgroups;
  $json['folderparent'] = $parentid;

  $json['content'] = $s;  
  $json['ok'] = '1';  
  
} else 
   $json['ok'] = '0'; 
echo json_encode($json);
?>
