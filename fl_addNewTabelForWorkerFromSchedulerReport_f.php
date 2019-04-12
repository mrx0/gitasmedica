<?php

//fl_addNewTabelForWorkerFromSchedulerReport_f.php
//Добавить табель администратору или ассистенту

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else {
        //var_dump($_POST);

        if ($_POST) {
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['worker_id']) || !isset($_POST['filial_id']) || !isset($_POST['type']) ||
                !isset($_POST['month']) || !isset($_POST['year']) ||
                !isset($_POST['oklad']) || !isset($_POST['w_percenthours']) || !isset($_POST['worker_revenue_percent']) ||
                !isset($_POST['per_from_salary']) || !isset($_POST['filialmoney'])  || !isset($_POST['w_revenue_summ']) || !isset($_POST['worker_category_id']) ||
                !isset($_POST['w_hours']) || !isset($_POST['summ'])
            ) {
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            } else {
                if (($_POST['filial_id'] != 0) && ($_POST['w_hours'] != 0)) {
                    //Смотрим, нет ли у этого сотрудника уже табеля за этот месяц
                    $msql_cnnct = ConnectToDB();

                    $query = "SELECT * FROM `fl_journal_tabels` WHERE `worker_id`='{$_POST['worker_id']}' AND `month`='{$_POST['month']}' AND  `year`='{$_POST['year']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    //Если ничего нет
                    if ($number == 0) {

                        $time = date('Y-m-d H:i:s', time());

                        $query = "
                    INSERT INTO `fl_journal_tabels` (`worker_id`, `office_id`, `type`, 
                    `month`, `year`,
                    `salary`, `hours_percent`, `revenue_percent`,
                    `per_from_salary`, `percent_summ`, `filial_summ`, `category`,
                    `hours_count`, `summ`, `create_time`, `create_person`
                    )
                    VALUES (
                    '{$_POST['worker_id']}', '{$_POST['filial_id']}', '{$_POST['type']}',
                    '{$_POST['month']}', '{$_POST['year']}',
                    '{$_POST['oklad']}', '{$_POST['w_percenthours']}', '{$_POST['worker_revenue_percent']}', 
                    '{$_POST['per_from_salary']}', '{$_POST['w_revenue_summ']}', '{$_POST['filialmoney']}','{$_POST['worker_category_id']}', 
                    '{$_POST['w_hours']}', '{$_POST['summ']}', '{$time}', '{$_SESSION['id']}'
                    )";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        echo json_encode(array('result' => 'success', 'data' => 'Ok'));

                    } else {
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Табель сотрудника за этот месяц уже создан.</div>'));
                    }
                }
            }
        }
    }
?>