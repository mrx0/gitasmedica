<?php

//upload_video4Univer.php
//Загрузка видео для Univer

    session_start();

    //var_dump($_FILES);
    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{


        // A list of permitted file extensions
        $allowed = array('mp4', 'pdf');

        //if(isset($_POST['upload_file'])){
        if(isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {

            include_once 'functions.php';
            include_once 'DBWorkPDO_2.php';

            $uploadfile = $_FILES["upload_file"]["tmp_name"];
            $extension = pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION);
            $orig_name = $_FILES['upload_file']['name'];
            $new_name = Translit(pathinfo($_FILES['upload_file']['name'], PATHINFO_FILENAME));
            $folder = "univerFiles/";
            //var_dump($_FILES["upload_file"]["name"]);
            //var_dump($new_name);
            //var_dump($extension);

            //Проверка расширения файла
            if (!in_array(strtolower($extension), $allowed)) {
                echo '{"status":"error", "data":"Ошибка #81. Неверный формат файла"}';
                exit;
            } else {

                $db = new DB2();

                //Проверка файла на существование
                if (file_exists($folder . $new_name . '.' . $extension)) {

                    //Ищем его в базе
                    $query = "SELECT * FROM `journal_upl_files` WHERE `path_name`=:path_name";
                    //var_dump($query);

                    $args = [
                        'path_name' => $new_name . '.' . $extension
                    ];

                    $exist_file = $db::getRow($query, $args);
                    //var_dump($exist_file);

                    echo '{"status":"error", "data":"Ошибка #82. Файл с таким именем уже загружен. <a href=\"\" onclick=\"addFileInThisUniverAddSession('.$exist_file['id'].', \''.$orig_name.'.'.$extension.'\', \''.$new_name.'\', \''.$extension.'\');\">Прикрепить</a> его? "}';

                    exit;

                } else {
                    //Закачиваем файл
                    move_uploaded_file($_FILES["upload_file"]["tmp_name"], $folder.$new_name.'.'.$extension);
        //            if(move_uploaded_file($_FILES['upload_file']['tmp_name'], $folder.$new_name.'.'.$extension)){
        //                echo '{"status":"error"}';
        //                exit;
        //            }

                    //Добавляем в базу
                    $create_time = date('Y-m-d H:i:s', time());

                    $args = [
                        'name' => $orig_name,
                        'path_name' => $new_name . '.' . $extension,
                        'ext' => $extension,
                        'create_time' => $create_time,
                        'create_person' => $_SESSION['id']
                    ];

                    $query = "INSERT INTO `journal_upl_files`
                                (`name`, `path_name`, `ext`, `create_time`, `create_person`)
                                VALUES (:name, :path_name, :ext, :create_time, :create_person);";

                    $db::sql($query, $args);

                    //ID новой позиции, последней $mysql_insert_id, last_insert_id
                    $insert_id = $db->lastInsertId();

                    //Добавляем в сессию
//                    if (isset($_SESSION['univer'])) {
                        $_SESSION['univer']['file_data']['id'] = $insert_id;
                        $_SESSION['univer']['file_data']['name'] = $args['name'];
                        $_SESSION['univer']['file_data']['path_name'] = $args['path_name'];
                        $_SESSION['univer']['file_data']['ext'] = $args['ext'];
//                    }

                    //Выводит в результате ответом обратно
                    //echo '<img src="'.$folder."".$_FILES["upload_file"]["name"].'">';

        //    echo '
        //    <video width="640" height="480" controls poster="">
        //        <!--<source src="video/nubex.ogv" type=\'video/ogg; codecs="theora, vorbis"\'>
        //        <source src="video/nubex.mp4" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>-->
        //        <source src="'.$folder.$_FILES["upload_file"]["name"].'" <!--type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'-->>
        //        <!--<source src="video/Сергей Мельников Кураторы лечения в стоматологии-master.m3u8" type="application/x-mpegURL">-->
        //        <!--<source src="video/nubex.webm" type=\'video/webm; codecs="vp8, vorbis"\'>-->
        //        Ваш браузер не поддерживает тег video.
        //    </video>';

                    echo '{"status":"Ok", "data":"<span style=\'color: green; margin-left: 20px;\'>Файл был успешно загружен</span>"}';
                }
            }

        }

    exit;

    }
