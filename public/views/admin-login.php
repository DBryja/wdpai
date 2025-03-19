<?php
require_once __DIR__."/../utils/ComponentLoader.php";
ComponentLoader::load('header', ['title' => 'Admin Login']);
?>

<main>
    <h2>Welcome to the Admin Login</h2>
    <p>Please log in to access the admin panel</p>
    <div class="messages">
        <?php
            if(isset($messages)) {
                foreach ($messages as $message) {
                    echo "<p>$message</p>";
                }
            }
        ?>
    </div>
    <form action="/adminLogin" method="POST">
        <label for="email">email:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</main>


<?php
ComponentLoader::load('footer');
?>

