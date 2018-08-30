<?php
/**
 * Class Answer
 */
class Answer
{
    protected $id;
    protected $right; // Правильно или неправильно отвечен
    protected $exist; // Существует ли ответ
    protected $groupid;
    
    public function __construct($id, $groupid, $right, $exist)
    {
        $this->id = $id;
        $this->groupid = $groupid;
        $this->right = $right;
        $this->exist = $exist;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroupId()
    {
        return $this->groupid;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function getExist()
    {
        return $this->exist;
    }
}

