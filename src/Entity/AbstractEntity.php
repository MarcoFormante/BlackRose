<?php

namespace App\Entity;

class AbstractEntity{
    private ?array $files = null;

  
    public function getFiles()
    {
        return $this->files;
    }

    
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }
}