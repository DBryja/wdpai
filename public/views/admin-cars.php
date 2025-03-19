<?php
require_once __DIR__."/../utils/ComponentLoader.php";
ComponentLoader::load('header', ['title' => 'Admin']);
?>

<main>
    <?php ComponentLoader::load("admin-nav"); ?>
    <h2>Welcome to the Admin Panel --- Cars Archive</h2>
</main>

<?php
ComponentLoader::load('footer');
?>
