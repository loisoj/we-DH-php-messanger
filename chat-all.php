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

$main_text = 'Общий чат';
include 'header.php';
include 'chat_body.html';
include 'footer.html'
?>
<script type="text/javascript" src="js/chat.js"></script>
<script type="text/javascript">
    var last_message_id = 0, // номер последнего сообщения, что получил пользователь
        a_id = <?= intval($_SESSION['id']) ?>,
        first_load_happened = false;

    $(document).ready(function () { //запускаем, когда документ загрузится
        var renderMessages = function (messages) {
            var html = '';
            messages.forEach(function (message) {
                var avatar, viewClass;
                if (parseInt(message.MessageAuthorId) === a_id) {
                    viewClass = "cv-host";
                } else {
                    viewClass = "cv-friend";
                }
                html += '<div class="chat-view ' + viewClass + '">';
                html += '<div class="cv-avatar"><img class="circle" src="' + message.UserPic + '"></div>';
                html += '<div class="cv-text">' + addBrs(escapeHtml(message.MessageText)) + '<br><span class="date"><strong>' + escapeHtml(message.UserName) + '</strong>&nbsp;@&nbsp;' + formatUnixTimestamp(message.UnixDate) + '</span></div>';
                html += '</div>';

                // html5 уведомление
                if(first_load_happened && parseInt(message.MessageAuthorId) !== a_id) {
                    sendNotification(
                        escapeHtml(message.UserName),
                        {
                            body: escapeHtml(message.MessageText),
                            icon: message.UserPic,
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
                    act: "load_all",
                    last: last_message_id
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
                        act: "send_all",
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
