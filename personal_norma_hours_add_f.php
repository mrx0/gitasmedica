<?php

//personal_norma_hours_add_f.php
//Функция добавления персональной нормы часов непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){

            if (!isset($_POST['worker']) || !isset($_POST['norma'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                include_once 'DBWork.php';
                include_once('DBWorkPDO.php');

                $workerSearch = SelDataFromDB('spr_workers', $_POST['worker'], 'worker_full_name');

                if ($workerSearch == 0) {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Сотрудник не найден</div>'));
                } else {

                    $worker_id = $workerSearch[0]['id'];

                    $db = new DB();

                    $args = [
                        'worker_id' => $worker_id
                    ];

                    //Проверить, есть ли уже такой
                    $query = "SELECT `id` FROM `fl_spr_normahours_personal` WHERE `worker_id` = :worker_id LIMIT 1";

                    $count = $db::getValue($query, $args);

                    if (!$count) {
                        $args['count'] = $_POST['norma'];

                        $query = "INSERT INTO `fl_spr_normahours_personal`
                            (`worker_id`, `count`)
                            VALUES (:worker_id, :count);";

                        $db::sql($query, $args);

                        echo json_encode(array('result' => 'success', 'data' => $count));
                    }else{
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">У сотрудника уже заполнена норма. Сначала удалите старую.</div>'));
                    }
                }

            }
        }
    }
?>