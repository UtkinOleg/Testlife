<?php
/**
 * Class Test
 * 
 * for adaptive logic 
 * 
 * v.1.0.2 15.02.15 add var content 
 *  
 */
  
class Test
{
    protected $groups = array();
    protected $signature;
    protected $token;
    protected $id;
    protected $name;
    protected $attempt;
    protected $type;
    protected $maxid;
    protected $minid;
    protected $kind;
    protected $external;
    protected $viewcnt;
    protected $content;

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
        $this->attempt = $test['attempt'];
        $this->name = $test['name'];
        $this->type = $test['testtype'];
        $this->kind = $test['testkind'];
        $this->content = $test['content'];
        $this->external = $test['external'];
        $this->viewcnt = $test['viewcnt'];
        mysqli_free_result($sql);
        
        // Сформируем Тест
        $td = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM testdata WHERE testid='".$this->id."' ORDER BY id");
        if ($td != false)
        { 
        while($testdata = mysqli_fetch_array($td))
         {
          if ($testdata['qcount']>0 and $testdata['random'])
          {
            $qg = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT * FROM questgroups WHERE id='".$testdata['groupid']."' LIMIT 1;");
            $questgroup = mysqli_fetch_array($qg);

            $know = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT name FROM knowledge WHERE id='".$questgroup['knowsid']."' LIMIT 1");
            $knowdata = mysqli_fetch_array($know);
            mysqli_free_result($know);

            $group = new Group($testdata['groupid'],
                            $questgroup['singleball'], 
                            $questgroup['singletime'],
                            $questgroup['name'],
                            $questgroup['comment'],
                            $knowdata['name']);
            $this->addGroup($group);

            // Сгененритуем группу вопросов 
            $qq = mysqli_query($mysqli,"/*" . MYSQLND_QC_ENABLE_SWITCH . "*/" . "SELECT id FROM questions WHERE qgroupid='".$testdata['groupid']."' ORDER BY id;");

            while($quest = mysqli_fetch_array($qq))
             $group->addQuestion(new Question($quest['id'], 
                            $questgroup['singleball'], 
                            $questgroup['singletime'], 
                            $questgroup['id']));

            mysqli_free_result($qq);
            mysqli_free_result($qg);
          } 
         }
        mysqli_free_result($td);
        }
      }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMaxId()
    {
        return $this->maxid;
    }

    public function getMinId()
    {
        return $this->minid;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getKind()
    {
        return $this->kind;
    }

    public function getExternal()
    {
        return $this->external;
    }

    public function getViewcnt()
    {
        return $this->viewcnt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAttempt()
    {
        return $this->attempt;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function addGroup(Group $group)
    {
        $this->groups[] = $group;
        $this->maxid = $group->getId();
        $this->minid = $group->getId();
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

    public function getLowLevelGroup(Group $basicgroup) {
        $g = array();
        $mind = array();
        //Найдем сначала группы с нижним уровнем
        foreach($this->groups as $group) {
           if ($group->getDifficultys() < $basicgroup->getDifficultys())
           {
             $g[] = $group;
             $mind[] = $basicgroup->getDifficultys() - $group->getDifficultys(); 
           } 
         }
        // Из полученного списка найдем с минимальной разницей 
        $kmin = 1000000;
        $ii = 0;
        for($i = 0, $size = count($mind); $i < $size; ++$i) {
         if ($mind[$i]<$kmin)
         {
          $ii = $i;
          $kmin = $mind[$i];
         } 
        }
        if (count($g)==0)
         return $basicgroup;
        else
         return $g[$ii];
    }

    public function getHighLevelGroup(Group $basicgroup) {
        $g = array();
        $mind = array();
        //Найдем сначала группы с нижним уровнем
        foreach($this->groups as $group) {
           if ($group->getDifficultys() > $basicgroup->getDifficultys())
           {
            $g[] = $group;
            $mind[] = $group->getDifficultys() - $basicgroup->getDifficultys(); 
           } 
         }
        // Из полученного списка найдем с минимальной разницей 
        $kmin = 1000000;
        $ii = 0;
        for($i = 0, $size = count($mind); $i < $size; ++$i) {
         if ($mind[$i]<$kmin)
         {
          $ii = $i;
          $kmin = $mind[$i];
         } 
        }
        if (count($g)==0)
         return $basicgroup;
        else
         return $g[$ii];
    }
    
    // Возвращает начальную группу средней сложности из условий range нижней и верхней групп (начальная точка адаптивного графа)
    
    public function getAverageGroup(Group $minimum, Group $maximum) {
        $gmax = $this->getLowLevelGroup($maximum);
        $gmin = $this->getHighLevelGroup($minimum);
        $gmino = $gmin;
        if ($gmax === $gmin) // Три группы
         return $gmin;
        else // Больше трех групп с неравномерной сложностью
        {
         while ($gmax->getDifficultys() > $gmin->getDifficultys())
         {
           $gmax = $this->getLowLevelGroup($gmax);
           $gmin = $this->getHighLevelGroup($gmin);
           if ($gmax === $gmin) // середина
           {
            $g = $gmin;
            break;
           }
           else
           if ($gmax->getDifficultys() === $gmin->getDifficultys()) // середина
           {
            $g = $gmin;
            break;
           }
           else
           if ($gmax->getDifficultys() < $gmin->getDifficultys())
           {
            $g = $gmin;
            break;
           }
         }
         if ($g==null)
          return $gmino;
         else
          return $g;
        }
    }

    public function getMaximumGroup()
    {
      $maximum = 0;
      $maximumg = $this->getGroup($this->maxid);
      
      foreach($this->groups as $group) 
      {
        if ($group->getDifficultys() > $maximum)
        {
         $maximum = $group->getDifficultys();
         $maximumg = $group;
        }
      }
      return $maximumg;
    }

    public function getMinimumGroup()
    {
      $maximum = 1000000;
      $maximumg = $this->getGroup($this->maxid);

      foreach($this->groups as $group) 
      {
        if ($group->getDifficultys() < $maximum)
        {
         $maximum = $group->getDifficultys();
         $maximumg = $group;
        }
      }
      return $maximumg;
    }
    
    // Возвращает начальную группу средней сложности (начальная точка адаптивного графа)
    
    public function getSuperAverageGroup()
    {
      $minimum = 1000000; 
      $maximum = 0;
      $minimumId = 0; 
      $maximumId = 0;
      
      foreach($this->groups as $group) 
      {
        if ($group->getDifficultys() < $minimum)
        {
         $minimum = $group->getDifficultys();
         $minimumId = $group->getId();
        }
        if ($group->getDifficultys() > $maximum)
        {
         $maximum = $group->getDifficultys();
         $maximumId = $group->getId();
        }
      }
      
      if ($maximumId === $minimumId)
      // Группы имеют одинаковую сложность - вернем любую группу
      {
        $avergroup = $this->getGroup($maximumId);
      }
      else
      // 1.1 Ищем группу
      {
        $aver = (int)floor(($maximum + $minimum) / 2);
        $averId = 0;
        foreach($this->groups as $group) 
        {
         if ($group->getDifficultys() === $aver)
         {
          $averId = $group->getId();
          break;
         }
        }
        // Возвратим вопрос
        if ($averId>0)
          $avergroup = $this->getGroup($averId);
        else // Группа не найдена - тогда неравномерное распределение сложности
        {
          if ($minimum == $maximum) // Две группы
           $avergroup = $this->getGroup($maximumId);
          else // Больше двух ?
           $avergroup = $this->getAverageGroup($this->getGroup($minimumId), $this->getGroup($maximumId));
        }
      }
      return $avergroup;
    }
    
/*    public function maxBall()
    {
      $maxball = 0;

      $ming = $this->getMinimumGroup();
      $maxg = $this->getMaximumGroup();
      
      if ($maxg === $ming)
      {
        $maxball = $maxg->getBall() * 7;
      }
      else
      {
        $aver = (int)floor(($maxg->getDifficultys() + $ming->getDifficultys()) / 2);
        $averId = 0;
        foreach($this->groups as $group) 
        {
         if ($group->getDifficultys() === $aver)
         {
          $averId = $group->getId();
          break;
         }
        }
        
        if ($averId>0)
        {
          $avergroup = $this->getGroup($averId);
          $maxball = $avergroup->getBall()*2;
          while ($this->GetHighLevelGroup($avergroup) != $maxg)
          {
           $avergroup = $this->GetHighLevelGroup($avergroup);
           $maxball += $avergroup->getBall() * 2;
          }
          $maxball += $maxg->getBall() * 7;
        }
        else // Группа не найдена - тогда неравномерное распределение сложности
        {
          if ($ming === $maxg) // Две группы
          {
           $avergroup = $maxg;
           $maxball = $avergroup->getBall()*2;
           while ($this->GetHighLevelGroup($avergroup) != $maxg)
           {
            $avergroup = $this->GetHighLevelGroup($avergroup);
            $maxball += $avergroup->getBall() * 2;
           }
           $maxball += $maxg->getBall() * 7;
          
          }
          else // Больше двух ?
          {
           $avergroup = $this->getAverageGroup($test->getGroup($minimumId), $test->getGroup($maximumId));
           $maxball = $avergroup->getBall()*2;
           while ($this->GetHighLevelGroup($avergroup) != $maxg)
           {
            $avergroup = $this->GetHighLevelGroup($avergroup);
            $maxball += $avergroup->getBall() * 2;
           }
           $maxball += $maxg->getBall() * 7;
          }
        }
      }
      return $maxball;
     }     */
    
    
}