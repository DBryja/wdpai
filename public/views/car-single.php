<?php
require_once __DIR__."/../utils/ComponentLoader.php";

use repository\CarRepository;
$carRepository = new CarRepository();
$car = $carRepository->findByIdWithDetails($_GET['id']);
ComponentLoader::load('header', ['title' => $car['title']]);
?>

<main class="single">
    <h1><?php echo $car["title"] ?></h1>
    <div class="single-details">
        <?php foreach ($car as $key => $value): ?>
            <p><?php echo htmlspecialchars($key) . ': ' . htmlspecialchars($value); ?></p>
        <?php endforeach; ?>
    </div>

    <h3>Images:</h3>
    <div class="single-images">
        <?php
        $uploadDir = __DIR__ . "/../uploads/cars/" . htmlspecialchars($car['id']) . "/";
        if (is_dir($uploadDir)) {
            $images = array_diff(scandir($uploadDir), array('.', '..'));
            foreach ($images as $image) {
                $imagePath = "/public/uploads/cars/" . htmlspecialchars($car['id']) . "/" . htmlspecialchars($image);
                echo "<img src='{$imagePath}' alt='Car Image' />";
            }
        }
        ?>
    </div>
</main>

<?php
ComponentLoader::load('footer');
?>
