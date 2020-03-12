<?php

require('loginhandler.php');
require('blockedhandler.php');
require('adminhandler.php');


require_once "../config.php";



$sql2 = "SELECT id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 1) AND (blocked=0);";
$admins =  array();

if ($stmt = mysqli_prepare($link, $sql2)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $id, $username, $vorname, $nachname, $unternehmen, $blocked);
            $usercount = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $admins[$usercount]["id"] = $id;
                $admins[$usercount]["username"] = $username;
                $admins[$usercount]["vorname"] = $vorname;
                $admins[$usercount]["unternehmen"] = $unternehmen;
                $admins[$usercount]["nachname"] = $nachname;
                $admins[$usercount]["blockiert"] = $blocked;
                $usercount = $usercount + 1;
            }
        } else {
            // keine Admins
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.!!";
    }
}



mysqli_stmt_close($stmt);




$sql3 = "SELECT id, username, vorname, nachname, unternehmen, blocked FROM users WHERE (admin = 1) AND (blocked = 1);";
$blockedadmins =  array();

if ($stmt = mysqli_prepare($link, $sql3)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $id, $username, $vorname, $nachname, $unternehmen, $blocked);
            $blockedusercount = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $blockedadmins[$blockedusercount]["id"] = $id;
                $blockedadmins[$blockedusercount]["username"] = $username;
                $blockedadmins[$blockedusercount]["vorname"] = $vorname;
                $blockedadmins[$blockedusercount]["unternehmen"] = $unternehmen;
                $blockedadmins[$blockedusercount]["nachname"] = $nachname;
                $blockedadmins[$blockedusercount]["blockiert"] = $blocked;
                $blockedusercount = $blockedusercount + 1;
            }
        } else {
            // keine blockierten Admins
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.!!";
    }
}



mysqli_stmt_close($stmt);
