<?php

//fl_add_in_tabel_f.php
//Новый табель добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabelForAdding'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                if (isset($_SESSION['fl_calcs_tabels'])) {

                    if (!empty($_SESSION['fl_calcs_tabels'])) {

                        $calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                        $typeID = $calcData_Arr[1];
                        $filialID = $calcData_Arr[3];
                        $workerID = $calcData_Arr[2];

                        $summCalcs = 0;

                        $msql_cnnct = ConnectToDB2();

                        //Вставим новый табель
                        /*$query = "INSERT INTO `fl_journal_tabels` (`office_id`, `worker_id`, `type`, `month`, `year`, `summ`)
                          VALUES (
                          '{$filialID}', '{$workerID}', '{$typeID}', '{$_POST['tabelMonth']}', '{$_POST['tabelYear']}', '{$_POST['summCalcs']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //ID новой позиции
                        $mysqli_insert_id = mysqli_insert_id($msql_cnnct);*/

                        $query = '';

                        $calcArr = $_SESSION['fl_calcs_tabels']['main_data'];

                        foreach ($calcArr as $calcID) {
                            $query .= "INSERT IGNORE INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$_POST['tabelForAdding']}', '{$calcID}');";

                            //$summCalcs += $rezData['summ'];

                        }

                        //тут пример ожидание MySQL, ждём все инсерты перед селектом
                        if (count($calcArr) > 1) {

                            $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            while (mysqli_next_result($msql_cnnct)) // flush multi_queries
                            {
                                if (!mysqli_more_results($msql_cnnct)) break;
                            }
                        //А если всего 1, то и нечего паузы ставить
                        }else{
                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        }

                        unset($_SESSION['fl_calcs_tabels']);

                        CloseDB ($msql_cnnct);

                        //Обновим баланс табеля
                        updateTabelBalance($_POST['tabelForAdding']);

                        echo json_encode(array('result' => 'success', 'data' => ''));
                        //var_dump($arr);

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