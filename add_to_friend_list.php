<?php
session_start();
header('Content-type: text/plain; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("Location: index.php");
}

$author_id = intval($_SESSION['id']);

if (isset($_GET['friend_id'])) {
    $friend_id = $_GET['friend_id'];
    $friend_id = trim($friend_id);
    if ($friend_id == '' || $friend_id == $author_id) {
        unset($friend_id);
    }
}

if (empty($friend_id)) {
    header("Location: find_contacts.php");
} else {
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $friend_id = intval($friend_id);
    $result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserId`='$friend_id'");
    $myrow = mysqli_fetch_array($result);
    if (empty($myrow)) {
        header("Location: find_contacts.php");
    } else {
        if (mysqli_query($con, "INSERT INTO `Friends` (`FriendOneId`,`FriendTwoId`) VALUES ('$author_id','$friend_id')")) {
            header("Location: chat.php?friend_id=" . $friend_id);
        } else {
            exit("Ошибка");
        }
    }
    mysqli_close($con);
}
