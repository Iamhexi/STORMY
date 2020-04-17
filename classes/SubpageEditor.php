<?php 

require_once "DatabaseControl.php";

interface SubpageManagement{
    public function createSubpage(string $url, string $title): bool;
    public function removeSubpageWithUrl(string $url): bool;
    public function removeSubpageWithId(int $id): bool;
    public function editSubpageWithUrl(string $url, $title, $content): bool;
    public function editSubpageWithId(int $id, $title, $content): bool;
    public function renderEditor(string $destination, string $url): void;
    public function renderListOfSubpages(): void;
}

class Subpage {
    public $id;
    public $url;
    public $title;
    public $content;
    
    public function __construct(int $id = 0, string $url = "", string $title = "", string $content = ""){
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
    }
}

class SubpageEditor implements SubpageManagement{
    use DatabaseControl;
    
    private $currentSubpage;
    private $subpages;
    
    public function __construct(){
        $this->table = DatabaseControl::$pagesTable;
        $subpages = array();
    }
    
    private function createNewSubpage(string $url, string $title): ?Exception{
        $content = '<h1>Nowa podstrona</h1><p>Właśnie utworzyłeś nową podstronę. Teraz możesz edytować ją z poziomu panelu admnistratora. Dodaj tutaj, co tylko chcesz. Nie zapomnij tylko podpiąć tej podstrony do menu.    Miłego tworzenia!</p>';
        $query = "INSERT INTO $this->table (url, title, content) VALUES ('$url', '$title', '$content')";
        
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't create a new subpage with title = $title and url = $url!");
        
        return null;
    }
    
    public function createSubpage(string $url, string $title): bool{
        try {
            $this->createNewSubpage($url, $title);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function selectRemovingQuery($url = null, $id = null){
        if ($url === null && $id === null)
            throw new Exception("It is obligatory to give either id or url in order to remove this subpage. None of the above has been given!");
            
        else if ($url !== null)
            return "DELETE FROM $this->table WHERE url = '$url'";
            
        else if ($id !== null)
            return "DELETE FROM $this->table WHERE id = '$id'";
    }
    
    private function removeSubpage($url, $id): ?Exception{
        $query = $this->selectRemovingQuery($url, $id);
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't remove a subpage with given id = $id or url = $url [only 1 needs to be given]!");
        return null;
    }
    
    public function removeSubpageWithUrl(string $url): bool{
        try {
            $this->removeSubpage($url, null);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    
    public function removeSubpageWithId(int $id): bool{
        try {
            $this->removeSubpage(null, $id);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function selectEditingQuery(string $url = null, int $id = null, $title = null, $content = null){
         if ($url === null && $id === null)
            throw new Exception("It is obligatory to give either id or url in order to edit a subpage. None of the above has been given!");
            
        else if ($url !== null)
            return "UPDATE $this->table SET title = '$title', content = '$content' WHERE url = '$url'";
        
        else if ($id !== null)
            return "UPDATE $this->table SET title = '$title', content = '$content' WHERE id = '$id'";
    }
    
    private function editSubpage($url, $id, $title, $content): ?Exception{
        $query = $this->selectEditingQuery($url, $id, $title, $content);
        if (@!($this->performQuery($query)))
            throw new Exception("Couldn't edit a subpage with given id = $id or url = $url [only 1 needs to be given]!");
        return null;
    }
    
    
    public function editSubpageWithUrl(string $url, $title, $content): bool{
        try {
            $this->editSubpage($url, null, $title, $content);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    
    public function editSubpageWithId(int $id, $title, $content): bool{
        try {
            $this->editSubpage(null, $id, $title, $content);
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    private function loadByUrl(string $url): Subpage{
        try {
            $url = $this->sanitizeInput($url);
            
            if (empty($url)) 
                throw new Exception("Incorrect subpage url, please enter correct url!");
            
            $query = "SELECT id, title, content FROM $this->table WHERE url = '$url'";

            if (@!($fetched = $this->performQuery($query, true)))
                throw new Exception("Couldn't retrieve a subpage with the url '$url' from the database!");
            
            $subpage = new Subpage($fetched['id'], $url, $fetched['title'], $fetched['content']);
            return $subpage;
            
            
        } catch (Exception $e){
            $this->reportException($e);
            $emptySubpage = new Subpage();
            return $emptySubpage;
        }  
    }
    
    
    
    public function renderEditor(string $destination, string $url): void{
        $currentSubpage = $this->loadByUrl($url);
        
        echo<<<END
        <form action="$destination" method="POST" class="subpageEditor">
            <input style="display:none;" type="number" name="id" value="{$currentSubpage->id}">
            <div><label>URL strony <input class="addingInput" type="text" name="url" value="$url" disabled></label></div>
            <div><label>Tytuł strony <input class="addingInput" type="text" name="title" value="{$currentSubpage->title}"></label></div>
            <div><label>Zawartość HTML <textarea class="subpageEditorTextarea" cols="70" rows="25" name="content">{$currentSubpage->content}</textarea></label></div>
            <div><input type="submit" class="subpageEditorButton" name="savingSubpage" value="Zapisz"></div>
        </form>
END;
    }
    
    
      private function loadListOfSubpagesFromDb(): void{
        $query = "SELECT * FROM $this->table";

        if(@!$connection = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME)) 
            throw new Exception($connection->connect_error);

        if(@!mysqli_query($connection, "SET CHARSET utf8")) 
            throw new Exception($connection->connect_error);

        if(@!$result = $connection->query($query)) 
            throw new Exception("Couldn't load categories from database!");

        while ($fetched = $result->fetch_array(MYSQLI_BOTH))
            $this->subpages[] = array("title" => $fetched['title'], "url" => $fetched['url']);
        
    }
    
    private function loadListOfSubpages(): bool{
        try {
            $this->loadListOfSubpagesFromDb();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }
    
    public function renderListOfSubpages(): void{
        if ($this->loadListOfSubpages() && !empty($this->subpages)){
            echo '<article class="subpageList"><header class="header">Edycja podstron</header>';
            foreach($this->subpages as $page){
                echo '<a href="editor.php?purl='.$page['url'].'">'.$page['title'].'</a><br>';
            }
            echo '</article>';
        }
    }
    
    private function getArrayOfSubpages(): array{
        if ($this->loadListOfSubpages() && $this->subpages !== null)
            return $this->subpages;
        else 
            return array();
    }
    
    
}