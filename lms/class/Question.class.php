<?php
/**
 * Class Question
 */
class Question
{
    protected $id;
    protected $ball; // Сложность вопроса - 1 параметр сложности
    protected $time; // Время ответа - 2 параметр сложности
    protected $groupid;
    
    public function __construct($id, $ball, $time, $groupid)
    {
        $this->id = $id;
        $this->ball = $ball;
        $this->time = $time;
        $this->groupid = $groupid;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBall()
    {
        return $this->ball;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getDifficulty()
    {
        return $this->time * $this->ball;
    }

    public function getGroupId()
    {
        return $this->groupid;
    }
}

