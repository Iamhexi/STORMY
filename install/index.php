<title>Instalacja STORMY...</title>
<?php

require_once '../classes/ClassAutoLoader.php';
$autoLoader = new ClassAutoLoader();

if (isset($_POST['dbServer'], $_POST['dbUser'], $_POST['dbName'], $_POST['adminEmail']))
   $installer = new Installer($_POST['dbServer'], $_POST['dbUser'], $_POST['dbPassword'], $_POST['dbName'], $_POST['adminEmail']);

else 
    Installer::renderInstallationForm("index.php");