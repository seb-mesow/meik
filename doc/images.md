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

- Das Titelbild als Thumbnail bekommen
- Das Titelbild im Original bekommen
- Ein Bild als Thumbnail bekommen
- Ein Bild im Original bekommen
- Alle IDs von öffentlichen Bildern mit Beschreibung bekommen

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
