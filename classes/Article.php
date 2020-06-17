<?php


class Article { 
    
    public int $id;
    public string $title;
    public string $content;
    public string $photo;
    public string $url;
    public string $author;
    public string $category;
    public ?string $publicationDate;
    public ?string $additionalCategory;
    
    
    public function __construct(
        int $id,
        string $title,
        string $content,
        string $photo,
        string $url,
        string $author,
        string $category,
        ?string $additionalCategory,
        ?string $publicationDate
    ){
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->photo = $photo;
        $this->url = $url;
        $this->author = $author;
        $this->category = $category;
        $this->additionalCategory = $additionalCategory;
        $this->publicationDate = $publicationDate;
    }
    
}