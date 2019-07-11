<?php

//fl_paidout_add_f.php
//Функция добавления выплаты в табель непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['tabel_id']) || !isset($_POST['paidout_summ'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $rez = array();

                $msql_cnnct = ConnectToDB();

                $time = date('Y-m-d H:i:s', time());

                if ($_POST['noch'] == 1){
                    $tabel_id = 0;
                    $tabel_noch_id = $_POST['tabel_id'];
                }else{
                    $tabel_id = $_POST['tabel_id'];
                    $tabel_noch_id = 0;
                }

                $query = "INSERT INTO `fl_journal_paidouts` (`tabel_id`, `tabel_noch_id`, `filial_id`, `type`, `summ`, `noch`, `descr`, `create_time`, `create_person`)
                            VALUES (
                            '{$tabel_id}', '{$tabel_noch_id}', '{$_POST['filial_id']}', '{$_POST['type']}', '".round($_POST['paidout_summ'], 2)."', '{$_POST['noch']}', '{$_POST['descr']}', '{$time}', '{$_SESSION['id']}');";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                //ID новой позиции
                $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                if ($_POST['noch'] == 1){
                    updateTabelNochPaidoutSumm ($_POST['tabel_id']);
                }else{
                    updateTabelPaidoutSumm ($_POST['tabel_id']);
                }

                //!!! Тест пробуем добавить в БД вычеты, че кому сколько выдали с какого филиала
//                if (isset($_POST['subtractions'])){
//                    if (!empty($_POST['subtractions'])){
//
//                        foreach ($_POST['subtractions'] as $filial_id => $summ){
//                            $query = "INSERT INTO `fl_journal_filial_subtractions` (`tabel_id`, `tabel_noch_id`, `month`, `year`, `filial_id`, `worker_id`, `paidout_id`, `type`, `summ`, `noch`, `create_time`, `create_person`)
//                            VALUES (
//                            '{$tabel_id}', '{$tabel_noch_id}', '{$_POST['month']}', '{$_POST['year']}', '{$filial_id}', '{$_POST['worker_id']}', '{$mysql_insert_id}', '{$_POST['type']}', '{$summ}', '{$_POST['noch']}', '{$time}', '{$_SESSION['id']}');";
//
//                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//                        }
//
//
//
//                    }
//                }


                echo json_encode(array('result' => 'success', 'data' => $query));

            }
        }
    }
?>