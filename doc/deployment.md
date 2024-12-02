# Deployment

Hier werden Einstellungen zur Produktiv-Umgebung notiert.

## TODO

- für Deployment Image, welches Node und PHP vereint.<br>
  siehe [Hinweisbox auf der Inertia-Seite](https://inertiajs.com/server-side-rendering)

## Notizen

- Server-Side Rendering (SSR) ist nur für die Produktiv-Umgebung sinnvoll.
- Für die lokale Development-Umgebung verwenden wir Hot Module Replacement,
  welches für SSR hinderlich ist.

## Einrichtung
- **`APP_DEBUG=false` !!!**
- PHP-Einstellungen anpassen
	- `zend.assertions=-1`
- hat sudo-Rechte

## Workflow
0. siehe [README](../README.md) — Einrichtung für alle
1. `resources/js/app.ts` anpassen
2. `artisan optimize` (Config, Event-Listener-Mapping, Routes cachen)
3. `npm run build-prod`
4. `npm run start-ssr-prod`

## Einloggen
1. `ssh BENUTZERNAME@DOMAIN_NAME`
2. Passwort eingeben

## Technische Paramtere
- Ubuntu 24.04 LTS (GNU/Linux 6.8.0-41-generic x86_64)
- 14.66 GB
