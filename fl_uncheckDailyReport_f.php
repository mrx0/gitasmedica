<?php

//ffl_uncheckDailyReport_f.php
//Функция снимает статус отметку проверено на отчет

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump ($_POST);

        if ($_POST) {

            if (!isset($_POST['report_id'])) {
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {

                include_once 'DBWork.php';
                include_once 'functions.php';

                include_once 'ffun.php';

                //require 'variables.php';

                $msql_cnnct = ConnectToDB2();

                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `id`='{$_POST['report_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    $arr = mysqli_fetch_assoc($res);

                    $day = $arr['day'];
                    $month = $arr['month'];
                    $year = $arr['year'];

                    $today = date('Y-m-d', time());
                    $monthStart15daysPlus = date('Y-m-d', strtotime('+1 month +14 days', gmmktime(0, 0, 0, $month, 1, $year)));
//!!! Временно открыл доступ куда угодно кому угодно 20200618
//!!! Временно открыл доступ куда угодно кому угодно 20200618
                    //if (($today <= $monthStart15daysPlus) || ($_SESSION['permissions'] == 3)) {
                        //Обновляем
                        $query = "UPDATE `fl_journal_daily_report` SET `status` = '0' WHERE `id`='{$_POST['report_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    //}

                }

                CloseDB($msql_cnnct);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));

            }
        }
    }

?>