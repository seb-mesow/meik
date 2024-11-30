# HTTP Methoden und Controller-Funktionen

- [HTTP Methoden und Controller-Funktionen](#http-methoden-und-controller-funktionen)
	- [für Web-Controller — `web.php`](#für-web-controller--webphp)
		- [Formular für eine neue Resource laden](#formular-für-eine-neue-resource-laden)
		- [ein neues Model speichern](#ein-neues-model-speichern)
		- [ein Model vollständig abrufen](#ein-model-vollständig-abrufen)
		- [alle Models einer Klasse auszugweise abrufen (Übersicht)](#alle-models-einer-klasse-auszugweise-abrufen-übersicht)
		- [ein Model löschen](#ein-model-löschen)
	- [für AJAXController — `ajax.php`](#für-ajaxcontroller--ajaxphp)
		- [ein existierendes Model in seiner Gesamtheit aktualisieren](#ein-existierendes-model-in-seiner-gesamtheit-aktualisieren)
		- [ein existierendes Model _spezifisch_ teilweise aktualisieren](#ein-existierendes-model-spezifisch-teilweise-aktualisieren)
		- [ein existierendes Model _unspezifisch_ teilweise aktualisieren](#ein-existierendes-model-unspezifisch-teilweise-aktualisieren)

Mehr als Orientierung gesehen, keine strikte Festlegung

Die Namen von Controller-Funktionen sollen sich an CRUD orientieren
und NICHT die HTTP-Methoden beinhalten.

Beispiele anhand des Models `Exhibit`.

## für Web-Controller — `web.php`
### Formular für eine neue Resource laden
- View: Detailseite, leer
- Primär-Schlüssel noch nicht festgelegt
```php
Route::get('/exhibit', [ExhibitController::class, ''])->name('exhibit.new');
```
### ein neues Model speichern
- primärer Schlüssel noch nicht festgelegt
- Weiterleitung auf Route `exhibit.details` mit neuem Primär-Schlüssel
```php
Route::post('/exhibit', [ExhibitController::class, 'create'])->name('exhibit.create');
```
### ein Model vollständig abrufen
- View: Detailseite, vorausgefüllt
```php
Route::get('/exhibit/{id}', [ExhibitController::class, 'details'])->name('exhibit.details');
```
### alle Models einer Klasse auszugweise abrufen (Übersicht)
- Für jede Ressource muss nur ein Teil aller Informationen geladen werden.
- View: Übersicht
```php
Route::get('/exhibits', [ExhibitController::class, 'overview'])->name('exhibit.overview');
```
### ein Model löschen
- wenn erfolgreich Weiterleitung zur Route `exhibit.overview`
```php
Route::delete('/exhibit/{id}', [ExhibitController::class, 'delete'])->name('exhibit.delete');
```
## für AJAXController — `ajax.php`
### ein existierendes Model in seiner Gesamtheit aktualisieren
```php
Route::put('/exhibit/{id}', [ExhibitAJAXController::class, 'update'])->name('exhibit.update');
```
### ein existierendes Model _spezifisch_ teilweise aktualisieren
```php
Route::patch('/exhibit/{id}/TEIL', [ExhibitAJAXController::class, 'change_TEIL'])->name('exhibit.change_TEIL');
```
### ein existierendes Model _unspezifisch_ teilweise aktualisieren
- Bitte vermeiden
```php
Route::patch('/exhibit/{id}', [ExhibitAJAXController::class, 'change'])->name('exhibit.change');
```
