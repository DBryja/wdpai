<?php
require_once __DIR__."/../utils/ComponentLoader.php";

ComponentLoader::load('header', ['title' => 'Homepage']);
?>

    <main>
        <h2>Welcome to the Homepage</h2>
        <!-- Add homepage content here -->
    </main>

<?php
ComponentLoader::load('footer');
?>