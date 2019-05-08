<?php

//fl_add_new_noch_f.php
//Новый рассчет за ночь добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['type_id']) || !isset($_POST['worker_id']) || !isset($_POST['filial_id']) || !isset($_POST['tabelForAdding'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #45. Что-то пошло не так</div>'));
            } else {

                if (isset($_SESSION['fl_calcs_tabels2'])) {
                    //echo json_encode(array('result' => 'success', 'data' => $_SESSION['fl_calcs_tabels2']['data']));

                    if (!empty($_SESSION['fl_calcs_tabels2'])) {

                        //$calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                        $typeID = $_SESSION['fl_calcs_tabels2']['type'];
                        $filialID = $_SESSION['fl_calcs_tabels2']['filial_id'];
                        $workerID =$_SESSION['fl_calcs_tabels2']['worker_id'];

                        $thisCalcIsInAnotherTabel = FALSE;
                        $CalcIsInAnotherTabelID = 0;

                        $summCalcs = 0;

                        $msql_cnnct = ConnectToDB2();

                        $calcArr = $_SESSION['fl_calcs_tabels2']['data'];

                        $time = date('Y-m-d H:i:s', time());

                        $rez = array();
                        //Соберём ID нарядов по РЛ'ам и проверим нет ли их уже в другом расчете ночи
                        foreach ($calcArr as $calcID => $status) {

                            $arr = array();

                            $query = "SELECT jc.invoice_id, jtn_ex.tabel_id AS tabel_id  FROM `fl_journal_calculate` jc
                                LEFT JOIN `fl_journal_tabels_noch_ex` jtn_ex ON jtn_ex.invoice_id = jc.invoice_id
                            WHERE jc.id = '$calcID' LIMIT 1;";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {

                                $thisCalcIsInAnotherTabel = TRUE;

                                while ($arr = mysqli_fetch_assoc($res)){
                                    array_push($rez, $arr);
                                }
                            }
                        }





                        //Вставим новый расчет
//                        $query = "INSERT INTO `fl_journal_tabels` (`office_id`, `worker_id`, `type`, `month`, `year`, `summ`, `create_time`, `create_person`)
//                          VALUES (
//                          '{$filialID}', '{$workerID}', '{$typeID}', '{$_POST['tabelMonth']}', '{$_POST['tabelYear']}', '{$_POST['summCalcs']}', '{$time}', '{$_SESSION['id']}')";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                        //ID новой позиции
//                        $mysqli_insert_id = mysqli_insert_id($msql_cnnct);

//                        $query = '';
//
//                        $calcArr = $_SESSION['fl_calcs_tabels']['main_data'];
//
//                        foreach ($calcArr as $calcID) {
//                            $query .= "INSERT INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$mysqli_insert_id}', '{$calcID}');";
//
//                            //$summCalcs += $rezData['summ'];
//
//                        }
//
//                        $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                        unset($_SESSION['fl_calcs_tabels']);
//
//                        //Обновим баланс табеля
//                        //updateTabelBalance($mysqli_insert_id);

                        echo json_encode(array('result' => 'success', 'data' => $rez));

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