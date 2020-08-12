<?php

//fl_money_from_outside_add_f.php
//Функция добавления прихода извне в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['summ']) || !isset($_POST['filial_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $time = date('Y-m-d H:i:s', time());
                //$date_in = date('Y-m-d H:i:s', strtotime($_POST['date_in']." 09:00:00"));

                $msql_cnnct = ConnectToDB ();

                $descr = addslashes($_POST['descr']);

                $query = "INSERT INTO `fl_journal_money_from_outside` (`month`, `year`, `filial_id`, `summ`, `descr`, `create_time`, `create_person`)
                        VALUES (
                        '{$_POST['month']}', '{$_POST['year']}', '{$_POST['filial_id']}', '".round($_POST['summ'], 2)."', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}');";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                echo json_encode(array('result' => 'success'));

            }
        }
    }
?>