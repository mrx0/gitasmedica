<?php

//Change_therapist.php
//

		$arr = array();
		$rez = array();
		$arr2 = array();
		$rez2 = array();
		require 'config.php';
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Невозможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		
		$query = "SELECT * FROM `spr_clients`";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
		}
		mysql_close();
		//var_dump ($rez);		
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Невозможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");		
		foreach ($rez as $value){
			if ($value['therapist'] != 0){
				
				$query = "SELECT `permissions` FROM `spr_workers` WHERE `id` = {$value['therapist']}";
				$res = mysql_query($query) or die(mysql_error());
				//var_dump ($res);
				$number = mysql_num_rows($res);
				if ($number != 0){
					$arr2 = mysql_fetch_assoc($res);
						//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
						//array_push($rez2, $arr2['permissions']);
						//var_dump ($arr2);
					if ($arr2['permissions'] == 6){
						$query = "UPDATE `spr_clients` SET `therapist`='0', `therapist2`='{$value['therapist']}'  WHERE `id`='{$value['id']}'";
						$res = mysql_query($query) or die(mysql_error());
						//echo $query.'<br />';
					}

				}

				
			}
		}
		mysql_close();
		
		//var_dump ($rez2);	
		
		echo 'Ok';


?>