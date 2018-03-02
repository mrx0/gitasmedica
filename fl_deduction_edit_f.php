<?php

//fl_deduction_edit_f.php
//Функция добавления вычета из табеля непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';

            if (!isset($_POST['tabel_id']) || !isset($_POST['deduction_summ'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                $query = "INSERT INTO `fl_journal_deductions` (`tabel_id`, `deduction_summ`, `descr`, `create_time`, `create_person`)
                            VALUES (
                            '{$_POST['tabel_id']}', '{$_POST['deduction_summ']}', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}');";
                            
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));

            }
        }
    }
?>