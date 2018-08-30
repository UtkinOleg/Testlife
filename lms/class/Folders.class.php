<?php
/**
 * Class Folders
 */
 
class Folders
{
    protected $folders = array();
    protected $subfolders = array();

    public function addFolder(Folder $folder)
    {
        $this->folders[] = $folder;
    }
    
    public function getFolders($parentid) {
        
        $this->subfolders = array();
        
        foreach($this->folders as $folder) {
           if ($folder->getParent() == $parentid)
           {
            $subfolders[] = $folder;
           }
         }
         return $subfolders;
    }

   
    public function getCount() {
        return count($this->folders);
    }

    public function getAll() {
        return $this->folders;
    }

}