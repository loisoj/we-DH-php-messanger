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
$main_text = "Чаты";
include 'header.php';
?>
<main>
    <div id="im_list" class="row teal lighten-5">
        <div class="col s12">
            <?php
            $id = $_SESSION['id'];
            $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
            $id = trim($id);
            $id = intval($id);
            $result = mysqli_query($con, "SELECT U.`UserId`, U.`UserName`, U.`UserPic`, M.`MessageText`, M.`MessageDateTime` FROM `User` AS U LEFT JOIN `Message` AS M ON (U.`UserId`=M.`MessageAuthorId` OR U.`UserId`=M.`MessageRecipientId`) WHERE U.`UserId` IN (SELECT `FriendOneId` AS Id FROM `Friends` WHERE `FriendTwoId`='$id' UNION SELECT `FriendTwoId` AS Id FROM `Friends` WHERE `FriendOneId`='$id') AND M.`MessageDateTime` IN (SELECT MAX(`MessageDateTime`) FROM `Message` WHERE (`MessageAuthorId`='$id' AND `MessageRecipientId`=U.`UserId`) OR (`MessageAuthorId`=U.`UserId` AND `MessageRecipientId`='$id')) ORDER BY M.`MessageDateTime` DESC");

            $num_rows = mysqli_num_rows($result);
            if ($num_rows > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $str = htmlspecialchars($row["MessageText"]);
                    if (mb_strlen($str) > 100) {
                        $str = substr($str, 0, 100) . "&#8230;";
                    }
                    echo '<a class="im-view" href="chat.php?friend_id=' . $row["UserId"] . '">
                            <div class="myrow">
                                <div class="im-avatar"><img class="circle" src="' . $row["UserPic"] . '"></div>
                                <div class="im-name"><span class="im-name-header">' . htmlspecialchars($row["UserName"]) . '</span><br><span class="im-name-body">' . $str . '</span></div>
                            </div>
                        </a><div class="clearfix"></div>';
                }
            }
            mysqli_close($con);
            ?>
        </div>
    </div>
</main>
<? include 'footer.html'?>
<script type="text/javascript">
    $(document).ready(function () { //запускаем, когда документ загрузится
        var refreshHeight = function () {
            $("#im_list").height(document.documentElement.clientHeight - $("#nav").height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();
    });
</script>
</body>
</html>