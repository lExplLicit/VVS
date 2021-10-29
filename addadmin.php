<?php
session_start();

require('handlers/loginhandler.php');
require('handlers/blockedhandler.php');
require('handlers/adminhandler.php');



require_once "config.php";


function randomPassword($chars)
{

    $data = '!?*+-#%&_1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($data), 0, $chars);
}



$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Bitte Email angeben.";
    } else {

        $sql = "SELECT user_id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_username);


            $param_username = trim($_POST["username"]);


            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Der Benutzer mit dieser Email Adresse existiert bereits.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Error 92734652365742316";
            }
        }


        mysqli_stmt_close($stmt);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Bitte Passwort angeben.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Das Passwort muss aus mindestens 6 Zeichen bestehen.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["vorname"]))) {
        $vorname_err = "Bitte Vornamen eingeben.";
    } elseif (strlen(trim($_POST["vorname"])) < 3) {
        $vorname_err = "Vorname muss aus mindestens 3 Zeichen bestehen.";
    } else {
        $vorname = trim($_POST["vorname"]);
    }

    if (empty(trim($_POST["nachname"]))) {
        $nachname_err = "Bitte Nachnamen eingeben.";
    } elseif (strlen(trim($_POST["nachname"])) < 3) {
        $nachname_err = "Nachname muss aus mindestens 3 Zeichen bestehen.";
    } else {
        $nachname = trim($_POST["nachname"]);
    }
    $unternehmen = htmlspecialchars($_POST["unternehmen"]);


    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($vorname_err) && empty($nachname_err)) {


        $sql = "INSERT INTO users (username, password, vorname, nachname, unternehmen, admin) VALUES (?, ?, ?, ? , ?, 1)";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $vorname, $nachname, $unternehmen);


            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash


            if (mysqli_stmt_execute($stmt)) {
                $empfaenger = $param_username;
                $betreff = "Ihr Account wurde erstellt.";
                $text = "Hallo " . $vorname . " " . $nachname . ",<br>Ihr Administratoraccount für das Vorlesungs-Verwaltungs-System der DHBW-Lörrach wurde erstellt. <br><br> Ihre Initialpasswort lautet<br><br><strong>" . $password . "</strong><br><br>Sie können sich hier anmelden:<br><a href=\"https://code.roberteckermann.eu/vorlesungsplaner/login.php\">Zugang zum VVS System</a>";

                $mailheader = "From: VVS-System <info@roberteckermann.eu> \r\n";
                $mailheader .= "Mime-Version: 1.0 \r\n";
                $mailheader .= "Content-type: text/html; charset=utf-8";

                if ($sendmail) {
                    mail($empfaenger, $betreff, $text, $mailheader);
                }


                // Redirect to login page
                header("location: admins.php?success=1");
            } else {
                echo "Error 8932658760126";
            }
        }


        mysqli_stmt_close($stmt);
    }


    mysqli_close($link);
}
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
                    <h3 class="title"> Administrator hinzufügen </h3>
                    <p class="title-description"> Hier können Sie einen Admin anlegen. </p>
                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-block">

                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                                        <div class="form-group <?php echo (!empty($vorname_err)) ? 'has-error' : ''; ?>">
                                            <label>Vorname</label>
                                            <input type="text" name="vorname" class="form-control" value="<?php echo $vorname; ?>" required>
                                            <span class="has-error"><?php echo $vorname_err; ?></span>

                                        </div>


                                        <div class="form-group <?php echo (!empty($nachname_err)) ? 'has-error' : ''; ?>">
                                            <label>Nachname</label>
                                            <input type="text" name="nachname" class="form-control" value="<?php echo $nachname; ?>" required>
                                            <span class="has-error"><?php echo $nachname_err; ?></span>

                                        </div>

                                        <div class="form-group">
                                            <label>Unternehmen</label>
                                            <input type="text" name="unternehmen" class="form-control" value="<?php echo $unternehmen; ?>">


                                        </div>
                                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                            <label>Email-Adresse</label>
                                            <input type="email" name="username" class="form-control" value="<?php echo $username; ?>" required>
                                            <span class="has-error"><?php echo $username_err; ?></span>
                                        </div>
                                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                            <label>Initialpasswort</label>
                                            <input type="password" name="password" class="form-control" value="<?php echo randomPassword(12); ?>" required>
                                            <span class="has-error"><?php echo $password_err; ?></span>
                                        </div>

                                        <div class="form-group">
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
</body>

</html>