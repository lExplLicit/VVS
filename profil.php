<?php
session_start();



    switch ($_SESSION["admin"]) {
    case 1:
        header("location: adminprofil.php");
        break;
    case 0:
        header("location: userprofil.php");
        break;
    }
	exit;
	


?>