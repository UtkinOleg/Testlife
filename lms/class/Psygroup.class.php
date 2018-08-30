<?php
/**
 * Class PsyGroup
 * 
 * for adaptive test logic 
 */
 
class Psygroup
{
    protected $questions = array(); // Массив вопросов
    protected $id;
    protected $ball; 
    protected $name;
    protected $content;

    public function __construct($id, $name, $content)
    {
        $this->id = $id;
        $this->ball = 0;
        $this->name = $name;
        $this->content = $content;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBall()
    {
        return $this->ball;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function addQuestion(Psyquestion $psyquestion)
    {
        $this->questions[] = $psyquestion;
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
    

    public function setPsyAnswer($questid, $answerid, $rtball) {
      foreach($this->questions as $question) 
      {
        if ($question->getId()==$questid and $question->getAnswerId()==$answerid)
         $this->ball += $rtball;
      }
    }
    
}