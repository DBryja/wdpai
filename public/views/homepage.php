<?php
require_once __DIR__."/../utils/ComponentLoader.php";

use repository\CarRepository;
$carRepository = new CarRepository();
$cars = $carRepository->findByAttributes([], 1, 9);

ComponentLoader::load('header', ['title' => 'Homepage']);
?>
<main>
    <h2>Welcome to the Homepage</h2>
    <div class="cars">
        <aside class="cars-filters">
            <form id="filters-form">
                <input type="hidden" id="current-page" name="page" value="1">
                <input type="hidden" id="current-page" name="per-page" value="9">
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

                <label for="sort">
                    Sort by:
                    <select id="sort" name="sort">
                        <option value="">Default</option>
                        <option value="price-asc">Price Ascending</option>
                        <option value="price-desc">Price Descending</option>
                        <option value="year-asc">Year Ascending</option>
                        <option value="year-desc">Year Descending</option>
                        <option value="mileage-asc">Lowest Mileage</option>
                        <option value="mileage-desc">Highest Mileage</option>
                        <option value="power-asc">Lowest Engine Power</option>
                        <option value="power-desc">Highest Engine Power</option>
                    </select>
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
                        <p class="car-card__price"><?= $car['price'] ?>$</p>
                        <p class="car-card__year">Y: <?= $car['year'] ?></p>
                        <p class="car-card__mileage">Mil: <?= $car['mileage'] ?></p>
                        <p class="car-card__hp">HP: <?= $car['hp'] ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <div id="loader">Loading...</div>
    </div>
</main>
<script src="/public/js/autocomplete.js"></script>
<script src="/public/js/infiniteScroll.js"></script>

<?php
ComponentLoader::load('footer');
?>

