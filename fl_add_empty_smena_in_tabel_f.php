<?php

//fl_add_empty_smena_in_tabel_f.php
//Функция добавления надбавки за "пустые" смены в табель непосредственно в БД

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

                $empty_smena_price = 500;

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                $query = "INSERT INTO `fl_journal_tabel_emptysmens` 
                                (`tabel_id`, `price`, `count`, `summ`, `create_time`, `create_person`)
                            VALUES 
                                ('{$_POST['tabel_id']}', '{$empty_smena_price}', '{$_POST['count']}', '". $empty_smena_price * $_POST['count'] ."', '{$time}', '{$_SESSION['id']}')
                            ON DUPLICATE KEY UPDATE
					            `count` = '{$_POST['count']}',
					            `summ` = '". $empty_smena_price * $_POST['count'] ."';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                updateTabelEmptySmensSumm ($_POST['tabel_id']);

                echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }
    }
?>