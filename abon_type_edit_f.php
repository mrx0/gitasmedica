<?php

//abon_type_edit_f.php
//Функция редактирования типа абонемента

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            //include_once 'DBWork.php';

            if (!isset($_POST['name']) || !isset($_POST['min_count']) || !isset($_POST['exp_days']) || !isset($_POST['summ']) || !isset($_POST['abon_type_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                include_once('DBWorkPDO.php');

                $db = new DB();

                $create_time = date('Y-m-d H:i:s', time());

                $args = [
                    'name' => $_POST['name'],
                    'min_count' => $_POST['min_count'],
                    'exp_days' => $_POST['exp_days'],
                    'summ' => $_POST['summ'],
                    'last_edit_time' => $create_time,
                    'last_edit_person' => $_SESSION['id'],
                    'id' => $_POST['abon_type_id']
                ];

                $query = "UPDATE `spr_solar_abonements` SET 
                            `name`=:name, `min_count`=:min_count, `exp_days`=:exp_days, `summ`=:summ, `last_edit_time`=:last_edit_time, `last_edit_person`=:last_edit_person
                        WHERE `id`=:id;";

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Обновлено</div>'));

            }
        }
    }
?>