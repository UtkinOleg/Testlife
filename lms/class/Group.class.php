<?php
/**
 * Class Group
 * 
 * for adaptive test logic 
 */
 
class Group
{
    protected $questions = array(); // Массив вопросов
    protected $answers = array();   // Массив ответов 
    protected $id;
    protected $ball; // Сложность вопроса - 1 параметр сложности
    protected $time; // Время ответа - 2 параметр сложности
    protected $name;
    protected $comment;
    protected $knowname;

    public function __construct($id, $ball, $time, $name, $comment, $knowname)
    {
        $this->id = $id;
        $this->ball = $ball;
        $this->time = $time;
        $this->name = $name;
        $this->comment = $comment;
        $this->knowname = $knowname;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBalls()
    {
        $balls = count($this->questions) * $this->ball;
        return $balls;
    }

    public function getBall()
    {
        return $this->ball;
    }

    public function getTimes()
    {
        $times = count($this->questions) * $this->ball;
        return $times;
    }

    public function getDifficultys()
    {
        $bt = $this->time * $this->ball;
        return $bt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getKnowname()
    {
        return $this->knowname;
    }

    public function addQuestion(Question $question)
    {
        $this->questions[] = $question;
        shuffle($this->questions);
    }
    
    public function getQuestion($id) {
        
        $q = null;
        
        foreach($this->questions as $question) {
           if ($question->getId() == $id)
           {
            $q = $question;
            break;
           } 
         }
         return $q;
    }

    public function getFirstQuestion() {
        return $this->questions[0];
    }
    
    public function getCount() {
        return count($this->questions);
    }
    
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
    }

    // Ищет первый попавшийся незаданный вопрос
    public function getFindQuestion() {
      
      $q = null;
        
      foreach($this->questions as $question) {
        $found = false;
        foreach($this->answers as $answer) {
           if ($answer->getId() == $question->getId())
           {
            $found = true;
            break;
           } 
        }
        if (!$found) 
        {
         $q = $question;
         break;
        }
      }
      return $q;
    }
    
    public function getAnswerByQuestionId($id) {
        
        $a = null;
        foreach($this->answers as $answer) {
           if ($answer->getQuestionId() == $id)
           {
            $a = $answer;
            break;
           } 
         }
         return $a;
    }
    
    // Количество правильных ответов
    public function getRightAnswers() {
        $cnt = 0;
        foreach($this->answers as $answer) {
          if ($answer->getRight() and $answer->getExist())
            $cnt++;
        }
        return $cnt;
    }

    // Количество двух последних непрерывных правильных ответов 
    public function getTwoLastRightAnswers() {
        $cnt = count($this->answers);
        if ($cnt >= 2)
        {
          if ($this->answers[$cnt-1]->getRight() and $this->answers[$cnt-1]->getExist() and
          $this->answers[$cnt-2]->getRight() and $this->answers[$cnt-2]->getExist())
           return true;
          else
           return false;
        }
        else
         return false;
    }

    // Количество неправильных ответов
    public function getNonRightAnswers() {
        $cnt = 0;
        foreach($this->answers as $answer) {
          if (!$answer->getRight() and $answer->getExist())
            $cnt++;
        }
        return $cnt;
    }

    // Количество последних непрерывных неправильных ответов 
    public function getTwoLastNonRightAnswers() {
        $cnt = count($this->answers);
        if ($cnt >= 2)
        {
          if (!$this->answers[$cnt-1]->getRight() and $this->answers[$cnt-1]->getExist() and
          !$this->answers[$cnt-2]->getRight() and $this->answers[$cnt-2]->getExist())
           return true;
          else
           return false;
        }
        else
         return false;
    }

    // Количество существующих ответов
    public function getExistAnswers() {
        $cnt = 0;
        foreach($this->answers as $answer) {
          if ($answer->getExist())
            $cnt++;
        }
        return $cnt;
    }

    public function getAnswerCount() {
        return count($this->answers);
    }
    
}