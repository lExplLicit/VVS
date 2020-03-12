<?php


if ($_GET['error'] == 1) {
    echo '<div class="card card-danger"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Es ist ein Fehler aufgetreten! </p></div></div><div class="card-block"><p>Der letzte Adminstrator kann nicht blockiert oder gelöscht werden.</p></div></div>';
}

if ($_GET['error'] == 2) {
    echo '<div class="card card-danger"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Es ist ein Fehler aufgetreten! </p></div></div><div class="card-block"><p>Der Beginn einer Vorlesungsstunde muss vor dem Ende liegen.</p></div></div>';
}
if ($_GET['error'] == 5) {
    echo '<div class="card card-danger"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Es ist ein Fehler aufgetreten! </p></div></div><div class="card-block"><p>Das angagebene Datum liegt ausserhalb der Vorlesungszeit.</p></div></div>';
}
if ($_GET['error'] == 6) {
    echo '<div class="card card-danger"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Es ist ein Fehler aufgetreten! </p></div></div><div class="card-block"><p>Beim Löschen ist ein fehler aufgetreten. Bitte erneut versuchen.</p></div></div>';
}
if ($_GET['error'] == 7) {
    echo '<div class="card card-danger"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Es ist ein Fehler aufgetreten! </p></div></div><div class="card-block"><p>Diese Vorlesung existiert nicht oder Sie dürfen diese Vorlesung nicht bearbeiten.</p></div></div>';
}

if ($_GET['success'] == 1) {
    echo '<div class="card card-success"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Erfolg! </p></div></div><div class="card-block"><p>Der Vorgang wurde erfolgreich durchgeführt.</p></div></div>';
}

if ($_GET['success'] == 2) {
    echo '<div class="card card-success"><div class="card-header"><div class="header-block"><p style="color:white" class="title">  Zugang wurde erfolgreich beantragt! </p></div></div><div class="card-block"><p>Ihre Anfrage wird schnellstmöglich vom Sekretariat behandelt. Sie erhalten nach erfolgreicher Freischaltung eine Email mit Ihren Zugangsdaten. <br>(Diese Funktion ist noch nicht aktiv)</p></div></div>';
}
