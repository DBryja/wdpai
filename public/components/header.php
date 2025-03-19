<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
          />
    <link href="/public/css/index.css" rel="stylesheet" />
    <title><?php echo $title ?? "Car Dealership" ?></title>
  </head>
  <body>
  <header class="header">
    <nav>
        <a href="/">Homepage</a>
        <a href="/admin">Admin</a>
    </nav>
      <form action="/logout" method="post">
          <button type="submit">Logout</button>
      </form>
  </header>