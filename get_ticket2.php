<?php

//get_ticket2.php
//есть ли новые или изменённые Тикеты

    session_start();

	if ($_POST){
        if (isset($_POST['type']) && isset($_SESSION['id'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            include_once 'DBWork.php';
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

            $arr = array();
            $rez = array();

            $query_dop = '';

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

            //Выбираем количество
            /*$query = "SELECT COUNT(id) AS total FROM `journal_tickets` j_ticket
            WHERE j_ticket.id NOT IN 

            (SELECT j_ticket.id FROM `journal_tickets` j_ticket
            LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id
            WHERE (j_ticket.status <> '9'
            {$query_dop}
            OR j_ticket.create_person = '{$_SESSION['id']}')
            AND jticket_rm.status = '1')";*/

            /*$query = "SELECT j_ticket.id, FROM `journal_tickets` j_ticket
            LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
            LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
            WHERE (j_ticket.status <> '9'
            {$query_dop}
            OR j_ticket.create_person = '{$_SESSION['id']}')
            AND jticket_rm.status <> '1'";*/

            $query = "SELECT COUNT(j_ticket.id) as total FROM `journal_tickets` j_ticket
            WHERE (j_ticket.status <> '9'
            {$query_dop}
            OR j_ticket.create_person = '{$_SESSION['id']}')
            AND j_ticket.id NOT IN (SELECT jticket_rm.ticket_id  FROM `journal_tickets_readmark` jticket_rm WHERE j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}' AND jticket_rm.status = '1')";

            
            
            //Выбираем количество
            /*$query = "SELECT COUNT(*) AS total FROM `journal_announcing` jann
            WHERE jann.id NOT IN 
            (SELECT `announcing_id` FROM `journal_announcing_readmark` jannrm 
            WHERE jannrm.create_person = '{$_SESSION['id']}' AND jann.id = jannrm.announcing_id AND jannrm.status = '1')
            {$query_dop}";*/

            $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

            $arr = mysqli_fetch_assoc($res);

            CloseDB ($msql_cnnct2);

            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
        }
    }
?>
	