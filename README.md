# MEIK

- [MEIK](#meik)
	- [Starten der Web-App](#starten-der-web-app)
	- [nach Rebasen](#nach-rebasen)
	- [Einrichtung](#einrichtung)
		- [Einrichtung für alle](#einrichtung-für-alle)
		- [Empfehlungen für Windows](#empfehlungen-für-windows)
	- [Tech-Stack](#tech-stack)
	- [Konventionen](#konventionen)
		- [Variablen und Klassenschreibweisen](#variablen-und-klassenschreibweisen)
		- [Quelltext](#quelltext)
		- [Branch-Konzept](#branch-konzept)
			- [Rebasen](#rebasen)
			- [Backup wieder herstellen](#backup-wieder-herstellen)


## Starten der Web-App
0. Docker (Engine) im Host starten
1. `drb` ("docker restart build")
2. ggf. Schritte wie auch nach Rebases
3. [Web-App öffnen (HTTP)](http://meik.localhost:8080)
4. Im Freifox Cache deaktivieren !

[CouchDB-GUI](http://couchdb.localhost:5984/_utils)

[PhpMyAdmin](http://phpmyadmin.localhost:8081)

## nach Rebasen
1. `drb`
2. `ci`
3. `npm ci`
4. `artisan migrate:fresh --seed`
5. `artisan ziggy:generate --types resources/js/ziggy/ziggy.js`

## Einrichtung

https://linuxcapable.com/how-to-install-php-on-linux-mint/

**[Einrichtung von Docker auf Windows](doc/docker_einrichtung)**
- **[Performance-Verbesserung durch Projekt in Ubuntu-VM](doc/vs_code_in_wsl.md)**

### Einrichtung für alle
1. VS Code einrichten:
	1. _Git: Allow Force Push_ aktivieren<br>
		Restliche Einstellungen zu Force-Pushing auf default belassen.
2. folgende VS Code Extensions installieren:
	1. _PHP_ von _DEVSENSE_
	2. _PHP Debug_ von _Xdebug_
	3. _Vue - Offical_ von _Vue_
	4. _optional:_ _GitLens — Git supercharged_ von _GitKraken_
	5. _optional:_ [_Docker_ von _Microsoft_](https://code.visualstudio.com/docs/containers/overview)
3. folgendes Firefox-Addon installieren:
	1. _Xdebug Helper for Firefox_ von _BrianGilbert_
	2. Firefox schließen und neustarten
4. `git clone https://github.com/seb-mesow/meik.git`
5. `cd meik`
6. Git-Repo
	1. checken, ob nicht schon gesetzt: `git config user.name`
	2. dann ggf: `git config user.name 'GITHUB_USERNAME'`
	3. checken, ob nicht schon gesetzt: `git config user.email`
	4. dann ggf: `git config user.email 'GITHUB_MAIL_ADRESSE'`
		- siehe GitHub -> Account -> Settings -> Emails -> Primary email address
		- sieht z.B. so aus `12345678+username@users.noreply.github.com`
7. `.bashrc`
	1. `.bashrc.dist` zu `.bashrc` kopieren
	2. in der `.bashrc` die Variable `REPO_DIR` anpassen (siehe dort)
	3. mit den Aliasen in `.bashrc` vertraut machen. Sie beschleunigen das Arbeiten in der Kommandozeile enorm.
	4. `.bashrc` anpassen
	5. Eigenes Terminal so einstellen, dass _diese_ `.bashrc` geladen wird.
		- _Beispiel_-Befehl für Einstellung des Terminals: `bash --init-file /home/username/meik/.bashrc`
		- Dafür am besten ein separates Profil anlegen.
8.  `id www-data`, sollte u.A. `gid=33` ausgeben (sonst eine weitere Gruppe mit der GID `33` anlegen)
9.  `sudo usermod -a -G www-data USERNAME`<br>
	(`USERNAME` durch Benutzernamen im Host ersetzen)
10. im Unterordner `docker`
	1. `.env.dist` zu `.env` kopieren
	2. `.env` anpassen
	3. eine leere Datei `compose.dev.override.yml` anlegen
11. `.env` (in der Wurzel des Repos)
	1. `.env.example` zu `.env` kopieren
	2. sicherstellen, dass folgende Variablen wie folgt gesetzt sind:
		1. `APP_ENV=local`
		2. `APP_DEBUG=true`
12. im Unterordner `.vscode`
	1. `launch.dist.json` zu `launch.json` kopieren
	2. `settings.dist.json` zu `settings.json` kopieren
13. `drb` ("docker restart build")
14. `ci` ("composer install")
	- (notfalls als Ersatz: `docker_compose_run_normal app composer install`)
15. `npm ci` (JS/TS-Abhängigkeiten aus `packages.lock` installieren)
	- (notfalls als Ersatz: `docker_compose_run_normal node npm ci`)
17. Computer oder VM neustarten
18. `drb`
19. `artisan migrate`
20. `artisan optimize:clear`
21. `artisan key:generate`
22. `artisan ziggy:generate --types resources/js/ziggy/ziggy.js`
23. `artisan db:seed`

**Es ist _zur Zeit_ nicht möglich mit Windows von VS Code in einem Docker-Container ein PHP-Skript zu starten.**<br>Dies geht nur über die Kommandozeile.

### Empfehlungen für Windows
1. Windows-Terminal installieren
2. Git für Windows (erneut) installieren
	- Dabei unbedingt auch Git-Bash installieren
3. MEIK-spezifisches Git-Bash-Profil in Windows-Terminal einrichten

## Tech-Stack
- Backend:
  - [CouchDB 3](https://docs.couchdb.org/en/stable/)
  - [PHPOnCouch 4.0](https://php-on-couch.readthedocs.io)
  - [Laravel 11](https://laravel.com/docs/11.x)
  - [PHP 8.3](https://www.php.net/manual/en/)
  - [Composer 2.7](https://getcomposer.org/doc/)
- Frontend
  - [TypeScript](https://www.typescriptlang.org/docs/) (mit ES Modules -> `import` und `export`)
  - [Node 20.17](https://nodejs.org/docs/latest-v20.x/api/index.html)
  - [npm 10.8](https://docs.npmjs.com/)
  - [Vue.js](https://vuejs.org/)
  - [PrimeVue](https://primevue.org/)
- Environment
  - [Visual Studio Code](https://code.visualstudio.com/docs) als IDE
  - [Bash](https://www.gnu.org/savannah-checkouts/gnu/bash/manual/bash.html) als Shell
  - [Docker](https://docs.docker.com/) für die lokale Entwicklungs-Umgebung
    - [Nginx](https://nginx.org/en/docs/) — Abschnitt _Modules reference_
  - Ubuntu-VM (mit Docker?) als Produktiv-Umgebung
- Figma für GUI-Mockups

## Konventionen

### Variablen und Klassenschreibweisen
- `snake_case` für Variablen und Funktionen
- `PascalCase` für Klassen und Interfaces

### Quelltext

- Quellcode in Englisch
  - Kommentare egal

- Nicht-Quellcode-Dokumentation in Deutsch

- Tabs statt Spaces
- LF als Zeilenende

### Branch-Konzept
- `main` ist der einzige Haupt-Branch.
- Davon zieht sich jeder seinen eigenen Entwicklungs-Branch.
- In den `main`-Branch wird zunächst nur per vorherigem Rebase "gemergt".
- Git-Tags für funktionierende Versionen

- **Es gibt keinen Grund jemals ein `git pull` zu machen!**

#### Rebasen
Empfehlung: jeden Tag einmal machen

1. alle zwischenzeitlichen Änderungen commiten oder stashen
2. `gb` (eigenen Feature-Branch backupen)
3. `gf` (alle Branches aktualisieren)
4. in VS Code: rebasen und zwar auf `origin/main` (nicht `main`!)
5. (`gp`)

#### Backup wieder herstellen
```bash
# Name des Backup-Branches ermitteln (z.B. images_in_db_backup_2024-11-27_18-10-35)
git branch
# Backup-Branch auschecken
git checkout images_in_db_backup_2024-11-27_18-10-35
# alten normalen Branch löschen
git branch -D images_in_db
# neuen normalen Branch aus Backup-Branch erstellen
git checkout -b images_in_db
```
