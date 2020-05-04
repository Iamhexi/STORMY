<?php

require_once "DatabaseControl.php";
require_once "Page.php";

class CustomPage extends Page {
    use DatabaseControl;
    
    private $id;
    protected $title;
    private $url;
    private $content;
    
    public function setHTMLContent(string $html): void{
        $this->content = $html;
    }
    
    public function renderContent(): void{
        echo $this->content;
    }
    
    public function renderPage(): void{
        $this->renderHead();
        $this->renderMenu();
        $this->renderContent();
        $this->renderFooter();
    }
    
    public function __get(string $variable){
        return $this->$variable;
    }
       
    public function __set(string $variable, $value): void{
        $this->$variable = $value;
    }
    
}