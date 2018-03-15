<?php

			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$filials = SelDataFromDB('spr_filials', '', '');
			if ($filials !=0){
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
						

				for ($i=0;$i<count($filials);$i++){
					$arr = array();
					$rez = array();
					//Собираем 
					$query = "SELECT * FROM `scheduler_stom` WHERE `office` = '{$filials[$i]['id']}' AND `year`='2016' AND `month`='8' AND `day`<'8'";

					$res = mysql_query($query) or die($q);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
					}else{
						$rez = 0;
					}
					//var_dump($rez);
					
					if ($rez != 0){
						foreach ($rez as $value){
							var_dump($value);
							
							if ($value['smena'] != 9){
								$query = "INSERT INTO `sheduler_template` (
									`filial`, `day`, `smena`, `kab`, `worker`, `type`) 
									VALUES (
									'{$value['office']}', '{$value['day']}', '{$value['smena']}',
									'{$value['kab']}', '{$value['worker']}', '5')";	
								
								//mysql_query($query) or die(mysql_error());
							}else{
								$query = "INSERT INTO `sheduler_template` (
									`filial`, `day`, `smena`, `kab`, `worker`, `type`) 
									VALUES (
									'{$value['office']}', '{$value['day']}', '1',
									'{$value['kab']}', '{$value['worker']}', '5')";	
								
								//mysql_query($query) or die(mysql_error());
								
								$query = "INSERT INTO `sheduler_template` (
									`filial`, `day`, `smena`, `kab`, `worker`, `type`) 
									VALUES (
									'{$value['office']}', '{$value['day']}', '2',
									'{$value['kab']}', '{$value['worker']}', '5')";	
								
								//mysql_query($query) or die(mysql_error());
							}
						}
					}
				}
				
				mysql_close();
			}
?>