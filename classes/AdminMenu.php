<?php

require_once "AdminAuth.php";

interface AdministratorMenu {
    function renderMenu(): void;
}

class AdminMenu implements AdministratorMenu {
    
    private $panelLocation;
    
    public function __construct(string $panelLocation = "panel.php"){
        $this->panelLocation = $panelLocation;
    }
    
    public function renderMenu(): void{
        $a = new AdminAuth();
        $p = $this->panelLocation;
        echo<<<END
<nav>
    <ul class="mainMenu">
        <li><a href="#">Wpisy</a>
            <ul>
                <li><a href="$p?action=addEntry">Dodaj nowy</a></li>
                <li><a href="$p?action=entryList">Wszystkie</a></li>
            </ul>
        </li>
        
        <li><a href="#">Komentarze</a>
            <ul>
                <li><a href="$p?action=10lastComments">Ostatnio dodane</a></li>
                <li><a href="$p?action=commentsReviewPanel">Moderuj</a></li>
                <li><a href="$p?action=commentStats">Statystyki</a></li>
            </ul>
        </li>
        
        <li><a href="$p?action=normalStats">Statystyki</a></li>
        
        <li><a href="#">Podstrony</a>
            <ul>
                <li><a href="$p?action=addSubpage">Dodaj nową</a></li>    
                <li><a href="$p?action=listSubpages">Wszystkie</a></li>    
            </ul>
        </li>
        
        <li><a href="#">Kategorie</a>
            <ul>
                <li><a href="$p?action=addCategory">Dodaj nową</a></li>    
                <li><a href="$p?action=removeCategory">Usuń istniejącą</a></li>    
            </ul>
        </li>
        
        <li><a href="#">Edytor Menu</a>
            <ul>
                <li><a href="$p?action=addOption">Dodaj element</a></li>
                <li><a href="$p?action=editOptions">Edytuj elementy</a></li>
            </ul>
        </li>
        <li><a href="$p?action=settings">Ustawienia</a></li>
        
        <!--<li><a href="$p?action=errorLog">Dziennik Błędów</a></li>-->
        <!--<li class="logoutButton"><a href="logout.php">Wyloguj</a></li>-->
        <li>
END;
        $a->renderLoggingOutForm();
        
        echo<<<END
        </li> 
    </ul>
</nav> 
END;
    }
    
}