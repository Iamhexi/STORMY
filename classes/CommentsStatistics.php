<?php

require_once "Comments.php";
require_once "DatabaseControl.php";

interface iCommentsStatistics {
    function countAll(?int $startingDate = null, ?int $endingDate = null): int;
    function countForArticle(string $articleUrl): int;
    function renderPanel(string $destination, $startingDate = null, $endingDate = null, $score = 0): void;
    function renderCommentsPreview(int $howMany = 10): void;
}

class CommentsStatistics extends Comments implements iCommentsStatistics {
    
    private function getNumberOfCommentsFromDB(){
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName";
        
        if (@!($fetched = $this->performQuery($query, true)))
            throw new Exception("Couldn't coun't comments in database!");

        return (int) $fetched['numberOfComments'];
    }
    
    public function countAll(?int $startingDate = null, ?int $endingDate = null): int{ // returns -1 if error occurs
        try {
            if ($startingDate !== null || $endingDate !== null)
                $number = $this->countDuringPeroid($startingDate, $endingDate);
            
            else 
                $number = $this->getNumberOfCommentsFromDB();
            
            return (is_int($number)) ? $number : -1;
        } catch (Exception $e){
            $this->reportException($e);
            return -1;
        }
    }
    
    private function countForArticleFromDB(string $articleUrl){
        $articleUrl = $this->sanitizeInput($articleUrl);
        
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName WHERE ArticleUrl = '$articleUrl'";
      
        if (@!($rowsNumber = $this->performQuery($query, true))) 
            throw new Exception("Couldn't count number of comments in the article with id = $articleUrl!");
        
        return (int)$rowsNumber[numberOfComments];
    }
    
    public function countForArticle(string $articleUrl): int{ // returns -1 if error occurs
        try {
            $number = $this->countForArticleFromDB($this->sanitizeInput($articleUrl));
            return (is_int($number)) ? $number : -1;
        } catch (Exception $e){
            $this->reportException($e);
            return -1;
        }
    }
    
    public function renderPanel(string $destination, $startingDate = null, $endingDate = null, $score = 0): void{
        
        if ($score === null) $score = 0;
        
        $lastWeek = time()-(60*60*24*7);
        $lastMonth = time()-(60*60*24*30);
        $lastYear = time()-(60*60*24*365);
        $fromBeginning = 0;
        
        echo<<<END
            <div class="statisticsPanel"> 
                <header class="header">Statystyki komentarzy</header>
                <form action="$destination" id="statisticsForm" method="POST">
                <label for="startingDatePicker">Okres wyświetlania statystyk</label>
                <select id="startingDatePicker" name="startingDatePicker" onselect="submitStatsSelection()">
                    <option selected>-- Wybierz okres --</option>
                    <option value="$lastWeek">Ostatni tydzień</option>
                    <option value="$lastMonth">Ostatnie 30 dni</option>
                    <option value="$lastYear">Ostatni rok</option>
                    <option value="$fromBeginning">Od początku</option>
                </select>
                </form>
                <p>Komentarzy na stronie: $score</p>
            </div>
            
            <script>
                function submitStatsSelection(){
                     document.getElementById("statisticsForm").submit();
                }
                
                document.getElementById("startingDatePicker").addEventListener("change", submitStatsSelection); 
                
            </script>
END;
    }
    
    private function convertTimestampToDate(int $timestamp): string{
        return date("Y-m-d G:i:s", $timestamp);
    }
    
    private function handleTimePeriod(bool $isEnding, int $timestamp = null){
        if ($isEnding)
            return ($timestamp === null) ? date("Y-m-d G:i:s") : $this->convertTimestampToDate($timestamp);
        else
            return ($timestamp === null) ? date("00-00-00 00:00:00") : $this->convertTimestampToDate($timestamp);
    }
    
    private function countDuringPeroid($startingDate, $endingDate){
        $startingDate = $this->handleTimePeriod(false, $startingDate);
        $endingDate = $this->handleTimePeriod(true, $endingDate);
        
        $query = "SELECT COUNT(*) AS numberOfComments FROM $this->tableName WHERE additionDate BETWEEN '$startingDate' AND '$endingDate'";
        
        if (@!($fetched = $this->performQuery($query, true)))
            throw new Exception("Couldn't coun't comments in database!");

        return (int) $fetched['numberOfComments'];
    }
    
    private function prepareQueryForRetrievingComments(int $howMany){
        return "SELECT articleUrl, author, content, additionDate FROM $this->tableName ORDER BY additionDate DESC LIMIT $howMany";   
    }
    
    private function findArticleTitleInDb(string $articleUrl){
        $table = DatabaseControl::$contentTable;
        $query = "SELECT title FROM $table WHERE articleUrl = '$articleUrl'";
        
        if (@!($fetched = $this->performQuery($query, true)))
            throw new Exception("The article, which the comment was added for, does not exist! Missing article: url = '$articleUrl' ");
        if ($fetched['title'] === null)
            throw Exception("Couldn't find corresponding the article for the comment!");        
    
        return $fetched['title'];
    }
    
    private function findArticleTitle(?string $articleUrl): string{
        try {
            $title = $this->findArticleTitleInDb($articleUrl);
            return $title;
        } catch (Exception $e){
            $this->reportException($e);
            return "Ten komentarz dodano do już nieistniejącego artykułu!";
        }
    }
    
    private function renderComment(string $articleTitle, string $articleUrl, string $author, string $content, string $additionDate): void{
        echo<<<END
            <div class="commentPreview">
                <div class="commentPreviewWhereFrom"><b>Komentarz do artykułu:</b> <a target="_blank" href="../read.php?url=$articleUrl">$articleTitle</a></div>
                <div class="commentPreviewAuthor"><b>Autor:</b> $author</div>
                <div class="commentPreviewContent"><b>Treść:</b><p> $content</p></div>
                <div class="commentPreviewDate"><b>Data dodania:</b> $additionDate</div>
            </div>
END;
    }
    
    private function renderCommentsFromDb(int $howMany): ?Exception{
        $query = $this->prepareQueryForRetrievingComments($howMany);
        
        $result = $this->performQuery($query, false, true);
        
        while ($retrieved = $result->fetch_array(MYSQLI_BOTH)){
            $articleTitle = $this->findArticleTitle($retrieved['articleUrl']);
            $this->renderComment($articleTitle, $retrieved['articleUrl'], $retrieved['author'], $retrieved['content'], $retrieved['additionDate']);
        }

        
        return null;
    }
    
    public function renderCommentsPreview(int $howMany = 10): void{
        try {
            echo '<div class="commentsPreviewWrapper"><header class="header">10 ostatnich komentarzy</header>';
            $this->renderCommentsFromDb($howMany);
            echo '</div>';
        } catch (Exception $e){
            $this->reportException($e);
        }
    }
    
    
}