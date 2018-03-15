<?php 

//get_prev_zapis.php
//Извлекаем предыдущую запись

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		$data = array();
		$req = 0;
		$next_time_start = 0;
		
		if ($_POST){
			if ($_POST['datatable'] == 'scheduler_stom'){
				$datatable = 'zapis_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$datatable = 'zapis_cosm';
			}else{
				$datatable = 'zapis_stom';
			}
			
			$day = $_POST['day'];
			$month = $_POST['month'];
			$year = $_POST['year'];
			$kab = $_POST['kab'];
			$start_time = $_POST['start_time'];
			$filial = $_POST['filial'];

            $msql_cnnct = ConnectToDB ();

			$query = "SELECT * FROM `zapis` WHERE `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `kab` = '$kab' AND `office` = '$filial' AND `start_time` < '$start_time' ORDER BY `start_time` ASC LIMIT 1";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($data, $arr);
				}
				$req = 1;
			}else{
				$req = 0;
			}

            CloseDB ($msql_cnnct);

			if ($req != 0){
				$next_time_start = $data[0]['start_time'];
			}
			echo '{"req":"'.$req.'", "next_time_start":"'.$next_time_start.'"}';
		}
	}
?>