<?php

//get_univer_new.php
//есть ли новые задания по UNIVER

    session_start();

	if ($_POST){
        if (isset($_POST['type']) && isset($_SESSION['id'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            //include_once 'DBWork.php';
            include_once('DBWorkPDO_2.php');
            include_once 'functions.php';

            //разбираемся с правами
            $god_mode = FALSE;

            require_once 'permissions.php';

            //$msql_cnnct2 = ConnectToDB_2 ('config_ticket');
            $db = new DB2();

            $arr = array();
            $rez = array();

            $query_dop = '';

            //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `task_id` = j_task.id)";
            }

            //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                if (isset($_SESSION['filial'])) {
                    $query_dop .= " AND j_task.id IN (SELECT `task_id` FROM `journal_tasks_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `task_id` = j_task.id)";
                }
            }

            //Надо выбрать те, которые относятся к конкретному сотруднику
            if (($ticket['see_all'] != 1) && (!$god_mode)){
                $query_dop .= " OR j_task.id IN (SELECT `task_id` FROM `journal_tasks_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `task_id` = j_task.id)";
            }

            //Выбираем количество
            $query = "SELECT COUNT(j_task.id) as total FROM `journal_tasks` j_task
            WHERE (TRUE 
            {$query_dop}
            OR j_task.create_person = '{$_SESSION['id']}') AND j_task.status = '1'
            AND j_task.id NOT IN (SELECT jtask_rm.task_id  FROM `journal_tasks_readmark` jtask_rm WHERE j_task.id = jtask_rm.task_id AND jtask_rm.create_person = '{$_SESSION['id']}' AND jtask_rm.status = '1')";

            //!!! доделать
            //$res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

            //$arr = mysqli_fetch_assoc($res);

            //CloseDB ($msql_cnnct2);

            echo json_encode(array('result' => 'success', 'data' => $query));
        }
    }
?>
	