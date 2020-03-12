<?php

session_start();

$init = @json_decode(file_get_contents('configuration/install.json'), true);
if ($init['INIT'] || !isset($init['INIT'])) {
    header("location: installscript.php?step=1");
    die();
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}


require_once "config.php";


$username = $password = "";
$username_err = $password_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (empty(trim($_POST["username"]))) {
        $username_err = "Bitte Email eingeben.";
    } else {
        $username = trim($_POST["username"]);
    }


    if (empty(trim($_POST["password"]))) {
        $password_err = "Bitte Passwort eingeben.";
    } else {
        $password = trim($_POST["password"]);
    }


    if (empty($username_err) && empty($password_err)) {

        $sql = "SELECT user_id, username, password, admin, blocked, vorname, nachname, created_at FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_username);


            $param_username = $username;


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_store_result($stmt);


                if (mysqli_stmt_num_rows($stmt) == 1) {

                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $admin, $blocked, $vorname, $nachname, $erstellt);

                    if (mysqli_stmt_fetch($stmt)) {

                        if (password_verify($password, $hashed_password)) {

                            session_start();


                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["admin"] = $admin;
                            $_SESSION["blockiert"] = $blocked;
                            $_SESSION["vorname"] = $vorname;
                            $_SESSION["nachname"] = $nachname;
                            $_SESSION["erstellt"] = $erstellt;



                            header("location: index.php");
                        } else {

                            $password_err = "Passwort oder Email Adresse ist nicht gültig.";
                        }
                    }
                } else {

                    $password_err = "Passwort oder Email adresse ist nicht gültig.";
                }
            } else {
                echo "Error sdfsdf865a26ß9ß8f12";
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
    <title>VVS - Login</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/vendor.css">

    <link rel="stylesheet" id="theme-style" href="css/app-red.css">
    <script>
        var themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) : {};
        var themeName = themeSettings.themeName || '';
        if (themeName) {
            document.write('<link rel="stylesheet" id="theme-style" href="css/app-' + themeName + '.css">');
        } else {
            document.write('<link rel="stylesheet" id="theme-style" href="css/app.css">');
        }
    </script>
</head>

<body>
    <div style="background-color: lightgrey" class="auth">
        <div class="auth-container">
            <?php
            require('handlers/errorhandler.php');
            ?>
            <div class="card">
                <header class="auth-header">
                    <h1 class="auth-title">

                        <img height=40px; src="https://upload.wikimedia.org/wikipedia/de/thumb/1/1d/DHBW-Logo.svg/1200px-DHBW-Logo.svg.png" alt="logo">

                        <br> <br>
                        Benutzerlogin
                    </h1>
                    <br>
                </header>
                <div class="auth-content">

                    <form autocomplete="off" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                            <label>Email Adresse</label>
                            <input type="email" name="username" class="form-control underlined">
                            <span class="has-error"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                            <label>Passwort</label>
                            <input type="password" name="password" class="form-control underlined">

                            <span class="has-error"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <br>
                            <input type="submit" class="btn btn-block btn-primary" value="Einloggen">
                        </div>
                        <br>
                    </form>
                    <br>
                    <p style="text-align: center">VVS Verison 1.0.0</p>
                </div>
            </div>

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