<?php

require_once "DatabaseControl.php";

class AddingArticle {
    use DatabaseControl;
    
    private $title;
    public static $photoDirectory = "upload/photos/"; /// All uploaded photo will be store here
    private $photo;
    private $content;
    private $publicationDate;
    private $articleUrl;
    private $category;
    private $additionalCategory;
    
    private function prepareQuery(): string{
        $table = DatabaseControl::$contentTable;
        return "INSERT INTO $table (title, photo, content, articleUrl, publicationDate, category, additionalCategory) VALUES ('{$this->title}', '{$this->photo}', '{$this->content}', '{$this->articleUrl}', '{$this->publicationDate}', '{$this->category}', '{$this->additionalCategory}')";
    }
    
    public function __construct(string $title, string $photo, string $content, string $articleUrl, string $category, string $additionalCategory = null, string $publicationDate = null){
        $this->title = $title;
        $this->photo = $photo;
        $this->content = $content;
        $this->articleUrl = $articleUrl;
        $this->category = $category;
        $this->publicationDate = $publicationDate;
        
        $this->additionalCategory = ($additionalCategory === null) ? $category : $additionalCategory;
        $this->addArticle();
    }
    
    private function addArticle(){
        try {
            $query = $this->prepareQuery();
            if (@!$this->performQuery($query)) throw new Exception("Couldn't add an article to the database!");
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public static function renderForm(string $destination = "processor.php"){
        echo<<<END
        <form action="$destination" method="POST" class="addingForm" enctype="multipart/form-data">
            <div><label>Tytuł <input type="text" class="addingInput" name="title" size="70" required></label></div>
            <div><label>Zdjęcie <input type="file" name="photo" class="addingInput"></label></div>
            <div><label>URL <input type="text" name="url" placeholder="przyjazny-link-123" class="addingInput" required></label></div>
            <div><label>Kategoria <input type="text" name="category" class="addingInput" required></label></div>
            <div><label>Kategoria dodatkowa <input type="text" name="additionalCategory" class="addingInput"></label></div>
            <div><label>Data publikacji <input type="date" name="publicationDateOnly" class="addingInput"></label></div>
            <div><label>Godzina publikacji <input type="time" name="publicationTimeOnly" class="addingInput"></label></div>
            <div><label>Treść <textarea rows="4" cols="50" name="content" class="addingInput" required></textarea></label></div>    

            
            <div><input type="submit" value="Dodaj wpis!" name="addingArticle" class="addingSubmitButton"></div>
        </form>
END;
    }
    
    
}