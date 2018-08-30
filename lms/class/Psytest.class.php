<?php
/**
 * Class PsyTest
 * 
 * for psy test logic 
 *  
 */
  
class Psytest
{
    protected $groups = array();
    protected $signature;
    protected $token;
    protected $id;
    protected $name;

    public function __construct($mysqli, $id, $signature, $userid)
    {
       if (empty($signature) and !empty($id))
        $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE id = $id LIMIT 1");
       else
       if (!empty($signature) and empty($id))
        $sql = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testgroups WHERE signature = '".$signature."' LIMIT 1");

       if ($sql != false) 
       {
        $test = mysqli_fetch_array($sql);
        $id1 = $test['id'];
        $signature1 = $test["signature"];  // Уникальный ID
        $token1 = md5($signature1.$id.$userid);  // Уникальная сигнатура теста пользователя

        $this->id = $id1;
        $this->signature = $signature1;
        $this->token = $token1;
        $this->name = $test['name'];
        mysqli_free_result($sql);
        
        // Сформируем Тест
        $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM psymode WHERE testid='".$this->id."' ORDER BY id;");
        if ($td != false)
        { 
         while($testdata = mysqli_fetch_array($td))
         {
            $group = new Psygroup($testdata['id'],
                            $testdata['name'],
                            $testdata['content']);
            $this->addGroup($group);

            // Сгененритуем массив идентификации ответов
            $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM psyquestions WHERE psyid='".$testdata['id']."' ORDER BY id;");

            while($quest = mysqli_fetch_array($qq))
             if ($quest['selected']==true)
              $group->addQuestion(new Psyquestion($quest['questionid'], 
                            1, 
                            $testdata['id'], 
                            $quest['answerid']));

            mysqli_free_result($qq);
         } 
         mysqli_free_result($td);
        }
      }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function addGroup(Psygroup $group)
    {
        $this->groups[] = $group;
    }
    
    public function getGroups() {
         return $this->groups;
    }
    
    public function getCount() {
        return count($this->groups);
    }
    
    public function getGroup($id) {
        foreach($this->groups as $group) {
           if ($group->getId() == $id)
           {
            $g = $group;
            return $g;
           } 
         }
    }

    public function setPsyAnswer($questid, $answerid, $rtball) {
        foreach($this->groups as $group) {
          $group->setPsyAnswer($questid, $answerid, $rtball);
        }
    }
    
}