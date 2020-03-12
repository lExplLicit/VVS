<?php


$database = json_decode(file_get_contents('configuration/database.json'), true);
$nofifications = json_decode(file_get_contents('configuration/notifications.json'), true);


$sendmail = $nofifications['SEND'];
$mainadmin = $nofifications['MAIN_ADMIN'];





$link = mysqli_connect($database['SERVER'], $database['USERNAME'], $database['PASSWORD'], $database['NAME']);


if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$link->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
