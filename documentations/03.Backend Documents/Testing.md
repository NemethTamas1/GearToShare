# Testing

> **Kapcsolódó dokumentumok:** [Gears](./Gears/03.BuisnessLogic.md) · [Rentals](./Rentals/03.BusinessLogic.md)

## Teszt-réteg — Feature tesztek (PHPUnit)

A projekt PHPUnit-ot használ (nem Pest), class-alapú, test_ prefixű metódusnevekkel.

## Közös teszt-infrastruktúra — `TestCase::postAsFrontend()`

```php
protected function postAsFrontend(string $uri, array $data = [])
{
    return $this->withHeaders([
        'Origin' => 'http://localhost',
        'Accept' => 'application/json',
    ])->post($uri, $data);
}
```

**Miért kell ez:** a Sanctum `statefulApi()` middleware csak akkor kapcsolja be a session-kezelést, ha a kérés "frontendről érkezőnek" ismeri fel magát (`Origin` fejléc alapján, összevetve a `SANCTUM_STATEFUL_DOMAINS`-szel). A PHPUnit teszt-kliens alapból nem küld ilyen fejlécet — enélkül `Session store not set on request` hibát kapnál minden session-igénylő endpointnál.

Az `Accept: application/json` külön szükséges: enélkül egy nem-hitelesített kérés a védett route-okra **nem** tiszta `401` JSON választ ad, hanem a Laravel `Authenticate` middleware-je megpróbál egy `login` nevű route-ra redirectelni — mivel ilyen route nincs a projektben (tisztán API-alapú SPA-authentikáció), ez egy másodlagos `RouteNotFoundException`-t dob, elfedve az eredeti hibát. Lásd a kapcsolódó bugfix ticketet.

## `RegisterTest.php`

| Teszt | Mit ellenőriz |
|---|---|
| `test_user_can_register_with_valid_data` | Sikeres regisztráció, helyes payloaddal → 200 |
| `test_registration_fails_without_required_fields` | Üres payload → 422 |
| `test_registration_fails_when_passwords_dont_match` | Eltérő `password`/`password_confirmation` → 422 |

## `GearTest.php`

| Teszt | Mit ellenőriz |
|---|---|
| `test_authenticated_user_can_create_hand_tool_gear` | Sikeres létrehozás → 201, `assertDatabaseHas` a helyes `user_id`-ra |
| `test_guest_cannot_create_gear` | Bejelentkezés nélkül → 401 |
| `test_gear_creation_fails_without_required_fields` | Üres payload → 422 |
| `test_cordless_gear_requires_battery_capacity` | `cordless` kategória, hiányzó `battery_capacity_mah` → 422, `assertJsonValidationErrors` |
| `test_hand_tool_gear_rejects_foreign_category_attributes` | `hand_tool` kategóriánál küldött `power_w` → 422 (`prohibited_unless` ellenőrzése) |
| `test_owner_can_update_own_gear` | Tulajdonos sikeresen módosítja saját gear-jét → 200 |
| `test_non_owner_cannot_update_gear` | Idegen user nem módosíthatja más gear-jét → 403, adat változatlan marad |
| `test_owner_can_delete_own_gear` | Tulajdonos sikeresen törli saját gear-jét → 204, rekord eltűnik |
| `test_non_owner_cannot_delete_gear` | Idegen user nem törölheti más gear-jét → 403, rekord megmarad |

## Teszt-segéd metódusok — `TestCase.php`

A `postAsFrontend()` mellett bővítve `putAsFrontend()` és `deleteAsFrontend()` metódusokkal, azonos header-összeállítással (`Origin`, `Accept: application/json`) — mindhárom a session/CSRF-kezeléshez szükséges frontend-szerű kérést szimulálja.