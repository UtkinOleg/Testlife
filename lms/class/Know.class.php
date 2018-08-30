<?php
/**
 * Class Know
 */
class Know
{
    protected $id;
    protected $name;
    protected $content;
    protected $parentid;
    protected $userid;
    
    public function __construct($id, $name, $content, $parentid, $userid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
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

    public function getContent()
    {
        return $this->content;
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

