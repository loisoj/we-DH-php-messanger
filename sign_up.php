<?php
session_start();
if (!empty($_SESSION['login']) && !empty($_SESSION['id'])) {
    header("Location: chat.php");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Messenger</title>
    <meta name="theme-color" content="#00897B">
    <link href="css/icons.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <style type="text/css">
        body {
            background-color: #00897B;
        }

        h1, h3 {
            color: #ffffff;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
<header>
    <h1 class="center hide-on-small-only">
        Регистрация
    </h1>
    <h3 class="center hide-on-med-and-up">
        Регистрация
    </h3>
</header>
<main>
    <div class="row">
        <div class="col s0 m2 l3">
            <pre></pre>
        </div>
        <div class="col s12 m8 l6">
            <div class="card">
                <div class="card-content">
                    <div class="row center">
                        <div class="col s12">
                            <img src="ico/logo-dark.png">
                        </div>
                    </div>
                    <div class="row">
                        <form enctype="multipart/form-data" class="col s12" action="save_user.php" method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="login_field" name="login" type="text" class="validate" length="50"
                                           maxlength="50" required>
                                    <label for="login_field">Придумайте логин</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password_field" name="password" type="password" class="validate"
                                           length="100" maxlength="100" required>
                                    <label for="password_field">Придумайте пароль</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password_repeat_field" name="password_repeat" type="password"
                                           class="validate" length="100" maxlength="100" required>
                                    <label for="password_repeat_field">Введите пароль еще раз</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p class="center-align" style="color:#00b0ff">Все пробелы будут удалены!</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn">
                                        <span>Выбрать аватар (необязательно)</span>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="20971520"/>
                                        <input name="avatar" type="file" accept="image/*">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p class="center-align" style="color:#777">Изображение должно иметь формат JPEG,
                                        PNG, GIF или PNG (до 20 МБ)</p>
                                </div>
                            </div>
                            <div class="row center">
                                <div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action">
                                        Зарегистрироваться
                                        <i class="material-icons right">&#xE163;</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s0 m2 l3">
            <pre></pre>
        </div>
    </div>
</main>
<footer></footer>
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
</body>
</html>