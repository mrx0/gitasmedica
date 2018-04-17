<?php

//get_topic2.php
//есть ли новые непрочитанные объявления

    session_start();

	if ($_POST){
        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            if (isset($_SESSION['id'])) {
                $rezult = '';
                $rezult_arr = array();

                include_once 'DBWork.php';
                include_once 'functions.php';

                $msql_cnnct = ConnectToDB();

                $arr = array();
                $rez = array();

                if ($_SESSION['permissions'] != 777) {
                    $query_dop = "AND jann.id IN (SELECT `annoncing_id` FROM `journal_announcing_worker` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `annoncing_id` = jann.id)";
                } else {
                    $query_dop = '';
                }

                //Выбираем количество непрочитанных сообщений
                $query = "SELECT COUNT(*) AS total FROM `journal_announcing` jann
                WHERE jann.id NOT IN 
                (SELECT `announcing_id` FROM `journal_announcing_readmark` jannrm 
                WHERE jannrm.create_person = '{$_SESSION['id']}' AND jann.id = jannrm.announcing_id AND jannrm.status = '1')
                {$query_dop}";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $arr = mysqli_fetch_assoc($res);

                echo json_encode(array('result' => 'success', 'data' => $arr['total']));
            }
        }
    }
?>
	