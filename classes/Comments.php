<?php
require_once "DatabaseControl.php";

class Comments{
    use DatabaseControl;
    
    protected $tableName;
    private $maxLineLength = 140;
    
    public function __construct(){
        $this->tableName = Comments::$commentsTable;
    }
    
    private function addCommentToDB(string $articleUrl, string $author, string $content){
        
        $articleUrl = $this->sanitizeInput($articleUrl);
        $author = $this->sanitizeInput($author);
        $content = $this->sanitizeInput($content);
        
        
        $query = "INSERT INTO $this->tableName (articleUrl, author, content) VALUES ('$articleUrl', '$author', '$content')";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new comment!");
    }
    
    public function addComment(string $articleUrl, string $author, string $content): void{
        try {
            $this->addCommentToDB($articleUrl, $author, $content);
            echo '<div class="prompt success">Komentarz został dodany pomyślnie!</div>';
        } catch (Exception $e){
            $this->reportException($e);
            echo '<div class="prompt fail">Nie udało się dodać Twojego komentarza, spróbuj ponownie!</div>';
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
    
    private function breakLongLines(string $old){
        $new = "";
        $oldLength = strlen($old);
        
        for ($i=0;$i<round($oldLength/$this->maxLineLength);$i++){
            $new .= (substr($old, $i*$this->maxLineLength, $this->maxLineLength).'<br>');
        }
        
        return $new;
    }
    
    private function displayCommentAsHTML(string $author, string $content, string $ad){
            $additionDate = $ad[8].$ad[9]."-".$ad[5].$ad[6]."-".$ad[0].$ad[1].$ad[2].$ad[3]; // formating date
            $content = $this->breakLongLines($content);
            
            ECHO<<<END
            
                <div class="comment">
                    <div class="commentAuthor">$author</div>
                    <div class="commentContent"><p>$content</p></div>
                    <div class="commentDate">$additionDate</div>
                </div>
END;
    }
    
    public function renderComments(string $articleUrl){
        try {
            $articleUrl = $this->sanitizeInput($articleUrl);
            $query = "SELECT author, content, additionDate FROM $this->tableName WHERE articleUrl = '$articleUrl' ORDER BY additionDate DESC";

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
    
    public function renderCommentForm(string $url){
        $url = $this->sanitizeInput($url);
        $file = basename($_SERVER['PHP_SELF']);
        
        echo<<<END
                <form action="$file?url=$url" method="POST" class="commentForm">
                    <input type="text" placeholder="Jak się nazywasz?" name="name" required>
                    <input type="text" placeholder="Treść komentarza..." name="content" required>
                    <input type="submit" name="commented" value="Skomentuj!">
                </form>
        
END;
    }
    
}