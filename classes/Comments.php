<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface CommentsManager {
    function addComment(string $articleUrl, string $author, string $content): bool;
    function removeAllComments(): bool;
    function removeComment(int $id): bool;
    function acceptComment(int $id): bool;
    function renderComments(string $articleUrl): bool;
    function renderCommentsReviewPanel(string $destination): bool;
    function renderCommentForm(string $url): void;
}


class Comments implements CommentsManager {
    use DatabaseControl;
    
    protected string $tableName;
    private int $commentDefaultStatus;
    private static int $maxLineLength = 140;
    
    public function __construct(string $settingsLocation = "settings/default.json"){
        $this->tableName = Comments::$commentsTable;
        
        $settings = new PageSettings($settingsLocation);
        switch ($settings->__get('commentsPolicy')){
            case 'safetyPolicy':
                $this->commentDefaultStatus = 0;
                break;
            
            case 'freedomPolicy':
                $this->commentDefaultStatus = 1;
                break;

            case 'smartFilter':
                $this->commentDefaultStatus = 2;
                break;
        }
        // safetyPolicy = 0, freedomPolicy = 1, smartFilter = 2
    }
    
    private function addCommentToDB(string $articleUrl, string $author, string $content): void {
        
        $articleUrl = $this->sanitizeInput($articleUrl);
        $author = $this->sanitizeInput($author);
        $content = $this->sanitizeInput($content);
        
        if (empty($articleUrl) || empty($author) || empty($content))
            throw new Exception('Neither url nor author nor content cannot be empty!');
            
        $status = $this->determineCommentStatus($content);
        
        $query = "INSERT INTO $this->tableName (articleUrl, author, content, isPublished) VALUES ('$articleUrl', '$author', '$content', '$status')";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't add a new comment!");
    }
    
    public function addComment(string $articleUrl, string $author, string $content): bool {
        try {
            $this->addCommentToDB($articleUrl, $author, $content);
            echo '<div class="prompt success">Komentarz został dodany pomyślnie!</div>';
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            echo '<div class="prompt fail">Nie udało się dodać Twojego komentarza, spróbuj ponownie!</div>';
            return false;
        }
    }

    private function determineCommentStatus(string $content): int {
        $status = $this->commentDefaultStatus;
        if ($status === 2){
            $smartFilter = new LanguageFilter();
            if ($smartFilter->isVulgar($content))
                $status = 0; // to be checked
            else
                $status = 1; // to be published
        }

        return $status;
    }

    private function removeAllCommentsFromDB(){
        $query = "TRUNCATE TABLE $this->tableName";
        
        if (@!$this->performQuery($query))
            throw new Exception("Couldn't remove all comments!");
    }
    
    public function removeAllComments(): bool{
        try {
            $this->removeAllCommentsFromDB();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function breakLongLines(string $old): string{
        $new = "";
        $oldLength = strlen($old);
        
        if ($oldLength <= self::$maxLineLength)
            return $old;
        
        for ($i=0;$i<round($oldLength/self::$maxLineLength);$i++)
            $new .= (substr($old, $i*self::$maxLineLength, self::$maxLineLength).'<br>');
        
        
        return $new;
    }
    
    private function removeCommentWithId(int $id){
        $query = "DELETE FROM $this->tableName WHERE id = '$id'";
        if (@!$this->performQuery($query, false, true))
            throw new Exception("Couldn't remove comment with id = $id!");
    }
    
    public function removeComment(int $id): bool{
        try {
            $this->removeCommentWithId($id);
            return true;
        } catch(Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function acceptCommentWithId(int $id){
        $query = "UPDATE $this->tableName SET isPublished = 1 WHERE id = '$id'";
        if (@!$this->performQuery($query, false, true))
            throw new Exception("Couldn't accept comment with id = $id!");
    }
    
    public function acceptComment(int $id): bool{
        try {
            $this->acceptCommentWithId($id);
            return true;
        } catch(Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    protected function displayCommentAsHTML(string $author, string $content, string $ad){
            $additionDate = $ad[8].$ad[9]."-".$ad[5].$ad[6]."-".$ad[0].$ad[1].$ad[2].$ad[3]; // formating date
            $content = $this->breakLongLines($content);
            
            echo<<<END
            
                <div class="comment">
                    <div class="commentAuthor">Autor: $author</div>
                    <div class="commentContent"><p>$content</p></div>
                    <div class="commentDate">Dodano: $additionDate</div>
                </div>
END;
    }
    
    protected function displayCommentToReviewAsHTML(string $destination, string $author, string $content, string $ad, int $commentId){
            $additionDate = $ad[8].$ad[9]."-".$ad[5].$ad[6]."-".$ad[0].$ad[1].$ad[2].$ad[3]; // formating date
            $content = $this->breakLongLines($content);
            
            echo<<<END
            
                <div class="comment">
                    <div class="commentAuthor">Autor: $author</div>
                    <div class="commentContent"><p>$content</p></div>
                    <div class="commentDate">Dodano: $additionDate</div>
                    <form class="acceptForm" method="POST" action="$destination"><input style="display:none;" type="number" name="commentId" value="$commentId"><input name="acceptComment" type="submit" value="&#10004;"></form>
                    <form class="refuseForm" method="POST" action="$destination"><input style="display:none;" type="number" name="commentId" value="$commentId"><input name="refuseComment" type="submit" value="&#10007;"></form>
                </div>
END;
    }
    
    private function prepareResultForRenderingComments(string $query): mysqli_result {
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't render comments for article with url = $articleUrl");
        return $result;
    }
    
    private function prepareQueryForRenderingComments(string $articleUrl): string{
        $articleUrl = $this->sanitizeInput($articleUrl);
        return "SELECT author, content, additionDate FROM $this->tableName WHERE articleUrl = '$articleUrl' AND isPublished = 1 ORDER BY additionDate DESC";
    }
    
    private function executeRenderingComments(mysqli_result $result): void{
        $counter = 0;
        while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
            $this->displayCommentAsHTML($fetched['author'], $fetched['content'], $fetched['additionDate']);
            $counter++;
        }

        if ($counter == 0)
            throw new Exception('Url of article is incorrect or no comments has been added yet!');
    }
    
    public function renderComments(string $articleUrl): bool {
        try {
            $query = $this->prepareQueryForRenderingComments($articleUrl);
            $result = $this->prepareResultForRenderingComments($query);
            $this->executeRenderingComments($result);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function prepareQueryForReviewPanel(): string{
         return "SELECT id, author, content, additionDate FROM $this->tableName WHERE isPublished = 0 ORDER BY additionDate DESC";
    }
    
    private function renderCommentsForReviewPanel(): mysqli_result{
        $query = $this->prepareQueryForReviewPanel();
        
        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!$connection->query("SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't render comments for the review panel!");
        
        return $result;
    }
    
    public function renderCommentsReviewPanel(string $destination): bool{
        try {
            $noComments = true;
            echo '<div class="commentsWrapper"><header class="header">Zarządzanie komentarzami</header>';

            $result = $this->renderCommentsForReviewPanel();
            
            while ($fetched = $result->fetch_array(MYSQLI_BOTH)){
                $this->displayCommentToReviewAsHTML($destination, $fetched['author'], $fetched['content'], $fetched['additionDate'], $fetched['id']);
                $noComments = false;
            }
            
            if ($noComments)
                echo '<p>Żaden komentarz nie czeka na akceptację. Jeśli chcesz, aby każdy komentarz najpierw musiał przejść ręczną akceptację, w ustawieniach ustaw opcję "Polityka komentarzy" na "Najpierw zaakceptuj, potem publikuj".</p>';
            
            echo '</div>';
            return true;

        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function renderCommentForm(string $url): void{
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