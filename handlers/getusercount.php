<?php

require('loginhandler.php');
require('blockedhandler.php');



require_once "./config.php";


$sql = "SELECT user_id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 0) AND (blocked = 0);";


if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);
        $usercount = mysqli_stmt_num_rows($stmt);
    } else {
        echo "Error 9139898ß734486";
    }
}



mysqli_stmt_close($stmt);


$unblockedusercount = $usercount;


$sql = "SELECT user_id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 0) AND (blocked = 1);";


if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);

        $blockedusersc = mysqli_stmt_num_rows($stmt);
    } else {
        echo "Error 128756572983ß732";
    }
}



mysqli_stmt_close($stmt);

$blockedusercount = $blockedusersc;
