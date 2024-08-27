# MEIK

## Links zur Anwendung

- starten mit
  1. `drb`
  2. `npm run dev` 
- [CouchDB-GUI](http://localhost:5984/_utils)

## Einrichtung

https://linuxcapable.com/how-to-install-php-on-linux-mint/

### Empfehlungen für Windows
1. Windows-Terminal installieren
2. Git-Bash installieren
3. MEIK-spezifisches Git-Bash-Profil in Windows-Terminal einrichten

### Einrichtung für alle
1. VS Code einrichten
   1. _Git: Allow Force Push_ aktivieren<br>
      Restliche Einstellungen zu Force-Pushing auf default belassen.
2. folgende VS Code Extensions installieren
   1. PHP von DEVSENSE
   2. PHP Debug von Xdebug
   3. _optional:_ [Docker von Microsoft](https://code.visualstudio.com/docs/containers/overview)
3. folgendes Firefox-Addon installieren
   1. Xdebug Helper for Firefox von BrianGilbert_
   2. Firefox schließen und neustarten
4. `git clone https://github.com/seb-mesow/meik.git`
5. `cd meik`
6. `.bashrc.dist` zu `.bashrc` umkopieren
7. `.bashrc` anpassen
8. `drb`
9.  `ci`
10. `npm ci`

**Es ist _zur Zeit_ nicht möglich mit Windows von VS Code in einem Docker-Container ein PHP-Skript zu starten.**<br>Dies geht nur über die Kommandozeile.

## Tech-Stack
- VS Code zur Entwicklung
- Bash als Shell
- Backend:
  - linuxartig
  - CouchDB 3
  - Laravel 11
  - PHP 8.3
  - composer
- Frontend
  - TypeScript (mit ES Modules (import und export)
  - Node ???
  - npm ???
  - Bootstrap?
  - vue.js

- Figma für GUI-Mockups

- Docker für Virtualisierung


## Konventionen

### Variablen und Klassenschreibweisen
- snake_case für Variablen und Funktionen
- PascalCase für Klassen

### Branch
- main ist der einzige Haupt-Branch
- davon zieht sich jeder eigenen Entwicklungs-Branch
- in den main-Branch wird zunächst nur per vorherigem Rebase
- Git-Tags für funktionierende Versionen

- Quellcode in Englisch
  - Kommentare egal

- Nicht-Quellcode-Dokumentation in Deutsch

- Tabs statt Spaces

# Docker - Hilfe

## Größe Festplattenspeicher Docker-VM verringern

- gilt für Windows-8
- alles als Administrator

1. einige Windows-Features aktivieren
    - Hyper-V Module for Windows Powershell
    - Hyper-V Services (nur wenn es nicht ohne funktioniert)
2. neustarten
3. eine Powershell öffnen
4. `Optimize-VHD -Path $ENV:LOCALAPPDATA\Docker\wsl\disk\docker_data.vhdx -Mode Full`

## Beobachtungen
- Die Performance von PHP steht und fällt mit der Performance Festplatte zu Docker-VM.<br>
da bei jeder Request die Metadaten der nötigen PHP-Dateien (je nach Einstellung des OpCache)
alle x Sekunden abgefragt werden.<br>
(Beweis: Repo in ein Docker-Volume kopieren. Das ist wesentlich schneller.)
- Die Performance von PHP steht und fällt mit der SSD-Performance<br>
(Beweis: Auf meinem Arbeits-Laptop sind unsere Docker-Anwendungen bei gleichen Einstellungen schneller.)
