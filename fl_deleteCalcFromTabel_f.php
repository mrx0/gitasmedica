<?php

//fl_deleteCalcFromTabel_f.php
//Удалить РЛ из табеля

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['tabel_id']) || !isset($_POST['calculate_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{

                include_once 'DBWork.php';
                include_once 'ffun.php';

                //Подключаемся к базе
                $msql_cnnct = ConnectToDB ();

                //Сначала выясним табель с РЛ ночной или нет
                $query = "SELECT `noch` FROM `fl_journal_tabels_ex` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `calculate_id` = '{$_POST['calculate_id']}' ;";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $arr = mysqli_fetch_assoc($res);

                $noch = $arr['noch'];

                //Удаляем из табеля
                $time = date('Y-m-d H:i:s', time());

                $query = "DELETE FROM `fl_journal_tabels_ex` WHERE `tabel_id` = '{$_POST['tabel_id']}' AND `calculate_id` = '{$_POST['calculate_id']}' AND `noch`='{$noch}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //Ставим метку обновлено на РЛ
                $query = "UPDATE `fl_journal_calculate` SET `last_edit_time`='{$time}', `last_edit_person` = '{$_SESSION['id']}' WHERE `id`='{$_POST['calculate_id']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                CloseDB ($msql_cnnct);

                //Обновим баланс табеля
                if ($noch == 1){
                    updateTabelBalanceNoch($_POST['tabel_id']);
                }else {
                    updateTabelBalance($_POST['tabel_id']);
                }

                echo json_encode(array('result' => 'success', 'data' => ''));

            }
        }
    }
?>