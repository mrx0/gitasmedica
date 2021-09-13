<?php

//univer_add_in_db_f.php
//Добавление задания в БД для UNIVER

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            //!!!Массив тех, кому видно заявку по умолчанию, потому надо будет вывести это в базу или в другой файл
            $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

            if (isset($_SESSION['univer'])){
                if (!empty($_SESSION['univer'])) {

//                    $time = date('Y-m-d H:i:s', time());
//
//                    $descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
//                    $plan_date = date('Y-m-d H:i:s', strtotime($_POST['plan_date'] . " 21:00:00"));
//                    $workers = $_POST['workers'];
//                    $workers_type = array();
//                    if (!isset($_POST['workers_type'])) {
//                        $workers_type = $permissionsWhoCanSee_arr;
//
//                        //хак для админов!!! так не должно быть
//                        if ($_SESSION['permissions'] == 4) {
//                            array_push($workers_type, $_SESSION['permissions']);
//                            $workers_type = array_unique($workers_type);
//                        }
//                    } else {
//                        $workers_type = $_POST['workers_type'];
//                    }
//                    $filials = $_POST['filial'];

                    $time = date('Y-m-d H:i:s', time());

                    //Что будем добавлять
                    $task_id = $_SESSION['univer']['id'];
                    $file_id = $_SESSION['univer']['file_data']['id'];
                    $task_theme = trim(strip_tags(stripcslashes(htmlspecialchars(($_SESSION['univer']['theme'])))));
                    $task_descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_SESSION['univer']['descr'])))));
                    $task_workers = $_SESSION['univer']['workers'];
                    $task_workers_type = $_SESSION['univer']['workers_type'];
                    $task_filial = $_SESSION['univer']['filial'];
                    $task_status = $_POST['status'];

                    //Если есть Вопрос/задание
                    if ($task_descr != '') {

//                        //Если вместо времени какая-то дичь, то берем сегодня 21:00:00
//                        if (($plan_date == '1970-01-01 03:00:00') || ($plan_date == '')) {
//                            //Не берём ничего, не будет планового времени, если оно не указано
//                            $plan_date = date('Y-m-d' . ' 21:00:00', time());
//                        }

                        //Удаляем повторяющихся сотрудников
//                        if (!empty($workers)) {
//                            $workers = array_unique($workers);
//                        }

                        //Если указан филиал в сессии, то привязываем его
                        if (isset($_SESSION['filial'])) {
                            $filial_id = $_SESSION['filial'];
                        } else {
                            $filial_id = 0;
                        }

                        include_once 'DBWorkPDO_2.php';

                        //Подключаемся к другой базе специально созданной для UNIVER
                        $db = new DB2();

                        //Добавляем в базу

                        $args = [
                            'theme' => $task_theme,
                            'descr' => $task_descr,
                            'type' => 1,
                            'file_id' => $file_id,
                            'create_time' => $time,
                            'create_person' => $_SESSION['id'],
                            'status' => $task_status
                        ];

                        $query = "INSERT INTO `journal_tasks`
                                (`theme`, `descr`, `type`, `file_id`, `create_time`, `create_person`, `status`)
                                VALUES (:theme, :descr, :type, :file_id, :create_time, :create_person, :status);";

                        $db::sql($query, $args);

                        //ID новой позиции, последней $mysql_insert_id, last_insert_id
                        $mysql_insert_id = $db->lastInsertId();

                        //(20210912 отключено)
//                        if (!empty($task_workers)) {

                            //Собираем строку запроса
                            $query = '';

                            //Добавляем исполнителей
                            if (!empty($task_workers)) {
                                foreach ($task_workers as $worker_id) {
                                    $query .= "INSERT INTO `journal_tasks_workers` (`task_id`, `worker_id`)
                                    VALUES (
                                    '$mysql_insert_id', '$worker_id');";
                                }
                            }
//                        }else {

                            //Добавляем филиалы
                            if (!empty($task_filial) && ($task_filial != '')) {
                                foreach ($task_filial as $filial_id) {
                                    $query .= "INSERT INTO `journal_tasks_filial` (`task_id`, `filial_id`)
                                        VALUES (
                                        '$mysql_insert_id', '$filial_id');";
                                }
                            }

                            //Добавляем категории сотрудников
                            if (!empty($task_workers_type) && ($task_workers_type != '')) {
                                foreach ($task_workers_type as $workers_type_id) {
                                    $query .= "INSERT INTO `journal_tasks_worker_type` (`task_id`, `worker_type`)
                                        VALUES (
                                        '$mysql_insert_id', '$workers_type_id');";
                                }
                            }
//                        }

                        //Добавляем лог
                        $query .= "INSERT INTO `journal_tasks_logs` (`task_id`, `create_time`, `create_person`, `descr`)
                                VALUES (
                                '$mysql_insert_id', '$time', '{$_SESSION['id']}', 'Новая заявка добавлена');";

                        //Добавляем отметку о прочтении (мы же создали это сами)
                        $query .= "INSERT INTO `journal_tasks_readmark` (`task_id`, `create_time`, `create_person`, `status`)
                                VALUES (
                                '$mysql_insert_id', '$time', '{$_SESSION['id']}', '1');";

                        //Делаем большой запрос
                        //$res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2) . ' -> ' . $query);
                        $db::sql($query, []);

                        //Закрываем соединение
                        //CloseDB($msql_cnnct2);

                        unset($_SESSION['univer']);

                        $data = '
                                 <div class="query_ok">
                                     Задание добавлено
                                 </div>';

                        echo json_encode(array('result' => 'success', 'data' => $data));
                    } else {
                        $data = '
                                <div class="query_neok">
                                    Не заполнено Вопрос/задание
                                </div>';
                        echo json_encode(array('result' => 'error', 'data' => $data));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
            }else{
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }
        }
    }
?>