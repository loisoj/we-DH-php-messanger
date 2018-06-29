<?php
session_start();
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '') {
        unset($login);
    }
}
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if ($password == '') {
        unset($password);
    }
}
if (empty($login) || empty($password)) {
    header('Content-type: text/html; charset=utf-8');
    exit('<!DOCTYPE html>
        <html lang="ru">
        <head>
        <meta charset="UTF-8">
        <title>Messenger</title>
        <meta name="theme-color" content="#00897B">
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <body style="background-color:#00897B;">
        <h3 style="color:#fff;" class="center">Заполните поля!</h3>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>');
} else {
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $login = trim($login);
    $login = mysqli_real_escape_string($con, $login);
    $password = trim($password);
    $password = mysqli_real_escape_string($con, $password);
    $result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserName`='$login'");
    $myrow = mysqli_fetch_array($result);
    if (empty($myrow['UserPassword'])) {
        mysqli_close($con);
        header('Content-type: text/html; charset=utf-8');
        exit('<!DOCTYPE html>
            <html lang="ru">
            <head>
            <meta charset="UTF-8">
            <title>Messenger</title>
            <meta name="theme-color" content="#00897B">
            <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <body style="background-color:#00897B;">
            <h3 style="color:#fff;" class="center">Пользователя с таким логином не существует!</h3>
            <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
            </body>
            </html>');
    } else {
        if ($myrow['UserPassword'] == crypt($password, $myrow['UserPassword'])) {
            $_SESSION['login'] = $myrow['UserName'];
            $_SESSION['id'] = $myrow['UserId'];
            mysqli_close($con);
            header("Location: chat.php");
        } else {
            mysqli_close($con);
            header('Content-type: text/html; charset=utf-8');
            exit('<!DOCTYPE html>
                <html lang="ru">
                <head>
                <meta charset="UTF-8">
                <title>Messenger</title>
                <meta name="theme-color" content="#00897B">
                <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                </head>
                <body style="background-color:#00897B;">
                <h3 style="color:#fff;" class="center">Пароль неверный!</h3>
                <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                </body>
                </html>');
        }
    }
}
