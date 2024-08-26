# MEIK

## Links zur Anwendung

- [CouchDB-GUI](http://localhost:5984/_utils)

## Einrichtung

https://linuxcapable.com/how-to-install-php-on-linux-mint/

- folgende VS Code Extensions installieren
  - PHP von DEVSENSE
  - PHP Debug von Xdebug
  - optional: [Docker von Microsoft](https://code.visualstudio.com/docs/containers/overview)

- **Es ist _zur Zeit_ nicht möglich mit Windows von VS Code in einem Docker-Container ein PHP-Skript zu starten.**<br>Dies geht nur über die Kommandozeile.

## Tech-Stack

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
