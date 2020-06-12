<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iEditingArticle {
    function renderEditor(string $destination = "processor.php"): void;
    function saveChanges(string $title = null, string $content, string $category = null, string $additionalCategory = null, string $publicationDate = null): bool;
}

class EditingArticle extends Article implements iEditingArticle {
    use DatabaseControl;
    
    public function renderEditor(string $destination = "processor.php"): void{
        $photoDir = '../'.AddingArticle::$photoDirectory;
        echo<<<END
            <form class="articleEditor" action="$destination?url={$this->articleUrl}" method="POST">
                <div><label><span>Tytuł</span><input class="articleEditorInput" type="" value="{$this->title}" name="title" required></label></div>
                <div><img class="articleEditorPhoto" src="$photoDir$this->photo" alt="Zdjęcie do artykułu pt. {$this->title}"></div>
                <div><label><span>Treść</span><textarea rows="4" cols="50" name="content" value="{$this->content}" id="content" class="articleEditorTextarea" required>{$this->content}</textarea></label></div>
END;
        DatabaseControl::renderCategorySelector($this->category, "category");
        DatabaseControl::renderCategorySelector($this->additionalCategory, "additionalCategory");
        echo<<<END
                <div><label><span>Data publikacji</span><input type="text" value="{$this->publicationDate}" name="publicationDate" class="articleEditorInput" required></label></div>
                <div><input type="submit" value="Zapisz zmiany" name="savingArticle" class="button" required></div>
            </form>
END;
        AddingArticle::provideEditor();
    }
    
    public function saveChanges(string $title = null, string $content, string $category = null, string $additionalCategory = null, string $publicationDate = null): bool{
        $table = Article::$contentTable;
    
        if (!is_null($title)) $this->title = $title;
        if (!is_null($content)) $this->content = $content;
        if (!is_null($category)) $this->category = $category;
        if (!is_null($additionalCategory)) $this->additionalCategory = $additionalCategory;
        if (!is_null($publicationDate)) $this->publicationDate = $publicationDate;
        
        $query = "UPDATE $table SET title = '$this->title', content = '$this->content', publicationDate = '$this->publicationDate', category='$this->category', additionalCategory = '$this->additionalCategory' WHERE articleUrl = '$this->articleUrl'";
        
        if (!($this->performQuery($query))) return false;
        return true;
    }
    
}