<?php

//fl_add_new_noch2_f.php
//Новый новый рассчет за ночь добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['type_id']) || !isset($_POST['worker_id']) || !isset($_POST['filial_id']) || !isset($_POST['dopData']) || !isset($_POST['tabelForAdding'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #50. Что-то пошло не так</div>'));
            } else {

                $typeID = $_POST['type_id'];
                $filialID = $_POST['filial_id'];
                $workerID = $_POST['worker_id'];

                $summ = $_POST['dopData']['summ'];

                $day = $_POST['dopData']['day'];
                if ((int)$day < 10) $day = '0'.(int)$day;
                $month = $_POST['dopData']['month'];
                if ((int)$month < 10) $month = '0'.(int)$month;
                $year = $_POST['dopData']['year'];

                $msql_cnnct = ConnectToDB2();

                $time = date('Y-m-d H:i:s', time());

                //Вставим новый ночной табель
                $query = "INSERT INTO `fl_journal_tabels_noch` (`day`, `month`, `year`, `filial_id`, `worker_id`, `type`, `tabel_id`, `summ`, `create_time`, `create_person`)
                VALUES (
                '{$day}', '{$month}', '{$year}', '{$filialID}', '{$workerID}', '{$typeID}', '{$_POST['tabelForAdding']}', '{$summ}', '{$time}', '{$_SESSION['id']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //ID новой позиции
                //$mysqli_insert_id = mysqli_insert_id($msql_cnnct);

                //Рассчитаем и обновим ночной баланс табеля
                $query = "SELECT SUM(`summ`) AS `summ` FROM `fl_journal_tabels_noch` WHERE `tabel_id`='{$_POST['tabelForAdding']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $arr = mysqli_fetch_assoc($res);

                $query = "UPDATE `fl_journal_tabels` SET `night_smena` = '".round($arr['summ'], 2)."' WHERE `id`='{$_POST['tabelForAdding']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //Обновим баланс табеля
                //updateTabelBalance($mysqli_insert_id);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }
        }
    }

?>