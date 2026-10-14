## CI/CD

![Backend tests](https://github.com/NemethTamas1/GearToShare/actions/workflows/backend.yml/badge.svg?branch=dev)
![Frontend build](https://github.com/NemethTamas1/GearToShare/actions/workflows/frontend.yml/badge.svg?branch=dev)

A projekt GitHub Actions workflow-kat használ. A `dev` ág automatikusan kikerül a staging környezetre, ha minden ellenőrzés zöld.

### Pipeline

| Esemény | Backend (`backend.yml`) | Frontend (`frontend.yml`) |
|---|---|---|
| Pull request a `dev` felé | `test` job: `composer install`, `php artisan test` | `build` job: `npm ci`, `npm run build` |
| Push a `dev` ágra | `test`, majd `deploy` (staging) | `build`, majd `deploy` (staging) |

- A tesztek SQLite `:memory:` adatbázison futnak, MySQL nem kell hozzájuk.
- A frontend build a TypeScript fordítást is futtatja, így a nem használt változók és típushibák még deploy előtt kiderülnek.
- A `deploy` job csak a `dev` ágra történő push után indul, és csak akkor, ha a `test` / `build` job sikeres (`needs`). Pull requestnél kimarad.

### Védett ág

A `dev` ágra egy ruleset vonatkozik:

- csak pull requesten keresztül lehet bele mergelni
- a `test` és a `build` check kötelező, piros CI mellett a merge le van tiltva
- force push és az ág törlése tiltott

### Deploy (Fly.io staging)

- A `deploy` jobok a `staging` GitHub environmentben futnak, amely csak a `dev` ágról érhető el.
- Három külön, appenként szűkített Fly deploy token van environment secretként tárolva (backend, frontend, db), a legkisebb jogosultság elve szerint.
- A backend deploy előtt a workflow elindítja a leállított `db` gépet. A `release_command` sima `php artisan migrate --force`-ot futtat, a `migrate:fresh` kézi művelet marad.

### Ismert korlátok

- A `db` gép a deploy után nem áll le automatikusan (költségtakarékos staging, `auto_start_machines = false`).
- A `db` indulására egy fix várakozás (`sleep 15`) van, health check helyett.
- Módosított, már lefutott migrációhoz kézi `migrate:fresh --seed` kell a stagingen.
- Production környezet és `main` ágra szóló deploy még nincs, a terv: külön `production` environment, kötelező jóváhagyóval.