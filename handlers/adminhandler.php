<?php
session_start();

if(!isset($_SESSION["admin"]) || $_SESSION["admin"] !== 1){
    header("location: error-401.html");
    exit;
}

?>