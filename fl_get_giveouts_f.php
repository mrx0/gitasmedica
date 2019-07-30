<?php

//fl_get_giveouts_f.php
//Функция поиска выплат выдач за период

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            require 'variables.php';
            include_once 'fl_DBWork.php';

            if (!isset($_POST['filial_id']) || !isset($_POST['month']) || !isset($_POST['year'])){
                echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'summCalc' => 0));
            }else {
                //var_dump ($_POST);

                //Приводим месяц к виду 01 02 09 ...
                //$month = dateTransformation ($month);

                $msql_cnnct = ConnectToDB();

                //Выплаты
                $subtractions_j = array();

                $query = "SELECT * FROM  `fl_journal_filial_subtractions` WHERE `filial_id`='{$_POST['filial_id']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($subtractions_j, $arr);
                    }
                }
                //var_dump($subtractions_j);

                //Выплаты !!! не доделал, переделать всё, если понадобится вообще.
                //!!!! в базе не хранится filial_id
                $fl_refunds_j = array();

//                $query = "SELECT * FROM  `fl_journal_refund` WHERE `filial_id`='{$_POST['filial_id']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($fl_refunds_j, $arr);
//                    }
//                }
                //var_dump($fl_refunds_j);

                //Затраты на материалы
                $material_consumption_j = array();

//                $query = "SELECT * FROM `journal_inv_material_consumption` WHERE `filial_id`='{$_POST['filial_id']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $number = mysqli_num_rows($res);
//
//                if ($number != 0) {
//                    while ($arr = mysqli_fetch_assoc($res)) {
//                        array_push($material_consumption_j, $arr);
//                    }
//                }
//                var_dump($material_consumption_j);
//

                echo json_encode(array('result' => 'success', 'subtractions_j' => $subtractions_j, 'fl_refunds_j' => $fl_refunds_j, 'material_consumption_j' => $material_consumption_j));

            }
        }else{
            echo json_encode(array('result' => 'error', 'status' => '0', 'data' => '<div class="query_neok">Какая-то ошибка.</div>', 'summCalc' => 0, 'notDeployCount' => 0));
        }
    }
?>