<?php 

//fl_createDailyReport_add_f.php
//Функция для добавления ежежневного отчёта

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['date']) && isset($_POST['filial_id'])){
				include_once 'DBWork.php';

                $rez = array();

                $msql_cnnct = ConnectToDB ();

                $data_temp_arr = explode(".", $_POST['date']);

                $d = $data_temp_arr[0];
                $m = $data_temp_arr[1];
                $y = $data_temp_arr[2];

                $query = "SELECT * FROM `fl_journal_daily_report` WHERE `filial_id`='{$_POST['filial_id']}' AND `year`='$y' AND `month`='$m' AND `day`='$d'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                }

                CloseDB ($msql_cnnct);

                if (!empty($rez)){

                    echo json_encode(array('result' => 'success', 'data' => $rez, 'count' => count($rez), 'query' => $query));
                }else{

                    echo json_encode(array('result' => 'success', 'data' => '', 'count' => 0, 'query' => $query));
                }


			}else{
                echo json_encode(array('result' => 'success', 'data' => '<div class="query_neok">Что-то пошло не так</div>', 'count' => 0, 'query' => $query));
			}
		}
	}
?>