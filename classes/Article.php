<?php

require_once "DatabaseControl.php";

class Article extends DatabaseControl{ 
    
    private $id;
    private $title;
    private $content;
    private $imageLink;
    private $friendlyUrl;
    private $additionDate;
    private $category;
    private $additionalCategory;
    
    
    private function sanitizeInput(string $input): string{
        return filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    
    public function setUrl(string $url): void{
        $this->friendlyUrl = $this->sanitizeInput($url);
    }
    
    public function __construct(string $friendlyUrl){
        $this->setUrl($friendlyUrl);
    }
    
    public function loadByUrl(): bool{
        if (empty($this->friendlyUrl)) return false;
        if (!$this->loadFromDBUsingUrl()) return false;
        return true;
        
    }
    
    private function loadFromDBUsingUrl(): bool{
        $table = Article::$contentTable;
        
        $query = "SELECT * FROM $table WHERE friendlyUrl = '$this->friendlyUrl'";
        if (!($data = $this->performQuery($query, true))) return false;
        
        $this->id = $data['news_id'];
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->imageLink = $data['photo'];
        $this->additionDate = $data['additionDate'];
        $this->category = $data['category'];
        $this->additionalCategory = $data['additionalCategory'];
        
        return true;
    }
    
    public function renderArticle(){
        echo '<article class="article"><h1 class="articleTitle">'.$this->title.'</h1>';
        echo '<img src="upload/'.$this->imageLink.'" class="mainArticleImage" alt="Zdjęcie dla artykuły pt. '.$this->title.'">';
        echo '<div class="articleText">'.$this->content.'</div>';
        echo '</article>';
    }
    
    
}