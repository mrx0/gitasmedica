<?php 

//fl_change_personal_surcharge_f.php
//Персональные надбавки (фиксированные)

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){

            if (!isset($_POST['worker_id']) || !isset($_POST['val'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $time = date('Y-m-d H:i:s', time());

                include_once 'ffun.php';

                $rez = array();
                $surcharge_j = array();
                $surcharge_j_id  = 0;

                $msql_cnnct = ConnectToDB2 ();

                $query = "SELECT `id` FROM `fl_spr_surcharges` WHERE `worker_id` = '{$_POST['worker_id']}' AND `type` = '1' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $surcharge_j_id = $arr['id'];
                    }
                }
                //var_dump ($percents_personal_j);

                //if (!empty($percents_personal_j)){
                if ($surcharge_j_id != 0){
                    $query = "UPDATE `fl_spr_surcharges` SET `summ`='{$_POST['val']}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$surcharge_j_id}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                }else{
                    $query = "INSERT INTO `fl_spr_surcharges` (
                    `summ`, `worker_id`, `type`, `create_time`, `create_person`)
                    VALUES (
                    '{$_POST['val']}', '{$_POST['worker_id']}', '1', '{$time}', '{$_SESSION['id']}'
                    );";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                }

                //Если спец. цена, обнулим все остальное
                /*if ($_POST['type'] == 3){
                    $query = "UPDATE `fl_spr_percents_personal` SET `percent`='0', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `worker_id` = '{$_POST['worker_id']}' AND `percent_cats` = '{$_POST['cat_id']}' AND (`type` = '1' OR  `type` = '2')";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                }*/

                //Удаляем из БД
                //$query = "DELETE FROM `fl_journal_calculate` WHERE `id`='{$_POST['id']}'";
                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


                echo json_encode(array('result' => 'success', 'data' => 'Обновлено'));

            }
		}
	}
	
?>