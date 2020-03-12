<?php
session_start();
switch ($_SESSION["admin"]) {
    case 1:
        header("location: adminindex.php");
        break;
    case 0:
        header("location: userindex.php");
        break;
}
exit;
