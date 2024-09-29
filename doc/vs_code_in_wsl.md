# VS Code als Remote IDE zu WSL, Projekt direkt in einer Linux WSL VM

Ziel: schnellerer Dateizugriff

Weg:
- Projekt in Linux WSL VM clonen
- in dieser oder einer zweiten Linux WSL VM läuft Docker
- VS Code auf dem Windows Host kommuniziert mit dem VS Code Server in der Linux VM

Ergebnis: Ziel erfüllt

**`USERNAME` durch einen eigenen Namen ersetzen, z.B. `sebastian`**

## 1. Ubuntu-Distribution in WSL installieren

1. in Powershell oder Windows CMD:
```powershell
wsl --install -d Ubuntu-24.04
...
Enter new UNIX username: USERNAME
New passwort: USERNAME
...
exit

wsl --set-default Ubuntu-24.04
```
2. neustarten
3. in Powershell oder Windows CMD:
```powershell
wsl --manage Ubuntu-24.04 --set-sparse true
wsl --manage docker-desktop --set-sparse true
```

## 2. Ubuntu-VM konfigurieren

### 2.1 updaten
in Powershell oder Windows CMD
```powershell
wsl -u root
```
darin
```bash
apt-get update
```

### 2.2 Default User einstellen
in Powershell oder Windows CMD
```powershell
wsl -u root
```
darin
```bash
nano /etc/wsl.conf
```
Es öffnet sich der Editor nano.
Dort folgendes _ergänzen_:
```
[user]
default = USERNAME
```
Dies ist der Default-User mit dem der VS Code Server in der VM läuft. (wichtig für Dateirechte)

### 2.3 Ubuntu-VM neustarten
in Powershell oder Windows CMD
```powershell
wsl --terminate Ubuntu-24.04
```

mind. 10 Sekunden warten

## 3. In VS Code WSL Extension installieren und aktivieren

1. WSL Extension installieren und aktivieren<br>
2. WSL Extension aktivieren
3. VS Code neustarten

## 4. Projekt in Ubuntu-VM clonen oder kopieren

In Powershell oder Windows CMD
```powershell
wsl
```
darin
```bash
cd
git clone https://github.com/seb-mesow/meik.git
```

## 5. VS Code starten

1. Vs Code in Windows Host öffnen
2. Sidebar -> WSL Extension
3. bei _Ubuntu-24.04_ den Pfeil _->_ klicken
4. (VS Code startet sich neu)

## 6. Projekt in Ubuntu-VM konfigurieren

siehe [normale Einrichtung](../README.md)
- wichtig dabei das Kopieren und Anpassen der `.bashrc`
- wichtig dabei das Kopieren und Anpassen der `docker/compose.override.yml`

## 7. Windows Terminal Profil einrichten

- Befehlszeile: `wsl -d Ubuntu-24.04 --cd ~/meik --exec bash --init-file .bashrc`
- Icon: `https://assets.ubuntu.com/v1/49a1a858-favicon-32x32.png`
- Startverzeichnis: nicht angeben

