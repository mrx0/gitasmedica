<?php

//ticket_i_read_all_f.php
//Типа всё прочитали

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump (htmlspecialchars($_POST['descr']);

        if ($_POST){

            if (!isset($_POST['worker_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                //разбираемся с правами
                $god_mode = FALSE;

                require_once 'permissions.php';

                $log = array();

                $time = date('Y-m-d H:i:s', time());

                $msql_cnnct2 = ConnectToDB_2('config_ticket');

                $tickets_arr = array();

                $arr = array();
                $rez = array();

                $query_dop = '';

                $show_option_str = " AND j_ticket.id NOT IN (SELECT `ticket_id` FROM `journal_tickets_readmark` jticket_rm2 WHERE j_ticket.id = jticket_rm2.ticket_id AND jticket_rm2.create_person = '{$_SESSION['id']}' AND jticket_rm2.status = '1')";

                //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `ticket_id` = j_ticket.id)";
                }

                //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    if (isset($_SESSION['filial'])) {
                        $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `ticket_id` = j_ticket.id)";
                    }
                }

                //Надо выбрать те, которые относятся к конкретному сотруднику
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    $query_dop .= " OR j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `ticket_id` = j_ticket.id)";
                }

                //Выборка объявлений не удалённых (j_ticket.status <> '9')
                //и плюс статус прочитан он данным сотрудником или нет
                //и плюс если текущий пользователь указан как исполнитель
                $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
                GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
                FROM `journal_tickets` j_ticket
                LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = j_ticket.id
                WHERE (TRUE
                {$query_dop}
                OR j_ticket.create_person = '{$_SESSION['id']}')
                {$show_option_str}
                
                GROUP BY `id`";

                $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($tickets_arr, $arr);
                    }

                    $query = '';

                    //Удаляем отметки о прочтении
                    //$query_dop .= "DELETE FROM `journal_tickets_readmark` WHERE `ticket_id` = '{$_POST['ticket_id']}';";

                    foreach ($tickets_arr as $tickets_data) {
                        //Добавляем отметку о прочтении
                        $query .= "INSERT INTO `journal_tickets_readmark` (`ticket_id`, `create_time`, `create_person`, `status`)
                            VALUES (
                            '{$tickets_data['id']}', '{$time}', '{$_SESSION['id']}', '1');";
                    }

                    //Делаем большой запрос
                    $res = mysqli_multi_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                }

                CloseDB($msql_cnnct2);

                echo json_encode(array('result' => 'success', 'data' => ''));
            }
        }
    }

?>