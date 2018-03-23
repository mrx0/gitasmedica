<?php

//fl_emptySmenaTabel_delete_f.php
//Функция удаления надбавки за ночные смены в табель непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabel_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                $query = "DELETE FROM `fl_journal_tabel_emptysmens` WHERE `tabel_id` = '{$_POST['tabel_id']}'";
                            
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //Обновим и табель
                $query = "UPDATE `fl_journal_tabels` SET `empty_smena` = '0' WHERE `id`='{$_POST['tabel_id']}';";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                //updateTabelNightSmensSumm ($_POST['tabel_id']);

                echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }
    }
?>