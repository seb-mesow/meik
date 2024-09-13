# VS Code als Remote IDE zu WSL, Projekt direkt in einer Linux WSL VM

Ziel: schnellerer Dateizugriff

Weg:
- Projekt in Linux WSL VM clonen
- in dieser oder einer zweiten Linux WSL VM läuft Docker
- VS Code auf dem Windows Host kommuniziert mit dem VS Code Server in der Linux VM

## 1. Ubuntu-Distribution in WSL installieren

1. Powershell oder Windows CMD:
```powershell
wsl --install -d Ubuntu-24.04
...
Enter new UNIX username: sebastian
New passwort: sebastian
...
exit

wsl --set-default Ubuntu-24.04
```
2. neustarten
3. Powershell oder Windows CMD:
```powershell
wsl --manage Ubuntu-24.04 --set-sparse true
wsl --manage docker-desktop --set-sparse true
```

## 2. Ubuntu-VM konfigurieren

In Powershell oder Windows CMD
```powershell
wsl -u root
```
darin
```bash
apt-get update
```

## 3. Projekt in Ubuntu-VM clonen oder kopieren

1. Windows Explorer öffnen
2. Projekt-Ordner kopieren
2. In der Adress-zeile `\\wsl$\Ubuntu-24.04\home\sebastian` eingeben und Enter

## 3. In VS Code WSL Extension installieren und aktivieren

1. WSL Extension installieren und aktivieren<br>
2. WSL Extension aktivieren
3. VS Code neustarten