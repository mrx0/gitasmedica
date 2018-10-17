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
            if ((isset($_POST['new_time'])) && isset($_POST['invoice_id'])) {

                $data = '';

                $time = time();

                $msql_cnnct = ConnectToDB();

                $invoice_j = array();

                $query = "SELECT `id`, `create_time`, `closed_time`, `zapis_id` FROM `journal_invoice` WHERE `id`='" . $_POST['invoice_id'] . "' LIMIT 1";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        $invoice_j[$arr['id']] = $arr;
                    }
                }

                if (!empty($invoice_j)){

                    //Старое время закрытия работы
                    $time_hm_old = date("H:i", strtotime($invoice_j[$_POST['invoice_id']]['closed_time']));

                    $query = "SELECT `day`, `month`, `year`, `create_time` FROM `zapis` WHERE `id`='" .  $invoice_j[$_POST['invoice_id']]['zapis_id'] . "' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            //Время создания записи пациента
                            $zapis_create_time = strtotime($arr['day'].'.'.$arr['month'].'.'.$arr['year']);
                        }
                    } else {
                        $zapis_create_time = 0;
                    }

                    if ($zapis_create_time != 0) {
                        if (strtotime($_POST['new_time'].'') >= $zapis_create_time){
                            if (strtotime($_POST['new_time'].'') >= strtotime(date("m/d/y 00:00:00", strtotime($invoice_j[$_POST['invoice_id']]['create_time'])))){
                                if (strtotime($_POST['new_time'].'') <= time()){

                                    //Текущее время
                                    $time = date('Y-m-d', time());

                                    $new_time = date('Y-m-d H:i:s', strtotime($_POST['new_time'].' '.$time_hm_old));

                                    $query = "UPDATE `journal_invoice` SET `closed_time`='{$new_time}', `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}' WHERE `id`='{$_POST['invoice_id']}'";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                    //Ок
                                    $data = '<div class="query_ok" style="padding-bottom: 10px;"><h3>Дата закрытия работы по наряду изменена.</h3></div>';
                                    echo json_encode(array('result' => 'success', 'data' => $data));

                                }else {

                                    $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>Закрывать работу будущим временем не получится.</h3></div>';
                                    echo json_encode(array('result' => 'error', 'data' => $data));
                                }
                            }else{

                                $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>Работа не может быть закрыта раньше, чем был выписан наряд.</h3></div>';
                                echo json_encode(array('result' => 'error', 'data' => $data));
                            }
                        }else{

                            $data = '<div class="query_neok" style="padding-bottom: 10px;"><h3>Работа не может быть закрыта раньше, чем пациент был записан.</h3></div>';
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