<?php

session_start();

require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/userhandler.php');


require_once "config.php";


$vorlesung = (int) htmlspecialchars($_GET['vorlesung']);

$sql = 'SELECT vorlesungen.vorlesungs_id , vorlesungen.beschreibung, kurse.name , kurse.studenten ,users.vorname , users.nachname , vorlesungen.start, vorlesungen.ende, vorlesungen.sollstunden, SUM(vorlesungsstunden.dauer) ' .
    'FROM vorlesungen ' .
    'INNER JOIN kurse ON vorlesungen.kurs_id = kurse.kurs_id ' .
    'INNER JOIN users ON vorlesungen.user_id = users.user_id ' .
    'LEFT JOIN vorlesungsstunden ON vorlesungen.vorlesungs_id = vorlesungsstunden.vorlesungs_id ' .
    'WHERE vorlesungen.vorlesungs_id = ' . $vorlesung . ' AND vorlesungen.user_id = ' . $_SESSION['id'] . ' ' .
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
            header("Location: meinevorlesungen.php?error=7");
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




if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {




    if ((int) $_POST['uhrbis'] - (int) $_POST['uhrvon'] > 0) {


        if ((int) $_POST['uhrvon'] <= 999) {

            $stunden_v = floatval((int) (((string) (int) $_POST['uhrvon'])[0]));
            $minuten_v = floatval((int) (((string) (int) $_POST['uhrvon'])[1] . ((string) (int) $_POST['uhrvon'])[2]));
            $dauer_dez_v = ($stunden_v) + ($minuten_v / 60);
        } else {
            $stunden_v = floatval((int) (((string) (int) $_POST['uhrvon'])[0] . ((string) (int) $_POST['uhrvon'])[1]));
            $minuten_v = floatval((int) (((string) (int) $_POST['uhrvon'])[2] . ((string) (int) $_POST['uhrvon'])[3]));
            $dauer_dez_v = ($stunden_v) + ($minuten_v / 60);
        }

        if ((int) $_POST['uhrbis'] <= 999) {

            $stunden_b = floatval((int) (((string) (int) $_POST['uhrbis'])[0]));
            $minuten_b = floatval((int) (((string) (int) $_POST['uhrbis'])[1] . ((string) (int) $_POST['uhrbis'])[2]));
            $dauer_dez_b = ($stunden_b) + ($minuten_b / 60);
        } else {
            $stunden_b = floatval((int) (((string) (int) $_POST['uhrbis'])[0] . ((string) (int) $_POST['uhrbis'])[1]));
            $minuten_b = floatval((int) (((string) (int) $_POST['uhrbis'])[2] . ((string) (int) $_POST['uhrbis'])[3]));
            $dauer_dez_b = ($stunden_b) + ($minuten_b / 60);
        }

        $dauer_dez = floatval($dauer_dez_b) - floatval($dauer_dez_v);
    } else {

        $errcode = 2;
    }

    if (strtotime($start) <= strtotime($_POST['date'])   &&   strtotime($ende) >= strtotime($_POST['date'])) {
    } else {

        $errcode = 5;
    }







    if (empty($errcode)) {


        $sql = "INSERT INTO vorlesungsstunden (stunden_id, vorlesungs_id, datum, dauer, start_uhrzeit, ende_uhrzeit) VALUES (NULL, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "sssss", $vorlesungs_id_param, $datum_param, $dauer_param, $start_uhrzeit_param, $ende_uhrzeit_param);


            $vorlesungs_id_param = $_POST['vorlesung'];
            $datum_param = date("Y-m-d", strtotime($_POST['date']));
            $dauer_param = $dauer_dez;
            $start_uhrzeit_param = $_POST['uhrvon'];
            $ende_uhrzeit_param = $_POST['uhrbis'];

            if (mysqli_stmt_execute($stmt)) {



                header("location: editvorlesung.php?success=1&vorlesung=" . $_POST['vorlesung']);
                die();
            } else {

                echo "Errror 1246125ys8dxf609707l3245";
            }
        }


        mysqli_stmt_close($stmt);
    } else {
        header("location: editvorlesung.php?error=" . $errcode . "&vorlesung=" . $_POST['vorlesung']);
        die();
    }
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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
                    <h3 class="title"> <?php echo htmlspecialchars($vorlesungen[1]["kurs"]) . " - " .  htmlspecialchars($vorlesungen[1]["beschreibung"])  ?> </h3>


                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">

                            <?php require('handlers/errorhandler.php'); ?>

                            <div class="card">
                                <div class="card-block">
                                    <div class="card-title-block">
                                        <?php



                                        switch (true) {

                                            case ($vorlesungen[1]["offen"] > 0):
                                                echo "<h4 style=\"color:red\">Sie müssen noch " . $vorlesungen[1]["offen"] . " Stunden planen</h4>";
                                                break;
                                            case ($vorlesungen[1]["offen"] == 0):
                                                echo "<h4 style=\"color:green\">Sie haben alle Stunden geplant. Super!</h4>";
                                                break;
                                            case ($vorlesungen[1]["offen"] < 0):
                                                echo "<h4 style=\"color:orange\">Achtung! Sie haben " . abs($vorlesungen[1]["offen"]) . " Stunde" . (abs($vorlesungen[1]["offen"]) == 1 ? '' : 'n') . " zu viel geplant.</h4>";

                                                break;
                                        }




                                        ?>
                                        <br>

                                        <h5>Sollstunden: <?php echo htmlspecialchars($vorlesungen[1]['sollstunden']) ?></h5>
                                        <h5>Iststunden: <?php echo htmlspecialchars($vorlesungen[1]['iststunden']) ?></h5>
                                        <h5>Zeitraum der Vorlesung: <?php echo htmlspecialchars($vorlesungen[1]['start']) . " - " . htmlspecialchars($vorlesungen[1]['ende']) ?></h5>
                                        <h5>Anzahl der Studierenden: <?php echo htmlspecialchars($vorlesungen[1]['anzahlstudies']) ?></h5>

                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <div class="card-title-block">
                                        <h4>Hinzufügen einer neuer Vorlesungsstunde</h4>

                                    </div>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?vorlesung=" . $vorlesung; ?>" method="post">

                                        <div class="row">

                                            <div class="col-md-3 form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
                                                <label>Datum</label>
                                                <input type="text" name="date" value="<?php echo $vorlesungen[1]['start']; ?>" class="form-control" onfocus="blur();" required />
                                                <span class="has-error"><?php echo $date_err; ?></span>
                                            </div>


                                            <div class="col-md-3 form-group">
                                                <label>Beginn</label>
                                                <select type="text" name="uhrvon" class="form-control">
                                                    <option value="800">8:00</option>
                                                    <option value="830">8:30</option>
                                                    <option value="900" selected>9:00</option>
                                                    <option value="930">9:30</option>
                                                    <option value="1000">10:00</option>
                                                    <option value="1030">10:30</option>
                                                    <option value="1100">11:00</option>
                                                    <option value="1130">11:30</option>
                                                    <option value="1200">12:00</option>
                                                    <option value="1230">12:30</option>
                                                    <option value="1300">13:00</option>
                                                    <option value="1330">13:30</option>
                                                    <option value="1400">14:00</option>
                                                    <option value="1430">14:30</option>
                                                    <option value="1500">15:00</option>
                                                    <option value="1530">15:30</option>
                                                    <option value="1600">16:00</option>
                                                    <option value="1630">16:30</option>
                                                    <option value="1700">17:00</option>
                                                    <option value="1730">17:30</option>
                                                    <option value="1800">18:00</option>
                                                    <option value="1830">18:30</option>
                                                    <option value="1930">19:00</option>
                                                    <option value="1930">19:30</option>
                                                    <option value="2000">20:00</option>

                                                </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                                <label>Ende</label>
                                                <select type="text" name="uhrbis" class="form-control">
                                                    <option value="800">8:00</option>
                                                    <option value="830">8:30</option>
                                                    <option value="900">9:00</option>
                                                    <option value="930">9:30</option>
                                                    <option value="1000">10:00</option>
                                                    <option value="1030">10:30</option>
                                                    <option value="1100">11:00</option>
                                                    <option value="1130">11:30</option>
                                                    <option value="1200" selected>12:00</option>
                                                    <option value="1230">12:30</option>
                                                    <option value="1300">13:00</option>
                                                    <option value="1330">13:30</option>
                                                    <option value="1400">14:00</option>
                                                    <option value="1430">14:30</option>
                                                    <option value="1500">15:00</option>
                                                    <option value="1530">15:30</option>
                                                    <option value="1600">16:00</option>
                                                    <option value="1630">16:30</option>
                                                    <option value="1700">17:00</option>
                                                    <option value="1730">17:30</option>
                                                    <option value="1800">18:00</option>
                                                    <option value="1830">18:30</option>
                                                    <option value="1930">19:00</option>
                                                    <option value="1930">19:30</option>
                                                    <option value="2000">20:00</option>

                                                </select>
                                            </div>

                                            <div class="col-md-3 form-group">
                                                <label>&nbsp;</label><br>
                                                <input type="hidden" id="vorlesung" name="vorlesung" value="<?php echo $vorlesungen[1]['id']; ?>">
                                                <input type="submit" class="btn btn-success" value="Hinzufügen">

                                            </div>

                                        </div>


                                    </form>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">

                                    <div class="card-title-block">

                                        <h4>Übersicht Ihrer Termine</h4>

                                    </div>
                                    <section class="example">


                                        <div class="table-responsive">

                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>

                                                        <th>Datum</th>
                                                        <th>Uhrzeit</th>
                                                        <th>Anzahl Stunden</th>
                                                        <th>Aktion</th>




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
                                                        echo "<td>";
                                                        //echo "<button type=\"button\" class=\"btn btn-success btn-sm\"><a style=\"text-decoration:none; color: white\" href=\"viewvorlesung.php?vorlesung=" . $vorlesung["id"] . "\">Anzeigen</button> ";
                                                        echo "<button type=\"button\" class=\"btn btn-danger btn-sm\"><a style=\"text-decoration:none; color: white\" href=\"delvorlesungsstunde.php?vorlesung=" . $vorlesung . "&stundenid=" . $vorlesungsstunde["id"] . "\">Löschen</button>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    };

                                                    ?>




                                                </tbody>
                                            </table>
                                        </div>



                                        <br>
                                        <?php
                                        echo "<button type=\"button\" class=\"btn btn-primary btn\"><a style=\"text-decoration:none; color: white\" href=\"meinevorlesungen.php\">Zurück</button>";
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('input[name="date"]').daterangepicker({
                singleDatePicker: true,
                minDate: '<?php echo $vorlesungen[1]['start']; ?>',
                maxDate: '<?php echo $vorlesungen[1]['ende']; ?>',
                locale: {
                    format: 'DD.MM.YYYY',
                    "daysOfWeek": [
                        "So",
                        "Mo",
                        "Di",
                        "Mi",
                        "Do",
                        "Fr",
                        "Sa"
                    ],
                    "monthNames": [
                        "Januar",
                        "Februar",
                        "März",
                        "April",
                        "Mai",
                        "Juni",
                        "Juli",
                        "August",
                        "September",
                        "Oktober",
                        "November",
                        "Dezember"
                    ],
                    "firstDay": 1
                }

            });


        });
    </script>
</body>

</html>
