# Entscheidungen

## Plätze und Exponate
- Der Platz an einem Exponat ist optional
- Grund: Es soll möglich sein zuerst das Exponat und dann den Platz anzulegen
- Es soll auf den Kacheln eiN Warniung Icon dzu gehben, wenn kein Platz an einem Exponat angelegt ist.

## Plätze und Standort löschen
- Wenn man einen Platz oder Standort löscht, dann werden alle Platz-Refernezen der betreffenden Exponate gelöscht.
- Es gibt dazu eine eindeutig arnign Meldung an den Nutzer.

## Setter vs. Constructors von Models
- orientieren an Symphony
- readonly-Properties müssen im Konstruktor gesetzt werden.
- Für alle veränderbaren Properties sollten Setter-Funktionen o.Ä. definiert werden.
- Vorteil: Ein Form von named Parametern.
