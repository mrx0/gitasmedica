<?php

//fl_deleteNightFromTabel_f.php
//Удалить ночной отчёт из табеля

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['tabel_id']) || !isset($_POST['tabel_night_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                include_once 'DBWork.php';
                include_once 'ffun.php';

                //Подключаемся к базе
                $msql_cnnct = ConnectToDB2 ();

                //Удаляем
                //$query = "DELETE FROM `fl_journal_tabels_noch` WHERE `id` = '{$_POST['tabel_night_id']}' AND `tabel_id` = '{$_POST['tabel_id']}' ;";
                $query = "DELETE FROM `fl_journal_tabels_noch_ex` WHERE `id` = '{$_POST['tabel_night_id']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                CloseDB ($msql_cnnct);

                //Обновим баланс табеля
                updateTabelBalanceNoch($_POST['tabel_id']);
                //Рассчитаем и обновим ночной баланс табеля
//                $query = "SELECT SUM(`summ`) AS `summ` FROM `fl_journal_tabels_noch` WHERE `tabel_id`='{$_POST['tabel_id']}'";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                $arr = mysqli_fetch_assoc($res);
//
//                $query = "UPDATE `fl_journal_tabels` SET `night_smena` = '".round($arr['summ'], 2)."' WHERE `id`='{$_POST['tabel_id']}';";
//
//                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);



                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>