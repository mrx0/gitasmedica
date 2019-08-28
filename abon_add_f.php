<?php

//abon_add_f.php
//Функция добавления абонемента непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';

            if (!isset($_POST['num']) || !isset($_POST['abon_type'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                //Соберем данные по типу абонемента
                $abon_types_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `spr_solar_abonements` WHERE `id` = '{$_POST['abon_type']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    $arr = mysqli_fetch_assoc($res);

                    $min_count = $arr['min_count'];
                    $exp_days = $arr['exp_days'];
                    $summ = $arr['summ'];

                    //А нет ли уже такого номера в базе?
                    $query = "SELECT * FROM `journal_abonement_solar` WHERE `num`='{$_POST['num']}'";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Абонемент с таким номером уже присутствует в базе.</div>'));
                    } else {
                        //Добавляем абонемент в базу
                        $cert_id = WriteAbonToDB_Edit($_SESSION['id'], $_POST['num'], $_POST['abon_type'], $min_count, $exp_days, $summ);

                        echo json_encode(array('result' => 'success', 'data' => '<div class="query_ok"><a href="abonement.php?id=' . $cert_id . '" class="ahref">Абонемент</a> добавлен.</div>'));
                    }

                }
            }
        }
    }
?>