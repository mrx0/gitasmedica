<?php


		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `journal_cosmet`";
		$res = mysql_query($query) or die($q);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			
			//var_dump ($rez);
			echo  count($rez);
			
			foreach ($rez as $value){
				$description_temp_arr = json_decode($value['description'], true);
				
				/*Лайфхак такой*/
				if (!isset($description_temp_arr['20'])) $description_temp_arr['20'] = 0;
				if (!isset($description_temp_arr['21'])) $description_temp_arr['21'] = 0;
				if (!isset($description_temp_arr['22'])) $description_temp_arr['22'] = 0;
				if (!isset($description_temp_arr['200'])) $description_temp_arr['200'] = 0;
				if (!isset($description_temp_arr['201'])) $description_temp_arr['201'] = 0;
				
				//var_dump ($description_temp_arr);
				
				$query = "INSERT INTO `journal_cosmet1` (`id`,
					`office`, `client`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`, 
					`c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `c11`, `c12`, `c13`, `c14`, `c15`, `c16`, `c17`, `c18`, `c19`, `c20`, `c21`, `c22`, `c200`, `c201`) 
					VALUES (
					'{$value['id']}', '{$value['office']}', '{$value['client']}', '{$value['create_time']}', '{$value['create_person']}', '{$value['last_edit_time']}', '{$value['last_edit_person']}', '{$value['worker']}', '{$value['comment']}', 
					'{$description_temp_arr[1]}', 
					'{$description_temp_arr[2]}', 
					'{$description_temp_arr[3]}', 
					'{$description_temp_arr[4]}', 
					'{$description_temp_arr[5]}', 
					'{$description_temp_arr[6]}', 
					'{$description_temp_arr[7]}', 
					'{$description_temp_arr[8]}', 
					'{$description_temp_arr[9]}', 
					'{$description_temp_arr[10]}', 
					'{$description_temp_arr[11]}', 
					'{$description_temp_arr[12]}', 
					'{$description_temp_arr[13]}', 
					'{$description_temp_arr[14]}', 
					'{$description_temp_arr[15]}', 
					'{$description_temp_arr[16]}', 
					'{$description_temp_arr[17]}', 
					'{$description_temp_arr[18]}', 
					'{$description_temp_arr[19]}', 
					'{$description_temp_arr[20]}', 
					'{$description_temp_arr[21]}', 
					'{$description_temp_arr[22]}', 
					'{$description_temp_arr[200]}', 
					'{$description_temp_arr[201]}'				
					) ";
				
				//echo $query;
				
				mysql_query($query) or die(mysql_error());
		
				
			}
			
			
			
		}else{
			echo 'nothing';
		}
		mysql_close();
		
		
		

?>