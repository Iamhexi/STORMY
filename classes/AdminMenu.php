<?php

class AdminMenu {
    
    private $panelLocation;
    
    public function __construct(string $panelLocation = "panel.php"){
        $this->panelLocation = $panelLocation;
    }
    
    public function renderMenu(): void{
        $p = $this->panelLocation;
        echo<<<END
<nav>
    <ul class="mainMenu">
        <li><a href="#">Wpisy</a>
            <ul>
                <li><a href="$p?action=addEntry">Dodaj nowy</a></li>
                <li><a href="$p?action=entryList">Lista wpisów</a></li>
            </ul>
        </li>
        
        <li><a href="#">Komentarze</a>
            <ul>
                <li><a href="$p?action=10lastComments">10 ostatnich komentarzy</a></li>
                <li><a href="$p?action=commentsReviewPanel">Moderuj komentarze</a></li>
                <li><a href="$p?action=commentStats">Statystyki komentarzy</a></li>
            </ul>
        </li>
        
        <li><a href="$p?action=normalStats">Statystyki</a></li>
        
        <li><a href="#">Podstrony</a>
            <ul>
                <li><a href="$p?action=addSubpage">Dodaj nową</a></li>    
                <li><a href="$p?action=listSubpages">Lista podstron (edycja/usuwanie)</a></li>    
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
        <li></li>
    </ul>
</nav> 
END;
    }
    
}