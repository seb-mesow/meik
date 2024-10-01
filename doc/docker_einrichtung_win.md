# Einrichtung von Docker auf Windows 10

Wir benutzen erstmal das WSl 2-Backend (statt dem Hyper-V-Backend).

[empfohlenes Youtube-Video](https://www.youtube.com/watch?v=rATNU0Fr8zs)

1. _empfohlen:_ Docker-Account einrichten
   - Vorteil: mehr Images-Pulls möglich
2. Daten backup
3. sicherstellen, dass mindestens 10 GB Festspeicher auf dem Hauptlaufwerk C:\ frei sind.
4. reboot
5. Virtualisierungs-Extension der CPU im UEFI (BIOS) einstellen
6. reboot
7. bestimmte "Windows-Features" aktivieren
    - _Windows Subsystem for Linux_
    - _Hyper-V:_
      - _Hyper-V Management Tools_
        - _Hyper-V Module for Windows Powershell_
      - _Hyper-V Platform_
        - _Hyper-V Services_
8. reboot
9.  Docker Desktop mit Adminrechten installieren
10. Docker Desktop konfigurieren
11. WSL über Datei `%USERPROFIE%\.wslconfig` konfigurieren
12. reboot
13. _empfohlen: mit Docker-Account in in Docker Desktop anmelden

**bei Problemen Sebastian anrufen!**

## Vorlage für `.wslconfig`

```
# Settings apply across all Linux distros running on WSL 2
[wsl2]

# Limits VM memory to use no more than 4 GB, this can be set as whole numbers using GB or MB
memory=3GB

# Sets the VM to use two virtual processors
processors=1

# Specify a custom Linux kernel to use with your installed distros. The default kernel used can be found at https://github.com/microsoft/WSL2-Linux-Kernel
# kernel=C:\\temp\\myCustomKernel

# Sets additional kernel parameters, in this case enabling older Linux base images such as Centos 6
# kernelCommandLine = vsyscall=emulate

# Sets amount of swap storage space to 8GB, default is 25% of available RAM
swap=3GB

# Sets swapfile path location, default is %USERPROFILE%\AppData\Local\Temp\swap.vhdx
# swapfile=C:\\temp\\wsl-swap.vhdx

# Disable page reporting so WSL retains all allocated memory claimed from Windows and releases none back when free
# pageReporting=false

# Turn on default connection to bind WSL 2 localhost to Windows localhost. Setting is ignored when networkingMode=mirrored
# localhostforwarding=true

# Disables nested virtualization
# nestedVirtualization=false

# Turns on output console showing contents of dmesg when opening a WSL 2 distro for debugging
# debugConsole=true

# Enable experimental features
[experimental]
sparseVhd=true
autoMemoryReclaim=gradual
```

# Docker - Hilfe

## Größe Festplattenspeicher Docker-VM verringern
- gilt für Windows 10

**Einrichtung**
1. als Administrator einloggen
1. einige Windows-Features aktivieren
    - Hyper-V Module for Windows Powershell
    - Hyper-V Services (nur wenn es nicht ohne funktioniert)
2. neustarten

**Verkleinern (immer wieder)**
1. eine Powershell als Administrator öffnen
2. `Optimize-VHD -Path $ENV:LOCALAPPDATA\Docker\wsl\disk\docker_data.vhdx -Mode Full`

## nicht mehr Adminpasswort eingeben müssen

siehe [dockeraccesshelper](https://github.com/tfenster/dockeraccesshelper)

0. als normaler Benutzer abmelden
1. als Administrator anmelden
2. Powershell
```powershell
Install-Module -Name dockeraccesshelper
# Fragen mit "Y" bestätigen
Set-ExecutionPolicy -Scope Process -ExecutionPolicy RemoteSigned
Import-Module dockeraccesshelper
Add-AccountToDockerAccess "COMPUTERNAME\Benutzername"
```

## Beobachtungen
- Die Performance von PHP steht und fällt mit der Performance Festplatte zu Docker-VM.<br>
da bei jeder Request die Metadaten der nötigen PHP-Dateien (je nach Einstellung des OpCache)
alle x Sekunden abgefragt werden.<br>
(Beweis: Repo in ein Docker-Volume kopieren. Das ist wesentlich schneller.)
- Die Performance von PHP steht und fällt mit der SSD-Performance<br>
(Beweis: Auf meinem Arbeits-Laptop sind unsere Docker-Anwendungen bei gleichen Einstellungen schneller.)
