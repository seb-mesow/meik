# Code-Style für Repositories

Alle Methoden eines Repositories dürfen nur durch Erfordernis vorhanden sein.
Sie müssen also mindestens 1 Referenz aufweisen.

Der primäre Schlüssel im Model ist immer _ohne_ das Dokument-Typ-Präfix.
Der primäre Schlüssel im Dokument in der DB ist immer _mit_ dem Dokument-Typ-Präfix.

In den folgenden Festlegungen wird beispielsweise das Repository für die Klasse `Exhibit` definiert.
Bei der Klasse Exhibit heißt das Attribut für den primären Schlüssel bspw. "id".
Das Attribut "id" ist bspw. vom Datentyp `string`.

- `public function find(string $id): ?Exhibit`
	- sucht ein Model mithilfe des primären Schlüssels
	- wenn gefunden: gibt das Model zurück
	- wenn nicht existiert: gibt `null` zurück
- `public function get(string $id): Exhibit`
	- gibt ein Model mithilfe des primären Schlüssels zurück
	- wenn gefunden: gibt das Model zurück
	- wenn nicht existiert: eine passende Exception werfen
- `public function get_all(): array`
	- gibt einen Array von allen Models zurück
- `public function insert(Exhibit $exhibit): Exhibit`
	- speichert ein neues Model
	- Der primäre Schlüssel des übergebenen Models kann oder kann nicht gesetzt sein.
		- Konkretisierung je nach Model möglich
	- Das Attribut `rev` des übergebenen Models muss noch `null` sein.
	- gibt das gleiche Model zurück, aber das Attribut `rev` ist auf die Revisions-ID gesetzt.
- `public function update(Exhibit $exhibit): void`
	- ändert ein bestehendes Model
	- Der primäre Schlüssel des übergebenen Models muss gesetzt sein.
	- Das Attribut `rev` des übergebenen Models muss mit der _bisherigen_ Revisions-ID gesetzt sein.
	- setzt die Revisions-ID des Models auf die _neue_ Revisions-ID.
	- wenn Model nicht in DB gefunden: wirft eine passende Exception
	- wenn Revisions-ID nicht mehr aktuell: wirft eine passende Exception
- `public function remove(Exhibit $exhibit): void`
	- löscht ein bestehendes Model
	- Der primäre Schlüssel des übergebenen Models muss gesetzt sein.
	- Das Attribut `rev` des übergebenen Models muss mit der Revisions-ID gesetzt sein.
	- kein Rückgabewert
- `private function create_doc_from_exhibit(Exhibit $exhibit): stdClass`
	- wandelt ein Model in ein `stdClass`-Objekt um
- `private function create_exhibit_from_doc(stdClass $exhibit_doc): Exhibit`
	- wandelt ein `stdClass`-Objekt in ein Model um
