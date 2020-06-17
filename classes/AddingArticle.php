<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();


class AddingArticle {
    use DatabaseControl;
    use UrlGenerator;
    
    private string $title;
    public static string $photoDirectory = "upload/photos/";  
    /// All uploaded photo will be store here

    private string $photo;
    private string $content;
    private ?string $publicationDate;
    private string $articleUrl;
    private string $author;
    private string $category;
    private ?string $additionalCategory;
    
    private function prepareQuery(): string{
        $table = DatabaseControl::$contentTable;
        $p = (strlen($this->publicationDate) === 16) ? "'{$this->publicationDate}'," : '';
        $p2 = (strlen($this->publicationDate) === 16) ? "publicationDate," : '';
        
        return "INSERT INTO $table (title, photo, content, articleUrl, author, $p2 category, additionalCategory) VALUES ('{$this->title}', '{$this->photo}', '{$this->content}', '{$this->articleUrl}', '{$this->author}', $p '{$this->category}', '{$this->additionalCategory}')";
    }
    
    public function __construct(string $title, string $photo, string $content, string $author, string $category, ?string $additionalCategory = null, ?string $publicationDate = null){
        
        $this->title = $this->sanitizeInput($title);
        $this->photo = $this->sanitizeInput($photo);
        $this->content = $content;
        $this->articleUrl = $this->generateUrlFromTitle($title);
        $this->author = $this->sanitizeInput($author);
        $this->category = $this->sanitizeInput($category);
        
        $this->publicationDate = ($publicationDate != null) ?
            $this->sanitizeInput($publicationDate) :
            null;
            
       $this->additionalCategory =
            ($additionalCategory === null) ?
            $this->category :
            $this->sanitizeInput($additionalCategory);
        
        $this->addArticle();

    }

    
    private function addArticle(){
        try {
            $query = $this->prepareQuery();
            if (@!$this->performQuery($query)) 
                throw new Exception("Couldn't add an article to the database!");
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public static function provideEditor(): void{
        echo<<<END
        <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
        <script>
            ClassicEditor
                .create( document.querySelector( '#content' ) )
                .catch( error => {
                    console.error( error );
                } );
        </script>
END;
    }
    
    public static function renderForm(string $destination = "processor.php"){
        echo<<<END
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        
        <form action="$destination" method="POST" class="addingForm" enctype="multipart/form-data">
        <header class="header">Dodawanie wpisu</header>
        <div><label><span>Tytuł</span><input type="text" class="addingInput" name="title" required></label></div>
        <div><label><span>Zdjęcie</span><i class="fas fa-upload photoUploaderIcon"></i><input id="photoUploaderInput" type="file" name="photo" class="addingInput"></label></div>
        <div><label><span>Autor</span><input type="text" name="author" class="addingInput" required></label></div>

END;
            DatabaseControl::renderCategorySelector("", "category");
            DatabaseControl::renderCategorySelector("", "additionalCategory");
        echo<<<END
            <div><label><span>Data publikacji</span><input type="date" name="publicationDateOnly" class="addingInput"></label></div>
            <div><label><span>Godzina publikacji</span><input type="time" name="publicationTimeOnly" class="addingInput"></label></div>
            <div><label><span>Treść</span><textarea spellcheck="true" id="content" name="content" class="addingInput" required><p></textarea></label></div>    


            <div><input type="submit" value="Dodaj wpis" name="addingArticle" class="addingSubmitButton"></div>
            </form>
            <script>
            document.querySelector('#photoUploaderInput').addEventListener('input', function () {
               let fileName = this.files[0].name;
               console.log(this.files);
               if (fileName !== null || undefined) {
                  const inputFileLabel = document.querySelector('.photoUploaderIcon');
                  inputFileLabel.style.fontSize = '0.8rem';
                  fileName = fileName.slice(0, 35);
                  inputFileLabel.textContent = '  Wybrano: ' + fileName;
                  
               }
            }, true);
            </script>
END;
        self::provideEditor();
    }
    
    
}