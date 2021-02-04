<?php
session_start();
$init = @json_decode(file_get_contents('configuration/install.json'), true);
if (isset($init['INIT_REQUIRED']) && $init['INIT_REQUIRED'] == false) {
  header("location: index.php");
  die();
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Installationsassistent</title>
  <meta name="author" content="lExplLicit">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="background:lightgrey; text-align:center">
  <h1>Installationsassistent - VVS</h1>
  <hr>


  <?php
  if ($_GET['step'] == 1 || !isset($_GET['step'])) {

    $_SESSION['steptwo'] = rand(10000, 90000);
  ?>


    <h2>Verbindung zur Datenbank herstellen.</h2><br>
    <p>Bei einer Docker Installation können die bereits eingetragenen Verbindungsdaten übernommen werden.</p>
    <p>Bei erstmaliger Docker installation dauert es ca 30 Sekunden bis die Datenbank verfügbar ist.</p>


    <form action="installscript.php?step=<?php echo $_SESSION['steptwo']; ?>" method="POST">



      <label for="fname">Datenbank Server (+ :port)</label><br>
      <input type="text" name="server" value="vvs_db" required><br>
      <label for="lname">Datenbank Name</label><br>
      <input type="text" name="name" value="vvs_database" required><br>
      <label for="lname">Datenbank Benutzer</label><br>
      <input type="text" name="benutzer" value="vvs_user" required><br>
      <label for="lname">Datenbank Passwort</label><br>
      <input type="password" name="passwort" value="vvs_password" required><br><br>
      <input type="submit" value="Bestätigen">
    </form>

  <?php
  } elseif (($_GET['step'] == $_SESSION['steptwo']) && isset($_POST['server']) && isset($_POST['benutzer']) && isset($_POST['passwort']) && isset($_POST['name'])) {


    $link = @mysqli_connect($_POST['server'], $_POST['benutzer'], $_POST['passwort'], $_POST['name']);


    if ($link === false) {
      echo "ERROR: Could not connect. " . mysqli_connect_error();

      echo "<br><br>Verbindung zur Datenbank nicht möglich..<br><br>";

      echo '<form action="installscript.php?step=1" method="GET"><input type="submit" value="Daten ändern"></form>';
      die();
    } else {

      $conndata = array(
        "SERVER" => $_POST['server'],
        "USERNAME" => $_POST['benutzer'],
        "PASSWORD" => $_POST['passwort'],
        "NAME" => $_POST['name']
      );


      $file = fopen('configuration/database.json', 'w');
      fwrite($file, json_encode($conndata, JSON_PRETTY_PRINT));
      fclose($file);

      echo "<br>Verbindung erfolgreich.. Zugangsdaten wurden gespeichert..<br><br>";
      echo "Die Tabellen in der Datanbank werden neu initialisiert.<br>Vorhandene Daten werden gelöscht. Möchten Sie fortfahren?<br><br><br>";
      $_SESSION['csfr'] = rand(10000, 50000);
      $_SESSION['dbinit'] = rand(10000, 90000);
      echo '<form action="installscript.php?step=' . $_SESSION['dbinit'] . '" method="POST"><input type="hidden" name="csrf" value="' . $_SESSION['csfr'] . '"><input type="submit" value="Fortfahren"></form>';
      die();
    }
  } elseif ($_GET['step'] == $_SESSION['dbinit']) {

    if ($_SESSION['csfr'] != $_POST['csrf']) {

      die("CSFR Token not matching");
    }


    echo "<br>Datenbank wird initialisiert...<br>";
    echo "Tabellen werden hinzugefügt...<br>";


    $database = json_decode(file_get_contents('configuration/database.json'), true);
    $link = mysqli_connect($database['SERVER'], $database['USERNAME'], $database['PASSWORD'], $database['NAME']);

    $link->query('DROP TABLE vorlesungsstunden;');
    $link->query('DROP TABLE vorlesungen;');
    $link->query('DROP TABLE kurse;');
    $link->query('DROP TABLE users;');




    $link->query('CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `vorname` varchar(50) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `unternehmen` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    $link->query('ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);');

    $link->query('ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;');


    $link->query('CREATE TABLE `kurse` (
  `kurs_id` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `studenten` int(11) NOT NULL,
  `fakultaet` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    $link->query('ALTER TABLE `kurse`
  ADD PRIMARY KEY (`kurs_id`);');

    $link->query('ALTER TABLE `kurse`
  MODIFY `kurs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2000;');

    $link->query('CREATE TABLE `vorlesungen` (
  `vorlesungs_id` int(11) NOT NULL,
  `beschreibung` varchar(150) NOT NULL,
  `kurs_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start` date NOT NULL,
  `ende` date NOT NULL,
  `sollstunden` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    $link->query('ALTER TABLE `vorlesungen`
  ADD PRIMARY KEY (`vorlesungs_id`),
  ADD KEY `vorlesungen_ibfk_1` (`kurs_id`),
  ADD KEY `vorlesungen_ibfk_2` (`user_id`);');

    $link->query('ALTER TABLE `vorlesungen`
  MODIFY `vorlesungs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3000;');

    $link->query('ALTER TABLE `vorlesungen`
  ADD CONSTRAINT `vorlesungen_ibfk_1` FOREIGN KEY (`kurs_id`) REFERENCES `kurse` (`kurs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vorlesungen_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;');

    $link->query('CREATE TABLE `vorlesungsstunden` (
  `stunden_id` int(11) NOT NULL,
  `vorlesungs_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `dauer` float NOT NULL,
  `start_uhrzeit` int(4) NOT NULL,
  `ende_uhrzeit` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    $link->query('ALTER TABLE `vorlesungsstunden`
  ADD PRIMARY KEY (`stunden_id`),
  ADD KEY `vorlesung` (`vorlesungs_id`);');

    $link->query('ALTER TABLE `vorlesungsstunden`
  MODIFY `stunden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4000;');

    $link->query('ALTER TABLE `vorlesungsstunden`
  ADD CONSTRAINT `vorlesungsstunden_ibfk_1` FOREIGN KEY (`vorlesungs_id`) REFERENCES `vorlesungen` (`vorlesungs_id`) ON DELETE CASCADE ON UPDATE CASCADE;');



    echo "<br><h2>Vorgang erfolgreich. Bitte Administratorkonto anlegen</h2><br>";

    $_SESSION['addadmin'] = rand(10000, 90000);
  ?>
    <form action="installscript.php?step=<?php echo $_SESSION['addadmin']; ?>" method="POST">
      <label>Vorname:</label><br>
      <input type="text" name="vorname" required><br>
      <label>Nachname:</label><br>
      <input type="text" name="nachname" required><br>
      <label>Email:</label><br>
      <input type="email" name="email" required><br>
      <label>Passwort:</label><br>
      <input type="password" name="pass" required><br><br>

      <input type="checkbox" id="notif" name="notif">
      <label for="notif">Email Benachrichtigungen an Dozenten aktivieren?</label>
      <br>
      <input type="checkbox" id="exam" name="exam">
      <label for="exam">Beispieldaten hinzufügen? (Dozenten und Administratoren)</label>
      <br><br><br>
      <input type="submit" value="Bestätigen">
    </form>

  <?php



  } elseif ($_GET['step'] == $_SESSION['addadmin']) {

    $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $database = json_decode(file_get_contents('configuration/database.json'), true);
    $link = mysqli_connect($database['SERVER'], $database['USERNAME'], $database['PASSWORD'], $database['NAME']);

    $link->query('INSERT INTO `users` (`user_id`, `username`, `password`, `admin`, `blocked`, `vorname`, `nachname`, `unternehmen`) VALUES
  (1000, \'' . trim($_POST['email']) . '\', \'' . $hash . '\', 1, 0, \'' . trim($_POST['vorname']) . '\', \'' . trim($_POST['nachname']) . '\', \'E-Corp\');');


    $install = array(
      "INIT_REQUIRED" => false
    );

    $file = fopen('configuration/install.json', 'w');
    fwrite($file, json_encode($install, JSON_PRETTY_PRINT));
    fclose($file);


    if ($_POST['notif'] == "on") {
      $notifications = true;
    } else {
      $notifications = false;
    }

    if ($_POST['exam'] == "on") {

      $link->query('INSERT INTO `kurse` (`kurs_id`, `name`, `studenten`, `fakultaet`, `created_at`) VALUES
(NULL, \'TMG17A\', 12, \'Technik\', \'2020-02-18 10:55:32\'),
(NULL, \'TIF-18-A\', 43, \'Technik\', \'2020-02-18 10:56:06\'),
(NULL, \'MGM-AM\', 14, \'Management\', \'2020-02-18 10:56:06\'),
(NULL, \'WWI12B-AM\', 20, \'Wirtschaft\', \'2020-02-18 10:56:06\'),
(NULL, \'WWI18B\', 15, \'Wirtschaft\', \'2020-03-09 10:56:29\');');

      $link->query('INSERT INTO `users` (`user_id`, `username`, `password`, `admin`, `blocked`, `vorname`, `nachname`, `unternehmen`, `created_at`) VALUES
        (NULL, \'a@a.a\', \'$2y$10$cFmr.JB.hM82s1ZFnGG8keU2pGoMI1kluFCLuueJ0Z7ow5DxvjRs6\', 0, 0, \'Andreas\', \'Alm\', \'\', \'2020-02-18 15:29:10\'),
(NULL, \'mritchie0@cnn.com\', \'b71dda489afc80eb136b78731e7e996b56ca9b486ecf756ca738438e11c2ea73\', 0, 0, \'Malia\', \'Ritchie\', \'\', \'2020-02-18 15:29:10\'),
(NULL, \'cmartinovic1@symantec.com\', \'e49e3b20ad840797105b8f43917c066a7400a3f9c6dcf3a894600883dea896db\', 1, 0, \'Chad\', \'Martinovic\', \'Quatz\', \'2020-02-18 15:29:10\'),
(NULL, \'dgobourn2@cbslocal.com\', \'294dc7f55560c00a7b13241c416e0815f4630ffd9f2ed62a3a4d5f12b5f81754\', 0, 0, \'Dolorita\', \'Gobourn\', \'Yata\', \'2020-02-18 15:29:10\'),
(NULL, \'nforgie3@amazonaws.com\', \'67977235bb1154d443b9c29b7fa9586ae039253813afc9bee01e5e1db2a87f35\', 0, 0, \'Nanete\', \'Forgie\', \'Twitterworks\', \'2020-02-18 15:29:10\'),
(NULL, \'mpock8@goodreads.com\', \'d1d0be7ebe4e36a11f72b497cbd73777afb3b80fb6fc4687cda4a6fcf070e642\', 1, 1, \'Margeaux\', \'Pock\', \'Jayo\', \'2020-02-18 15:29:10\'),
(NULL, \'mclewlowem@gov.uk\', \'35840644ebe7f4421fdbc63358a98ec1b99f442358732fcbe869fe94de6e294d\', 1, 0, \'Meaghan\', \'Clewlowe\', \'JumpXS\', \'2020-02-18 15:29:10\'),
(NULL, \'acanfieldt@squidoo.com\', \'059d873645c18b83cc059ea3faf562d777a5f9f0452e800ebc9de79fd341dad5\', 0, 0, \'Alix\', \'Canfield\', \'Skaboo\', \'2020-02-18 15:29:10\'),
(NULL, \'feadyu@youtube.com\', \'2f9113a80fb6ebbe22d715e7a3d50e96cb5ba070f79610ab10521851ea2a74cc\', 0, 0, \'Frances\', \'Eady\', \'Kayveo\', \'2020-02-18 15:29:11\'),
(NULL, \'edeinhardv@theglobeandmail.com\', \'c73ccea9dae69f8d66b80ce241ec7ff51e161428c15906ce455d23fea2ce920b\', 0, 1, \'Evelin\', \'Deinhard\', \'Zoovu\', \'2020-02-18 15:29:11\'),
(NULL, \'acrellimw@t.co\', \'2c4636e74b994d994b89af1d7634d5f72c85a0331734866c313c8ec2ba9c09d1\', 0, 0, \'Alasteir\', \'Crellim\', \'Youbridge\', \'2020-02-18 15:29:11\'),
(NULL, \'nvasser10@bizjournals.com\', \'4269407afeeb4aec5678b162ef4417d34abc5a0475b6e6fa7f10b4aaea78590c\', 0, 0, \'Nessa\', \'Vasser\', \'\', \'2020-02-18 15:29:11\'),
(NULL, \'lferraresi19@blogger.com\', \'8f905e422ef479483db3eb513be17eec84d3b3ec2a5291fc2171f9d7c86741a5\', 0, 1, \'Leslie\', \'Ferraresi\', \'\', \'2020-02-18 15:29:11\'),
(NULL, \'nlittefair1a@ocn.ne.jp\', \'d874267917fd6b99d447a6bd8f5b547e8f1077b094fe0cac9b7fd8dc64bc956b\', 0, 0, \'Natalie\', \'Littefair\', \'Agimba\', \'2020-02-18 15:29:11\'),
(NULL, \'mstansall1c@google.com.br\', \'a7a078f41a7e1540980bace987f118838456d18225a7b5756167a6a5de8123d1\', 0, 0, \'Michaelina\', \'Stansall\', \'Muxo\', \'2020-02-18 15:29:11\'),
(NULL, \'eezzle1d@list-manage.com\', \'e93f5a0b43b57af5a0b135c73b937e7fdeeb0f467f8d94a97f8caf04e815db79\', 0, 0, \'Ebeneser\', \'Ezzle\', \'Topiczoom\', \'2020-02-18 15:29:11\'),
(NULL, \'iyashnov1e@exblog.jp\', \'2623f6522c0b359c7b947baf7066b236f2f4620d4532cd081508db91ba5d0cfa\', 0, 0, \'Ivie\', \'Yashnov\', \'Podcat\', \'2020-02-18 15:29:11\'),
(NULL, \'mcobbin1g@squidoo.com\', \'87ba872fca78f06d13d8feda35da06bbaeef61ee9d0b8bea04b891aadb8ba993\', 0, 0, \'Mervin\', \'Cobbin\', \'Katz\', \'2020-02-18 15:29:11\');');
    }

    $notif = array(
      "SEND" => $notifications,
      "MAIN_ADMIN" => trim($_POST['email'])
    );

    $file = fopen('configuration/notifications.json', 'w');
    fwrite($file, json_encode($notif, JSON_PRETTY_PRINT));
    fclose($file);


    echo "<br>Installation wurde Abgeschlossen.<br><br>";

    echo '<form action="index.php?" method="GET"><input type="submit" value="Installation beenden"></form>';
    die();
  } else {
    echo '<br><form action="installscript.php" method="GET"><input type="submit" value="Installation beginnen"></form>';
    die();
  }


  ?>
</body>
