<?php

require('loginhandler.php');
require('blockedhandler.php');
require('adminhandler.php');


require_once "./config.php";



$sql = "SELECT user_id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 1) AND (blocked=0);";


if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);

        $unblockedadmincount = mysqli_stmt_num_rows($stmt);
    } else {
        echo "Error 123987438ß2673";
    }
}



mysqli_stmt_close($stmt);




$sql = "SELECT user_id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 1) AND (blocked = 1);";


if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);

        $blockedadmincount = mysqli_stmt_num_rows($stmt);
    } else {
        echo "Error 908562349825";
    }
}



mysqli_stmt_close($stmt);
