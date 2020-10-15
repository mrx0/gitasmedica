<?php

//fl_koeff_in_tabel_add_f.php
//Функция добавления коэффициента табеля непосредственно в БД

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            if (!isset($_POST['tabel_id']) || !isset($_POST['minus']) || !isset($_POST['koeff'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                include_once('DBWorkPDO.php');

                $db = new DB();

                $time = time();

                $args = [
                    'tabel_id' => $_POST['tabel_id'],
                    'koeff' => $_POST['koeff']
                ];

                if ($_POST['minus'] == 'true') {
                    $query = "UPDATE `fl_journal_tabels` SET `k_minus`= :koeff WHERE `id`= :tabel_id";
                }else{
                    $query = "UPDATE `fl_journal_tabels` SET `k_plus`= :koeff WHERE `id`= :tabel_id";
                }

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }
        }
    }
?>