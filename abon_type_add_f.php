<?php

//abon_type_add_f.php
//Функция добавления типа абонемента непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            //include_once 'DBWork.php';

            if (!isset($_POST['name']) || !isset($_POST['min_count']) || !isset($_POST['exp_days']) || !isset($_POST['summ'])){
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
                    'create_time' => $create_time,
                    'create_person' => $_SESSION['id']
                ];

                $query = "INSERT INTO `spr_solar_abonements`
                        (`name`, `min_count`, `exp_days`, `summ`, `create_time`, `create_person`)
                        VALUES (:name, :min_count, :exp_days, :summ, :create_time, :create_person);";

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok">Добавлено</div>'));

            }
        }
    }
?>