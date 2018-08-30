<?php
/**
 * Class Knows
 */
 
class Knows
{
    protected $knows = array();
    protected $subknows = array();

    public function addKnow(Know $know)
    {
        $this->knows[] = $know;
    }
    
    public function getKnows($parentid) {
        
        $this->subknows = array();
        
        foreach($this->knows as $know) {
           if ($know->getParent() == $parentid)
           {
            $subknows[] = $know;
           }
         }
         return $subknows;
    }
    
    public function getCount() {
        return count($this->knows);
    }

}