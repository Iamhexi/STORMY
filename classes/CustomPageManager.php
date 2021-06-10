<?php

require_once 'ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

interface PagesManager {
    function loadByUrl(string $url);
    function updateSubpage(string $content, $title = null): bool;
    function renderLoadedPage(): void;
    function getArrayOfSubpages(): array;
}

class CustomPageManager{
    use DatabaseControl;

    private array $subpages;
    private ?CustomPage $currentSubpage;
    private string $table;

    public function __construct(PageSettings $settings){
        $this->subpages = array();
        $this->table = DatabaseControl::$pagesTable;
        $this->currentSubpage = new CustomPage($settings);
    }

    public function loadByUrl(string $url){
        try {
            $url = $this->sanitizeInput($url);

            if (empty($url))
                throw new Exception("Incorrect subpage url, please enter correct url!");

            $query = "SELECT id, title, content FROM $this->table WHERE url = '$url'";

            if (@!($fetched = $this->performQuery($query, true)))
                throw new Exception("Couldn't retrieve a subpage with the url '$url' from the database!");

            $this->currentSubpage->content = $fetched['content'];
            $this->currentSubpage->title = $fetched['title'];
            $this->currentSubpage->id = $fetched['id'];

        } catch (Exception $e){
            $this->reportException($e);
        }
    }

    private function saveCurrentSubpage(){
        if (empty($this->currentSubpage->url))
            throw new Exception("Incorrect subpage url, please enter correct url!");

        $id = $this->currentSubpage->id;
        $t = $this->currentSubpage->title;
        $c = $this->currentSubpage->content;

        $query = "UPDATE $this->table SET title = '$t', content = '$c' WHERE id = 'id'";

        if (@!$this->performQuery($query))
            throw new Exception("Couldn't save a subpage with the id = '$this->id' to the database!");
    }

    public function updateSubpage(string $content, $title = null): bool {
        try {
            $this->currentSubpage->__set("content", $content);
            if ($title !== null)
                $this->currentSubpage->__set("title", $content);

            $this->saveCurrentSubpage();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }

   private function updateDisplayedTitle(): void {
       $this->currentSubpage->setTitle($this->currentSubpage->title);
   }

   public function renderLoadedPage(): void {
       $this->updateDisplayedTitle();
       $this->currentSubpage->renderPage();
   }


    private function loadListOfSubpagesFromDb(): void {
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

    private function loadListOfSubpages(): bool {
        try {
            $this->loadListOfSubpagesFromDb();
            return true;
        } catch (Exception $e){
            $this->reportException($e);
            return false;
        }
    }

    public function getArrayOfSubpages(): array{
        if (isset($this->subpages))
        if ($this->loadListOfSubpages() && $this->subpages !== null)
            return $this->subpages;
        else
            return array();
    }



}
