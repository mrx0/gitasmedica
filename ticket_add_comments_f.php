<?php

//ticket_add_comments_f.php
//Добавить новый коммент

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump (htmlspecialchars($_POST['descr']);

        if ($_POST){

            if (!isset($_POST['ticket_id']) && (!isset($_POST['descr']))){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $time = date('Y-m-d H:i:s', time());

                $log = array();

                $msql_cnnct2 = ConnectToDB_2('config_ticket');

                //$comment = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['descr'])))));
                $comment = htmlspecialchars($_POST['descr']);

                $comment = mysqli_real_escape_string($msql_cnnct2, $comment);

                $query = '';

                if ($comment != ''){
                    $query .= "INSERT INTO `journal_tickets_comments` (`ticket_id`, `create_time`, `create_person`, `descr`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '{$comment}');";
                }

                //Добавляем лог
                $query .= "INSERT INTO `journal_tickets_logs` (`ticket_id`, `create_person`, `descr`, `create_time`)
                    VALUES (
                    '{$_POST['ticket_id']}', '{$_SESSION['id']}', 'Добавлен комментарий', '{$time}');";

                $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2) . ' -> ' . $query);

                CloseDB($msql_cnnct2);

                echo json_encode(array('result' => 'success', 'data' => ''));
            }
        }
    }

?>