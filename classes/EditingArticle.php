<?php

require_once "Categories.php";
require_once "DatabaseControl.php";
require_once "Article.php";

class EditingArticle extends Article{
    use DatabaseControl;
    
    private function renderCategorySelector(string $defaultValue, string $htmlAttributeName){
        $categories = new Categories();
        $categoryList = $categories->getCategoriesArray();
        
        echo '<div><label>Kategoria <select name="'.$htmlAttributeName.'" class="articleEditorInput" required>';
        
        foreach($categoryList as $c){
            if ($c['categoryTitle'] == $defaultValue) echo "<option selected value=\"{$c['categoryTitle']}\">{$c['categoryTitle']}</option>";
            else echo "<option value=\"{$c['categoryTitle']}\">{$c['categoryTitle']}</option>";
        }
        
        echo '</select></label></div>';
    } 
    
    public function renderEditor(string $destination = "processor.php"): void{
        $photoDir = '../'.AddingArticle::$photoDirectory;
        echo<<<END
            <form class="articleEditor" action="$destination?url={$this->articleUrl}" method="POST">
                <div><label>Tytuł: <input class="articleEditorInput" type="" value="{$this->title}" name="title" size="100" required></label></div>
                <div><img class="articleEditorPhoto" src="$photoDir$this->photo" alt="Zdjęcie do artykułu pt. {$this->title}"></div>
                <div><label>Treść <textarea rows="4" cols="50" name="content" value="{$this->content}" class="articleEditorTextarea" required>{$this->content}</textarea></label></div>
END;
        $this->renderCategorySelector($this->category, "category");
        $this->renderCategorySelector($this->additionalCategory, "additionalCategory");
        echo<<<END
                <div><label>Data publikacji: <input type="text" value="{$this->publicationDate}" name="publicationDate" class="articleEditorInput" required></label></div>
                <div><input type="submit" value="Zapisz" name="savingArticle" class="articleEditorButton" required></div>
            </form>
        
END;
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