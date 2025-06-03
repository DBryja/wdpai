# CMS Dealera Samochodowego

## Przegląd

### O Projekcie
Ten projekt to podstawowy System Zarządzania Treścią (CMS) dla dealera samochodowego.  
Pozwala użytkownikom przeglądać, filtrować i sortować samochody na podstawie różnych atrybutów, takich jak cena, rok, przebieg i moc silnika.

## Informacje Techniczne

### Zbudowany z użyciem
- PHP
- PostgreSQL
- JavaScript
- HTML
- CSS
- PhpStorm
- Docker

## Strony

- **Home Page**: ( / ) Wyświetla listę samochodów z opcjami filtrowania i sortowania.
- **Car Details Page**: ( /car?id={id} ) Wyświetla szczegółowe informacje o konkretnym samochodzie.
- **Login Page**: ( /adminLogin ) Umożliwia zalogowanie się do panelu administracyjnego.
- **Admin Page**: ( /admin ) Pozwala na dodawanie, edytowanie i usuwanie samochodów.
- **Admin Manage Cars Page**: ( /admin/cars ) Wyświetla listę samochodów z opcjami dodawania, edytowania i usuwania każdego samochodu.
- **Admin Manage Users Page**: ( /admin/users ) Wyświetla listę użytkowników z opcjami dodawania i usuwania użytkowników.

## Funkcje

- **Lista samochodów**: Przeglądanie listy samochodów z informacjami o cenie, roku, przebiegu i mocy silnika.
- **Filtrowanie**: Filtrowanie samochodów na podstawie atrybutów, takich jak marka, model, przedział cenowy i zakres rocznika.
- **Sortowanie**: Sortowanie samochodów według ceny, roku, przebiegu i mocy silnika w kolejności rosnącej lub malejącej.
- **Infinite Scroll**: Automatyczne ładowanie kolejnych samochodów podczas przewijania strony.
- **Autocomplete**: Podpowiedzi dla pól wyboru marki i modelu samochodu.
- **Zagnieżdżone trasy**: Nawigacja do różnych stron z wykorzystaniem tras zagnieżdżonych.
- **Uwierzytelnianie**: Logowanie do panelu administracyjnego przy użyciu nazwy użytkownika i hasła.
- **Bezpieczeństwo**: Zapobieganie nieautoryzowanemu dostępowi do panelu administratora i endpointów API.

### Zaimplementowane funkcje:
- **Server Side Rendering**: Pierwsze zapytanie na stronie głównej jest renderowane po stronie serwera.
- **Client Side Rendering**: Implementacja filtrowania, sortowania, paginacji oraz infinite scroll po stronie klienta.
- **REST API**: API do pobierania i modyfikowania danych (dostępne pod ścieżką /api/), część endpointów wymaga nagłówka Admin-Email i Session-Token.
- **Autoryzacja**: Logowanie do panelu administratora, wylogowanie, zabezpieczenie ścieżki /admin/ (zarówno dla metod POST, jak i GET).
- **CRUD**: Dodawanie, edytowanie, usuwanie samochodów.
- **Relacje bazodanowe**:
    - 1:1 (Car -> CarDetails)
    - 1:N (Models -> Car)
    - N:M (Car <- car_tags -> Tags)
- **Loader komponentów**: Pośrednik umożliwiający wczytywanie komponentów z przekazywaniem argumentów, np. tytułu do nagłówka.  