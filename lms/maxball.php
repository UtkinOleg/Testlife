<?php

spl_autoload_register(function ($class) {
     include 'class/'.$class . '.class.php';
});


       function maxBall($mysqli, $testid, $sign, $userid)
       {
           
           $maxBall = 0;
           $test = new Test($mysqli, $testid, $sign, $userid);

           if (!empty($test))
           {
            $group = $test->getSuperAverageGroup();
            $maxBall = $group->getBall() * 2;
      
            while ($test->GetHighLevelGroup($group) != $test->getMaximumGroup())
            {
             $group = $test->GetHighLevelGroup($group);
             $maxBall += $group->getBall() * 2;
            }

            $group = $test->getMaximumGroup();
            $maxBall += $group->getBall() * 7;
           }
           
           return $maxBall;
       }

?>