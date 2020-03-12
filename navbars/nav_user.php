<?php
$sqlx = 'SELECT vorlesungen.vorlesungs_id , vorlesungen.beschreibung, kurse.name , users.vorname , users.nachname , vorlesungen.start, vorlesungen.ende, vorlesungen.sollstunden, SUM(vorlesungsstunden.dauer) ' .
    'FROM vorlesungen ' .
    'INNER JOIN kurse ON vorlesungen.kurs_id = kurse.kurs_id ' .
    'INNER JOIN users ON vorlesungen.user_id = users.user_id ' .
    'LEFT JOIN vorlesungsstunden ON vorlesungen.vorlesungs_id = vorlesungsstunden.vorlesungs_id ' .
    'WHERE vorlesungen.user_id = ' . $_SESSION['id'] . ' ' .
    'GROUP BY vorlesungen.vorlesungs_id ' .
    'ORDER BY kurse.name ASC;';

$vorlesungenx =  array();

if ($stmtx = mysqli_prepare($link, $sqlx)) {


    if (mysqli_stmt_execute($stmtx)) {

        mysqli_stmt_store_result($stmtx);


        if (!mysqli_stmt_num_rows($stmtx) <= 0) {

            mysqli_stmt_bind_result($stmtx, $idx, $beschreibungx, $kursx, $vornamex, $nachnamex, $startx, $endex, $sollstundenx, $iststundenx);

            $countx = 1;

            while (mysqli_stmt_fetch($stmtx)) {

                $vorlesungenx[$countx]["id"] = $idx;
                $vorlesungenx[$countx]["beschreibung"] = $beschreibungx;
                $vorlesungenx[$countx]["kurs"] = $kursx;
                $vorlesungenx[$countx]["vorname"] = $vornamex;
                $vorlesungenx[$countx]["nachname"] = $nachnamex;
                $vorlesungenx[$countx]["start"] = date("d.m.Y", strtotime($startx));
                $vorlesungenx[$countx]["ende"] = date("d.m.Y", strtotime($endex));
                $vorlesungenx[$countx]["sollstunden"] = $sollstundenx;
                $vorlesungenx[$countx]["iststunden"] = ($iststundenx <= NULL || empty($iststundenx)) ? '0' : $iststundenx;
                $vorlesungenx[$countx]["offen"] = $sollstundenx - $iststundenx;

                $countx = $countx + 1;
            }
        } else {
            $keinevorlesungen = true;
        }
    } else {
        echo "Error 231927496908";
    }
}


mysqli_stmt_close($stmtx);





?>
<nav class="menu">
    <ul class="sidebar-menu metismenu" id="sidebar-menu">




        <li <?php if (strpos($_SERVER["PHP_SELF"], 'userindex.php')) {
                echo 'class="active"';
            }; ?>>
            <a href="index.php">
                <i class="fa fa-home"></i> Dashboard </a>
        </li>

        <li <?php if (
                strpos($_SERVER["PHP_SELF"], 'usermonat.php')
            ) {
                echo 'class="active"';
            }; ?>>
            <a href="usermonat.php">
                <i class="fa fa-calendar"></i> Monatsübersicht
            </a>

        </li>

        <li <?php if (
                strpos($_SERVER["PHP_SELF"], 'meinevorlesungen.php')
            ) {
                echo 'class="active"';
            }; ?>>
            <a href="meinevorlesungen.php">
                <i class="fa fa-tasks"></i> Vorlesungsübersicht
            </a>

        </li>


        <?php if (!$keinevorlesungen) { ?>

            <li <?php if (

                    strpos($_SERVER["PHP_SELF"], 'editvorlesung.php')
                ) {
                    echo 'class="active open"';
                }; ?>>
                <a href="">
                    <i class="fa fa-sitemap"></i> Meine Vorlesungen <i class="fa arrow"></i>
                </a>
                <ul class="sidebar-nav">


                    <?
                    foreach ($vorlesungenx as $vorlesungx) {
                        echo "<li ><a href=\"editvorlesung.php?vorlesung=" . $vorlesungx["id"] . "\">(" . $vorlesungx["kurs"] . ") - " . $vorlesungx["beschreibung"] . "</a></li>";
                    }
                    ?>

                </ul>
            </li>
        <?php } ?>

    </ul>
</nav>