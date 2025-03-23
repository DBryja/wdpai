<?php
require_once __DIR__."/../utils/ComponentLoader.php";

use repository\CarRepository;
$carRepository = new CarRepository();
$cars = $carRepository->findAll();

ComponentLoader::load('header', ['title' => 'Homepage']);
?>
<main>
    <h2>Welcome to the Homepage</h2>
    <div class="cars">
        <aside class="cars-filters">
            <form id="filters-form">
                <div class="autocomplete-wrapper">
                    <label for="brand">
                        Brand:
                        <input type="text" id="brand" name="brand" autocomplete="off">
                    </label>
                    <div tabindex="-1" id="brand-list" class="autocomplete-list"></div>
                </div>
                <div class="autocomplete-wrapper disabled">
                    <label for="model" >
                        Model:
                        <input disabled type="text" id="model" name="model" autocomplete="off">
                    </label>
                    <div tabindex="-1" id="model-list" class="autocomplete-list"></div>
                </div>
                <label for="price">
                    Price:
                    <input type="number" id="price-min" name="price-min" min="0" max="100000" placeholder="Min Price">
                    <input type="number" id="price-max" name="price-max" min="0" max="100000" placeholder="Max Price">
                </label>
                <label for="year">
                    Year:
                    <input type="number" id="year-min" name="year-min" min="1900" max="2100" placeholder="Min Year">
                    <input type="number" id="year-max" name="year-max" min="1900" max="2100" placeholder="Max Year">
                </label>
                <label for="isNew">
                    Is New:
                    <input type="checkbox" id="isNew" name="isNew">
                </label>
                <input type="submit" value="Apply Filters">
            </form>
        </aside>
        <div class="cars-list">
            <?php foreach ($cars as $car): ?>
                <a href="/car?id=<?= $car["id"] ?>" class="car-card">
                    <div class="car-card__image">
                       <img src="<?= $carRepository->getCarThumbnail($car["id"]) ?>" alt="<?= $car['title'] ?>">
                    </div>
                    <h3 class="car-card__title"><?= $car['title'] ?></h3>
                    <div class="car-card__details">
                        <p class="car-card__price">Price: <?= $car['price'] ?></p>
                        <p class="car-card__year">Year: <?= $car['year'] ?></p>
                        <p class="car-card__isNew">Is New: <?= $car['is_new'] ? 'Yes' : 'No' ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<script>
//    TODO:
//          Dodać sortowania po cenie i roku
//          Dodać zaawansowane filtry
//          Dodać paginację
    const filtersForm = document.getElementById('filters-form');
    const carsList = document.querySelector('.cars-list');

    filtersForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(filtersForm);
        const response = await fetch('/api/getCarsByAttributes', {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const cars = await response.json();
        carsList.innerHTML = cars.map(car => `
            <a href="/car?id=${car.id}" class="car-card">
                <div class="car-card__image">
                ${car.images.length ? `<img src="${car.images[0]}" alt="${car.title}"/>` : ''}
                </div>
                <h3 class="car-card__title">${car.title}</h3>
                <div class="car-card__details">
                    <p class="car-card__price">Price: ${car.price}</p>
                    <p class="car-card__year">Year: ${car.year}</p>
                    <p class="car-card__isNew">Is New: ${car.isNew ? 'Yes' : 'No'}</p>
                </div>
            </a>
        `).join('');
    });
</script>
<script src="/public/js/autocomplete.js"></script>

<?php
ComponentLoader::load('footer');
?>

