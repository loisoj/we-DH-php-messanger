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
$main_text = "Собеседники";
include 'header.php';
?>
<main>
    <div id="friend_list" class="row teal lighten-5">
        <div class="col s12">
            <!-- Список друзей -->
            <?php
            $id = $_SESSION['id'];
            $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
            $id = trim($id);
            $id = intval($id);
            $result = mysqli_query($con, "SELECT * FROM `Friends` WHERE `FriendOneId`='$id' OR `FriendTwoId`='$id'");
            $list = array();
            while ($row = mysqli_fetch_array($result)) {
                $e = array();
                if ($id == $row['FriendOneId']) {
                    $subid = $row['FriendTwoId'];
                } else {
                    $subid = $row['FriendOneId'];
                }
                $subresult = mysqli_query($con, "SELECT * FROM `User` WHERE `UserId`='$subid'");
                while ($subrow = mysqli_fetch_array($subresult)) {
                    $e['UserName'] = $subrow['UserName'];
                    $e['UserPic'] = $subrow['UserPic'];
                    $e['UserId'] = $subrow['UserId'];
                }
                array_push($list, $e);
            }
            mysqli_close($con);
            array_multisort($list);
            foreach ($list as &$elem) {
                echo '
                    <a class="friend" href="chat.php?friend_id=' . $elem["UserId"] . '">
                        <table>
                            <tr>
                                <td class="friend-image"><img class="circle" src="' . $elem["UserPic"] . '"></td>
                                <td class="friend-name">' . htmlspecialchars($elem["UserName"]) . '</td>
                                <td class="friend-delete"><a class="button_delete hide-on-large-only" href="delete_contact.php?friend_id=' . $elem["UserId"] . '"><i class="material-icons krest">&#xE14c;</i></a></td>
                            </tr>
                        </table>
                    </a><div class="clearfix"></div>
                    ';
            }
            ?>
        </div>
    </div>
</main>
<? include 'footer.html'?>
<script type="text/javascript">
    $(document).ready(function () { //запускаем, когда документ загрузится
        var refreshHeight = function () {
            $("#friend_list").height(document.documentElement.clientHeight - $("#nav").height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();
    });
</script>
</body>
</html>