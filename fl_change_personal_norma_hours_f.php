<?php 

//fl_change_personal_norma_hours_f.php
//Персональные нормы часов

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){

            if (!isset($_POST['worker_id']) || !isset($_POST['norma_id']) || !isset($_POST['val'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else {

                $time = date('Y-m-d H:i:s', time());

                //!!! @@@
                //Баланс контрагента
                include_once 'ffun.php';

                $rez = array();
                $percents_personal_j = array();
                $norma_hours_personal_j_id = 0;

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT `id` FROM `fl_spr_normahours_personal` WHERE `worker_id` = '{$_POST['worker_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                         $norma_hours_personal_j_id = $arr['id'];
                    }
                }
                //var_dump ($percents_personal_j);

                if ( $norma_hours_personal_j_id != 0){
                    $query = "UPDATE `fl_spr_normahours_personal` SET `count`='{$_POST['val']}' WHERE `id`='{$norma_hours_personal_j_id}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                }else{
                    $query = "INSERT INTO `fl_spr_normahours_personal` (
                    `worker_id`, `count`)
                    VALUES (
                    '{$_POST['worker_id']}', '{$_POST['val']}'
                    );";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
                }

                echo json_encode(array('result' => 'success', 'data' => 'Обновлено'));

            }
		}
	}
	
?>