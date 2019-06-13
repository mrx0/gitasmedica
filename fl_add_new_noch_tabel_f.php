<?php

//fl_add_new_noch_tabel_f.php
//Новый табель + рассчет за 1 ночь добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['type_id']) || !isset($_POST['worker_id']) || !isset($_POST['filial_id'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #53. Что-то пошло не так</div>'));
            } else {

                $typeID = $_POST['type_id'];
                $filialID = $_POST['filial_id'];
                $workerID = $_POST['worker_id'];

                $tabelDay = $_POST['dopData']['day'];
                if ((int) $tabelDay < 10)  $tabelDay = '0'.(int) $tabelDay;
                $tabelMonth = $_POST['dopData']['month'];
                if ((int) $tabelMonth < 10)  $tabelMonth = '0'.(int) $tabelMonth;
                $tabelYear = $_POST['dopData']['year'];

                $summ = $_POST['dopData']['summ'];

                $msql_cnnct = ConnectToDB2();

                $time = date('Y-m-d H:i:s', time());

                $date_in = date('Y-m-d', strtotime($tabelDay.'.'.$tabelMonth.'.'.$tabelYear.""));

                //Вставим новый ночной табель
                $query = "INSERT INTO `fl_journal_tabels_noch` (`filial_id`, `worker_id`, `type`, `month`, `year`, `create_time`, `create_person`)
                VALUES (
                '{$filialID}', '{$workerID}', '{$typeID}', '{$tabelMonth}', '{$tabelYear}', '{$time}', '{$_SESSION['id']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //ID новой позиции
                $mysqli_insert_id = mysqli_insert_id($msql_cnnct);

                //Вставим сразу расчёт по дате
                $query = "INSERT INTO `fl_journal_tabels_noch_ex` (`tabel_id`, `filial_id`, `worker_id`, `type`, `date_in`, `day`, `month`, `year`, `summ`, `create_time`, `create_person`)
                VALUES (
                '{$mysqli_insert_id}', '{$filialID}', '{$workerID}', '{$typeID}', '{$date_in}', '{$tabelDay}', '{$tabelMonth}', '{$tabelYear}', '{$summ}', '{$time}', '{$_SESSION['id']}')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //Обновим баланс ночного табеля
                //updateTabelBalance($mysqli_insert_id);

                //echo json_encode(array('result' => 'error', 'data' => $date_in));
                echo json_encode(array('result' => 'success', 'data' => 'Ok'));

            }
        }
    }

?>