<?php 

//invoice_time_edit_f.php
//Функция для изменения времени наряда

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';
		if ($_POST){
            if ((isset($_POST['new_create_time'])) && isset($_POST['id'])) {

                $data = '';

                $time = time();

                $msql_cnnct = ConnectToDB();

                //проверим, есть ли оплаты по этому наряду
                //Документы закрытия/оплаты нарядов списком
                $invoice_j = array();

                $query = "SELECT `id`, `create_time`, `zapis_id` FROM `journal_invoice` WHERE `id`='" . $_POST['id'] . "' LIMIT 1";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        $invoice_j[$arr['id']] = $arr;
                    }
                } else {

                }

                if (!empty($invoice_j)){

                    $time_hm_old = date("H:i", strtotime($invoice_j[$_POST['id']]['create_time']));

                    $query = "SELECT `day`, `month`, `year`, `create_time` FROM `zapis` WHERE `id`='" .  $invoice_j[$_POST['id']]['zapis_id'] . "' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            $zapis_create_time = strtotime($arr['day'].'.'.$arr['month'].'.'.$arr['year']);
                        }
                    } else {
                        $zapis_create_time = 0;
                    }

                    if ($zapis_create_time != 0) {
                        if (strtotime($_POST['new_create_time'].' 00:00:00') >= $zapis_create_time){
                            if (strtotime($_POST['new_create_time'].' 00:00:00') <= time()){

                                $time = date('Y-m-d H:i:s', time());

                                $new_create_time = date('Y-m-d H:i:s', strtotime($_POST['new_create_time'].' '.$time_hm_old));

                                $query = "UPDATE `journal_invoice` SET `create_time`='{$new_create_time}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['id']}'";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                //Ок
                                $data = '<div class="query_ok" style="padding-bottom: 10px;"><h3>Дата внесения наряда изменена.</h3></div>';
                                echo json_encode(array('result' => 'success', 'data' => $data));

                            }else{

                                $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>Вносить наряд в будущее не получится.</h3></div>';
                                echo json_encode(array('result' => 'error', 'data' => $data));

                            }
                        }else{

                            $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>Вносить наряд раньше, чем была сделана запись, не получится.</h3></div>';
                            echo json_encode(array('result' => 'error', 'data' => $data));

                        }
                    }

                } else {

                    //$data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>neOK.</h3></div>';
                    //echo json_encode(array('result' => 'error', 'data' => $data));

                }
            }
		}

	}
	
?>