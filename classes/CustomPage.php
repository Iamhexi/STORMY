<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iCustomPage {
    public function setHTMLContent(string $html): void;
    public function renderContent(): void;
    public function renderPage(): void;
    public function __get(string $variable);
    public function __set(string $variable, $value): void;
}

class CustomPage extends Page {
    use DatabaseControl;
    
    private int $id;
    protected string $title;
    private string $url;
    private string $content;
    
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