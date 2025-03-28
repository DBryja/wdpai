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
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <link href="public/css/index.css" rel="stylesheet" />
    <title>DASHBOARD</title>
  </head>
  <body>
    <nav>
      <div class="menu">
        <a href="#" class="icon-group">
          <i class="fa-solid fa-home"></i>
          <span>Home</span>
        </a>
        <a href="#" class="icon-group">
          <i class="fa-solid fa-info"></i>
          <span>About</span>
        </a>
      </div>
      <a href="#" class="login icon-group">
        <i class="fa-solid fa-user"></i>
        <span>Login</span>
      </a>
    </nav>
    <main>
      <section>
          <?php
            if(isset($cards))
                foreach($cards as $card)
                    echo "<div class='card'>
                            <img src='public/uploads/{$card->getImage()}' alt='' />
                            <p class='flex-row-center-center'>{$card->getTitle()}</p>
                            </div>";
          ?>
        <div class="card">
          <img src="https://picsum.photos/200" alt="" />
          <p class="flex-row-center-center">card</p>
        </div>
        <div class="card">
          <img src="https://picsum.photos/200" alt="" />
          <p class="flex-row-center-center">card</p>
        </div>
        <div class="card">
          <img src="https://picsum.photos/200" alt="" />
          <p class="flex-row-center-center">card</p>
        </div>
      </section>
      <aside>
        <div class="sticky">
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod nulla cupiditate eos facilis deleniti molestiae
          dignissimos obcaecati laudantium, ducimus incidunt accusamus totam omnis sint nemo, esse excepturi,
          repellendus sunt eum!
        </div>
      </aside>
    </main>
  </body>
</html>
