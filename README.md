# Veb aplikacija za Pub Kviz

Seminarski rad iz predmeta Serverske veb tehnologije.

## Opis
Aplikacija služi za organizaciju i praćenje pub kviza. Vodi se evidencija sezona, timovi se registruju, a rang lista se automatski ažurira nakon svakog održanog događaja.

## Članovi tima
- Andrija Zečević, 2023/0089
- [Ime Prezime drugara], [broj indeksa drugara]

## Tehnologije
- Laravel (PHP)
- SQLite baza podataka
- Laravel Sanctum za autentifikaciju
- Postman za testiranje

## Struktura baze
- seasons - sezone kviza (naziv, datum početka, datum završetka)
- teams - timovi, svaki tim pripada jednoj sezoni
- events - kviz večeri, svako pripada jednoj sezoni
- results - osvojeni poeni jednog tima na jednom događaju

Veze: jedna sezona ima više timova i više događaja. Jedan događaj ima više rezultata. Jedan tim ima više rezultata.

## Pokretanje projekta
1. git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-veb_aplikacija_za_pub_kviz_2023_0089.git pub-kviz
2. cd pub-kviz
3. composer install
4. copy .env.example .env
5. php artisan key:generate
6. php artisan migrate 