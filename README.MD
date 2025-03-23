# Car Dealership CMS

## Overview

### About the Project
This project is a basic Content Management System (CMS) for a car dealership. 
It allows users to browse, filter, and sort cars based on various attributes such as price, year, mileage, and engine power.

## Technical Information

### Built With
- PHP
- PostgreSQL
- JavaScript
- HTML
- CSS
- PhpStorm
- Docker

## Pages

- **Home Page**: ( / ) Displays a list of cars with filtering and sorting options.
- **Car Details Page**: ( /car?id={id} ) Displays detailed information about a specific car.
- **Login Page**: ( /adminLogin ) Allows users to log in to the admin page.
- **Admin Page**: ( /admin ) Allows users to add, edit, and delete cars.
- **Admin Manage Cars Page**: ( /admin/cars ) Displays a list of cars with options to add, edit or delete each car.
- **Admin Manage Users Page**: ( /admin/users ) Displays a list of users with options to add or delete each user.

## Features

- **Car Listings**: View a list of cars with details such as price, year, mileage, and engine power.
- **Filtering**: Filter cars based on attributes like brand, model, price range, and year range.
- **Sorting**: Sort cars by price, year, mileage, and engine power in ascending or descending order.
- **Infinite Scroll**: Load more cars as the user scrolls down the page.
- **Nested Routing**: Navigate to different pages using nested routes.
- **Authentication**: Log in to the admin page using a username and password.
- **Security**: Prevent unauthorized access to the admin page and api endpoints.

### Zaimplementowane funkjce:
- **Server Side Rendering**: Pierwsze zapytanie na homepage jest renderowane po stronie serwera
- **Client Side Rendering**: Implementacja filtrowania, sortowania, paginacji, infinite scroll po stronie klienta
- **REST API**: API do pobierania i modyfikowania danych (po ścieżce /api/), część endpointów wymaga nagłówka Admin-Email i Session-Token
- **Autoryzacja**: Logowanie do panelu admina, wylogowanie, zabezpieczenie ścieżki /admin/ (zarówno POST jak i GET)
- **CRUD**: Dodawanie, edytowanie, usuwanie samochodów
- **Relacje bazo-danowe**: 1:1 (Car -> CarDetails), 1:N (Models -> Car), N:M (Nie implementowałem, mógłym wcisnąć np. Car -> CarTags lub Car -> Colors)
- **Loader komponentów**: Pośrednik pozwalający na wczytywanie komponentów podając przy tym argumenty np title do headera