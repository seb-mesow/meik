# Docker - Technische Dokumentation

## Ports

| Zweck | Domain | Host-Port | Ziel-Container | interne Domain | Container-Port | Kommentar |
| --- | --- | --- | --- | --- | --- | --- |
| HTTP | meik.local | 8080 | web | web | 80 | "HTML der Webseiten auf Anfrage (URL) bereitstellen" |
| HTTPS | meik.local | 444 | web | web | 443 | "HTML der Webseiten auf Anfrage (URL) bereitstellen" |
| Vite Dev Server | meik.local | 8080 | node | node | 5173 | Frontend-Assets (vorallem JS, CSS) bereitstellen, nginx als Proxy |
| Vite Dev Server | meik.local | 444 | node | node | 5174 | Frontend-Assets (vorallem JS, CSS) bereitstellen, nginx als Proxy |
