<?php

//ffl_deletePaidoutFromTabel_f.php
//Удалить Выплату из табеля

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['tabel_id']) || !isset($_POST['paidout_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                include_once 'DBWork.php';
                include_once 'ffun.php';

                //Подключаемся к другой базе специально созданной для тикетов
                $msql_cnnct = ConnectToDB2 ();

                //Добавляем категории сотрудников
                $query = "DELETE FROM `fl_journal_paidouts` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `id` = '{$_POST['paidout_id']}' ;";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                CloseDB ($msql_cnnct);

                //Обновим баланс табеля
                updateTabelPaidoutSumm($_POST['tabel_id']);

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>