<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface iEditingArticle {
    function renderEditor(string $destination = "processor.php"): void;
    function saveChanges(
        ?string $title = null,
        ?string $content = null,
        ?string $category = null,
        ?string $additionalCategory = null,
        ?string $publicationDate = null
      ): bool;
}


class EditingArticle extends ArticleManager implements iEditingArticle {
    use DatabaseControl;

    public function __construct(string $url){
        self::$tableName = DatabaseControl::$contentTable;
        $this->loadArticleByUrl($url);
    }
    
    public function renderEditor(string $destination = 'processor.php'): void{
        $photoDir = '../'.AddingArticle::$photoDirectory;
        echo<<<END
            <form class="articleEditor" action="$destination?url={$this->loadedArticle->url}" method="POST">
                <div><label><span>Tytuł</span><input class="articleEditorInput" type="" value="{$this->loadedArticle->title}" name="title" required></label></div>
                <div><img class="articleEditorPhoto" src="$photoDir/{$this->loadedArticle->photo}" alt="Zdjęcie do artykułu pt. {$this->loadedArticle->title}"></div>
                <div><label><span>Treść</span><textarea rows="4" cols="50" name="content" value="{$this->loadedArticle->content}" id="content" class="articleEditorTextarea" required>{$this->loadedArticle->content}</textarea></label></div>
END;

        DatabaseControl::renderCategorySelector($this->loadedArticle->category, "category");
        DatabaseControl::renderCategorySelector($this->loadedArticle->additionalCategory, "additionalCategory");
        echo<<<END
                <div><label><span>Data publikacji</span><input type="text" value="{$this->loadedArticle->publicationDate}" name="publicationDate" class="articleEditorInput" required></label></div>
                <div><input type="submit" value="Zapisz zmiany" name="savingArticle" class="button" required></div>
            </form>
END;
        AddingArticle::provideEditor();
    }
    
    public function saveChanges(
        ?string $title = null,
        ?string $content = null,
        ?string $category = null,
        ?string $additionalCategory = null,
        ?string $publicationDate = null
       ): bool {

        $table = self::$tableName;
    
        if (!is_null($title)) 
            $this->loadedArticle->title = $title;
        if (!is_null($content)) 
            $this->loadedArticle->content = $content;
        if (!is_null($additionalCategory)) 
            $this->loadedArticle->additionalCategory = $additionalCategory;
        if (!is_null($publicationDate)) 
            $this->loadedArticle->publicationDate = $publicationDate;
        
        $query = "UPDATE $table SET 
            title = '{$this->loadedArticle->title}',
            content = '{$this->loadedArticle->content}',
            publicationDate = '{$this->loadedArticle->publicationDate}',
            category='{$this->loadedArticle->category}',
            additionalCategory = '{$this->loadedArticle->additionalCategory}'
            WHERE articleUrl = '{$this->loadedArticle->url}'";
        
        if (!($this->performQuery($query))) 
            return false;
        else
            return true;
    }
    
}