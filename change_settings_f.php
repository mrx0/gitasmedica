<?php

//change_settings_f.php
//Функция изменения настроек

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            include_once 'fl_DBWork.php';

            if (!isset($_POST['option']) || !isset($_POST['value'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                include_once('DBWorkPDO.php');

                $db = new DB();

                $time = time();

                if ($_POST['value'] == 'true'){
                    $value = 'false';
                }else{
                    $value = 'true';
                }

                $args = [
                    'value' => $value,
                    'option' => $_POST['option']
                ];

                $query = "UPDATE `settings` SET `value`= :value WHERE `option`= :option";

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }
        }
    }
?>