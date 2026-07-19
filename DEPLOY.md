# Wdrożenie na Hostinger — instrukcja krok po kroku

Strona: **Torty Kuchciwróżki**
Repo: `https://github.com/Pa-jak/tortykuchciwr-zki.git`
Domena (tymczasowa): `https://forestgreen-wolverine-476671.hostingersite.com`

## 1. Podpięcie repozytorium GitHub (jednorazowo)

1. Zaloguj się do **hPanel** → wybierz stronę → **Zaawansowane → GIT**.
2. W sekcji *Utwórz nowe repozytorium* wpisz:
   - **Repozytorium:** `https://github.com/Pa-jak/tortykuchciwr-zki.git`
   - **Gałąź:** `main`
   - **Katalog:** zostaw puste (deploy prosto do `public_html`)
3. Kliknij **Utwórz**. Hostinger sklonuje repo do `public_html`.

> Jeśli repo jest prywatne: hPanel pokaże klucz SSH (deploy key) — dodaj go w GitHub:
> repo → *Settings → Deploy keys → Add deploy key* (bez uprawnień do zapisu)
> i użyj adresu SSH `git@github.com:Pa-jak/tortykuchciwr-zki.git` zamiast HTTPS.

## 2. Automatyczny deploy po każdym pushu (webhook)

1. W hPanel, w tej samej sekcji **GIT**, przy podpiętym repozytorium skopiuj **Webhook URL**.
2. W GitHub: repo → **Settings → Webhooks → Add webhook**:
   - **Payload URL:** wklejony adres z hPanel
   - **Content type:** `application/json`
   - **Which events:** *Just the push event*
3. Zapisz. Od teraz każdy `git push` na `main` automatycznie aktualizuje stronę.

## 3. Utworzenie bazy danych (jednorazowo)

Baza już istnieje w Hostingerze (utworzona w hPanel → **Bazy danych → MySQL**).
Trzeba tylko zaimportować schemat i dane startowe:

1. hPanel → **Bazy danych → phpMyAdmin** → otwórz bazę `u950362364_torty`.
2. Zakładka **Import** → wybierz plik `schema.sql` z tego repo → **Wykonaj**.

To tworzy tabele i wypełnia stronę domyślną treścią (edytowalną potem w panelu admina).

## 4. Utworzenie config.php na serwerze (jednorazowo)

Plik `config.php` zawiera hasła i **celowo nie ma go w repo** (jest w `.gitignore`) —
tworzy się go ręcznie na serwerze i deploy go nie nadpisuje.

1. hPanel → **Pliki → Menedżer plików** → `public_html`.
2. Skopiuj `config.sample.php` jako **`config.php`** (prawy przycisk → Copy → zmień nazwę).
3. Edytuj `config.php` i uzupełnij:
   - `DB_NAME` — nazwa bazy MySQL
   - `DB_USER` — użytkownik MySQL
   - `DB_PASS` — hasło do bazy
   - `ADMIN_PASSWORD` — **własne hasło do panelu admina** (wymyśl mocne)
   - `SITE_URL` — adres strony (po zmianie na docelową domenę zaktualizuj tutaj)

## 5. Zmiana hasła do panelu admina (w dowolnym momencie)

hPanel → **Menedżer plików** → `public_html/config.php` → edytuj linijkę:

```php
define('ADMIN_PASSWORD', 'TwojeNoweHaslo');
```

Zapisz — działa od razu, bez restartów.

## 6. Panel administracyjny

- Adres: `https://forestgreen-wolverine-476671.hostingersite.com/admin/`
- Logowanie hasłem z `config.php`.
- Edycja wszystkich treści strony + wgrywanie zdjęć (oferta, galeria, „O nas").
- Zdjęcia trafiają do katalogu `uploads/` na serwerze — **nie są w repo** i deploy ich nie rusza.

## 7. Codzienna praca (zmiany w kodzie)

```
git add .
git commit -m "opis zmiany"
git push
```

Po pushu strona aktualizuje się sama w ciągu ~1 minuty (webhook).

## Uwagi bezpieczeństwa

- **Nigdy nie commituj** `info.txt` ani `config.php` (oba są w `.gitignore`).
- Po zakończeniu projektu **zmień hasło do bazy** w hPanel (Bazy danych → zmień hasło użytkownika) i zaktualizuj `config.php` — hasło krążyło wcześniej w notatkach.
- Przy przejściu na docelową domenę: zmień `SITE_URL` w `config.php` — canonical, sitemap i JSON-LD podmienią się same. Dodatkowo zaktualizuj adres w `robots.txt` (linijka `Sitemap:`).
