<?php

session_start();

require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/adminhandler.php');
if (isset($_GET['search']) && $_GET['search'] != "") {

    $searchstringraw = trim(htmlspecialchars($_GET['search']));
    $searchstring = strtolower(str_replace(" ", "%", $searchstringraw));
    $searchstring = str_replace('"', "", $searchstring);
    $searchstring = str_replace("/", "", $searchstring);
    $searchstring = str_replace("'", "", $searchstring);
    $searchstring = str_replace(">", "", $searchstring);
    $searchstring = str_replace("<", "", $searchstring);
    $searchstring = str_replace(";", "", $searchstring);
    $searchstring = str_replace("“", "", $searchstring);
    $searchstring = str_replace("'", "", $searchstring);
    $searchstring = str_replace("*", "%", $searchstring);


    $sql =  'SELECT user_id, username, vorname, nachname, unternehmen, blocked ' .
        'FROM users WHERE admin = 1 ' .
        'AND ((username LIKE \'%' . $searchstring . '%\') ' .
        'OR (vorname LIKE \'%' . $searchstring . '%\') ' .
        'OR (nachname LIKE \'%' . $searchstring . '%\') ' .
        'OR (unternehmen LIKE \'%' . $searchstring . '%\')) ' .
        'ORDER BY nachname ASC;';

    $title = "Suchergebnisse für \"" . $searchstringraw . "\":";
} else {

    $sql = 'SELECT user_id, username, vorname, nachname, unternehmen, blocked ' .
        'FROM users ' .
        'WHERE admin = 1 ' .
        'ORDER BY nachname ASC;';

    $title = "Liste aller Admins";
}


require_once "config.php";


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
            // keine Admins
        }
    } else {
        echo "Error 0924987908";
    }
}
mysqli_stmt_close($stmt);


?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dozenten</title>
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


                <?php require('navbars/searchform.php'); ?>
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
                    <h3 class="title"> Administratorenverwaltung </h3>
                    <p class="title-description"> Hier können Sie Administratoren verwalten. </p>
                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">

                            <?php require('handlers/errorhandler.php'); ?>

                            <div class="card">
                                <div class="card-block">
                                    <div class="card-title-block">
                                        <h3 class="title"> <?php echo $title; ?> </h3>
                                    </div>


                                    <section class="example">


                                        <button type="button" class="btn btn-success"><a style="text-decoration:none; color:white" href="addadmin.php"> Neuen Admin anlegen</a></button> <br><br>
                                        <div class="table-responsive">

                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>

                                                        <th>Nachname</th>
                                                        <th>Vorname</th>
                                                        <th>Unternehmen</th>
                                                        <th>Email Adresse</th>
                                                        <th>Blockiert</th>
                                                        <th>Aktionen</th>

                                                    </tr>
                                                </thead>
                                                <tbody>


                                                    <?php


                                                    foreach ($users as $user) {
                                                        echo "<tr>";
                                                        if ($user["blockiert"] == 1) {
                                                            $text = "Ja";
                                                            $action = "Freischalten";
                                                            $action_key = 0;
                                                        } else {
                                                            $text = "";
                                                            $action = "Blockieren";
                                                            $action_key = 1;
                                                        }

                                                        // echo "<td>".$user["id"]."</td>";
                                                        echo "<td>" . str_replace($searchstringraw, "<u>" . $searchstringraw . "</u>", htmlspecialchars($user["nachname"])) . "</td>";
                                                        echo "<td>" . str_replace($searchstringraw, "<u>" . $searchstringraw . "</u>", htmlspecialchars($user["vorname"])) . "</td>";
                                                        echo "<td>" . str_replace($searchstringraw, "<u>" . $searchstringraw . "</u>", htmlspecialchars($user["unternehmen"])) . "</td>";
                                                        echo "<td>" . str_replace($searchstringraw, "<u>" . $searchstringraw . "</u>", htmlspecialchars($user["username"])) . "</td>";
                                                        echo "<td>" . htmlspecialchars($text) . "</td>";
                                                        echo "<td>";
                                                        //echo " <button type=\"button\" class=\"btn btn-info btn-sm\" data-toggle=\"modal\" data-target=\"#modal-edit\" >Bearbeiten</button>";
                                                        echo " <button type=\"button\" class=\"btn btn-warning btn-sm\" ><a style=\"text-decoration:none; color: white\" href=\"blockadmin.php?user=" . $user["id"] . "&action=" . $action_key . "\">" . $action . "</button> ";
                                                        echo "<button type=\"button\" class=\"btn btn-danger btn-sm\"><a style=\"text-decoration:none; color: white\" href=\"deladmin.php?user=" . $user["id"] . "\">Löschen</button></td>";

                                                        echo "</tr>";
                                                    };

                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
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