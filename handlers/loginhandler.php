<?php
session_start();

$init = @json_decode(file_get_contents('configuration/install.json'), true);
if ($init['INIT_REQUIRED'] || !isset($init['INIT_REQUIRED'])) {
    header("location: installscript.php?step=1");
    die();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {

    header("location: login.php");
    exit;
}

require('reloadhandler.php');
