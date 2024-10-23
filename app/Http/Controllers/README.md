# Controller

Web-Controller geben immer eine Vue-Seite zurück (Inertia).
Sie heißen per Konvention nur `*Controller`.
Sie liegen immer im Namesraum `App\Http\Controllers\Web`.

AJAX-Controller sind für die (_interne_ API).
Sie heißen per Konvention `*APIController`.
Sie liegen immer im Namesraum `App\Http\Controllers\AJAX`.

API-Controller sind für die _externe_ API.
Sie heißen per Konvention `*APIController`.
Sie liegen immer im Namesraum `App\Http\Controllers\API`.
