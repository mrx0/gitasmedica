<?php

//ffl_deletePaidoutFromTabel_f.php
//Удалить Выплату из табеля

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['tabel_id']) || !isset($_POST['paidout_id']) || !isset($_POST['noch'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                include_once 'DBWork.php';
                include_once 'ffun.php';

                //Подключаемся к другой базе
                $msql_cnnct = ConnectToDB2 ();

                //Удаляем выплаты
                if ($_POST['noch'] == 0) {
                    $query = "DELETE FROM `fl_journal_paidouts` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `id` = '{$_POST['paidout_id']}' ;";
                }else{
                    $query = "DELETE FROM `fl_journal_paidouts` WHERE `tabel_noch_id` = '{$_POST['tabel_id']}' AND `id` = '{$_POST['paidout_id']}' ;";
                }
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //Надо удалить вычеты по филиалам, связанные с этой выплатой / для одной из версий
                if ($_POST['noch'] == 0) {
                    $query = "DELETE FROM `fl_journal_pos_filials_subtractions` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `paidout_id` = '{$_POST['paidout_id']}' ;";
                }else{
                    $query = "DELETE FROM `fl_journal_pos_filials_subtractions` WHERE `tabel_noch_id` = '{$_POST['tabel_id']}' AND `paidout_id` = '{$_POST['paidout_id']}' ;";
                }
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //Надо удалить вычеты по филиалам, связанные с этой выплатой / для другой версии
                if ($_POST['noch'] == 0) {
                    $query = "DELETE FROM `fl_journal_filial_subtractions` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `paidout_id` = '{$_POST['paidout_id']}' ;";
                }else{
                    $query = "DELETE FROM `fl_journal_filial_subtractions` WHERE `tabel_noch_id` = '{$_POST['tabel_id']}' AND `paidout_id` = '{$_POST['paidout_id']}' ;";
                }
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                CloseDB ($msql_cnnct);

                //Обновим баланс табеля
                if ($_POST['noch'] == 1){
                    updateTabelNochPaidoutSumm ($_POST['tabel_id']);
                }else{
                    updateTabelPaidoutSumm ($_POST['tabel_id']);
                }

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>