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