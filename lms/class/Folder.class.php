<?php
/**
 * Class Folder
 */

class Folder
{
    protected $id;
    protected $name;
    protected $parentid;
    protected $userid;
    
    public function __construct($id, $name, $parentid, $userid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentid = $parentid;
        $this->userid = $userid;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parentid;
    }

    public function getUser()
    {
        return $this->userid;
    }
}

