<?php

interface iTheme {
    function loadStylesheets(): void;
    function renderStyles(): void;
}

class Theme implements iTheme{
    
    protected $name;
    protected static $themeDirectory = 'themes';
    protected static $scriptSubdirectory = 'js';
    
    protected $adminStylesheetPath = '../themes/admin.css';
    protected $mainStylesheetPath;
    
    protected $mainStylesheet = 'main.css';
    
    protected $path;
    
    public function loadStylesheets(): void{
        $this->mainStylesheetPath = $this->path.'/'.Theme::$themeDirectory.'/'.$this->name.'/'.$this->mainStylesheet;
    }
    
    public function __construct(string $name, string $path = null){
        $this->path = (!is_null($path)) ? $path : ".";
        $this->name = $name;
        $this->loadStylesheets();
        
       
    }
    
    private function isThisAdminSite(): bool{
        if (strpos($_SERVER['REQUEST_URI'], 'admin') === false) return false;
        else return true;
    }
    
    public function renderStyles(): void{ //
        if ($this->isThisAdminSite())
            echo '<link rel="stylesheet" href="'.$this->adminStylesheetPath.'">';
        else
            echo '<link rel="stylesheet" href="'.$this->mainStylesheetPath.'">';
    }
    
}