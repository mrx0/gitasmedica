<?php 

//fl_check_calculate_in_tabel_f.php
//Проверка есть ли этот расчёт в каком-то табеле

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['calculate_id'])){
                echo json_encode(array('result' => 'error', 'data' => -1));
			}else{
                include_once 'ffun.php';

                $arr['total'] = 0;

                $msql_cnnct = ConnectToDB2 ();

                $query = "SELECT `tabel_id` AS total FROM `fl_journal_tabels_ex` WHERE `calculate_id` = '{$_POST['calculate_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                $arr = mysqli_fetch_assoc($res);

                CloseDB ($msql_cnnct);

                if ($number <= 0){
                    echo json_encode(array('result' => 'success', 'data' => $number));
                }else{
                    echo json_encode(array('result' => 'error', 'data' => $arr['total']));
                }

			}
		}
	}
?>