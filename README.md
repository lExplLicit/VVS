# VVS
[![Generic badge](https://img.shields.io/badge/Version-1.0-green.svg)](#) [![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/lExplLicit/VVS/blob/master/LICENCE) [![Maintenance](https://img.shields.io/badge/Maintained%3F-no-red.svg)](https://github.com/lExplLicit/VVS)

Vorlesungs Verwaltungs System im Rahmen der Vorlesung "Web-Development".
Wurde nur auf Ubuntu 18.04 (Digitalocean Droplet) getestet. Repository wird nicht weiter gepflegt.

[Bild](https://github.com/lExplLicit/VVS/raw/master/assets/screenshot_vvs.png)

## Installation

### Das wird benötigt

* Docker und Docker-Compose

oder

* WebServer (Apache, Nginx)
* PHP (7.3)
* MySQL Datenbank
* git, zip, unzip, wget

### Docker installation

1. Die benötigten Dockerfiles als zip herunterladen, entpacken und Benutzerrechte anpassen:
```console
wget https://github.com/lExplLicit/VVS/raw/master/docker/VVS_Docker.zip
unzip VVS_Docker.zip
chown -R www-data vvs_data/config/
```
2. Docker Compose ausführen:
```console
docker-compose up -d
```
3. Anweisungen befolgen:
```console
Webbrowser öffnen (http://localhost/) und auf 'Zum Login' klicken.
Den Anweisungen im Installer folgen. Zugangsdaten zur Datenbank sind bereits ausgefüllt.
Die Zugangsdaten der Datanbank findet man in der docker-compose-yml.
Administratoraccount anlegen.
```
### Manuelle Installation

1. Dieses Repository herunterladen und in den Webroot Ordner kopieren und gegebenfalls Ordner (in 'VVS') umbenennen:
```console
https://github.com/lExplLicit/VVS/archive/master.zip
```
2. Installation starten:
```console
Im Webbrowser den Ordner VVS öffnen. (http://localhost/VVS)
```
3. Anweisungen befolgen:
```console
Den Anweisungen im Installer folgen.
Es wird eine externe Datenbank benötigt.
(Datenbank verbinden und Admin Account anlegen)
Wenn bei der Installation Fehler auftreten kann es daran liegen, dass die Benutzerrechte des Ordners 'configuration' angepasst werden müssen.
```



