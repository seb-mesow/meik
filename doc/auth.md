# Notizen zur Implementierung der Authentifizierung

Die Sitzungen werden erstmal noch in der MariaDB gespeichert.
siehe `config/session.php`

Auch für nicht angemeldete Nutzer wird eine Sitzung angelegt, wobei die `user_id` noch `NULL` ist.

**TODO** Migration für Feld user_id in sessions table: muss vom Typ varchar(255) sein.

Die Klasse der Benutzer muss das Interface `Illuminate\Contracts\Auth\Authenticatable` implementieren.

Die Benutzer werden durch ein Repository, welches das Interface `Illuminate\Contracts\Auth\UserProvider` implementieren muss bereitgestellt.

TODO eigene Session mit nur einem Cookie implementieren

