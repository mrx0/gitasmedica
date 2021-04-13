<?php

//add_empty_shed_f.php
//оздаём пустую смену, чтобы открывался шаблона графика плана

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (isset($_POST['type_id']) && isset($_POST['filial_id'])){

                include_once('DBWorkPDO.php');

                $db = new DB();

                $args = [
                    'type' => $_POST['type_id'],
                    'filial' => $_POST['filial_id'],
                    'day' => 1,
                    'smena' => 1,
                    'kab' => 1,
                    'worker' => 0
                ];

                $query = "INSERT INTO `sheduler_template`
                                (`filial`, `day`, `smena`, `kab`, `worker`, `type`)
                                VALUES (:filial, :day, :smena, :kab, :worker, :type);";

                $db::sql($query, $args);

                echo json_encode(array('result' => 'success'));
            }
        }
    }

?>