<?php
session_start();
require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/userhandler.php');

require_once "config.php";



$sql = 'DELETE vs FROM vorlesungsstunden vs INNER JOIN vorlesungen vl USING(vorlesungs_id) WHERE vl.user_id = ' . (int) $_SESSION['id'] . ' AND vs.stunden_id = ' . (int) $_GET['stundenid'] . ';';



if (!$link->query($sql)) {

    $link->close();
    header("Location: editvorlesung.php?vorlesung=" . $_GET['vorlesung'] . "&error=6");
    die();
} else {

    $link->close();
    header("Location: editvorlesung.php?vorlesung=" . $_GET['vorlesung'] . "&success=1");
    die();
}
