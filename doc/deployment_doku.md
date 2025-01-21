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
3. `git clone https://github.com/seb-mesow/meik.git`
4. `cd meik`
5. `docker/.env.dist` nach `docker/.env` kopieren
6. User-ID des aktuellen Nutzers ermitteln: `id`
7. in der `docker/.env` die User-ID des aktuellen Nutzer als der Wert der Variablen `NORMAL_UID_GID` angeben
8. ggf. ausloggen und mit root-Rechten im System anmelden
9.  `./meik up`

## System herunterfahren
`./meik down`
