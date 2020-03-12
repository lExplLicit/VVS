<?php
session_start();
if($_SESSION["blockiert"] == 1 ){
    
    header("location: error-401-blocked.html");
    exit;
}

?>