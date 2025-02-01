# Notizen zur Implementierung der Authentifizierung

- [Notizen zur Implementierung der Authentifizierung](#notizen-zur-implementierung-der-authentifizierung)
	- [Aufbau](#aufbau)
	- [Ablauf Login](#ablauf-login)
	- [Ablauf Authentifizierung einer Request, wenn ordentlich eingeloggt](#ablauf-authentifizierung-einer-request-wenn-ordentlich-eingeloggt)
	- [Ablauf Authentifizierung einer Request nur über Remember-Token](#ablauf-authentifizierung-einer-request-nur-über-remember-token)
	- [Ausloggen](#ausloggen)
	- [Remember-Token](#remember-token)
	- [Session](#session)
	- [CSRF-Token](#csrf-token)

Kurzlebige Daten, insbesondere Sitzungsdaten, werden in der MariaDB gespeichert.
siehe `config/session.php`

Auch für nicht angemeldete Nutzer wird eine Sitzung angelegt, wobei die `user_id` noch `NULL` ist.

**TODO** Migration für Feld user_id in sessions table: muss vom Typ varchar(255) sein.

Die Klasse der Benutzer muss das Interface `Illuminate\Contracts\Auth\Authenticatable` implementieren.

Die Benutzer werden durch ein Repository, welches das Interface `Illuminate\Contracts\Auth\UserProvider` implementieren muss bereitgestellt.

TODO eigene Session mit nur einem Cookie implementieren


## Aufbau
Der Einsprungpunkt für die Authentifizierung ist immer die `auth`-Middleware,
welche vor eigentlichen Verarbeitung von Requests für bestimmte Routen ausgeführt wird.
Diese benutzt den default _Authentication Guard_. Dies ist bei uns der mitgelieferte Authentication Guard `web`.

Zu jedem Guard muss 1. ein _Authentication Driver_ und 2. ein _Authentification Provider_ angegeben werden.

Die Kern der Authentifizierung ist immer eine _Authentification Provider_.
Dies ist bei uns `couchdb` von der Klasse `CouchDBUserProvider`.
Dieser überprüft letzendlich z.B. ob das eingegebene Passwort mit dem Hash in der CouchDB übereinstimmt.


## Ablauf Login
Der CSRF-Token wird in keiner From mit gesendet (weder als Header noch als Teil der Login-Formular-Daten).

1. `LoginController::login()`
   1. `LoginRequest::authenticate()`
      2. `SessionGuard::attempt(array $credentials, bool $remember)`
         1. `$user = UserProvider::retrieveByCredentials($credentials)`
         2. `$validated = UserProvider::validateCredentials($user, array $credentials)`
         3. wenn `$validated`, dann
            1. `UserProvider::rehashPasswordIfRequired($user, array $credentials)`
            2. `SessionGuard::login()`
               1. `User::getAuthIdentifier()`
               2. aktuellen Primary Key des Benutzers in die aktuelle Sitzung unter dem Schlüssel `login_<SESSION_GUARD>_<SHA1-HASH_CLASS-STRING_PROVIDER>` speichern
               3. ggf. Remember Token setzen
                  1. `$token = User::getRememberToken()`
                  2. Wenn `$token` empty, dann
                     1. `SessionGuard::cycleRememberToken($user)`
                        1. `$new_token =` new random String, 60 characters long
                        2. `User::setRememberToken($new_token)`
                        3. `UserProvider::updateRememberToken($user, $token)`


## Ablauf Authentifizierung einer Request, wenn ordentlich eingeloggt
1. `Middleware\Authenticate::handle()`
   1. `Middleware\Authenticate::authenticate`
      1. `$checked = SessionGuard::check()`
         1. `SessionGuard::user()`
            1. `$user_id` = ermittle Primary Key des Users aus Session-Daten
            2. `$user = UserProvider::retrieveById($user_id)`
            3. Setze `$user` als aktuellen User des `SessionGuard`
         2. `return true` wenn `$user !== null`
      2. wenn `$checked == false`, dann werfe `AuthenticationException`


## Ablauf Authentifizierung einer Request nur über Remember-Token
1. `Middleware\Authenticate::handle()`
   1. `Middleware\Authenticate::authenticate`
      1. `$checked = SessionGuard::check()`
         1. `SessionGuard::user()`
            1. `$user_id` = ermittle Primary Key des Users aus Session-Daten
            2. `$user_id` ist `null`
            3. `$recaller = SessionGuard::recaller()`
               1. ermittle Remember-Token aus Cookie `remember_<SESSION_GUARD>_<SHA1-HASH_CLASS-STRING_PROVIDER>`
            4. `$user = SessionGuard::userFromRecaller($recaller)`
               1. `return UserProvider::retrieveByToken(<User-ID>, <Remember-Token>)`
            5. Setze `$user` als aktuellen User des `SessionGuard`
            6. Speicher User-ID in Session-Payload unter dem Schlüssel `login_<SESSION_GUARD>_<SHA1-HASH_CLASS-STRING_PROVIDER>`
         2. `return true` wenn `$user !== null`
      2. wenn `$checked == false`, dann werfe `AuthenticationException`

## Ausloggen
1. aktuelle Session mit neuer Session-ID, ohne User-ID in separater Spalte und ohne User-ID in der Session-Payload überschreiben.
2. Cookie mit Remember-Token mit solchen Parametern senden, dass dieser vom Browser sofort gelöscht wird.

## Remember-Token
Dazu wird im Browser des Users ein langlebiger Cookie namens `remember_<SESSION_GUARD>_<SHA1-HASH_CLASS-STRING_PROVIDER>`.
Dabei ist `<SESSION_GUARD>` der (kurze) Bezeichner des genutzten _Authentification Guards:.
Dabei ist `<SHA1-HASH_CLASS-STRING_PROVIDER>` der `sha1()` des Class-Strings des genutzten _Authentification Provider_.

Der Wert diess Cookies ist nach dem folgenden Format aufgebaut.
USER_PRIMARY_KEY_|REMEMBER_TOKEN|PASSWORD_HASH


## Session
Der Wert des Cookies `meik_session` enthält nur die Session-ID,
wobei der Wert des Cookies zusätzlich symmetisch mit dem APP_KEY verschlüsselt ist.
Die Session-ID ist immer ein random String der 40 Zeichen lang ist.

In der Session-Tabelle wird zu der Session-ID folgendes gespeichert:
- Den Primary Key des Users, wenn eingeloggt
- Die Payload
  - nochmal den Primary Key des Users, wenn eingeloggt
  - den CSRF-Token unter dem Schlüssel `_token`

Beim erstmaligen Aufrufen der Login-Seite wird bereits eine Session mit einer neuen Session-ID erzeugt.
Dieser Session ist natürlich noch keine User-ID zugeordnet.
Die Session-ID wird im Cookie `meik_session` an den Browser übergeben.

Dem Einloggen wird die bestehende Session
- mit einer neuer Session ID,
- dem Primary Key des Users (User-ID) in separater Spalte
- und dem Primary Key des Users (User-ID) in der Session-Payload unter dem Schlüssel `login_<SESSION_GUARD>_<SHA1-HASH_CLASS-STRING_PROVIDER>`
überschrieben.
Damit kann die neue Session-ID zur Identifikation verwendet werden.
Die neue Session-ID wird im Cookie `meik_session` an den Browser übergeben.
Bei erneuten Aufrufen, solange man eingeloggt ist, bleibt die Session-ID erhalten.
Da der Cookie `meik_session` immer wieder mitgesendet wird, wird die Gültigkeitsdauer des Cookies `meik_session` immer wieder verlängert.

Beim Ausloggen wird die bestehende Session mit
- einer neuen Session ID,
- ohne Primary Key des Users (User-ID) in separater Spalte
- und ohne dem Primary Key des Users (User-ID) in der Session-Payload
überschrieben.
Die neue Session-ID wird im Cookie `meik_session` an den Browser übergeben.
Damit kann der Cookie `meik_session` nicht mehr zur Identifikation für irgendeinen User verwendet werden.


## CSRF-Token
Der aktuelle CSRF-Token eines Users ist in der Session-Payload unter dem Schlüssel `_token` gespeichert.

Der CSRF-Token kann auf drei Arten vom Browser angegeben werden:
1. Bei einer POST-Request als Teil der Formulardaten im Feld `_token`.
2. Als Wert des Headers `X-CSRF-TOKEN`, aber unverschlüsselt.
3. Als Wert des Headers `X-XSRF-TOKEN`, aber symmetrisch verschlüsselt mit dem APP_KEY.
Die angegebenen Arten werden von oben nach unten durchprobiert.
