# MEIK

starten mit
  0. Docker starten
  1. `drb` ("docker restart build")
  2. `npm run dev` (Vite Autoupdater/Hot Module Replacement starten)

[Web-App öffnen](http://localhost:8080)

[CouchDB-GUI](http://localhost:5984/_utils)

## Einrichtung

https://linuxcapable.com/how-to-install-php-on-linux-mint/

**[Einrichtung von Docker auf Windows](doc/docker_einrichtung)**
- **[Performance-Verbesserung durch Projekt in Ubuntu-VM](doc/vs_code_in_wsl.md)**

### Empfehlungen für Windows
1. Windows-Terminal installieren
2. Git für Windows (erneut) installieren
   - Dabei unbedingt auch Git-Bash installieren
3. MEIK-spezifisches Git-Bash-Profil in Windows-Terminal einrichten

### Einrichtung für alle
1. VS Code einrichten:
   1. _Git: Allow Force Push_ aktivieren<br>
      Restliche Einstellungen zu Force-Pushing auf default belassen.
2. folgende VS Code Extensions installieren:
   1. PHP von DEVSENSE
   2. PHP Debug von Xdebug
   3. _optional:_ [Docker von Microsoft](https://code.visualstudio.com/docs/containers/overview)
3. folgendes Firefox-Addon installieren:
   1. Xdebug Helper for Firefox von BrianGilbert
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
   2. mit den Aliasen in `.bashrc` vertraut machen. Sie beschleunigen das Arbeiten in der Kommandozeile enorm.
   3. `.bashrc` anpassen
8. `compose.override.yml`
   0. im Unterordner `docker`
   1. `compose.override.dist.yml` zu `compose.overide.yml` kopieren
   2. `compose.override.yml` anpassen
9. `.env`
   1. `.env.example` zu `.env` kopieren
10. `laravel.log`
   1. `rm -f storage/logs/laravel.log`
   2. `touch storage/logs/laravel.log`
   3. `chmod 0666 storage/logs/laravel.log`
11. `drb` ("docker restart build")
12. `ci` ("composer install")
13. `npm ci` (JS/TS-Abhängigkeiten aus `packages.lock` installieren)
14. `php src/Scripts/CreateDBUsersScript.php`

**Es ist _zur Zeit_ nicht möglich mit Windows von VS Code in einem Docker-Container ein PHP-Skript zu starten.**<br>Dies geht nur über die Kommandozeile.

## Tech-Stack
- VS Code zur Entwicklung
- Bash als Shell
- Backend:
  - CouchDB 3
  - [PHPOnCouch](https://php-on-couch.readthedocs.io)
  - Laravel 11
  - PHP 8.3
  - composer
- Frontend
  - TypeScript (mit ES Modules -> `import` und `export`)
  - Node ???
  - npm ???
  - PrimeVue
  - Vue.js

- Figma für GUI-Mockups

- Docker für Virtualisierung

- Ubuntu für Produktiv-Umgebung

## Konventionen

### Variablen und Klassenschreibweisen
- `snake_case` für Variablen und Funktionen
- `PascalCase` für Klassen und Interfaces

### Branch-Konzept
- `main` ist der einzige Haupt-Branch
- davon zieht sich jeder eigenen Entwicklungs-Branch
- in den `main`-Branch wird zunächst nur per vorherigem Rebase
- Git-Tags für funktionierende Versionen

### Quelltext

- Quellcode in Englisch
  - Kommentare egal

- Nicht-Quellcode-Dokumentation in Deutsch

- Tabs statt Spaces
- LF als Zeilenende
