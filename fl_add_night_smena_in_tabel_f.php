<?php

//fl_add_night_smena_in_tabel_f.php
//Функция добавления надбавки за ночные смены в табель непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabel_id']) || !isset($_POST['count'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $price = 1000;

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                $query = "INSERT INTO `fl_journal_tabel_nightsmens` (`tabel_id`, `price`, `count`, `summ`, `create_time`, `create_person`)
                            VALUES (
                            '{$_POST['tabel_id']}', '{$price}', '{$_POST['count']}', '". $price * $_POST['count'] ."', '{$time}', '{$_SESSION['id']}');";
                            
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                updateTabelNightSmensSumm ($_POST['tabel_id']);

                echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }
    }
?>