# GearToShare - P2P Eszközmegosztó Piactér

A **GearToShare** egy modern, kétoldalú (Bérlő és Bérbeadó) peer-to-peer piactér webalkalmazás, amely lehetővé teszi a felhasználók számára, hogy használaton kívüli gépeiket, szerszámaikat bérbe adják, vagy eszközöket béreljenek a közelükben.

Ez a projekt egy **kiélesített UAT (User Acceptance Testing) / Demo környezetként** funkcionál, amely hús-vér integrációkon keresztül mutatja be a szoftverarchitektúrát élethű tesztadatokkal.

---

## 🚀 Főbb Funkciók & Üzleti Logika

### 1. Felhasználói Szerepkörök (Actors)
*   **Bérlő (Renter):** Böngészi a katalógust, szűr kategóriára és helyszínre, valamint tranzakciókat indít.
*   **Bérbeadó (Lender):** Feltölti a saját eszközeit képpel, leírással, árazással, és kezeli a beérkező foglalásokat.
*   *Megjegyzés:* Egyazon regisztrált felhasználó mindkét szerepkört betöltheti a rendszerben.

### 2. Automatizált & Biztonságos Bérlési Folyamat
A szoftver a visszaélések (trollkodás, spamelés) ellen és a zökkenőmentes automatizáció érdekében a következő életciklust követi:
1.  **Igény leadása:** A bérlő kiválasztja a dátumintervallumot a naptárban. Ekkor a foglalás állapota `pending` lesz, az eszköz **nem** vonódik ki a forgalomból, így elkerülhető, hogy poénból blokkolják a piacteret.
2.  **Tulajdonosi Jóváhagyás:** A bérbeadó ellenőrzi az igényt. Jóváhagyás után a státusz `approved` lesz, a naptár zárolja az időszakot, és a rendszer generál egy egyedi fizetési hivatkozást.
3.  **Sandbox Fizetés & Webhook:** A bérlés véglegesítéséhez a bérlő a **Barion Sandbox** tesztkörnyezetén keresztül fizet. Sikeres tranzakció után a Barion háttér-értesítést (**Webhook**) küld a Laravel backendnek, ami emberi beavatkozás nélkül átállítja a bérlést `paid` állapotra.
4.  **Időzített Állapotgép (Laravel Scheduler):** A háttérben futó Cron Jobok automatikusan menedzselik az idő múlását:
    *   A bérlés első napján a státusz magától `active` lesz.
    *   A bérlés zárónapján a státusz `completed` lesz, és a rendszer automatikusan kiküldi az értékelő e-maileket a feleknek, ahol értékelhetik egymást egy megbízható közösség kiépítése érdekében.

---

## 🛠 Technológiai Stack

*   **Frontend:** React + Typescript (SPA) - reszponzív, modern webes felület állapottér-kezeléssel.
*   **Backend:** Laravel (REST API) - robusztus üzleti logika, időzített feladatok (Scheduler), Webhook fogadás.
*   **Adatbázis:** MySQL - optimalizált sémával és indexelt állapot-szűrésekkel.
*   **Fizetési kapu:** Barion API (Sandbox mód).
*   **Konténerizáció:** Docker & Docker Compose (külön konténer a Laravel API-nak, a React frontendnek és az adatbázisnak a lokális fejlesztéshez).

## 🔄 CI/CD & Deployment (UAT)
A projekt automatikus tesztelési és élesítési folyamata (CI/CD) teljesen automatizált, támogatva a modern DevOps gyakorlatokat:

1.  **Continuous Integration (CI):** Minden `main` ágra irányuló Pull Request esetén a **GitHub Actions** automatikusan elindítja a tesztkörnyezetet:
    * Lefuttatja a backend (PHPUnit) és frontend teszteket.
    * Ellenőrzi a kódminőséget és a formázást (linterek).
2.  **Continuous Deployment (CD):** Sikeres tesztek és a kód összeolvadása (Merge) után a GitHub Actions automatikusan elindítja a deployment folyamatot az UAT szerverre.
3.  **Deployment & Docker:** A célszerveren a friss kód alapján a rendszer újraépíti a Docker image-eket, majd a **Docker Compose** segítségével minimális leállási idővel (Zero-downtime vagy Rolling update logikával) újraindítja a konténereket, és lefuttatja a szükséges adatbázis migrációkat.

---

## 📊 Adatbázis Architektúra

A rendszer alapját az alábbi fő entitások és kapcsolatok határozzák meg:
*   `users`: Felhasználók alapadatai és hitelesítése.
*   `gears`: A bérbeadható eszközök (tulajdonoshoz kötve, kategóriával, árakkal, elérhetőséggel).
*   `rentals`: A bérlések központi gyűjtőtáblája, amely összeköti a bérlőt, a gépet, tárolja a pontos időintervallumot, a fizetési adatokat és a bérlés aktuális státuszát.
*   `ratings`: A bérlések lezárása után születő értékelések táblája. Tartalmazza a `rental_id`-t (melyik bérléshez kapcsolódik), a `reviewer_id`-t (ki értékel), a `reviewee_id`-t (kit értékelnek), a pontszámot (1-5 csillag) és a szöveges véleményt.

---

## 🧪 UAT & Demo Környezet Specifikációk

Mivel a projekt elsősorban bemutató célokat szolgál, a rendszer architektúrája **konfiguráció-vezérelt**. Csak néhány `.env` változó cseréje szükséges az azonnali, valós production üzemmódhoz.

### Demo Jellemzők:
*   **Élethű Seeder adatok:** Az éles tesztkörnyezet gyárilag fel van töltve valósághű magyar szerszámhirdetésekkel, leírásokkal és képekkel, hogy a kereső és szűrő funkciók azonnal tesztelhetőek legyenek.
*   **Demo Belépés:** A Login felületen előre konfigurált gombok találhatók a Teszt Bérlő és Teszt Bérbeadó profilok azonnali eléréséhez (nincs szükség manuális regisztrációra a teszteléshez).
*   **Teszt Kártya adatok:** A fizetési felületen és a főoldalon jól láthatóan fel van tüntetve a Barion Sandbox tesztkártya száma (`4242 4242 4242 4242`), amivel a teljes pénzügyi és webhook folyamat ingyen, kockázatmentesen végigjátszható.

---

## 📅 Projektmenedzsment & Tervezés
A fejlesztés nyomon követése és a feladatok kezelése agilis módszertan alapján történik:
*   **UI/UX Design:** [Figma Link](https://www.figma.com/design/JVdhxdx8fYKh8xU7QSv6nh/GearToShare?node-id=0-1&p=f&t=UEOKDSJ8uiwp14s0-0)
*   **Feladatkezelés:** [JIRA link](https://billory.atlassian.net/jira/software/projects/GEAR/boards/34)
*   **Verziókezelés:** Git (GitHub Flow ág-kezelési stratégiával)
