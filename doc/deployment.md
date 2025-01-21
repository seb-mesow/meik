# Deployment
Hier werden Einstellungen zur Produktiv-Umgebung notiert.

## TODO
- für Deployment Image, welches Node und PHP vereint.<br>
  siehe [Hinweisbox auf der Inertia-Seite](https://inertiajs.com/server-side-rendering)

## Notizen
- Server-Side Rendering (SSR) ist nur für die Produktiv-Umgebung sinnvoll.
- Für die lokale Development-Umgebung verwenden wir Hot Module Replacement,
  welches für SSR hinderlich ist.
- Vite einstweilen auf belassen wegen: https://github.com/laravel/vite-plugin/issues/316

## Einrichtung
- **`APP_DEBUG=false` !!!**
- PHP-Einstellungen anpassen
	- `zend.assertions=-1`
- hat sudo-Rechte

## Workflow
0. siehe [README](../README.md) — Einrichtung für alle
### Prod
```bash
artisan key:generate
artisan ziggy:generate --types
npm run prod-build
artisan migrate:fresh --seed
artisan optimize #(Config, Event-Listener-Mapping, Routes cachen)
docker_compose_up
	php-fpm
```
### Prod-SSR
```bash
artisan key:generate
artisan ziggy:generate --types
npm run prod-build-ssr
artisan migrate:fresh --seed
artisan optimize #(Config, Event-Listener-Mapping, Routes cachen)
docker_compose_up
	php-fpm
	php artisan inertia:start-ssr
```

## Einloggen
1. `ssh BENUTZERNAME@DOMAIN_NAME`
2. Passwort eingeben

## Technische Paramtere
- Ubuntu 24.04 LTS (GNU/Linux 6.8.0-41-generic x86_64)
- 14.66 GB
