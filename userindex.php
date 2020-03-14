<?php

session_start();

require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/userhandler.php');

require('handlers/getusercount.php');
$unblockedusercount = $unblockedusercount;
require('handlers/getkurse.php');
$kurscount = count($kurse);


$sql = 'SELECT vorlesungen.vorlesungs_id , vorlesungen.beschreibung, kurse.name , users.vorname , users.nachname , start, ende, sollstunden, SUM(vorlesungsstunden.dauer) ' .
    'FROM vorlesungen ' .
    'INNER JOIN kurse ON vorlesungen.kurs_id = kurse.kurs_id ' .
    'INNER JOIN users ON vorlesungen.user_id = users.user_id ' .
    'LEFT JOIN vorlesungsstunden ON vorlesungen.vorlesungs_id = vorlesungsstunden.vorlesungs_id ' .
    'WHERE vorlesungen.user_id = ' . $_SESSION['id']  . ' ' .
    'GROUP BY vorlesungen.vorlesungs_id ' .
    'ORDER BY beschreibung ASC;';


require_once "config.php";


$vorlesungen =  array();

if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $id, $beschreibung, $kurs, $vorname, $nachname, $start, $ende, $sollstunden, $iststunden);

            $count = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $vorlesungen[$count]["id"] = $id;
                $vorlesungen[$count]["beschreibung"] = $beschreibung;
                $vorlesungen[$count]["kurs"] = $kurs;
                $vorlesungen[$count]["vorname"] = $vorname;
                $vorlesungen[$count]["nachname"] = $nachname;
                $vorlesungen[$count]["start"] = date("d.m.Y", strtotime($start));
                $vorlesungen[$count]["ende"] = date("d.m.Y", strtotime($ende));
                $vorlesungen[$count]["sollstunden"] = $sollstunden;
                $vorlesungen[$count]["iststunden"] = ($iststunden <= NULL || empty($iststunden)) ? '0' : $iststunden;
                $vorlesungen[$count]["offen"] = $sollstunden - $iststunden;

                $count = $count + 1;
            }
        } else {
            // keine Vorlesungen
        }
    } else {
        echo "Error 231927496908";
    }
}



mysqli_stmt_close($stmt);



$summeoffen = 0;
foreach ($vorlesungen as $vorlesung) {
    $summeoffen += $vorlesung['offen'];
}



$sql = 'SELECT datum, start_uhrzeit,ende_uhrzeit, beschreibung, kurse.name FROM vorlesungsstunden INNER JOIN vorlesungen USING(vorlesungs_id) INNER JOIN kurse USING(kurs_id) WHERE(vorlesungen.user_id = ' . $_SESSION['id'] . ') AND ( DATE(datum) >= CURDATE() ) ORDER BY vorlesungsstunden.datum ASC LIMIT 3';

$stunden =  array();

if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $datum_p, $start_p, $ende_p, $beschreibung_p, $kurs_p);

            $counts = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $stunden[$counts]["datum"] = date("d.m.Y", strtotime($datum_p));
                $stunden[$counts]["start"] = $start_p;
                $stunden[$counts]["ende"] = $ende_p;
                $stunden[$counts]["beschreibung"] = $beschreibung_p;
                $stunden[$counts]["kurs"] = $kurs_p;



                $counts = $counts + 1;
            }
        } else {
            // keine Vorlesungen
        }
    } else {
        echo "Error 231927498756908";
    }
}



mysqli_stmt_close($stmt);



?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dashboard</title>
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
                    <?php require('navbars/nav_user.php'); ?>
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
                    <h3 class="title"> Dashboard </h3>
                    <p class="title-description"> Hier können Sie Ihre Vorlesungen verwalten. </p>
                </div>


                <div class="card">
                    <div class="card-block">
                        <div class="card-title-block">
                            <h3 class="title">Ihre nächtsten Vorlesungen (max. 3)</h3>

                        </div>
                        <?php
                        if (count($stunden) <= 0) {
                            echo "Sie haben noch keine Vorlesungsstunden geplant.";
                        };


                        foreach ($stunden as $stunde) {
                        ?>

                            <div class="card card-primary" style="margin-bottom:0px">
                                <div class="card-header">
                                    <div class="header-block">
                                        <p style="color:white" class="title"> <?php echo $stunde['datum']; ?> -
                                            <?php
                                            if ($stunde["start"] >= 999) {
                                                echo ((string) $stunde["start"])[0] . ((string) $stunde["start"])[1] . ":" . ((string) $stunde["start"])[2] . ((string) $stunde["start"])[3] . ' bis ';
                                            } else {
                                                echo ((string) $stunde["start"])[0] . ":" . ((string) $stunde["start"])[1] . ((string) $stunde["start"])[2] . ' bis ';
                                            }

                                            if ($stunde["ende"] >= 999) {
                                                echo ((string) $stunde["ende"])[0] . ((string) $stunde["ende"])[1] . ":" . ((string) $stunde["ende"])[2] . ((string) $stunde["ende"])[3];
                                            } else {
                                                echo ((string) $stunde["ende"])[0] . ":" . ((string) $stunde["ende"])[1] . ((string) $stunde["ende"])[2];
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div style="background:rgb(240, 240, 240); padding-bottom:3px; padding-top:10px" class="card-block">
                                    <h6><?php echo $stunde['kurs']; ?></h6>
                                    <h4><?php echo $stunde['beschreibung']; ?></h4>
                                </div>

                            </div>
                        <?php
                        }
                        ?>





                    </div>
                </div>

                <div class="card">
                    <div class="card-block">
                        <div class="card-title-block">
                            <h3 class="title"><?php echo "Statistik" ?></h3>

                        </div>
                        <div class="row">

                            <div class="col-lg-6 col-12">



                                <?php
                                switch (true) {

                                    case ($summeoffen > 0):
                                        $color = 'danger';
                                        break;
                                    case ($summeoffen == 0):
                                        $color = 'success';
                                        break;
                                    case ($summeoffen < 0):
                                        $color = 'warning';
                                        break;
                                }
                                ?>

                                <div style="padding:10px; margin-bottom:15px; text-align:center; color:white;" class="small-box bg-<?php echo $color; ?>">
                                    <div class="inner">
                                        <h2><?php echo $summeoffen ?></h2>
                                        <p>Ihre offenen Stunden</p>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <!-- small box -->
                                <div style="padding:10px; margin-bottom:15px; text-align:center; color:white;" class="small-box bg-success">
                                    <div class="inner">
                                        <h2><?php echo count($vorlesungen) ?></h2>

                                        <p>Anzahl Ihrer Vorlesungen</p>
                                    </div>


                                </div>
                            </div>


                            <!-- ./col -->


                            <div class="col-lg-6 col-12">
                                <!-- small box -->
                                <div style="padding:10px; margin-bottom:15px; text-align:center; color:white;" class="small-box bg-info">
                                    <div class="inner">
                                        <h2><?php echo $unblockedusercount ?></h2>
                                        <p>Aktive Dozenten der DHBW</p>
                                    </div>
                                </div>
                            </div>

                            <!-- ./col -->

                            <div class="col-lg-6 col-12">
                                <!-- small box -->
                                <div style="padding:10px; margin-bottom:15px; text-align:center; color:white;" class="small-box bg-warning">
                                    <div class="inner">
                                        <h2><?php echo $kurscount; ?></h2>

                                        <p>Anzahl Kurse an der DHBW</p>
                                    </div>
                                </div>
                            </div>





                        </div>

                    </div>
                </div>




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