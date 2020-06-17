<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();


class ArticleManager {
    use DatabaseControl;

    protected ?Article $loadedArticle = null;
    protected static string $tableName;

    public function __construct(){
        self::$tableName = DatabaseControl::$contentTable;
    }

    public function loadArticleByUrl(string $url): bool {
        try {
            $url = $this->sanitizeInput($url);
            return ($this->loadFromDbUsingUrl($url)) ? true : false;
        } catch(Exception $e){
            $this->reportException($e);
            return false;
        }
    }

    protected function loadFromDbUsingUrl(string $url): bool {
        $query = $this->prepareQuery($url);
        if (!($data = $this->performQuery($query, true))) return false;

        $article = new Article(
            $data['news_id'],
            $data['title'],
            $data['content'],
            $data['photo'],
            $url,
            $data['author'],
            $data['category'],
            $data['additionalCategory'],
            $data['publicationDate']
        );

        $this->loadedArticle = $article;
        
        return true;
    }

    protected function prepareQuery(string $url): string {
        $t = self::$tableName;
        return "SELECT * FROM $t WHERE articleUrl = '$url';";
    }

    public function renderLoadedArticle(): void {
        if ($this->loadedArticle === null)
            throw new Exception("No article is currently loaded!");

        $photo = $this->providePhotoName();
        $category = $this->provideCategoryName();
        $publicationDate = $this->providePublicationDate();

        echo<<<END
        <article class="article">
            <h1 class="articleTitle">{$this->loadedArticle->title}</h1>
            <div class="articleInfo">Opublikowano: $publicationDate | Kategoria: $category | Autor: {$this->loadedArticle->author}</div>
            <img src="$photo" class="mainArticleImage" alt="Zdjęcie dla artykuły pt. {$this->loadedArticle->title}">
            <div class="articleText">{$this->loadedArticle->content}</div>
        </article>
END;
    }

    protected function providePhotoName(): string {
        return AddingArticle::$photoDirectory.$this->loadedArticle->photo;
    }

    protected function provideCategoryName(): string {
        $categories = new Categories();
        return $categories->getCategoryNameByUrl($this->loadedArticle->category);
    }

    protected function providePublicationDate(): string {
        return substr($this->loadedArticle->publicationDate, 0, 16);
    }

    public function getTitle(): ?string {
        $t = $this->loadedArticle->title;
        if ($t == null)
            return null;
        else 
            return $t;
    }

    public function getAuthor(): ?string {
        $a = $this->loadedArticle->author;
        if ($a == null)
            return null;
        else 
            return $a;
    }
}