<?php

//fl_tabel_add3_f.php
//Новый ПУСТОЙ табель добавляем в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabelMonth']) || !isset($_POST['tabelYear']) || !isset($_POST['type_id']) || !isset($_POST['filial_id']) || !isset($_POST['worker_id'])) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #51. Что-то пошло не так</div>'));
            } else {

                //if (isset($_SESSION['fl_calcs_tabels2'])) {

//                    if (!empty($_SESSION['fl_calcs_tabels2'])) {

//                        //$calcData_Arr = explode('_', $_SESSION['fl_calcs_tabels']['data']);
                        $typeID = $_POST['type_id'];
                        $filialID = $_POST['filial_id'];
                        $workerID = $_POST['worker_id'];
//
//                        $thisCalcIsInAnotherTabel = FALSE;
//                        $CalcIsInAnotherTabelID = 0;

                        $summCalcs = 0;

                        $msql_cnnct = ConnectToDB2();

//                        $calcArr = $_SESSION['fl_calcs_tabels2']['data'];

                        //Проверить, нет ли их уже в другом табеле
//                        foreach ($calcArr as $calcID => $status) {
//
//                            $arr = array();
//
//                            $query = "SELECT `calculate_id` FROM `fl_journal_tabels_ex` WHERE `calculate_id` = '$calcID' LIMIT 1;";
//
//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                            $number = mysqli_num_rows($res);
//
//                            if ($number != 0) {
//
//                                $thisCalcIsInAnotherTabel = TRUE;
//                                while ($arr = mysqli_fetch_assoc($res)) {
//                                    $CalcIsInAnotherTabelID = $arr['calculate_id'];
//                                }
//                                break;
//
//                            }
//                        }

                        //if (!$thisCalcIsInAnotherTabel){

                            $time = date('Y-m-d H:i:s', time());

                            //Вставим новый табель
                            $query = "INSERT INTO `fl_journal_tabels` (`office_id`, `worker_id`, `type`, `month`, `year`, `summ`, `create_time`, `create_person`)
                            VALUES (
                            '{$filialID}', '{$workerID}', '{$typeID}', '{$_POST['tabelMonth']}', '{$_POST['tabelYear']}', '{$_POST['summCalcs']}', '{$time}', '{$_SESSION['id']}')";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            //ID новой позиции
                            //$mysqli_insert_id = mysqli_insert_id($msql_cnnct);


                            //$query = '';

//                            foreach ($calcArr as $calcID => $status) {
//
//                                $query .= "INSERT INTO `fl_journal_tabels_ex` (`tabel_id`, `calculate_id`) VALUES ('{$mysqli_insert_id}', '{$calcID}');";
//
//                                //$summCalcs += $rezData['summ'];
//
//                            }

//                            $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

//                            unset($_SESSION['fl_calcs_tabels2']);

                            CloseDB($msql_cnnct);

                            //Обновим баланс табеля
                            //updateTabelBalance($mysqli_insert_id);

                            echo json_encode(array('result' => 'success'));
//                        }else{
//
//                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #22. РЛ #'.$CalcIsInAnotherTabelID.' уже в другом табеле.</div>'));
//                        }
//                    } else {
//                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
//                    }
//                } else {
//                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
//                }
            }
        }
    }

?>