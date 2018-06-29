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
$main_text = "Поиск";
include 'header.php';
?>
<main>
    <div id="search_body">
        <div class="row" style="margin:0;">
            <div class="input-field card col s12">
                <i class="material-icons prefix" style="margin-top:10px;">&#xE8b6;</i>
                <input placeholder="Начните писать ник собеседника" type="text" id="search">
            </div>
        </div>
        <div id="find_list" class="row teal lighten-5">
            <div id="find_area" class="col s12"></div>
        </div>
    </div>
</main>
<? include 'footer.html'?>
<script type="text/javascript">
    $(document).ready(function ($) {
        var $searchBody = $("#search_body"),
            $nav = $("#nav"),
            $search = $("#search"),
            find_area = $("#find_area");

        var escapeHtml = function (unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        };

        var renderFinds = function (finds) {
            var html = '';
            finds.forEach(function (item) {
                html += '<a class="friend" href="add_to_friend_list.php?friend_id=' + item.UserId + '">';
                html += '<table>';
                html += '<tr>';
                html += '<td class="friend-image"><img class="circle" src="' + item.UserPic + '"></td>';
                html += '<td class="friend-name">' + escapeHtml(item.UserName) + '</td>';
                html += '</tr>';
                html += '</table>';
                html += '</a><div class="clearfix"></div>';
            });
            find_area.html(html);
        };

        var Find = function () {
            var value = $search.val();
            value = $.trim(value);
            if (value !== "") {
                $.ajax({
                    type: "POST",
                    url: 'ajax.php',
                    data: {
                        act: "find",
                        val: value
                    },
                    success: function (data) {
                        renderFinds(data.finds);
                    }
                })
            }
        };

        var refreshHeight = function () {
            $searchBody.height(document.documentElement.clientHeight - $nav.height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();

        $search.bind("keyup", function () {
            if ($search.val().trim() !== '') {
                Find();
            }
        });
    });
</script>
</body>
</html>