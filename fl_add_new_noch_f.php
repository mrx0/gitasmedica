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
                        $workerID = $_SESSION['fl_calcs_tabels2']['worker_id'];

                        $thisCalcIsInAnotherTabel = FALSE;
                        $CalcIsInAnotherTabelID = 0;

                        $tabelMonth = '00';
                        $tabelYear = '0000';

                        $summ = 0;

                        $revenue_percent = 0;
                        $revenue_summ = 0;

                        $msql_cnnct = ConnectToDB2();

                        $calcArr = $_SESSION['fl_calcs_tabels2']['data'];

                        $time = date('Y-m-d H:i:s', time());

                        $invoice_j = array();

                        //Соберём ID нарядов по РЛ'ам и проверим нет ли их уже в другом расчете ночи
                        foreach ($calcArr as $calcID => $status) {

                            $arr = array();

                            $query = "SELECT jc.invoice_id, ji.summ, ji.summins, jtn_ex.tabel_id AS tabel_id  FROM `fl_journal_calculate` jc
                            LEFT JOIN `fl_journal_tabels_noch_ex` jtn_ex ON jtn_ex.invoice_id = jc.invoice_id
                            LEFT JOIN `journal_invoice` ji ON ji.id = jc.invoice_id
                            WHERE jc.id = '$calcID' LIMIT 1;";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {

                                while ($arr = mysqli_fetch_assoc($res)){
                                    if ($arr['tabel_id'] != NULL){
                                        $thisCalcIsInAnotherTabel = TRUE;
                                        $CalcIsInAnotherTabelID = $calcID;
                                        break;
                                    }else{
                                        //Выручка за смену. Считается по суммам нарядам, у которых есть РЛ.
                                        $summ += $arr['summ'] + $arr['summins'];
                                        array_push($invoice_j, $arr);
                                    }
                                }
                            }
                        }

                        if (!$thisCalcIsInAnotherTabel){

                            //Надо получить месяц и год из табеля, куда будем добавлять ночной расчёт
                            $arr = array();

                            $query = "SELECT `month`, `year` FROM `fl_journal_tabels` WHERE `id` = '{$_POST['tabelForAdding']}' LIMIT 1;";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);

                            if ($number != 0) {

                                $arr = mysqli_fetch_assoc($res);

                                $tabelMonth = $arr['month'];
                                $tabelYear = $arr['year'];
                            }

                            //Берём из БД данные по процентам от выручки для сотрудника
                            //!!!---

                            //Рассчитываем сумму от выручки
                            //!!!--

                            //Вставим новый ночной табель
//                            $query = "INSERT INTO `fl_journal_tabels_noch` (`filial_id`, `worker_id`, `type`, `month`, `year`, `tabel_id`, `summ`, `revenue_percent`, `revenue_summ`)
//                            VALUES (
//                            '{$filialID}', '{$workerID}', '{$typeID}', '{$tabelMonth}', '{$tabelYear}', '{$_POST['tabelForAdding']}', '{$summ}', '{$revenue_percent}', '{$revenue_summ}')";
//
//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                            //ID новой позиции
//                            $mysqli_insert_id = mysqli_insert_id($msql_cnnct);
//
//                            $query = '';
//
//                            foreach ($invoice_j as $invoice_data) {
//                                $query .= "INSERT INTO `fl_journal_tabels_noch_ex` (`tabel_id`, `invoice_id`) VALUES ('{$mysqli_insert_id}', '{$invoice_data['invoice_id']}');";
//
//                                //$summCalcs += $rezData['summ'];
//
//                            }
//
//                            $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                            unset($_SESSION['fl_calcs_tabels2']);
//
//                            //Обновим баланс табеля
//                            //updateTabelBalance($mysqli_insert_id);



                            echo json_encode(array('result' => 'success', 'data' => $invoice_j));
                        }else{

                            echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #46. РЛ #'.$CalcIsInAnotherTabelID.' уже в другом табеле.</div>'));
                        }

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