<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface ArticlesGrid {
    function renderThumbnails(?string $category = null, bool $adminView = false): bool;
}

class ThumbnailView implements ArticlesGrid{
    use DatabaseControl;
    private string $tableName;
    private ?string $category;
    private int $postsNumber = 9;
    
    public function __construct(){
        $this->tableName = DatabaseControl::$contentTable;
    }
    
    
    private function renderThumbnailAsHTML(string $articleUrl, string $title, string $photo): void{
            $photoDirectory = AddingArticle::$photoDirectory;
            ECHO<<<END
            
                <section class="articleThumbnail">
                    <a class="articleThumbnailLink" href="read.php?url=$articleUrl">
                    <img class="articleThumbnailImage" src="$photoDirectory/$photo" alt="Zdjęcie do artykułu pt. $title">
                    <header class="articleThumbnailTitle">$title</header>
                    </a>
                </section>
END;
    }
    
    private function renderThumbnailForAdmin(string $articleUrl, string $title, string $photo): void{
        $photoDirectory = AddingArticle::$photoDirectory;
            ECHO<<<END
            
                <section class="adminArticleGrid">
                    <header class="adminArticleTitle">$title</header>
                    <a class="adminArticleLink" href="editor.php?url=$articleUrl">
                    <img class="adminArticlePhoto" src="../$photoDirectory/$photo" alt="Zdjęcie do artykułu pt. $title">
                    <div>Kliknij, aby edytować...</div>
                    </a>
                </section>
END;
    }
    
    private function prepareQuery(bool $adminView){
        if ($adminView)
            return "SELECT articleUrl, title, photo FROM $this->tableName ORDER BY publicationDate DESC LIMIT $this->postsNumber";
            
        else if ($this->category === null && $adminView === false)
            return "SELECT articleUrl, title, photo FROM $this->tableName WHERE publicationDate BETWEEN '00/00/0000 00:00:00.00' AND CURRENT_TIMESTAMP ORDER BY publicationDate DESC LIMIT $this->postsNumber";

        else 
            return "SELECT articleUrl, title, photo FROM $this->tableName WHERE (category = '$this->category' OR additionalCategory = '$this->category') AND publicationDate BETWEEN '00/00/0000 00:00:00.00' AND CURRENT_TIMESTAMP ORDER BY publicationDate DESC LIMIT $this->postsNumber";
    }
    
    private function prepareResult(string $query): mysqli_result{
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
                throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't render the thumbnails for the articles!");
        return $result;
    }
    
    private function chooseProperView(mysqli_result $result, bool $adminView){
        if ($adminView === false) 
            while ($fetched = $result->fetch_array(MYSQLI_BOTH))
                  $this->renderThumbnailAsHTML($fetched['articleUrl'], $fetched['title'], $fetched['photo']);
        else
            while ($fetched = $result->fetch_array(MYSQLI_BOTH))
                $this->renderThumbnailForAdmin($fetched['articleUrl'], $fetched['title'], $fetched['photo']);
    }
    
    private function executeRenderingThumbnails(?string $category, bool $adminView): void{
        $this->category = ($category === null) ? null : $this->sanitizeInput($category);

        $query = $this->prepareQuery($adminView);
        $result = $this->prepareResult($query);

        echo '<article class="articlesWrapper">';
        $this->chooseProperView($result, $adminView);
        echo '</article>';
    }
    
    public function renderThumbnails(?string $category = null, bool $adminView = false): bool{
        try {
            $this->executeRenderingThumbnails($category, $adminView);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
}