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
    <title>Add-project</title>
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
    <div class="flex-column-center-center">
        <h1>Add Card</h1>
        <div class="messages">
            <?php
            if(isset($messages))
                foreach($messages as $message)
                    echo $message;
            ?>
        </div>
        <form class="login-form flex-column-center-center"
              action="addProject"
              method="POST"
              enctype="multipart/form-data"
        >
            <input type="text" name="title" placeholder="title" />
            <textarea name="description" placeholder="description"></textarea>
            <input type="file" accept="image/png, image/jpg, image/jpeg, image/webp" name="file" />
            <button type="submit">
                <i class="fa-solid fa-right-to-bracket"></i>
                SEND
            </button>
        </form>
    </div>
</main>
</body>
</html>
