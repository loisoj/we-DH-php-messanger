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
$main_text = "Мой профиль";
include 'header.php';
?>
<main>
    <div id="change_avatar_div" class="row">
        <div class="col s12 center">
            <h4><?= htmlspecialchars($_SESSION['login']) ?></h4>
            <img class="circle" src="<?= $author_avatar_path ?>">
        </div>
        <form enctype="multipart/form-data" class="col s12" action="save_new_avatar.php" method="post">
            <h5 class="center">Изменить аватар</h5>
            <div class="row">
                <div class="file-field input-field col s12 m5 l5">
                    <div class="btn" style="height:100px;line-height:102px;width:100%;">
                        <span>Выбрать аватар<i class="material-icons right">&#xE2c8;</i></span>
                        <input type="hidden" name="MAX_FILE_SIZE" value="20971520"/>
                        <input name="avatar" type="file" accept="image/*" required>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;margin-top:1rem;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                        выбрать изображение. Оно должно иметь формат JPEG,&nbsp;PNG,&nbsp;GIF&nbsp;или&nbsp;PNG (до&nbsp;20&nbsp;МБ)
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m5 l5">
                    <button class="btn waves-effect waves-light" style="height:100px;line-height:102px;width:100%;"
                            type="submit" name="action">
                        Установить аватар
                        <i class="material-icons right">&#xE163;</i>
                    </button>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                        установить выбранное изображение</p>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m5 l5">
                    <a href="set_default_avatar.php" class="btn waves-effect waves-light red darken-3"
                       style="height:100px;line-height:102px;width:100%;">
                        Удалить аватар
                        <i class="material-icons right">&#xE14c;</i>
                    </a>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                        удалить свой аватар</p>
                </div>
            </div>
        </form>
        <form action="change_password.php" class="col s12" method="post">
            <h5 class="center">Изменить пароль</h5>
            <div class="row">
                <div class="input-field col s12">
                    <input id="old_pass" type="password" class="validate" name="old_pass" maxlength="150" length="150"
                           required>
                    <label for="old_pass">Старый пароль</label>
                </div>
                <div class="input-field col s12">
                    <input id="new_pass" type="password" class="validate" name="new_pass" maxlength="150" length="150"
                           required>
                    <label for="new_pass">Новый пароль</label>
                </div>
                <div class="input-field col s12">
                    <input id="new_pass_repeat" type="password" class="validate" name="new_pass_repeat" maxlength="150"
                           length="150" required>
                    <label for="new_pass_repeat">Повторите новый пароль</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p class="center-align" style="color:#00b0ff">Все пробелы будут удалены!</p>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center-align">
                    <button class="btn waves-effect waves-light" type="submit">Изменить пароль
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
<? include 'footer.html' ?>
<script type="text/javascript">
    $(document).ready(function () { //запускаем, когда документ загрузится
        var refreshHeight = function () {
            $("#change_avatar_div").height(document.documentElement.clientHeight - $("#nav").height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();
    });
</script>
</body>
</html>