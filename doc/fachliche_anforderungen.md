# Inhaltliche Anforderungen an das Projekt

Die Gruppe 2 und die Gruppe 3 erstellen in enger Abstimmung Software für das Verwaltungen und Präsentieren der Exponate des Museums zur Entwicklung der Informations- und Kommunikationstechnik – kurz MEIK.
Die Gruppe 2 erstellt die Software zur öffentlichen Präsentation der Exponate, insb auf Tablets.

**Wir – die Gruppe 3 – erstellen die interne Verwaltungssoftware zum Pflegen der Exponate.**

Dazu sind wir im engen Austausch mit der Gruppe 2.

## Benutzerverwaltung

- Der Zugriff auf die interne Verwaltungssoftware ist mit einer Anmeldung durch Eingabe von Benutzername und Passwort geschützt.
- Es gibt Admin-Benutzer und normale Benutzer.
- Alle Benutzer können
  - ihr Passwort ändern
  - ihren Benutzernamen ändern
  - ihren Vor- und Nachnamen
  - neue Exponate anlegen
  - Informationen zu Exponanten ergänzen, ändern, teilweise löschen
  - Standort und damit Sichtbarkeits-Status der Exponate ändern
- Nur Admin-Benutzer können
  - neue Benutzer anlegen
  - Benutzer löschen
  - Exponate löschen
  - Standorte und Plätze anlegen, ändern, löschen
    - Löschen nur wenn sich kein Exponat mehr an diesem Platz befindet

## Exponante

Die Exponate sind die zentralen Entitäten der Anwendung.
Zu Exponanten _müssen stets_ die folgenden Einzelinformationen erfasst werden (Stammdaten)
- _intern:_ eineindeutige Inventar-Nummer
- _öffentlich:_ eineindeutige Bezeichnung
- _öffentlich:_ Rubrik (bestimmt auch die Kategorie)
- _öffentlich oder intern:_ Standort (bestimmt den Status der öffentlichen Zugänglichkeit)
  - Platz (z.B. Vitrine, Regal)
- _öffentlich:_ (konkretes) Baujahr (Gerätschaften) bzw. Erscheinungsdatum (Software und Bücher)
- _öffentlich:_ Hersteller (bei Gerätschaften und Software)
- _öffentlich:_ Verlag (bei Büchern)
- _öffentlich:_ Autor(en) (bei Büchern)
- _intern:_ Zugangsdatum
- _öffentlich:_Art des Besitzes (Eigentum, Leihgabe, Miete, _intern:_ nicht mehr im Besitz)
- _intern:_ Eigentümer (Name, Anschrift, ggf. Geburtsdatum) (nur bei Leihgabe oder Miete)
- _intern:_ Verbleib (nur wenn Art des Besitzes = nicht mehr im Besitz)
- _intern:_ Zeitwert (Zudem wird das Änderungsdatum automatisch erfasst.)

Zu den Exponaten _können_ darüber hinaus folgende Einzelinformationen erfasst werden
- _intern:_ Art des Zugangs (neu bei Eigentum: Schenkung, Kauf, Fund; bei Leihgabe oder Miete: Überlassung)
- _intern:_ Entgegennehmer (Benutzer, welcher das Exponate für das Museum annahm)
- _intern:_ unmittelbarer Voreigentümer (Name, Anschrift, ggf. Geburtsdatum) (nur bei Schenkung, Kauf)
- _intern:_ Anschaffungspreis (nur wenn Art der Übernahme = Kauf)
- _öffentlich:_ Bauzeit (gebaut von ... bis ...)
- _öffentlich:_ Originalpreis mit Währung
- _öffentlich:_ Maße: Höhe, Tiefe, Breite
- _öffentlich:_ Gewicht
- _öffentlich:_ Verknüpfung zu anderen Exponaten
- _intern:_ zur Zeit zur Restauration bei ...

Die meisten Informationen zu den Exponaten werden in Freitextfeldern erfasst. Ein Nutzer kann beliebige und beliebig viele Freitextfelder zu einem Exponat anlegen. Neben dem Titel muss für jedes Freitextfeld angegeben werden, ob dieses öffentlich sichtbar sein soll.
Zum Beispiel sind folgende _öffentliche_ Freitextfelder denkbar:
- Kurzbeschreibung
- Beschreibung
- Technik
- Materialien
- Zubehör
- Erhaltungszustand
- Provienz
- Handhabung
- Literatur
Zum Beispiel sind folgende _interne_ Freitextfelder denkbar:
- Defekte
- Aufgaben (erledigte und zu erledigende)
- interne Bemerkungen

Zudem können Bilder der Exponate hinterlegt werden. Auch diese können öffentlich oder intern sein.

## Standorte und Plätze

Für jedes Exponate wird erfasst, wo genau es sich gerade befinden soll.
Dazu werden der Raum und der konkrete Platz innerhalb dieses Raumes erfasst.
Ein Platz kann z.B eine bestimmte Vitrine oder ein bestimmtes Regal sein.

Ein Standord kann öffentlich zugänglich sein oder ein Depot sein.

_Wenn es der Standort des Exponates öffentlich zugänglich ist,
dann gilt auch das Exponat als öffentlich zugänglich._

## Logging (nur intern)

Zur Nachvollziehbarkeit wird automatisch erfasst, welcher Benutzer an welchem Tag eine bestimmte Information hinzugefügt, gelöscht oder geändert hat.

## REST-API für die Präsentations-Software

Der unidirektionale Datenaustausch von der Verwaltungssoftware zur Präsentationssoftware erfolgt über eine REST-API.

Die genaue Schnittstellen-Beschreibung der REST-API wird direkt mit der Gruppe 2 abgestimmt.

Über die REST-API können nur _öffentlich zugängliche_ Exponate abgerufen werden.
Zu diesen können auch nur _allgemein öffentliche oder als öffentlich markierte_ Informationen übergeben werden.

## Datenblatt erstellen

Für Restaurierungs- und Reparaturaufträge kann ein internes Datenblatt mit den relevanten Informationen erstellt werden. Es enthält vorwiegend die Angabe zum Hersteller und technische/physikalische Angaben

## Barcode scannen (optional)

Mittels eines Barcode-Scanners können physisch vorliegende Exemplare gescannt werden und anhand dessen Informationen zu den Exponaten angezeigt werden.

Beim Einpflegen neuer Exponate kann der Barcode-Scanner verwendet werden, um die Inventar-Nummer automatisch zu erfassen.

## Frontend / Design

- Optimierung für die Größe eines Laptop-Monitores und größere Bildschirme

## Nicht-Funktonale Anforderungen

- Zugriff nur über HTTPS
- Zugriff erstmal nur aus dem Schulnetz heraus
