<?php
session_start();
header('Content-type: text/plain; charset=utf-8');
if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header("Location: index.php");
} else {
    if ($_FILES['avatar']['size'] > 20971520) {
        exit('Размер изображения слишком велик');
    } else {
        require_once 'db_config.php';
        $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
        $id = intval($_SESSION['id']);
        $login = $_SESSION['login'];
        $login = str_replace(" ", "", $login);
        $login = mysqli_real_escape_string($con, $login);
        $error = $_FILES['avatar']['error'];
        if ($error == 0) {
            $filename = $_FILES['avatar']['name'];
            $file_tmp = $_FILES['avatar']['tmp_name'];
            $info = new SplFileInfo($filename);
            $ext = $info->getExtension();
            if (strcasecmp($ext, "jpg") == 0 || strcasecmp($ext, "png") == 0 || strcasecmp($ext, "bmp") == 0 || strcasecmp($ext, "gif") == 0 || strcasecmp($ext, "jpeg") == 0) {
                $finfo = new finfo;
                $type = $finfo->file($file_tmp, FILEINFO_MIME_TYPE);
                if ($type == "image/x-ms-bmp" || $type == "image/bmp" || $type == "image/x-windows-bmp" || $type == "image/gif" || $type == "image/jpeg" || $type == "image/pjpeg" || $type == "image/png") {
                    $image = new Imagick($file_tmp);
                    $width = $image->getImageWidth();
                    $height = $image->getImageHeight();
                    if ($width > $height) {
                        $a = $width - $height;
                        $b = $a / 2;
                        $image->cropImage($height, $height, $b, 0);
                    } else {
                        $a = $height - $width;
                        $b = $a / 2;
                        $image->cropImage($width, $width, 0, $b);
                    }

                    $image->thumbnailImage(50, 50);
                    $image->setImageFormat('jpeg');
                    $uniq_name = md5($login) . bin2hex(openssl_random_pseudo_bytes(10));
                    $save_image_file_name = "userpics/" . $uniq_name . ".jpg";
                    if (!file_put_contents($save_image_file_name, $image)) {
                        mysqli_close($con);
                        exit("Извините, произошла ошибка!");
                    } else {
                        $val = 'userpics/' . $uniq_name . '.jpg';
                        if (mysqli_query($con, "UPDATE `User` SET `UserPic`='$val' WHERE `UserId`='$id'")) {
                            mysqli_close($con);
                            header("Location: friends.php");
                        } else {
                            exit("Извините, произошла ошибка!");
                        }
                    }
                } else {
                    file_put_contents("new_mime_types.txt", $type . "\n", FILE_APPEND | LOCK_EX);
                    mysqli_close($con);
                    exit('Изображение повреждено');
                }
            } else {
                mysqli_close($con);
                exit('Изображение неверного формата');
            }
        } else if ($error == 1) {
            mysqli_close($con);
            exit('Размер изображения слишком велик');
        } else if ($error == 2) {
            mysqli_close($con);
            exit('Размер изображения слишком велик');
        } else if ($error == 3) {
            mysqli_close($con);
            exit('Изображение было получено только частично :(');
        }
    }
}
