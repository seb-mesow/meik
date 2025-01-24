# Bilder von Exponaten

Das erste Bild ist immer das Titelbild.

## Use Cases
- Bilder ansehen
- Bild hochladen
- Bild austauschen
- Bildbeschreibung angeben
- Bild als öffentlich/intern markieren
- Reihenfolge der Bilder verändern
- Bild löschen

- Ein Bild als Thumbnail bekommen
- Ein Bild im Original bekommen
- Alle Image-IDs von öffentlichen Bildern mit Beschreibung und Maßen in Reihenfolge bekommen
  1. Exhibit-ID vorhanden
  2. Image-Order laden
  3. materialized View mit öffentlichen Bildern befragen
  4. die View-Antwort enthält bereits Beschreibung und Maße aller Images
  - View-Key
    - Image-ID
  - View updaten wenn:
    - Image ändern
  - View enthält materialized
    - Beschreibung
    - Maße

- Die Image-ID des öffentlichen Titelbildes eines Exponates mit Beschreibung und Maßen bekommen
  2. Exhibit-ID vorhanden
  3. Image-Order laden
  4. unmaterialized View mit öffentlichen Bildern befragen
  5. die erste Image-ID bestimmen, die in View-Antwort enthalten ist
  6. Image laden
  7. Image-ID, Beschreibung und Maße ausgeben
  - View Key
    - Image-ID
  - View updaten wenn:
    - Image ändern
  - View enthält nichts direkt

- Die ID des ersten Bildes eines Exponates mit Beschreibung und Maßen bekommen
  1. vorher alle Image-IDs des Exponates in Reihenfolge vorhanden (Image-Order)
  2. Image-ID des ersten Bild bestimmen
  3. einfach das erste Image laden
  4. Image-ID, Beschreibung und Maße ausgeben

Aufgabenangemessenheit
Robustheit ggü. Benutzungsfehlern
Selbstbeschreibungsfähigkeit
Steuerbarkeit
Erwartungskonformität
Lernförderlichkeit
Benutzerbindung

# GUI-Entwurf
- extra Seite
- oben Karussel mit allen Bildern groß
  - Pfeile nach rechts und links
- mitte Formular
  - einzeiliges, langes Eingabefeld für Bildbeschreibung
  - daneben intern/öffentlich Button
  - Speichern button links
  - Löschen-Button rechts
- unten Thumbnails mit allen Bilder auf einmal
  - dabei Bild hinzufügen Button
  - Drag and Drop für Reihenfolge
  - extra Save-Button für Reihenfolge
  - Titel-Bild hervorgehoben

# DB-Entwurf
- ImageOrder hat folgende Attribute
  - order: Array aus image_ids
  - einziges Dokument, welches für Reihenfolge vertauschen geladen und geschrieben werden muss
- seperate Query für Bilddaten zu Bild-Dokument
  - Wir können ausnutzen, dass Attachments per Default nicht mit geladen werden.
- Image hat folgende Attribute
  - image_id: Doc-ID von Image-Doc zum Nachschlagen
  - description
  - is_public
  - _attachments: Bild-Anhänge
    - data: original Bild
    - thumbnail: kleineres Thumbnail
- View um zu einem Exponat die Titelbild-ID zu erhalten
- View um zu einem Exponat die öffentlichen Bilder mit Bildbeschreibung zu erhalten
