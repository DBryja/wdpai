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

    <title>LOGIN</title>
  </head>
  <body id="login-page" class="flex-row-center-center">
    <div class="flex-column-center-center">
      <h1>LOGIN</h1>
        <div class="messages">
            <?php
                if(isset($messages))
                    foreach($messages as $message)
                        echo $message;
            ?>
        </div>
      <form class="login-form flex-column-center-center" action="login" method="POST">
        <div class="email">
          <input type="email" name="email" placeholder="email" />
          <i class="fa-solid fa-at flex-column-center-center"></i>
        </div>
        <div class="password">
          <input type="password" name="password" placeholder="password" />
          <i class="fa-solid fa-key flex-column-center-center"></i>
        </div>
        <button type="submit">
            <i class="fa-solid fa-right-to-bracket"></i>
            SIGN IN
        </button>
      </form>
    </div>
  </body>
</html>
