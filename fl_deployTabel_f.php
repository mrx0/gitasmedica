<?php

//fl_deployTabel_f.php
//Провести табель

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {

            if (!isset($_POST['tabel_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once 'DBWork.php';
                include_once 'functions.php';

                include_once 'ffun.php';

                require 'variables.php';

                $summItog = 0;

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_POST['tabel_id'], 'id');

                if ($tabel_j != 0) {

                    $msql_cnnct = ConnectToDB2();

                    //Отметки по дополнительным опциям
                    //!!! Здесь функция большая и избыточная, но лень переписывать
                    $spec_prikaz8_checked = '';
                    $spec_oklad_checked = '';
                    $spec_oklad_work_checked = '';

                    $query = "SELECT * FROM `options_worker_spec` WHERE `worker_id`='{$tabel_j[0]['worker_id']}' LIMIT 1";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    $spec_prikaz8 = false;
                    $spec_oklad = false;
                    $spec_oklad_work = false;

                    if ($number != 0){
                        $arr = mysqli_fetch_assoc($res);
                        if ($arr['prikaz8'] == 1){
                            $spec_prikaz8 = true;
                        }
                        if ($arr['oklad'] == 1){
                            $spec_oklad = true;
                        }
                        if ($arr['oklad_work'] == 1){
                            $spec_oklad_work = true;
                        }
                    }
//                    var_dump($spec_prikaz8);
//                    var_dump($spec_oklad);
//                    var_dump($spec_oklad_work);

                    //Общая сумма, которую осталось выплатить = сумма (РЛ) + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
                    $summItog = $tabel_j[0]['summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'];

                    //Если ассистент, то плюсуем сумму за РЛ
                    if (($tabel_j[0]['type'] == 7) || $spec_oklad_work){
                        $summItog += $tabel_j[0]['summ_calc'];
                    }

                    //Коэффициенты +/-
                    if (($tabel_j[0]['k_plus'] != 0) || ($tabel_j[0]['k_minus'] != 0)){
                        $summItog = $summItog + $summItog/100*($tabel_j[0]['k_plus'] - $tabel_j[0]['k_minus']);
                    }
                    //var_dump($summItog);

                    //Общая сумма, которую осталось выплатить = всего сумма - вычеты - оплачено - выплачено
                    $summItog = $summItog - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
//                        var_dump($summItog);

                    if (intval($summItog) == 0) {

                        //Обновляем
                        $query = "UPDATE `fl_journal_tabels` SET `status`='7' WHERE `id`='{$_POST['tabel_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        CloseDB($msql_cnnct);

                        echo json_encode(array('result' => 'success', 'data' => 'Ok'));
                    }else{
                        echo json_encode(array('result' => 'error', 'data' => $summItog));
                    }
                }
            }
        }
    }

?>