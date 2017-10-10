<?php

//get_topic2.php
//есть ли новые непрочитанные объявления

    session_start();

	if ($_POST){
        if (isset($_POST['type'])){
            //$_POST['type'] = 5;

            $rezult = '';
            $rezult_arr = array();

            include_once 'DBWork.php';
            include_once 'functions.php';

            $msql_cnnct = ConnectToDB();

            $arr = array();
            $rez = array();

            //Выбираем количество непрочитанных сообщений
            $query = "SELECT COUNT(*) AS total FROM `journal_announcing` jann
            WHERE jann.id NOT IN 
            (SELECT `announcing_id` FROM `journal_announcing_readmark` jannrm 
            WHERE jannrm.create_person = '{$_SESSION['id']}' AND jann.id = jannrm.announcing_id AND jannrm.status = '1')";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $arr = mysqli_fetch_assoc($res);

            echo json_encode(array('result' => 'success', 'data' => $arr['total']));
        }
    }
?>
	