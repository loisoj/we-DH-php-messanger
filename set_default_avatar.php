<?php
session_start();
header('Content-type: text/plain; charset=utf-8');
if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("Location: index.php");
} else {
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $id = intval($_SESSION['id']);
    if (mysqli_query($con, "UPDATE `User` SET `UserPic`='userpics/default.jpg' WHERE `UserId`='$id'")) {
        mysqli_close($con);
        header("Location: friends.php");
    } else {
        exit("Ошибка :(");
    }
}
