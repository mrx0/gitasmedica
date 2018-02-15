<?php

//ticket_edit_f.php
//РЕдактирование тикета

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            //!!!Массив тех, кому видно заявку по умолчанию, потому надо будет вывести это в базу или в другой файл
            $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

            if (!isset($_POST['descr']) || !isset($_POST['ticket_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                $time = date('Y-m-d H:i:s', time());

                $descr = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
                $plan_date = date('Y-m-d H:i:s', strtotime($_POST['plan_date']." 21:00:00"));
                $workers = $_POST['workers'];
                $workers_type = array();
                if (!isset($_POST['workers_type']) || ($_POST['workers_type'] == '')){
                    $workers_type = $permissionsWhoCanSee_arr;

                    //хак для админов!!! так не должно быть
                    if ($_SESSION['permissions'] == 4) {
                        array_push($workers_type, $_SESSION['permissions']);
                        $workers_type = array_unique($workers_type);
                    }
                }else{
                    $workers_type = $_POST['workers_type'];
                }
                $filials = $_POST['filial'];

                //Если есть описание
                if ($descr != ''){

                    //Если вместо времени какая-то дичь, то берем сегодня 21:00:00
                    if (($plan_date == '1970-01-01 03:00:00') || ($plan_date == '')){
                        //Не берём ничего, не будет планового времени, если оно не указано
                        $plan_date = date('Y-m-d'.' 21:00:00', time());
                    }

                    //Удаляем повторяющихся сотрудников
                    if (!empty($workers)){
                        $workers = array_unique($workers);
                    }

                    //Если указан филиал в сессии, то привязываем его
                    if (isset($_SESSION['filial'])){
                        $filial_id = $_SESSION['filial'];
                    }else{
                        $filial_id = 0;
                    }

                    include_once 'DBWork.php';

                    //Подключаемся к другой базе специально созданной для тикетов
                    $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                    /*$query = "INSERT INTO `journal_tickets` (`filial_id`, `descr`, `plan_date`, `create_time`, `create_person`)
                    VALUES (
                    '{$filial_id}', '{$descr}', '{$plan_date}', '{$time}', '{$_SESSION['id']}')";*/

                    $query = "UPDATE `journal_tickets` SET 
                    `filial_id`='$filial_id',
                    `descr`='$descr',
                    `plan_date`='$plan_date',
                    `last_edit_time`='$time',
                    `last_edit_person`='{$_SESSION['id']}'
                     WHERE `id`='{$_POST['ticket_id']}'";

                    $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                    //ID новой позиции
                    //$mysql_insert_id = mysqli_insert_id($msql_cnnct2);

                    //Собираем строку запроса
                    $query = '';

                    //Добавляем филиалы
                    $query .= "DELETE FROM `journal_tickets_filial` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                    if (!empty($filials) && ($filials != '')){
                        foreach ($filials as $filial_id){
                            $query .= "INSERT INTO `journal_tickets_filial` (`ticket_id`, `filial_id`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$filial_id}');";
                        }
                    }

                    //Добавляем категории сотрудников
                    $query .= "DELETE FROM `journal_tickets_worker_type` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                    if (!empty($workers_type) && ($workers_type != '')){
                        foreach ($workers_type as $workers_type_id){
                            $query .= "INSERT INTO `journal_tickets_worker_type` (`ticket_id`, `worker_type`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$workers_type_id}');";
                        }
                    }

                    //Добавляем исполнителей
                    $query .= "DELETE FROM `journal_tickets_workers` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                    if (!empty($workers)){
                        foreach ($workers    as $worker_id){
                            $query .= "INSERT INTO `journal_tickets_workers` (`ticket_id`, `worker_id`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$worker_id}');";
                        }
                    }

                    //Удаляем отметки о прочтении
                    $query .= "DELETE FROM `journal_tickets_readmark` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                    //Добавляем отметку о прочтении (мы же создали это сами)
                    $query .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '1');";

                    //Добавляем лог
                    $query .= "INSERT INTO `journal_tickets_logs` (`ticket_id`, `create_person`, `descr`, `create_time`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$_SESSION['id']}', 'Тикет был изменён', '{$time}');";

                    //Добавляем отметку о прочтении (мы же создали это сами)
                    /*$query .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                        VALUES (
                        '{$mysql_insert_id}', '{$time}', '{$_SESSION['id']}', '1');";*/

                    //Делаем большой запрос
                    $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                    //Закрываем соединение
                    CloseDB ($msql_cnnct2);

                    $data = '
                         <div class="query_ok">
                             Тикет добавлен
                         </div>';

                    echo json_encode(array('result' => 'success', 'data' => $data));
                }else{
                    $data = '
                        <div class="query_neok">
                            Не заполнено описание
                        </div>';
                    echo json_encode(array('result' => 'error', 'data' => $data));
                }

            }
        }
    }
?>