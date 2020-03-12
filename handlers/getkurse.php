<?php

require('loginhandler.php');
require('blockedhandler.php');



require_once "./config.php";

if (isset($_GET['search']) && $_GET['search'] != "") {

    $searchstringraw = trim(htmlspecialchars($_GET['search']));
    $searchstring = strtolower(str_replace(" ", "%", $searchstringraw));
    $searchstring = str_replace('"', "", $searchstring);
    $searchstring = str_replace("'", "", $searchstring);
    $searchstring = str_replace("/", "", $searchstring);
    $searchstring = str_replace(">", "", $searchstring);
    $searchstring = str_replace("<", "", $searchstring);
    $searchstring = str_replace(";", "", $searchstring);
    $searchstring = str_replace("“", "", $searchstring);
    $searchstring = str_replace("'", "", $searchstring);
    $searchstring = str_replace("*", "%", $searchstring);

    $title = "Suchergebnisse für \"" . $searchstringraw . "\":";

    $sql = 'SELECT kurs_id, name, studenten, fakultaet FROM kurse WHERE ((name LIKE \'%' . $searchstring . '%\') OR (fakultaet LIKE \'%' . $searchstring . '%\') OR (studenten LIKE \'%' . $searchstring . '%\')) ORDER BY name ASC;';
} else {

    $title = "Liste aller Kurse";

    $sql = "SELECT kurs_id, name, studenten, fakultaet FROM kurse ORDER BY name ASC;";
}
$kurse =  array();

if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $id, $name, $studenten, $fak);
            $count = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $kurse[$count]["id"] = $id;
                $kurse[$count]["name"] = $name;
                $kurse[$count]["studenten"] = $studenten;
                $kurse[$count]["fakultaet"] = $fak;

                $count = $count + 1;
            }
        } else {
            // keine Kurse
        }
    } else {
        echo "Error 4692ß98341234";
    }
}



mysqli_stmt_close($stmt);
