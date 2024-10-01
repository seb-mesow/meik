# Deployment

Hier werden Einstellungen zur Produktiv-Umgebung notiert.

## Notizen

- Server-Side Rendering (SSR) ist nur für die Produktiv-Umgebung sinnvoll.
- Für die lokale Development-Umgebung verwenden wir Hot Module Replacement,
  welches für SSR hinderlich ist.

## Workflow

**`APP_DEBUG=false` !!!**

0. siehe [README](../README.md) — Einrichtung für alle
1. `artisan optimize` (Config, Event-Listener-Mapping, Routes cachen)
2. `npm run build-prod`
3. `npm run start-ssr-prod`
