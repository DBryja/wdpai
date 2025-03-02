<?php
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindAR PHP Starter</title>
    <style>
      * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.hero {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    text-align: center;
    padding: 20px;
}

.hero-content {
    max-width: 600px;
}

h1 {
    font-size: 2.5rem;
}

p {
    font-size: 1.2rem;
    margin: 15px 0;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    background: white;
    color: #764ba2;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background 0.3s;
}

.btn:hover {
    background: #ddd;
}

section {
    padding: 50px 20px;
    text-align: center;
}

footer {
    text-align: center;
    padding: 20px;
    background: #222;
    color: white;
}

    </style>
</head>
<body>

<header class="hero">
    <div class="hero-content">
        <h1>Rozszerzona rzeczywistość z MindAR</h1>
        <p>Twórz interaktywne doświadczenia AR w przeglądarce z PHP!</p>
        <a href="#about" class="btn">Dowiedz się więcej</a>
    </div>
</header>

<section id="about">
    <h2>O projekcie</h2>
    <p>Ten projekt to prosty starter dla aplikacji AR z wykorzystaniem MindAR i backendu PHP.</p>
</section>

<footer>
    <p>&copy; <?= date("Y") ?> MindAR PHP Starter</p>
</footer>

</body>
</html>
