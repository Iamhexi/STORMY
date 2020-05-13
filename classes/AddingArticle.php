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
    private $author;
    private $category;
    private $additionalCategory;
    
    private function prepareQuery(): string{
        $table = DatabaseControl::$contentTable;
        $p = (strlen($this->publicationDate) === 16) ? "'{$this->publicationDate}'," : '';
        $p2 = (strlen($this->publicationDate) === 16) ? "publicationDate," : '';
        
        return "INSERT INTO $table (title, photo, content, articleUrl, author, $p2 category, additionalCategory) VALUES ('{$this->title}', '{$this->photo}', '{$this->content}', '{$this->articleUrl}', '{$this->author}', $p '{$this->category}', '{$this->additionalCategory}')";
    }
    
    public function __construct(string $title, string $photo, string $content, string $articleUrl, string $author, string $category, string $additionalCategory = null, string $publicationDate = null){
        $this->title = $title;
        $this->photo = $photo;
        $this->content = $content;
        $this->articleUrl = $articleUrl;
        $this->author = $author;
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
    
    private static function provideEnterSupport(): void{
        echo<<<END
        <script>
        document.getElementById("content")
            .addEventListener("keyup", function(event) {
            event.preventDefault();
        if (event.keyCode === 13) {
            let x = document.getElementById("content");
            x.value += "</p><p>";
        }
    }); 
    </script>
END;
    }
    
    public static function renderForm(string $destination = "processor.php"){
        echo<<<END
        <form action="$destination" method="POST" class="addingForm" enctype="multipart/form-data">
            <header class="header">Dodawanie wpisu</header>
            <div><label>Tytuł <input type="text" class="addingInput" name="title" size="70" required></label></div>
            <div><label>Zdjęcie <input type="file" name="photo" class="addingInput"></label></div>
            <div><label>URL <input type="text" name="url" placeholder="przyjazny-link-123" class="addingInput" required></label></div>
            <div><label>Autor <input type="text" name="author" class="addingInput" required></label></div>
END;
            DatabaseControl::renderCategorySelector("", "category");
            DatabaseControl::renderCategorySelector("", "additionalCategory");
        echo<<<END
            <div><label>Data publikacji <input type="date" name="publicationDateOnly" class="addingInput"></label></div>
            <div><label>Godzina publikacji <input type="time" name="publicationTimeOnly" class="addingInput"></label></div>
            <div><label>Treść <textarea spellcheck="true" id="content" rows="4" cols="50" name="content" class="addingInput" required><p></textarea></label></div>    

            
            <div><input type="submit" value="Dodaj wpis!" name="addingArticle" class="addingSubmitButton"></div>
        </form>
END;
                self::provideEnterSupport();
    }
    
    
}