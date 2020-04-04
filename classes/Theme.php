<?php

class Theme {
    
    protected $name;
    protected static $themeDirectory = 'themes';
    protected static $scriptSubdirectory = 'js';
    protected $main = 'main.css';
    protected $admin = 'admin.css';
    
    protected $mainStylesheet;
    protected $adminStylesheet;
    
    protected $path;
    
    public function loadStylesheets(){
        $this->mainStylesheet = $this->path.'/'.Theme::$themeDirectory.'/'.$this->name.'/'.$this->main;
        $this->adminStylesheet = $this->path.'/'.Theme::$themeDirectory.'/'.$this->name.'/'.$this->admin;
    }
    
    public function __construct(string $name, string $path = null){
        $this->path = (!is_null($path)) ? $path : ".";
        $this->name = $name;
        $this->loadStylesheets();
        
       
    }
    
    public function renderStyles(){ //
         echo '<link rel="stylesheet" href="'.$this->mainStylesheet.'">';
         echo '<link rel="stylesheet" href="'.$this->adminStylesheet.'">';
    }
    
}