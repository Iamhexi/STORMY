<?php
require_once "DatabaseControl.php";

class ThumbnailView{
    use DatabaseControl;
    private string $tableName;
    
    public function __construct(string $category = null){
        $this->tableName = ThumbnailView::$contentTable;
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
    
    public function renderThumbnailForAdmin(string $articleUrl, string $title, string $photo): void{
        $photoDirectory = AddingArticle::$photoDirectory;
            ECHO<<<END
            
                <section class="adminArticleGrid">
                    <a class="adminArticleLink" href="editor.php?url=$articleUrl">
                    Kliknij, aby edytować...
                    <img class="adminArticlePhoto" src="../$photoDirectory/$photo" alt="Zdjęcie do artykułu pt. $title">
                    </a>
                </section>
END;
    }
    
    
    private function renderPageCounter(){
        $howManyArticle;
        $articlesPerPage;
        
        echo '<nav class="pageCounter"></nav>';
    }
    
    
    public function renderThumbnails(string $category = null, bool $adminView = false){
        try {
            if ($adminView)
                 $query = "SELECT articleUrl, title, photo FROM $this->tableName ORDER BY publicationDate DESC";
            
            else if ($category === null && $adminView === false)
                $query = "SELECT articleUrl, title, photo FROM $this->tableName WHERE publicationDate BETWEEN '00/00/0000 00:00:00.00' AND CURRENT_TIMESTAMP ORDER BY publicationDate DESC";
            
            else 
                $query = "SELECT articleUrl, title, photo FROM $this->tableName WHERE (category = '$category' OR additionalCategory = '$category') AND publicationDate BETWEEN '00/00/0000 00:00:00.00' AND CURRENT_TIMESTAMP ORDER BY publicationDate DESC";
            

            if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
                throw new Exception($connection->connect_error);

            if(@!mysqli_query($connection, "SET CHARSET utf8")) 
                throw new Exception($connection->connect_error);

            if(@!$result = $connection->query($query)) 
                throw new Exception("Couldn't render a thumbnails for the articles!");
            
            echo '<article class="articlesWrapper">';
            
            if ($adminView === false) 
                while ($fetched = $result->fetch_array(MYSQLI_BOTH))
                      $this->renderThumbnailAsHTML($fetched['articleUrl'], $fetched['title'], $fetched['photo']);
                  
            else
                while ($fetched = $result->fetch_array(MYSQLI_BOTH))
                    $this->renderThumbnailForAdmin($fetched['articleUrl'], $fetched['title'], $fetched['photo']);
                    
            
            
            echo '</article>';


        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
}