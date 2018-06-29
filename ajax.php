<?php
session_start();
if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    exit();
}
header("Cache-Control: no-cache, must-revalidate"); // говорим браузеру, чтобы он не кешировал эту страницу
header("Pragma: no-cache");
header("Content-Type: application/json; charset=utf-8");

// проверяем есть ли переменная act (send или load), которая указывает нам, что делать
if (isset($_POST['act'])) {
    switch ($_POST['act']) {
        case "send" : // если она равняется send, вызываем функцию Send()
            Send();
            break;
        case "send_all" :
            SendAll();
            break;
        case "load" : // если она равняется load, вызываем функцию Load()
            Load();
            break;
        case "load_all" :
            LoadAll();
            break;
        case "find" :
            Find();
            break;
        default :
            exit();
    }
}

function Load()
{
    // тут мы получили переменную переданную нашим js'ом при помощи ajax
    // это:  $_POST['last'] - номер последнего сообщения которое загрузилось у пользователя
    $last_message_id = intval($_POST['last']);
    $friend_id = intval($_POST['friend_id']);
    $author_id = intval($_SESSION['id']);
    // выполняем запрос к базе данных для получения сообщений с номером большим чем $last_message_id
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $sql = "SELECT
            MessageId,
            MessageAuthorId,
            MessageText,
            Unix_timestamp(MessageDateTime) AS UnixDate
            FROM Message
            WHERE ((MessageAuthorId = " . $author_id . " AND MessageRecipientId = " . $friend_id . ") OR
            (MessageAuthorId = " . $friend_id . " AND MessageRecipientId = " . $author_id . ")) AND (MessageId > " . $last_message_id . ")
            ORDER BY MessageDateTime DESC
            LIMIT 50;";
    $query = mysqli_query($con, $sql);

    $num_rows = mysqli_num_rows($query);
    $messages = [];
    // проверяем есть ли какие-нибудь новые сообщения
    if ($num_rows > 0) {
        // следующий конструкцией мы получаем массив сообщений из нашего запроса

        while ($row = mysqli_fetch_array($query)) {
            array_push($messages, $row);
        }

        // записываем номер последнего сообщения
        // [0] - это вернёт нам первый элемент в массиве $messages, но так как мы выполнили запрос с параметром "DESC" (в обратном порядке),
        // то это получается номер последнего сообщения в базе данных
        $last_message_id = $messages[0]['MessageId'];

        // переворачиваем массив (теперь он в правильном порядке)
        $messages = array_reverse($messages);
    }
    $res['last_message_id'] = $last_message_id;
    $res['messages'] = $messages;
    echo json_encode($res);
    mysqli_close($con);
}

function LoadAll()
{
    // тут мы получили переменную переданную нашим js'ом при помощи ajax
    // это:  $_POST['last'] - номер последнего сообщения которое загрузилось у пользователя
    $last_message_id = intval($_POST['last']);
    // выполняем запрос к базе данных для получения сообщений с номером большим чем $last_message_id
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $sql = "SELECT
            m.MessageId,
            MessageAuthorId,
            m.MessageText,
            Unix_timestamp(m.MessageDateTime) AS UnixDate,
            u.UserPic,
            u.UserName
            FROM MessageAll m
            INNER JOIN User u ON m.MessageAuthorId = u.UserId
            WHERE MessageId > " . $last_message_id . "
            ORDER BY MessageDateTime DESC
            LIMIT 50;";
    $query = mysqli_query($con, $sql);

    $num_rows = mysqli_num_rows($query);
    $messages = [];
    // проверяем есть ли какие-нибудь новые сообщения
    if ($num_rows > 0) {
        // следующий конструкцией мы получаем массив сообщений из нашего запроса
        while ($row = mysqli_fetch_array($query)) {
            array_push($messages, $row);
        }

        // записываем номер последнего сообщения
        // [0] - это вернёт нам первый элемент в массиве $messages, но так как мы выполнили запрос с параметром "DESC" (в обратном порядке),
        // то это получается номер последнего сообщения в базе данных
        $last_message_id = $messages[0]['MessageId'];

        // переворачиваем массив (теперь он в правильном порядке)
        $messages = array_reverse($messages);
    }
    $res['last_message_id'] = $last_message_id;
    $res['messages'] = $messages;
    echo json_encode($res);
    mysqli_close($con);
}

// Функция выполняет сохранение сообщения в базе данных
function Send()
{
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $author_id = intval($_SESSION['id']);
    $friend_id = intval($_POST['friend_id']);
    $text = mysqli_real_escape_string($con, trim($_POST['text']));
    $sql = "INSERT INTO Message (MessageAuthorId, MessageRecipientId, MessageText, MessageDateTime)
            VALUES (" . $author_id . ", " . $friend_id . ", '" . $text . "', NOW());";
    $success = mysqli_query($con, $sql);
    mysqli_close($con);
    if ($success) {
        $response['status'] = 'ok';
    } else {
        $response['status'] = 'error';
    }
    echo json_encode($response);
}

function SendAll()
{
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $author_id = intval($_SESSION['id']);
    $text = mysqli_real_escape_string($con, trim($_POST['text']));
    $sql = "INSERT INTO MessageAll (MessageAuthorId, MessageText, MessageDateTime)
            VALUES (" . $author_id . ", '" . $text . "', NOW());";
    $success = mysqli_query($con, $sql);
    mysqli_close($con);
    if ($success) {
        $response['status'] = 'ok';
    } else {
        $response['status'] = 'error';
    }
    echo json_encode($response);
}

function Find()
{
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $text = mysqli_real_escape_string($con, trim($_POST['val']));
    $author_id = intval($_SESSION['id']);
    $sql = "SELECT u.UserId, u.UserName, u.UserPic FROM User u WHERE u.UserId NOT IN (SELECT f.FriendOneId
            FROM Friends f
            WHERE FriendTwoId = " . $author_id . "
            UNION SELECT f2.FriendTwoId
            FROM Friends f2
            WHERE f2.FriendOneId = " . $author_id . ") AND u.UserId != " . $author_id . " AND u.UserName LIKE '%" . $text . "%' LIMIT 50;";
    $query = mysqli_query($con, $sql);
    $num_rows = mysqli_num_rows($query);
    if ($num_rows > 0) {
        $finds = [];
        while ($row = mysqli_fetch_array($query)) {
            array_push($finds, $row);
        }
        $res['finds'] = $finds;
        echo json_encode($res);
    }
    mysqli_close($con);
}