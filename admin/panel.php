<?php
require_once '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

$adminPanel = new AdminPanel("processor.php");
@$adminPanel->renderPanel($_GET['action']);