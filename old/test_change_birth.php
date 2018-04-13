<?php

	include_once 'fl_DBWork.php';
	
	$msql_cnnct = ConnectToDB();
	
	//$query = "SELECT * FROM `spr_clients` WHERE `birthday2`='0000-00-00'";
	$query = "SELECT * FROM `spr_clients`";
	
	$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
	
	$number = mysqli_num_rows($res);
	
	if ($number != 0){
		while ($arr = mysqli_fetch_assoc($res)){
			$rez[$arr['id']] = $arr;
		}
	}
	
	if (!empty($rez)){
		//var_dump($rez);
		
		foreach ($rez as $client){
			
			if (($client['birthday'] != -1577934000) && ($client['birthday'] != 0)){

				$newDate = date('Y-m-d', $client['birthday']);

                $query = "UPDATE `spr_clients` SET `birthday2`='".$newDate."' WHERE `id`='".$client['id']."' AND `birthday2`='0000-00-00' LIMIT 1";

                mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			}else{
				$newDate = '0000-00-00';
			}
		
			/*$query = "UPDATE `spr_clients` SET `birthday2`='".$newDate."' WHERE `id`='".$client['id']."' AND `birthday2`='0000-00-00' LIMIT 1";
			
			mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);*/
			
			/*if (date('Y-m-d', $client['birthday']) != $client['birthday2']){
				echo $client['id'].' => '.$client['birthday'].' = '.date('Y-m-d', $client['birthday']).' => '.$client['birthday2'].'<br>';
			}*/

			echo 'Ok';

		}

	}
	
?>