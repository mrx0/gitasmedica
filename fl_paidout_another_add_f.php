<?php

//fl_paidout_another_add_f.php
//Функция добавления тестовой выплаты непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['worker']) || !isset($_POST['paidout_summ']) || !isset($_POST['paidout_id']) || !isset($_POST['filial_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $time = date('Y-m-d H:i:s', time());
                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                if (mb_strlen($_POST['worker']) > 0) {
                    $worker_j = SelDataFromDB('spr_workers', $_POST['worker'], 'worker_full_name');
                }else{
                    $worker_j = 0;
                }

                //if ($worker_j != 0){

                    $worker_id = $worker_j[0]['id'];

                    $msql_cnnct = ConnectToDB ();

                    $descr = addslashes($_POST['descr']);

                    $query = "INSERT INTO `fl_journal_paidouts_temp` (`month`, `year`, `worker_id`, `filial_id`, `type`, `summ`, `descr`, `create_time`, `create_person`)
                            VALUES (
                            '{$_POST['month']}', '{$_POST['year']}', '{$worker_id}', '{$_POST['filial_id']}', '{$_POST['paidout_id']}', '".round($_POST['paidout_summ'], 2)."', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}');";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    echo json_encode(array('result' => 'success', 'data' => $_POST['worker']));

//                }else{
//                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В нашей базе нет такого сотрудника</div>'));
//                }
            }
        }
    }
?>