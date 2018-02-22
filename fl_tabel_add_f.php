<?php

//fl_tabel_add_f.php
//Новый табель добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';

            if (!isset($_POST['tabelMonth']) || !isset($_POST['tabelYear']) || !isset($_POST['tabelYear'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                if (isset($_SESSION['fl_calcs_tabels'])) {

                    if (!empty($_SESSION['fl_calcs_tabels'])) {

                        $calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                        $typeID = $calcData_Arr[1];
                        $filialID = $calcData_Arr[3];
                        $workerID = $calcData_Arr[2];

                        $summCalcs = 0;

                        $msql_cnnct = ConnectToDB();

                        //Вставим новый табель
                        $query = "INSERT INTO `fl_journal_tabels` (`office_id`, `worker_id`, `type`, `month`, `year`, `summ`)
                          VALUES (
                          '{$filialID}', '{$workerID}', '{$typeID}', '{$_POST['tabelMonth']}', '{$_POST['tabelYear']}', '{$_POST['summCalcs']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //ID новой позиции
                        $mysqli_insert_id = mysqli_insert_id($msql_cnnct);

                        $query = '';

                        $calcArr = $_SESSION['fl_calcs_tabels']['main_data'];

                        foreach ($calcArr as $calcID) {
                            $query .= "INSERT INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$mysqli_insert_id}', '{$calcID}');";

                            //$summCalcs += $rezData['summ'];

                        }

                        $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        unset($_SESSION['fl_calcs_tabels']);

                        //Обновим баланс табеля
                        //updateTabelBalance($mysqli_insert_id);

                        echo json_encode(array('result' => 'success', 'data' => $query));

                    } else {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                    }
                } else {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
                }
            }
        }
    }

?>