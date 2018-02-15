<?php

//ajax_ticket_done.php
//Изменить статус тикета (закрыть)

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['ticket_id']) || !isset($_POST['workers_exist'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $time = date('Y-m-d H:i:s', time());
                $time2 = date('Y-m-d', time());

                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                $query_dop = '';

                if ($_POST['workers_exist'] != 'true'){
                    $query_dop .= "INSERT INTO `journal_tickets_workers` (`ticket_id`, `worker_id`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$_SESSION['id']}');";
                }

                if (isset($_POST['last_comment'])){
                    $comment = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['last_comment'])))));
                }

                if ($comment != ''){
                    $query_dop .= "INSERT INTO `journal_tickets_comments` (`ticket_id`, `create_time`, `create_person`, `descr`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '{$comment}');";
                }

                //Удаляем отметки о прочтении
                $query_dop .= "DELETE FROM `journal_tickets_readmark` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                //Добавляем отметку о прочтении (мы же создали это сами)
                $query_dop .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '1');";

                //Добавляем лог
                $query_dop .= "INSERT INTO `journal_tickets_logs` (`ticket_id`, `create_person`, `descr`, `create_time`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$_SESSION['id']}', 'Тикет был закрыт как завершённый', '{$time}');";

                $query = "UPDATE `journal_tickets` SET 
                    `last_edit_time`='$time',
                    `last_edit_person`='{$_SESSION['id']}',
                    `fact_date`='$time2',
                    `status`='1'
                     WHERE `id`='{$_POST['ticket_id']}';
                     {$query_dop}";

                //Делаем большой запрос
                $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $data = '
                        <div class="query_ok">
                            Тикет закрыт как завершённый
                        </div>';
                echo json_encode(array('result' => 'success', 'data' => $data));
            }
        }
    }
?>