<?php
require_once "../classes/AdminPanel.php";

$adminPanel = new AdminPanel("processor.php");
@$adminPanel->renderPanel($_GET['action']);