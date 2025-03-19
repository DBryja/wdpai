<?php
require_once __DIR__."/../utils/ComponentLoader.php";
ComponentLoader::load('header', ['title' => 'Admin']);
?>

<main>
    <h2>Welcome to the Admin Panel</h2>
</main>

<?php
ComponentLoader::load('footer');
?>
