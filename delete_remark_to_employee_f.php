<?php

//delete_remark_to_employee_f.php
//Функция удаления замечания

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);
        if ($_POST){
            //include_once 'DBWork.php';
            include_once 'ffun.php';

            if (!isset($_POST['remark_to_employee_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {
                include_once('DBWorkPDO.php');

                $db = new DB();

                $query = "DELETE FROM `journal_remark_to_employee` WHERE `id` = '{$_POST['remark_to_employee_id']}'";

                $db::sql($query, []);

                //логирование
                //AddLog (GetRealIp(), $session_id, '', 'Добавлен вычет. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

                echo json_encode(array('result' => 'success', 'data' => 'Ok'));
            }
        }
    }
?>