<?php

//fl_material_consumption_add_f.php
//Добавляем расход материалов

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {

            if ((!isset($_POST['invoice_id'])) || (!isset($_POST['positionsArr'])) || (!isset($_POST['summ']))) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once 'DBWork.php';
                include_once 'functions.php';

                include_once 'ffun.php';

                require 'variables.php';

                $invoice_j = SelDataFromDB('journal_invoice', $_POST['invoice_id'], 'id');

                if ($invoice_j != 0) {

                    if (!empty($_POST['positionsArr'])) {

                        $time = date('Y-m-d H:i:s', time());

                        $msql_cnnct = ConnectToDB();

                        $mat_cons_j_ex = array();

                        $query = "INSERT INTO `journal_inv_material_consumption` (`invoice_id`, `summ`, `descr`, `create_time`, `create_person`)
                        VALUES (
                        '{$_POST['invoice_id']}', '{$_POST['summ']}', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        //ID новой позиции
                        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                        $query = '';
                        //$Summ = 0;

                        foreach ($_POST['positionsArr'] as $inv_pos_id => $data){

                            $query .= "INSERT INTO `journal_inv_material_consumption_ex` (`inv_mat_cons_id`, `inv_pos_id`, `summ`)
                            VALUES (
                            '{$mysql_insert_id}', '{$inv_pos_id}', '{$data['mat_cons_sum']}');";

                            //Массив для вычетов затрат
                            if (!isset($mat_cons_j_ex['data'])){
                                $mat_cons_j_ex['data'] = array();
                            }

                            if (!isset($mat_cons_j_ex['data'][$inv_pos_id])){
                                $mat_cons_j_ex['data'][$inv_pos_id] = $data['mat_cons_sum'];
                            }


                        }

                        $res = mysqli_multi_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        CloseDB($msql_cnnct);

                        //fl_updateCalculatesData ($_POST['invoice_id'], array());

                        echo json_encode(array('result' => 'success', 'data' => fl_updateCalculatesData ($_POST['invoice_id'], $mat_cons_j_ex, false)));
                    }
                }
            }
        }
    }

?>