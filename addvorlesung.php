<?php

session_start();
require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/adminhandler.php');
require('handlers/getkurse.php');





require_once "config.php";

$sql = "SELECT user_id, username, vorname, nachname, unternehmen, blocked FROM users WHERE admin = 0 ORDER BY nachname ASC;";

$users =  array();

if ($stmt = mysqli_prepare($link, $sql)) {


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_store_result($stmt);


        if (!mysqli_stmt_num_rows($stmt) <= 0) {

            mysqli_stmt_bind_result($stmt, $id, $username, $vorname, $nachname, $unternehmen, $blocked);
            $usercount = 1;

            while (mysqli_stmt_fetch($stmt)) {

                $users[$usercount]["id"] = $id;
                $users[$usercount]["username"] = $username;
                $users[$usercount]["vorname"] = $vorname;
                $users[$usercount]["unternehmen"] = $unternehmen;
                $users[$usercount]["nachname"] = $nachname;
                $users[$usercount]["blockiert"] = $blocked;
                $usercount = $usercount + 1;
            }
        } else {
        }
    } else {
        echo "Error 13094325847610897489";
    }
}



mysqli_stmt_close($stmt);



if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $sql = "SELECT vorlesungs_id FROM vorlesungen WHERE beschreibung = ?;";

    if ($stmt = mysqli_prepare($link, $sql)) {

        mysqli_stmt_bind_param($stmt, "s", $param_beschreibung);


        $param_beschreibung = trim($_POST["beschreibung"]);


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                $beschreibung_err = "Diese Vorlesung existiert bereits.";
            } else {
                $beschreibung = trim($_POST["beschreibung"]);
            }
        } else {
            echo "Error 54216489ß12345465789ß23";
        }
    }

    mysqli_stmt_close($stmt);

    $sql = "SELECT name FROM kurse WHERE kurs_id = ?;";

    if ($stmt = mysqli_prepare($link, $sql)) {

        mysqli_stmt_bind_param($stmt, "s", $param_k_id);


        $param_k_id = trim($_POST["kurs"]);


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) != 1) {
                $kurs_err = "Ungültiger Kurs.";
            } else {

                mysqli_stmt_bind_result($stmt, $kursname);

                while (mysqli_stmt_fetch($stmt)) {

                    $notification["kurs"] = $kursname;
                }

                $kurs_p = trim($_POST["kurs"]);
            }
        } else {
            echo "Error 54216489ß1234546589ß4329789ß23";
        }
    }

    mysqli_stmt_close($stmt);



    $sql = "SELECT username, vorname, nachname FROM users WHERE user_id = ?;";

    if ($stmt = mysqli_prepare($link, $sql)) {

        mysqli_stmt_bind_param($stmt, "s", $param_u_id);


        $param_u_id = trim($_POST["dozent"]);


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) != 1) {
                $dozent_err = "Ungültiger Dozent.";
            } else {
                $dozent = trim($_POST["dozent"]);

                mysqli_stmt_bind_result($stmt, $email, $vorname_mail, $nachname_mail);

                while (mysqli_stmt_fetch($stmt)) {

                    $notification["mail"] = $email;
                    $notification["vorname"] = $vorname_mail;
                    $notification["nachname"] = $nachname_mail;
                }
            }
        } else {
            echo "Error 54216489ß1234546589ß432970qß3989ß23";
        }
    }

    mysqli_stmt_close($stmt);

    if (empty(trim($_POST["beschreibung"]))) {
        $beschreibung_err = "Bitte Namen angeben.";
    } elseif (strlen(trim($_POST["beschreibung"])) < 3) {
        $beschreibung_err = "Der Name muss aus mindestens 6 Zeichen bestehen.";
    } else {
        $beschreibung = trim($_POST["beschreibung"]);
    }

    if (empty(trim($_POST["soll"]))) {
        $soll_err = "Bitte Sollstunden angeben.";
    } elseif (strlen(trim($_POST["soll"])) < 1) {
        $soll_err = "Bitte Sollstunden angeben!";
    } else {
        $soll = trim($_POST["soll"]);
    }

    if (strtotime($_POST['datevon']) >=  strtotime($_POST['datebis'])) {
        $date_err = "Zeitraum ungültig";
    }

    $datevon = date("Y-m-d", strtotime($_POST['datevon']));
    $datebis = date("Y-m-d", strtotime($_POST['datebis']));
    //$kurs_p = (int) $_POST['kurs'];
    //$dozent = (int) $_POST['dozent'];




    if (empty($beschreibung_err) && empty($soll_err) && empty($kurs_err) && empty($dozent_err) && empty($date_err)) {


        $sql = "INSERT INTO vorlesungen (beschreibung, kurs_id, user_id, start, ende, sollstunden) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "ssssss", $beschreibung, $kurs_p, $dozent, $datevon, $datebis, $soll);



            if (mysqli_stmt_execute($stmt)) {

                $empfaenger = $notification["mail"];
                $betreff = "Ihnen wurde eine Vorlesung zugewiesen!";
                $text = "Hallo " . $notification["vorname"] . " " . $notification["nachname"] . ",<br>Ihnen wurde eine neue Vorlesung vom Sekretariat zugewiesen.<br><br>Bezeichnung: <strong>" . $beschreibung . "</strong><br>Kurs: <strong>" . $kursname . "</strong><br>Zeitraum: <strong>" . $_POST['datevon'] . " bis " . $_POST['datebis'] . "</strong><br>Stundenanzahl: <strong>" . $soll . "h</strong><br><br>Sie können sich hier anmelden um Ihre Termine einzutragen:<br><a href=\"https://code.roberteckermann.eu/vorlesungsplaner/login.php\">Zugang zum VVS System</a>";

                $mailheader = "From: VVS-System <info@roberteckermann.eu> \r\n";
                $mailheader .= "Mime-Version: 1.0 \r\n";
                $mailheader .= "Content-type: text/html; charset=utf-8";

                if ($sendmail) {
                    mail($empfaenger, $betreff, $text, $mailheader);
                }

                header("location: allevorlesungen.php?success=1");
                die();
            } else {

                echo "Errror 1246125ysdxf609707l3245";
            }
        }


        mysqli_stmt_close($stmt);
    } else {
    }


    mysqli_close($link);
}


?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Vorlesung hinzufügen</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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
                    <h3 class="title"> Vorlesung anlegen </h3>
                    <p class="title-description"> Hier können Sie eine Vorlesung anlegen. Der Dozent wird über die neue Vorlesung benachrichtigt.</p>
                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-block">

                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <div class="row">
                                            <div class="col-9 form-group <?php echo (!empty($beschreibung_err)) ? 'has-error' : ''; ?>">
                                                <label>Vorlesungsname</label>
                                                <input type="text" name="beschreibung" class="form-control" value="<?php echo $beschreibung; ?>" required>
                                                <span class="has-error"><?php echo $beschreibung_err; ?></span>

                                            </div>

                                            <div class="col-3 form-group">
                                                <label>Stunden</label>
                                                <input type="number" name="soll" class="form-control" value="<?php echo $soll; ?>" required>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-6 form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
                                                <label>Zeitraum von</label>
                                                <input type="text" name="datevon" value="<?php echo htmlspecialchars($_POST['datevon']); ?>" class="form-control" onfocus="blur();" required />
                                                <span class="has-error"><?php echo $date_err; ?></span>
                                            </div>
                                            <div class=" col-6 form-group">
                                                <label>Zeitraum bis</label>
                                                <input type="text" name="datebis" value="<?php echo htmlspecialchars($_POST['datebis']); ?>" class="form-control" onfocus="blur();" required />

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 form-group <?php echo (!empty($kurs_err)) ? 'has-error' : ''; ?>">
                                                <label for="sel2">Kurs</label>
                                                <select class="form-control" name="kurs" id="sel2" required>

                                                    <?php

                                                    foreach ($kurse as $kurs) {
                                                        if ($kurs_p == $kurs['id']) {
                                                            echo '<option value="' . $kurs['id'] . '" selected>' . $kurs['name'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $kurs['id'] . '" >' . $kurs['name'] . '</option>';
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                                <span class="has-error"><?php echo $kurs_err; ?></span>
                                            </div>

                                            <div class="col-6 form-group <?php echo (!empty($dozent_err)) ? 'has-error' : ''; ?>">

                                                <label for="sel1">Dozent</label>
                                                <select class="form-control" name="dozent" id="sel1" required>
                                                    <?php

                                                    foreach ($users as $user) {
                                                        if ($dozent == $user['id']) {
                                                            echo '<option value="' . $user['id'] . '" selected>' . $user['vorname'] . ' ' . $user['nachname'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $user['id'] . '" >' . $user['vorname'] . ' ' . $user['nachname'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="has-error"><?php echo $dozent_err; ?></span>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="form-group">
                                            <button class="btn btn-secondary"><a style="text-decoration:none;" href="allevorlesungen.php">Abbrechen</a></button>
                                            <input type="submit" class="btn btn-primary" value="Speichern">

                                        </div>

                                    </form>
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
            $('input[name="datevon"]').daterangepicker({
                singleDatePicker: true,
                minDate: '<?php echo date("d.m.Y", time()); ?>',
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

            $('input[name="datebis"]').daterangepicker({
                singleDatePicker: true,
                minDate: '<?php echo date("d.m.Y", time() + 60 * 60 * 24); ?>',
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
