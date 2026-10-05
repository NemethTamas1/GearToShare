# Testing

> **Kapcsolódó dokumentumok:** [Gears](./Gears/03.BuisnessLogic.md) · [Rentals](./Rentals/03.BusinessLogic.md)

## Teszt-réteg — Feature tesztek (PHPUnit)

A projekt PHPUnit-ot használ (nem Pest), class-alapú, test_ prefixű metódusnevekkel.

## Közös teszt-infrastruktúra — `TestCase` segéd-metódusok

```php
private function frontendHeaders(): array
{
    return [
        'Origin' => 'http://localhost',
        'Accept' => 'application/json',
    ];
}

protected function getAsFrontend(string $uri) { /* ... */ }
protected function postAsFrontend(string $uri, array $data = []) { /* ... */ }
protected function putAsFrontend(string $uri, array $data = []) { /* ... */ }
protected function patchAsFrontend(string $uri, array $data = []) { /* ... */ }
protected function deleteAsFrontend(string $uri) { /* ... */ }
```

Mind az öt metódus a `frontendHeaders()`-t használja, így a fejléc-összeállítás egyetlen helyen van.

**Miért kell ez:** a Sanctum `statefulApi()` middleware csak akkor kapcsolja be a session-kezelést, ha a kérés "frontendről érkezőnek" ismeri fel magát (`Origin` fejléc alapján, összevetve a `SANCTUM_STATEFUL_DOMAINS`-szel). A PHPUnit teszt-kliens alapból nem küld ilyen fejlécet, enélkül `Session store not set on request` hibát kapnánk.

Az `Accept: application/json` külön szükséges: enélkül egy nem-hitelesített kérés a védett route-okra nem tiszta `401` JSON választ ad, hanem a Laravel megpróbál egy `login` nevű route-ra redirectelni, ami nincs a projektben, és ez egy másodlagos `RouteNotFoundException`-t dob.

## `GearTest.php`

| Teszt | Mit ellenőriz |
|---|---|
| `test_authenticated_user_can_create_hand_tool_gear` | Sikeres létrehozás → 201, `assertDatabaseHas` a helyes `user_id`-ra |
| `test_guest_cannot_create_gear` | Bejelentkezés nélkül → 401 |
| `test_gear_creation_fails_without_required_fields` | Üres payload → 422 |
| `test_cordless_gear_requires_battery_capacity` | `cordless`, hiányzó `battery_capacity_mah` → 422 |
| `test_hand_tool_gear_rejects_foreign_category_attributes` | `hand_tool`-nál küldött `power_w` → 422 (`prohibited_unless`) |
| `test_owner_can_update_own_gear` | Tulajdonos módosítja a saját gear-jét → 200 |
| `test_non_owner_cannot_update_gear` | Idegen user → 403, az adat változatlan |
| `test_owner_can_delete_own_gear` | Tulajdonos törli a sajátját → 204, a rekord eltűnik |
| `test_non_owner_cannot_delete_gear` | Idegen user → 403, a rekord megmarad |
| `test_user_sees_only_own_gears_including_drafts` | `GET /api/my/gears`: csak a saját gear-ek (draft is), idegen gear nem szerepel |
| `test_guest_cannot_list_own_gears` | `GET /api/my/gears` bejelentkezés nélkül → 401 |
| `test_owner_can_change_gear_status` | `PATCH /api/gears/{gear}/status` tulajdonosnak → 200, a státusz módosul |
| `test_non_owner_cannot_change_gear_status` | Idegen user → 403, a státusz változatlan |
| `test_gear_status_rejects_invalid_value` | Érvénytelen érték (`rented`) → 422 |

## `RentalTest.php`

| Teszt | Mit ellenőriz |
|---|---|
| `test_authenticated_user_can_create_rental_request` | Sikeres bérlési igény → 201, `pending` státusszal |
| `test_guest_cannot_create_rental_request` | Bejelentkezés nélkül → 401 |
| `test_owner_cannot_rent_own_gear` | Tulajdonos nem bérelheti a saját eszközét → 422 |
| `test_rental_creation_fails_without_required_fields` | Üres payload → 422 |
| `test_rental_total_price_is_calculated_from_gear_price_and_days` | A `total_price` a `price_per_day × napok` szorzatából számolódik, foglaláskor rögzül |
| `test_owner_can_accept_pending_rental` | Gear tulajdonosa elfogadhatja a függő kérelmet → 200 |
| `test_owner_can_reject_pending_rental` | Gear tulajdonosa elutasíthatja → 200 |
| `test_renter_cannot_change_own_rental_status` | A bérlő nem módosíthatja a saját kérelme státuszát → 403 |
| `test_unrelated_user_cannot_change_rental_status` | Sem tulajdonos, sem bérlő nem lehet → 403 |
| `test_owner_cannot_change_status_of_non_pending_rental` | Már `accepted` státuszú rekord nem módosítható újra → 422 |
| `test_rental_status_rejects_invalid_value` | Érvénytelen érték (`active`) → 422, csak `accepted`/`rejected` engedett |

## `RatingTest.php`

| Teszt | Mit ellenőriz |
|---|---|
| `test_renter_can_rate_owner_after_completed_rental` | Lezárt bérlés után a bérlő értékelheti a tulajdonost → 201, a `rater_id`/`rated_id` helyes |
| `test_owner_can_rate_renter_after_completed_rental` | A tulajdonos értékelheti a bérlőt → 201 |
| `test_unrelated_user_cannot_rate` | Sem tulajdonos, sem bérlő → 403 |
| `test_cannot_rate_non_completed_rental` | `active` állapotú bérlés nem értékelhető → 422 |
| `test_cannot_rate_same_rental_twice` | Ugyanaz a fél kétszer nem értékelheti ugyanazt a bérlést → 422 |
| `test_rating_rejects_invalid_score` | `0`, `6`, nem szám → 422 |
| `test_guest_cannot_rate` | Bejelentkezés nélkül → 401 |
| `test_received_ratings_returns_average_and_count_for_current_user_only` | A `GET /api/myratings` csak a nekem szóló értékeléseket adja, helyes átlaggal és darabszámmal |

### Teszt-segéd: `completedRental()`

A `RatingTest` minden esete egy lezárt bérlésből indul, ezért egy privát segéd állítja elő: két user (tulajdonos, bérlő), egy gear és egy `completed` státuszú rental, amit tömbként ad vissza (`[$owner, $renter, $rental]`). A tesztek PHP destrukturálással bontják szét (`[$owner, $renter, $rental] = $this->completedRental();`), és csak a szükséges elemeket veszik fel (`[, $renter, $rental]`).

### A `completed` állapot csak teszt-adat

A `RentalFactory::completed()` közvetlenül `completed` rekordot állít elő. A valódi alkalmazásban a `completed` állapotba **jelenleg semmi nem juttat el** egy bérlést: az `accepted → active` (QR/Barion) és az `active → completed` (időzített lezárás) átmenet még nincs implementálva. Ezért az értékelés leadása a felületről egyelőre nem végigvihető, a lánc tesztelését a factory végzi.

### Kettős védelem a dupla értékelés ellen

A második értékelés ellen két szinten van védelem: a `RatingController` `422`-t ad, és a `ratings` tábla `(rental_id, rater_id)` egyedi kulcsa adatbázis szinten is megakadályozza. A `test_cannot_rate_same_rental_twice` az első szintet ellenőrzi. Ha a controller-ellenőrzés hibázna, az egyedi kulcs `500`-at okozna `422` helyett, ez a teszt ezt az esetet fogja meg.