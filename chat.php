<?php
session_start();
if (isset($_GET['out'])) {
    unset($_SESSION['id']);
    unset($_SESSION['login']);
    session_destroy();
    header("Location: index.php");
}

if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("Location: index.php");
}

if (isset($_GET['friend_id'])) {
    $id = intval($_SESSION['id']);
    $friend_id = intval($_GET['friend_id']);
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $res = mysqli_query($con, "SELECT * FROM `Friends` WHERE (`FriendOneId`='$friend_id' AND `FriendTwoId`='$id') OR (`FriendOneId`='$id' AND `FriendTwoId`='$friend_id')");
    if (mysqli_num_rows($res) > 0) {
        $result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserId`='$friend_id'");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $main_text = $row['UserName'];
                $friend_avatar_path = $row['UserPic'];
            }
        } else {
            header("Location: friends.php");
        }
    } else {
        header("Location: friends.php");
    }
} else {
    header("Location: friends.php");
}
include 'header.php';
include 'chat_body.html';
include 'footer.html';
?>
<script type="text/javascript" src="js/chat.js"></script>
<script type="text/javascript">
    var last_message_id = 0, // номер последнего сообщения, что получил пользователь
        f_id = <?= $friend_id ?>,
        friend_avatar_path = "<?= $friend_avatar_path ?>",
        author_avatar_path = "<?= $author_avatar_path ?>",
        friend_name = "<?= htmlspecialchars($main_text) ?>",
        first_load_happened = false;

    $(document).ready(function () { //запускаем, когда документ загрузится
        var renderMessages = function (messages) {
            var html = '';
            messages.forEach(function (message) {
                var avatar, viewClass;
                if (parseInt(message.MessageAuthorId) === f_id) {
                    viewClass = "cv-friend";
                    avatar = friend_avatar_path;
                } else {
                    viewClass = "cv-host";
                    avatar = author_avatar_path;
                }
                html += '<div class="chat-view ' + viewClass + '">';
                html += '<div class="cv-avatar"><img class="circle" src="' + avatar + '"></div>';
                html += '<div class="cv-text">' + addBrs(escapeHtml(message.MessageText)) + '<br><span class="date">' + formatUnixTimestamp(message.UnixDate) + '</span></div>';
                html += '</div>';

                // html5 уведомление
                if(first_load_happened && parseInt(message.MessageAuthorId) === f_id) {
                    sendNotification(
                        friend_name,
                        {
                            body: escapeHtml(message.MessageText),
                            icon: avatar,
                            dir: 'auto'
                        }
                    );
                }
            });
            $loader.addClass("hide");
            $chat.append(html);
            first_load_happened = true;
        };

        var Load = function () {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: 'ajax.php',
                data: {
                    act: "load",
                    last: last_message_id,
                    friend_id: f_id
                },
                success: function (data) {
                    last_message_id = data.last_message_id;
                    renderMessages(data.messages);
                    if (data.messages.length > 0)
                        scrollToBottom();
                },
                complete: function () {
                    setTimeout(Load, 5000);
                }
            });
        };

        var Send = function () {
            var t = $textarea.val();
            t = $.trim(t);
            if (t !== "") {
                $loader.removeClass("hide");
                scrollToBottom();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: 'ajax.php',
                    data: {
                        act: "send",
                        friend_id: f_id,
                        text: t
                    },
                    success: function (data) {
                        if (data.status === 'ok') {
                            $textarea.val(""); // очистим поле ввода сообщения
                            $textarea.height(22);
                        } else {
                            console.log("Ошибка сервера при отправке сообщения");
                        }
                    },
                    error: function () {
                        console.log("Ошибка AJAX-запроса при отправке сообщения");
                    }
                });
            }
        };

        $sendButton.click(Send);
        $chatForm.submit(function (e) {
            e.preventDefault();
            Send();
        });
        $textarea.focus();
        Load();
    });
</script>
</body>
</html>