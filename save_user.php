<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Messenger</title>
    <meta name="theme-color" content="#00897B">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="background-color:#00897B;">
<?php
session_start();
header('Content-type: text/html; charset=utf-8');

if ($_FILES['avatar']['size'] > 20971520) {
    exit('<h3 style="color:#fff;" class="center">Размер изображения слишком велик</h3>
          <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
}

if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '' || strlen($login) > 50) {
        unset($login);
    }
}
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if ($password == '' || strlen($password) > 100) {
        unset($password);
    }
}
if (isset($_POST['password_repeat'])) {
    $password_repeat = $_POST['password_repeat'];
    if ($password_repeat == '' || strlen($password_repeat) > 100) {
        unset($password_repeat);
    }
}

if (empty($login) || empty($password) || empty($password_repeat)) {
    exit('<h3 style="color:#fff;" class="center">Заполните поля!</h3>
          <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        </body>
        </html>
        ');
} else {
    require_once 'db_config.php';
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    $login = trim($login);
    $login = mysqli_real_escape_string($con, $login);
    $login = str_replace(" ", "", $login);
    $password = trim($password);
    $password = mysqli_real_escape_string($con, $password);
    $password = str_replace(" ", "", $password);
    $password_repeat = trim($password_repeat);
    $password_repeat = mysqli_real_escape_string($con, $password_repeat);
    $password_repeat = str_replace(" ", "", $password_repeat);
    if ($password == $password_repeat) {
        $result = mysqli_query($con, "SELECT * FROM `User` WHERE `UserName`='$login'");
        $myrow = mysqli_fetch_array($result);
        if (empty($myrow)) {

            // START обработка изображения

            $avatar_exist = false;
            $error = $_FILES['avatar']['error'];
            if ($error == 0) {
                $filename = $_FILES['avatar']['name'];
                $file_tmp = $_FILES['avatar']['tmp_name'];
                $info = new SplFileInfo($filename);
                $ext = $info->getExtension();
                if (strcasecmp($ext, "jpg") == 0 || strcasecmp($ext, "png") == 0 || strcasecmp($ext, "bmp") == 0 || strcasecmp($ext, "gif") == 0 || strcasecmp($ext, "jpeg") == 0) {
                    $finfo = new finfo;
                    $type = $finfo->file($file_tmp, FILEINFO_MIME_TYPE);
                    if ($type == "image/x-ms-bmp" || $type == "image/bmp" || $type == "image/x-windows-bmp" || $type == "image/gif" || $type == "image/jpeg" || $type == "image/pjpeg" || $type == "image/png") //if (strncmp($type, "image/", 6) == 0)
                    {
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
                        file_put_contents($save_image_file_name, $image);
                        $avatar_exist = true;

                    } else {
                        file_put_contents("new_mime_types.txt", $type . "\n", FILE_APPEND | LOCK_EX);
                        mysqli_close($con);
                        exit('<h3 style="color:#fff;" class="center">Изображение повреждено</h3>
                              <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                            </body>
                            </html>
                            ');
                    }
                } else {
                    mysqli_close($con);
                    exit('<h3 style="color:#fff;" class="center">Изображение неверного формата</h3>
                          <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                        </body>
                        </html>
                        ');
                }
            } else if ($error == 1) {
                mysqli_close($con);
                exit('<h3 style="color:#fff;" class="center">Размер изображения слишком велик</h3>
                      <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                    </body>
                    </html>
                    ');
            } else if ($error == 2) {
                mysqli_close($con);
                exit('<h3 style="color:#fff;" class="center">Размер изображения слишком велик</h3>
                      <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                    </body>
                    </html>
                    ');
            } else if ($error == 3) {
                mysqli_close($con);
                exit('<h3 style="color:#fff;" class="center">Изображение было получено только частично :(</h3>
                      <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                    </body>
                    </html>
                    ');
            }

            // END обработка изображения
            $blowfish_salt = "$2y$10$" . bin2hex(openssl_random_pseudo_bytes(22));
            $hashed_password = crypt($password, $blowfish_salt);

            if ($avatar_exist) {
                $path = "userpics/" . $uniq_name . ".jpg";
                $my_query = "INSERT INTO `User` (`UserName`, `UserPassword`, `UserPic`) VALUES ('$login', '$hashed_password', '$path')";
            } else {
                $my_query = "INSERT INTO `User` (`UserName`, `UserPassword`) VALUES ('$login', '$hashed_password')";
            }

            if (mysqli_query($con, $my_query)) {
                echo '<h3 style="color:#fff;" class="center">Регистрация пройдена!</h3>
                      <div class="center-align"><a href="index.php" class="btn waves-effect waves-light">Вперёд &raquo;</a></div>
                    </body>
                    </html>
                ';
            } else {
                unlink($save_image_file_name);
                mysqli_close($con);
                exit('<h3 style="color:#fff;" class="center">Извините, произошла ошибка! Попробуйте ещё раз.</h3>
                      <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                    </body>
                    </html>
                    ');
            }
        } else {
            mysqli_close($con);
            exit('<h3 style="color:#fff;" class="center">Этот логин уже занят</h3>
                  <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
                </body>
                </html>
                ');
        }
    } else {
        mysqli_close($con);
        exit('<h3 style="color:#fff;" class="center">Пароли не совпадают!</h3>
              <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
            </body>
            </html>
            ');
    }
    mysqli_close($con);
}
