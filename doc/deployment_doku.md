# Betriebs-Handbuch

## Voraussetzungen
1. nur auf GNU/Linux-basiertem Betriebssystem
2. bash-Shell verfügbar
3. Git installiert
4. Docker installiert
	1. [Docker installieren](https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository)
	2. [Docker konfigurieren](https://docs.docker.com/engine/install/linux-postinstall/)

## Installation
1. Zugang zum Git-Repo bei Sebastian Mesow beantragen
2. mit einem **harmlosen** Nutzer in das System einloggen
3. den harmlosen Nutzer zur Gruppe www-data hinzufügen:
   - Ubuntu: `sudo usermod -a -G www-data USERNAME`
4. `git clone https://github.com/seb-mesow/meik.git`
5. `cd meik`
6. `docker/.env.dist` nach `docker/.env` kopieren:<br>
   `cp docker`<br>
   `cp -T .env.dist .env`
7. User-ID des aktuellen Nutzers ermitteln:<br>
   `id`
8. in der `docker/.env` die User-ID des aktuellen Nutzer als der Wert der Variablen `NORMAL_UID_GID` angeben:<br>
   `nano .env`
9.  Inhalt der `docker/.env` kontrolliern:<br>
   `cat .env`<br>
10. TLS-zertifikate und das private Key-File in `docker/certificates` abspeichern:<br>
   `scp filename.cer user@host:/path/to/meik/docker/certificates`<br>
   `scp filename.key user@host:/path/to/meik/docker/certificates`<br>
11. Dateirechte einschränken - aber auch andere Nutzer brauchen Zugriff :<br>
    `chmod u=r,g=r,o=r certificates/filename.cer`<br>
    `chmod u=r,g=r,o=r certificates/filename.key`<br>
    **TODO Zertifikate doch mit in web-Image rein bauen**<br>
    `ls -al certificates`
12. `docker/compose.prod.override.dist.yml` nach `compose.prod.override.yml` kopieren:<br>
    `cp -T compose.prod.override.dist.yml compose.prod.override.yml`
13. in der `docker/compose.prod.override.yml`<br>
    den Dateinamen des TLS-zertifiaktes als Wert der Variablen `CERTIFICATE_FILENAME`<br>
    und den Dateinamen des privaten Key-Files als Wert der Variablen `PRIVATE_KEY_FILENAME` angeben:<br>
    `nano compose.prod.override.yml`
14. Inhalt der `docker/compose.prod.override.yml` kontrollieren:<br>
    `ls -al certificates`<br>
    `cat compose.prod.override.yml`<br>
    `cd ..`
15. ggf. ausloggen und mit root-Rechten im System anmelden (wegen Ausführung von Docker und Port-Belegung)
16. `./meik up`

## System herunterfahren
`./meik down`

## Updaten
**Großes TODO: Daten exportieren und importieren (Backup)**
1. ggf. mit root-Rechten einloggen
2. `./meik down`
3. ggf. mit **harmlosem** Nutzer einloggen
4. `git pull`
5. ggf. mit root-Rechten einloggen
6. `./meik up`
