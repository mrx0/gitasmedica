<?php

		$temp_arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `spr_clients`";
		$res = mysql_query($query) or die($q);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			
			
		
			foreach ($rez as $value){
				$temp_arr = explode(' ', $value['full_name']);
				var_dump ($temp_arr);
				$f = $temp_arr[0];
				$i = $temp_arr[1];
				if (isset($temp_arr[3]))
					$o = $temp_arr[2].' '.$temp_arr[3];
				else
					$o = $temp_arr[2];
				
				$query = "UPDATE `spr_clients` SET `f` = '$f', `i` = '$i', `o` = '$o' WHERE `id`='{$value['id']}'";
				mysql_query($query) or die(mysql_error());
				
				$query = "UPDATE `spr_clients` SET `birthday` = '-1577934000' WHERE `birthday`='0'";
				mysql_query($query) or die(mysql_error());
				
				echo $f.'<br />'.$i.'<br />'.$o.'<hr>';
			}
			
			
			
		}else{
			echo 'nothing';
		}
		mysql_close();
		
		
		

?>