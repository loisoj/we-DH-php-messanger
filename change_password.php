<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Messenger</title>
    <meta name="theme-color" content="#00897B">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="background-color:#00897B;">
<?php
session_start();

header('Content-type: text/html; charset=utf-8');
if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['old_pass'])) {
    $old_pass = trim($_POST['old_pass']);
    if ($old_pass == '' || strlen($old_pass) > 100) {
        unset($old_pass);
    }
}

if (isset($_POST['new_pass'])) {
    $new_pass = trim($_POST['new_pass']);
    if ($new_pass == '' || strlen($new_pass) > 100) {
        unset($new_pass);
    }
}

if (isset($_POST['new_pass_repeat'])) {
    $new_pass_repeat = trim($_POST['new_pass_repeat']);
    if ($new_pass_repeat == '' || strlen($new_pass_repeat) > 100) {
        unset($new_pass_repeat);
    }
}

if (empty($old_pass) || empty($new_pass) || empty($new_pass_repeat)) {
    exit('<h3 style="color:#fff;" class="center">Заполните поля!</h3>
          <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
}

if ($new_pass != $new_pass_repeat) {
    exit('<h3 style="color:#fff;" class="center">Пароли не совпадают!</h3>
          <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
}

require_once 'db_config.php';
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$old_pass = str_replace(" ", "", mysqli_real_escape_string($con, $old_pass));
$new_pass = str_replace(" ", "", mysqli_real_escape_string($con, $new_pass));
$user_id = intval($_SESSION['id']);
$result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserId`=" . $user_id);
$myrow = mysqli_fetch_array($result);
if (empty($myrow['UserPassword'])) {
    mysqli_close($con);
    exit('<h3 style="color:#fff;" class="center">Извините, произошла ошибка! Попробуйте ещё раз.</h3>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
}
if ($myrow['UserPassword'] == crypt($old_pass, $myrow['UserPassword'])) {
    $blowfish_salt = "$2y$10$" . bin2hex(openssl_random_pseudo_bytes(22));
    $hashed_password = crypt($new_pass, $blowfish_salt);
    $sql = "UPDATE User SET UserPassword = '" . $hashed_password . "' WHERE UserId = " . $user_id . ";";
    $success = mysqli_query($con, $sql);
    if ($success) {
        if (mysqli_affected_rows($con) == 0) {
            $success = false;
        }
    }
    if ($success) {
        mysqli_close($con);
        exit('<h3 style="color:#fff;" class="center">Успешно!</h3>
        <div class="center-align"><a href="/" class="btn waves-effect waves-light">На главную &raquo;</a></div>
        </body>
        </html>
        ');
    } else {
        mysqli_close($con);
        exit('<h3 style="color:#fff;" class="center">Извините, произошла ошибка! Попробуйте ещё раз.</h3>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
    }
} else {
    mysqli_close($con);
    exit('<h3 style="color:#fff;" class="center">Пароль неверный!</h3>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
}
