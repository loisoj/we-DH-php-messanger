<?php
require_once 'db_config.php';
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$id = intval($_SESSION['id']);
$result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserId`='$id'");
$myrow = mysqli_fetch_array($result);
$author_avatar_path = $myrow['UserPic'];
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Messenger</title>
    <meta name="theme-color" content="#00897B">
    <link href="css/icons.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/mystyle.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
<header>
    <nav id="nav" class="teal darken-1">
        <div class="container"><span class="brand-logo" style="font-size:20px;">
            <?php
            $str = htmlspecialchars($main_text);
            if (mb_strlen($str) > 15) {
                $str = substr($str, 0, 15) . "&#8230;";
            }
            echo $str;
            ?>
        </span>
            <ul class="right hide-on-med-and-down">
                <li>
                    <a href='?out'>
                        <img class="circle" style="height:35px;padding-top:15px;" src="<?= $author_avatar_path ?>">
                        <?php
                        $str = htmlspecialchars($_SESSION['login']);
                        if (mb_strlen($str) > 15) {
                            $str = substr($str, 0, 15) . "&#8230;";
                        }
                        ?>
                        &ensp;<?= $str ?>&ensp;|&ensp;Выйти
                    </a>
                </li>
            </ul>
            <ul id="slide-out" class="side-nav fixed">
                <li class="teal delete_default_padding">
                    <div id="logotip">
                        <div class="row" style="width:1px;"></div>
                        <div class="center">
                            <img src="ico/logo-light.png">
                        </div>
                    </div>
                </li>

                <li id="li-friends" class="delete_default_padding">
                    <a href="friends.php">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_friends.svg"></div>
                            <div class="col s10">Собеседники</div>
                        </div>
                    </a>
                </li>
                <li id="li-im" class="delete_default_padding">
                    <a href="im.php">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_chat.svg"></div>
                            <div class="col s10">Чаты</div>
                        </div>
                    </a>
                </li>
                <li id="li-im-all" class="delete_default_padding">
                    <a href="chat-all.php">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_chat_all.svg"></div>
                            <div class="col s10">Общий чат</div>
                        </div>
                    </a>
                </li>
                <li id="li-find-contacts" class="delete_default_padding">
                    <a href="find_contacts.php">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_search.svg"></div>
                            <div class="col s10">Поиск собеседников</div>
                        </div>
                    </a>
                </li>
                <li id="li-profile" class="delete_default_padding">
                    <a href="profile.php">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_avatar.svg"></div>
                            <div class="col s10">Мой профиль</div>
                        </div>
                    </a>
                </li>
                <li class="delete_default_padding">
                    <a href='?out'>
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_quit.svg"></div>
                            <div class="col s10">Выйти из системы</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <a href="#" data-activates="slide-out" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
    </nav>
</header>