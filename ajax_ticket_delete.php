<?php

//ajax_ticket_delete.php
//Изменить статус тикета (удалить)

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['ticket_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $time = date('Y-m-d H:i:s', time());
                $time2 = date('Y-m-d', time());

                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                $query_dop = '';

                if (isset($_POST['last_comment'])){
                    $comment = trim(strip_tags(stripcslashes(htmlspecialchars(($_POST['last_comment'])))));
                }

                if ($comment != ''){
                    $query_dop .= "INSERT INTO `journal_tickets_comments` (`ticket_id`, `create_time`, `create_person`, `descr`)
                            VALUES (
                            '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '{$comment}');";
                }

                //Удаляем отметки о прочтении
                //$query_dop .= "DELETE FROM `journal_tickets_readmark` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                //Добавляем отметку о прочтении (мы же создали это сами)
                /*$query_dop .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$time}', '{$_SESSION['id']}', '1');";*/

                //Добавляем лог
                $query_dop .= "INSERT INTO `journal_tickets_logs` (`ticket_id`, `create_person`, `descr`, `create_time`)
                        VALUES (
                        '{$_POST['ticket_id']}', '{$_SESSION['id']}', 'Тикет был удалён', '{$time}');";

                $query = "UPDATE `journal_tickets` SET 
                    `last_edit_time`='$time',
                    `last_edit_person`='{$_SESSION['id']}',
                    `status`='9'
                     WHERE `id`='{$_POST['ticket_id']}';
                     {$query_dop}";

                //Делаем большой запрос
                $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $data = '
                        <div class="query_ok">
                            Тикет удалён
                        </div>';
                echo json_encode(array('result' => 'success', 'data' => $data));
            }
        }
    }
?>