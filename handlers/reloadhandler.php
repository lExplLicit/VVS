<?php
session_start();

if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == true) {

    require_once "./config.php";


    $sql = "SELECT   admin, blocked, vorname, nachname  FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {

        mysqli_stmt_bind_param($stmt, "s", $param_username);

        $param_username = $_SESSION['username'];


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);


            if (mysqli_stmt_num_rows($stmt) == 1) {

                mysqli_stmt_bind_result($stmt, $admin5, $blocked5, $vorname5, $nachname5);
                if (mysqli_stmt_fetch($stmt)) {


                    $_SESSION["admin"] = $admin5;
                    $_SESSION["blockiert"] = $blocked5;
                    $_SESSION["vorname"] = $vorname5;
                    $_SESSION["nachname"] = $nachname5;
                }
            } else {
                //echo "Error 865a26ß9ß8ffefj12";
            }
        }

        mysqli_stmt_close($stmt);
    }
}
