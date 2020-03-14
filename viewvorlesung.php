<?php

session_start();

require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/adminhandler.php');


require_once "config.php";



if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
} else {

    $vorlesung = (int) htmlspecialchars($_GET['vorlesung']);

    $sql = 'SELECT vorlesungen.vorlesungs_id , vorlesungen.beschreibung, kurse.name , kurse.studenten , users.vorname , users.nachname , vorlesungen.start, vorlesungen.ende, vorlesungen.sollstunden, SUM(vorlesungsstunden.dauer) ' .
        'FROM vorlesungen ' .
        'INNER JOIN kurse ON vorlesungen.kurs_id = kurse.kurs_id ' .
        'INNER JOIN users ON vorlesungen.user_id = users.user_id ' .
        'LEFT JOIN vorlesungsstunden ON vorlesungen.vorlesungs_id = vorlesungsstunden.vorlesungs_id ' .
        'WHERE vorlesungen.vorlesungs_id = ' . $vorlesung . ' ' .
        'GROUP BY vorlesungen.vorlesungs_id ' .
        'ORDER BY beschreibung ASC;';

    $vorlesungen =  array();

    if ($stmt = mysqli_prepare($link, $sql)) {


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);


            if (!mysqli_stmt_num_rows($stmt) <= 0) {

                mysqli_stmt_bind_result($stmt, $id, $beschreibung, $kurs, $kurs_anzahl, $vorname, $nachname, $start, $ende, $sollstunden, $iststunden);
                $count = 1;

                while (mysqli_stmt_fetch($stmt)) {

                    $vorlesungen[$count]["id"] = $id;
                    $vorlesungen[$count]["beschreibung"] = $beschreibung;
                    $vorlesungen[$count]["kurs"] = $kurs;
                    $vorlesungen[$count]["anzahlstudies"] = $kurs_anzahl;
                    $vorlesungen[$count]["vorname"] = $vorname;
                    $vorlesungen[$count]["nachname"] = $nachname;
                    $vorlesungen[$count]["start"] = date("d.m.Y", strtotime($start));
                    $vorlesungen[$count]["ende"] = date("d.m.Y", strtotime($ende));
                    $vorlesungen[$count]["sollstunden"] = $sollstunden;
                    $vorlesungen[$count]["iststunden"] = ($iststunden <= NULL || empty($iststunden)) ? '0' : $iststunden;
                    $vorlesungen[$count]["offen"] = $sollstunden - $iststunden;

                    $count = $count + 1;

                    $count = $count + 1;
                }
            } else {
                header("Location: allevorlesungen.php");
                die();
            }
        } else {
            echo "Error 4692ß6983aasf412348";
        }
    }

    mysqli_stmt_close($stmt);



    $sql = 'SELECT stunden_id,datum,dauer,start_uhrzeit,ende_uhrzeit FROM vorlesungsstunden WHERE vorlesungs_id = ' . $vorlesung . ' ORDER BY datum ASC;';


    $vorlesungsstunden =  array();

    if ($stmt = mysqli_prepare($link, $sql)) {


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);



            mysqli_stmt_bind_result($stmt, $stunden_id, $datum, $dauer, $start_uhrzeit, $ende_uhrzeit);
            $count = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $vorlesungsstunden[$count]["id"] = $stunden_id;
                $vorlesungsstunden[$count]["datum"] = date("d.m.Y", strtotime($datum));
                $vorlesungsstunden[$count]["dauer"] = $dauer;
                $vorlesungsstunden[$count]["start_uhrzeit"] = $start_uhrzeit;
                $vorlesungsstunden[$count]["ende_uhrzeit"] = $ende_uhrzeit;

                $count = $count + 1;
            }
        } else {
            echo "Error 469s2ß6983aasf412348";
        }
    }

    mysqli_stmt_close($stmt);
}



?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Vorlesung anzeigen</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/vendor.css">

    <script>
        var themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) : {};
        var themeName = themeSettings.themeName || '';
        if (themeName) {
            document.write('<link rel="stylesheet" id="theme-style" href="css/app-' + themeName + '.css">');
        } else {
            document.write('<link rel="stylesheet" id="theme-style" href="css/app-red.css">');
        }
    </script>
</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <header class="header">
                <div class="header-block header-block-collapse d-lg-none d-xl-none">
                    <button class="collapse-btn" id="sidebar-collapse-btn">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="header-block header-block-nav">
                    <ul class="nav-profile">

                        <li class="profile dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <div class="img" style="background-image: url('assets/faces/8.jpg')">
                                </div>
                                <span class="name"><?php echo htmlspecialchars($_SESSION["vorname"]) . " ";
                                                    echo htmlspecialchars($_SESSION["nachname"]); ?></span>
                            </a>
                            <div class="dropdown-menu profile-dropdown-menu" aria-labelledby="dropdownMenu1">
                                <a class="dropdown-item" href="profil.php">
                                    <i class="fa fa-user icon"></i> Mein Account </a>

                                <div class="dropdown-divider">

                                </div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fa fa-power-off icon"></i> Logout </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </header>
            <aside class="sidebar">
                <div class="sidebar-container">
                    <div class="sidebar-header">
                        <div class="brand">
                            <div class="logo">
                                <span class="l l1"></span>
                                <span class="l l2"></span>
                                <span class="l l3"></span>
                                <span class="l l4"></span>
                                <span class="l l5"></span>
                            </div>&nbsp;VVS
                        </div>
                    </div>
                    <?php require('navbars/nav_admin.php'); ?>
                </div>
                <footer class="sidebar-footer">
                    <ul class="sidebar-menu metismenu" id="customize-menu">
                        <li>
                            <ul>
                                <li class="customize">
                                    <div class="customize-item">
                                        <div class="row customize-header">
                                            Hier können Sie die Benutzeroberfläche anpassen.
                                        </div>

                                    </div>
                                    <div class="customize-item">
                                        <ul class="customize-colors">
                                            <li>
                                                <span class="color-item color-red" data-theme="red"></span>
                                            </li>
                                            <li>
                                                <span class="color-item color-orange" data-theme="orange"></span>
                                            </li>
                                            <li>
                                                <span class="color-item color-green active" data-theme=""></span>
                                            </li>
                                            <li>
                                                <span class="color-item color-seagreen" data-theme="seagreen"></span>
                                            </li>
                                            <li>
                                                <span class="color-item color-blue" data-theme="blue"></span>
                                            </li>
                                            <li>
                                                <span class="color-item color-purple" data-theme="purple"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <a href="">
                                <i class="fa fa-cog"></i> UI Anpassen </a>
                        </li>
                    </ul>
                </footer>
            </aside>
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
            <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
            <div class="mobile-menu-handle"></div>

            <article class="content forms-page">
                <div class="title-block">
                    <h3 class="title"> <?php echo htmlspecialchars($vorlesungen[1]["kurs"]) . " - " .  htmlspecialchars($vorlesungen[1]["beschreibung"]) . " - " .  htmlspecialchars($vorlesungen[1]["vorname"]) . " " .  htmlspecialchars($vorlesungen[1]["nachname"]) ?> </h3>
                    <p class="title-description"> Hier können Sie sich die Vorlesungstunden anzeigen lassen. </p>

                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">



                            <div class="card">
                                <div class="card-block">
                                    <div class="card-title-block">
                                        <h4> Informationen zur Vorlesung</h4>
                                    </div>
                                    <h5>Sollstunden: <?php echo htmlspecialchars($vorlesungen[1]['sollstunden']) ?></h5>
                                    <h5>Iststunden: <?php echo htmlspecialchars($vorlesungen[1]['iststunden']) ?></h5>
                                    <h5>Zeitraum der Vorlesung: <?php echo htmlspecialchars($vorlesungen[1]['start']) . " - " . htmlspecialchars($vorlesungen[1]['ende']) ?></h5>
                                    <h5>Anzahl der Studierenden: <?php echo htmlspecialchars($vorlesungen[1]['anzahlstudies']) ?></h5>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <div class="card-title-block">
                                        <h4> Übersicht der Termine</h4>
                                    </div>
                                    <section class="example">


                                        <div class="table-responsive">

                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>

                                                        <th>Datum</th>
                                                        <th>Uhrzeit</th>
                                                        <th>Anzahl Stunden</th>




                                                    </tr>
                                                </thead>
                                                <tbody>


                                                    <?php


                                                    foreach ($vorlesungsstunden as $vorlesungsstunde) {

                                                        $color = $vorlesung['offen'] > 0 ? 'red' : 'black';

                                                        echo "<tr>";
                                                        echo "<td>" . $vorlesungsstunde["datum"] . "</td>";


                                                        if ($vorlesungsstunde["start_uhrzeit"] >= 999) {
                                                            echo '<td>' . ((string) $vorlesungsstunde["start_uhrzeit"])[0] . ((string) $vorlesungsstunde["start_uhrzeit"])[1] . ":" . ((string) $vorlesungsstunde["start_uhrzeit"])[2] . ((string) $vorlesungsstunde["start_uhrzeit"])[3] . ' bis ';
                                                        } else {
                                                            echo '<td>' . ((string) $vorlesungsstunde["start_uhrzeit"])[0] .  ":" . ((string) $vorlesungsstunde["start_uhrzeit"])[1] . ((string) $vorlesungsstunde["start_uhrzeit"])[2] . ' bis ';
                                                        }

                                                        if ($vorlesungsstunde["ende_uhrzeit"] >= 999) {
                                                            echo ((string) $vorlesungsstunde["ende_uhrzeit"])[0] . ((string) $vorlesungsstunde["ende_uhrzeit"])[1] . ":" . ((string) $vorlesungsstunde["ende_uhrzeit"])[2] . ((string) $vorlesungsstunde["ende_uhrzeit"])[3] . '</td>';
                                                        } else {
                                                            echo ((string) $vorlesungsstunde["ende_uhrzeit"])[0] .  ":" . ((string) $vorlesungsstunde["ende_uhrzeit"])[1] . ((string) $vorlesungsstunde["ende_uhrzeit"])[2] . '</td>';
                                                        }
                                                        echo "<td>" . $vorlesungsstunde["dauer"] . "</td>";
                                                        //echo "<td>";
                                                        //echo "<button type=\"button\" class=\"btn btn-success btn-sm\"><a style=\"text-decoration:none; color: white\" href=\"viewvorlesung.php?vorlesung=" . $vorlesung["id"] . "\">Anzeigen</button> ";
                                                        //echo "<button type=\"button\" class=\"btn btn-danger btn-sm\"><a style=\"text-decoration:none; color: white\" href=\"delvorlesung.php?vorlesung=" . $vorlesung["id"] . "\">Löschen</button>";
                                                        //echo "</td>";
                                                        echo "</tr>";
                                                    };

                                                    ?>




                                                </tbody>
                                            </table>
                                        </div>

                                        <br>
                                        <?php
                                        echo "<button type=\"button\" class=\"btn btn-primary btn\"><a style=\"text-decoration:none; color: white\" href=\"allevorlesungen.php\">Zurrück</button>";
                                        ?>





                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </article>


            <footer class="footer">
                <div class="footer-block buttons author">
                    <a href="https://github.com/lExplLicit/VVS"> VVS</a> entwickelt von <a href="https://github.com/lExplLicit">Robert Eckermann</a>
                </div>

            </footer>
        </div>
    </div>
    <!-- Reference block for JS -->
    <div class="ref" id="ref">
        <div class="color-primary"></div>
        <div class="chart">
            <div class="color-primary"></div>
            <div class="color-secondary"></div>
        </div>
    </div>
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-80463319-4', 'auto');
        ga('send', 'pageview');
    </script>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
</body>

</html>