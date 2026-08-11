# GearToShare — Adatmodell: `users` tábla

Ez a dokumentum a `users` tábla migrációját és kapcsolatait írja le
részletesen.

## Migráció mezői

| Mező | Típus | Megkötés | Megjegyzés |
|---|---|---|---|
| `id` | `bigIncrements` | PK | |
| `name` | `string(255)` | NOT NULL | |
| `email` | `string(255)` | NOT NULL, UNIQUE | |
| `email_verified_at` | `timestamp` | NULLABLE | Laravel default auth mezője |
| `password` | `string(255)` | NOT NULL | bcrypt hash |
| `phone` | `string(30)` | NULLABLE | handover-koordinációhoz hasznos |
| `renter_rating` | `decimal(3,2)` | NULLABLE, DEFAULT NULL | cache-elt átlag, `ratings.role='renter'` alapján |
| `lender_rating` | `decimal(3,2)` | NULLABLE, DEFAULT NULL | cache-elt átlag, `ratings.role='owner'` alapján |
| `remember_token` | `string(100)` | NULLABLE | Laravel default |
| `timestamps` | `created_at`, `updated_at` | | |

### Index
- `email` — UNIQUE index

## Kapcsolatok

### `users` ↔ `gears` — 1:N
```
gears.user_id → users.id
```
Egy user (mint hirdető/tulajdonos) több eszközt hirdethet meg. Egy
gear-nek pontosan egy tulajdonosa van.

**Üzleti szabály — tulajdonváltás:** ha egy eszköz fizikailag gazdát
cserél (pl. eladás), ez **új `gears` rekordot** jelent az új tulajdonos
`user_id`-jával, NEM a meglévő rekord `user_id`-jának módosítását. A
régi rekord  megmarad, hogy a
hozzá tartozó régi `rentals` történeti integritása ne sérüljön.

### `users` ↔ `rentals` — 1:N (renter oldal)
```
rentals.renter_id → users.id
```
Egy user bérlőként több `rentals` rekordhoz kapcsolódhat.

**A bérbeadó NEM közvetlen FK** a `rentals` táblában. A bérbeadó mindig a `gears` táblán keresztül, JOIN-nal
érhető el:
```
rentals.gear_id → gears.id → gears.user_id → users.id
```
Indoklás: tulajdonváltás esetén ez mindig új `gears` rekordot jelent
(lásd fentebb), tehát a történeti integritás JOIN-nal is garantált —
egy redundáns `rentals.owner_id` oszlop csak szinkronizálási terhet
adna hozzá, tényleges előny nélkül.

### Dupla `users` ↔ `ratings` — 1:N
```
ratings.rater_id → users.id   (ki adta az értékelést)
ratings.rated_id → users.id   (ki kapta az értékelést)
```
Két logikailag külön kapcsolat, mindkettő ugyanarra a `users` táblára
mutat — egy user egyszerre lehet értékelő is és értékelt is,
különböző bérléseknél.

## Eloquent kapcsolatok (`app/Models/User.php`)

```php
public function gears(): HasMany
{
    return $this->hasMany(Gear::class, 'user_id');
}

public function rentalsAsRenter(): HasMany
{
    return $this->hasMany(Rental::class, 'renter_id');
}

// bérbeadóként történt bérlések - a gears táblán KERESZTÜL, nem közvetlen FK
public function rentalsAsOwner(): HasManyThrough
{
    return $this->hasManyThrough(Rental::class, Gear::class, 'user_id', 'gear_id');
}

public function ratingsGiven(): HasMany
{
    return $this->hasMany(Rating::class, 'rater_id');
}

public function ratingsReceived(): HasMany
{
    return $this->hasMany(Rating::class, 'rated_id');
}
```

## Cache-elt aggregátumok: `renter_rating` / `lender_rating`

Denormalizált, előre kiszámolt átlagértékek, amiket a `ratings`
táblából számolunk, a `role` mező szerint szűrve:

```sql
-- lender_rating (bérbeadói átlag)
SELECT AVG(score) FROM ratings WHERE rated_id = ? AND role = 'owner';

-- renter_rating (bérlői átlag)
SELECT AVG(score) FROM ratings WHERE rated_id = ? AND role = 'renter';
```

**Miért cache:** teljesítmény — élő `AVG()` számítás minden egyes
profil-megjelenítésnél (pl. keresési listázásban sok userre egyszerre)
drága lenne.

**Szinkronban tartás:** eseményvezérelt, `RatingObserver`-en keresztül
(`Rating::created()` hook) — minden új rating létrehozásakor
újraszámolja és frissíti az érintett mezőt. Nincs globális "szerep"
flag a `users` táblán — a szerep mindig az adott `rentals`/`ratings`
kontextusból derül ki, mert ugyanaz a user egyszerre lehet renter is
és owner is.