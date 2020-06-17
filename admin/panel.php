<?php

require_once '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

@session_start();

$adminPanel = new AdminPanel("processor.php");
@$adminPanel->renderPanel($_GET['action']);