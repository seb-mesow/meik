# CouchDB Wissensspeicher

- [CouchDB Wissensspeicher](#couchdb-wissensspeicher)
	- [eine Beziehung auflösen](#eine-beziehung-auflösen)
	- [Document IDs](#document-ids)
		- [Lösung](#lösung)
		- [endgültiger Aufbau](#endgültiger-aufbau)

## eine Beziehung auflösen
Mit dem Feature "Linked Documents" können man in einer View (= nur eine DB-Request!)
nicht nur das Dokument von dem die Beziehung ausgeht geladen werden,
sondern auch das Dokument zu dem die Beziehung hingeht.

## Document IDs
Die Documents-IDs müssen selbst durch die Anwendung erzeugt werden,
damit es immer erkannt werden kann, wenn eine Insert-Operation fehlschlägt.
bzw. es wird verhindert, dass das zwei inhaltlich gleiche Dokumente (für die identische Entität der Domaine/Fachwelt)
erzeugt werden.

Die Document-IDs sollten nicht _komplett_ random produziert werden,
da dies zu einem hinsichtlich Festplatten-Zugriffen ungünstigen Aufbau des B+-Trees führt,
welchen CouchDb zur Speicherung und zum Auffinden der Dokumente benutzt.
* http://blog.inoi.fi/2010/11/impact-of-document-ids-on-performance.html
* https://docs.couchdb.org/en/stable/config/misc.html#uuids-configuration

Die Document-IDs mehrere gleichartiger Dokumente müssen unabhängig voneinander erzeugt werden können,
damit fehlschlagenden Insert-Operationen und den bekannten Problemen
von parallelen Lese- und Schreib-Zugriffen vorgebeugt wird.
Z.B. ist es nicht sinnvoll eine streng inkrementierende ID zu vergeben.

Was passiert, wenn gleichzeitig zwei gleichartige Dokumente eingefügt werden sollen?
Der bisherige Wert der Document-IDs wird gleichzeitig gelesen und unabhängig von einander
_auf den selben Wert_ inkrementiert. Beide neuen Dokumente würden die gleiche Document-ID erhalten.

### Lösung
Die Document-IDs tragen zur besseren Unterscheidung ein Präfix, welches den Entitätstyp bzw. Dokumenttyp erkenntlich macht.

Danach folgt für neue IDs der aktuelle Timestamp; genau bis auf eine Sekunde.
Dadurch kann der B+-Tree der CouchDB speicherplatz-sparender aufgebaut werden:
Die Timestamps ändern sich zunächst in den hinteren Stellen (z.B. die Sekunde).
Eine Auflösung bis auf Mikro- oder Nanosekunden führt zu längernen IDs und kann wie später beschrieben ersetzt werden.
Die vorderen Stellen (z.B. das Jahr) ändern sich nur selten.
Viele Dokument-IDs (für gleichartige Entitäten) haben als ein gemeinsames Präfix.
Dies nutzt der B+-Tree aus, da er das gemeinsame Präfix nur einmal entlang eines gemeinsamen Pfades von
Knoten speichern kann. Erst danach verzweigt sich der B+-Tree für die hinteren Stellen des Timestamps.

Nach dem Timestamp folgt ein _kleiner_ Teil, welcher zufällig erstellt wird,
damit (mit sehr hoher Wahrscheinlichkeit) keine doppelten IDs generiert werden.
(Um garantiert keine doppelten IDs zu erzeugen, müsste man geteilte Werte speichern,
was zu den bekannten Problemen führt.)

Berechnungsgrundlage:
* 10 neue IDs in einer Sekunde.
* Die Wahrscheinlichkeit für zwei gleiche IDs in dieser Sekunden soll kleiner gleich $10^{-6}$ - also ein Millonstel - sein.
* Die Wahrscheinlichkeit für jede möglichen ID erzeugt zu werden ist gleichverteilt.
Frage: Wie viele unterschiedliche IDs müssen für eine Sekunde mindestens gebildet werden können?
(Anzahl an IDs)

Werte: $n = 10, p \le 10^{-6}$, gesucht: $d \ge x$

Formel:
$$p \approx 1 - \textrm{e}^{-\frac{n(n-1)}{2d}}$$
umgestellt:
$$d \ge -\frac{n(n-1)}{\ln(1-p)}$$
Ergebnis: $d \ge 8\,999\,955$

Mit hexademimalen Ziffern (16 Möglichkeiten pro Stelle) sind mindestens 6 Stellen nötig.<br>
Mit den Groß- und Kleinbuchstaben, sowie den dezimalen Ziffern sind mindestens 4 Stellen nötig.

### endgültiger Aufbau
\<ID\> ::= \<Präfix\>\<Timestamp\>\<random\><br>
\<Präfix\> ::= {regex} [a-z]+<br>
\<Timestamp\> ::= {php} `gmdate('%Y%m%d%H%i%s') // GMT=UTC`<br>
\<ramdom\> ::= {regex} [A-Za-z0-9]{4} {viermal mit `mt_rand(0,61)`}

Das Präfix darf keine Ziffern beinhalten!
Beispiel: `exhibit20241229172734a6Bt`
