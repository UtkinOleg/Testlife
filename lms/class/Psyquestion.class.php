<?php
/**
 * Class Psyquestion
 */
class Psyquestion
{
    protected $id;
    protected $ball; 
    protected $psyid;
    protected $answerid;
    
    public function __construct($id, $ball, $psyid, $answerid)
    {
        $this->id = $id;
        $this->ball = $ball;
        $this->psyid = $psyid;
        $this->answerid = $answerid;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBall()
    {
        return $this->ball;
    }

    public function getPsyId()
    {
        return $this->psyid;
    }

    public function getAnswerId()
    {
        return $this->answerid;
    }
}

