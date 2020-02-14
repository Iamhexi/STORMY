<?php
require_once "DatabaseControl.php";

class Comments extends DatabaseControl{
    private $tableName;
    
    public function __construct(){
        $this->tableName = Comments::$commentsTable;
    }
    
    private function countAllCommentsFromDB(){
        $tableName = Comments::$commentsTable;
        $query = "SELECT COUNT(1) FROM $this->tableName";
      
        if (@!($rowsNumber = $this->performQuery($query, true))) 
            throw new Exception("Couldn't count number of rows in the comments!");
        
        return (int)$rowsNumber[0];
    }
    
    public function countAllComments(): ?int{
        try {
            return $this->countAllCommentsFromDB();
        } catch (Exception $e){
            $this->reportException($e);
            return null;
        }
    }
    
    private function countCommentsFromDB(string $articleUrl){
        $tableName = Comments::$commentsTable;
        $query = "SELECT COUNT(1) FROM $this->tableName WHERE ArticleUrl = '$articleUrl'";
      
        if (@!($rowsNumber = $this->performQuery($query, true))) 
            throw new Exception("Couldn't count number of rows in the article with id = $articleUrl!");
        
        return (int)$rowsNumber[0];
    }
    
    public function countComments(string $articleUrl): ?int{
        try {
            return $this->countCommentsFromDB($articleUrl);
        } catch (Exception $e){
            $this->reportException($e);
            return null;
        }
    }
    
    private function addCommentToDB(string $articleUrl, string $author, string $content){
        $query = "INSERT INTO $this->tableName (articleUrl, author, content) VALUES ('$articleUrl', '$author', '$content')";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new comment!");
    }
    
    public function addComment(string $articleUrl, string $author, string $content): void{
        try {
            $this->addCommentToDB($articleUrl, $author, $content);
        } catch (Exception $e){
            $this->reportException($e);
        }
    }

    private function removeAllCommentsFromDB(){
        $query = "TRUNCATE TABLE $this->tableName";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't remove all comments!");
    }
    
    public function removeAllComments(){
        try {
            $this->removeAllCommentsFromDB();
        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    private function displayCommentAsHTML(string $author, string $content, string $ad){
            $additionDate = $ad[8].$ad[9]."-".$ad[5].$ad[6]."-".$ad[0].$ad[1].$ad[2].$ad[3]; // formating date
            
            ECHO<<<END
            
                <div id="comment">
                    <div class="commentAuthor">$author</div>
                    <div class="commentContent">$content</div>
                    <div class="commentDate">$additionDate</div>
                </div>
END;
    }
    
    public function renderCommentsForArticle(string $articleUrl){
    try {
        $query = "SELECT author, author, content, additionDate FROM $this->tableName WHERE articleUrl = '$articleUrl' ORDER BY additionDate DESC";
        
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);
        
        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);
        
        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't render comments for article with url = $articleUrl");
        
        while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
            $this->displayCommentAsHTML($fetched['author'], $fetched['content'], $fetched['additionDate']);
        }

        
    } catch (Exception $e){
        $this->reportException($e);
        echo $e->getMessage()."<br>";
        return false;
    }
}
    
    
}