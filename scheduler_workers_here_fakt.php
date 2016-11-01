<?php 

//scheduler_workers_here_fakt.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			//var_dump($_POST);
			
			include_once 'functions.php';
			
			//получаем работников из базы
			$query = "SELECT `worker` FROM `scheduler` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}' AND `smena`='{$_POST['smena']}' AND `kab`='{$_POST['kab']}' AND `type`='{$_POST['type']}'";
			
			$shedWorkers = 0;
			
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			
			$arr = array();
			$rez = array();
				
			$res = mysql_query($query) or die(mysql_error().' -> '.$query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($rez, $arr);
				}
				$shedWorkers = $rez;
			}else{
				$shedWorkers = 0;
			}
			
			$rez = '';
			
			//var_dump ($shedWorkers);
						
			if ($shedWorkers != 0){
				//var_dump ($shedWorkers);
				
				foreach ($shedWorkers as $value){
					//var_dump ($value);
					
					$rez .= WriteSearchUser('spr_workers', $value['worker'], 'user', false).' <a href="#" class="b" onclick="DeleteWorkersSmenaFakt('.$value['worker'].', '.$_POST['filial'].', '.$_POST['day'].', '.$_POST['month'].', '.$_POST['year'].', '.$_POST['smena'].', '.$_POST['kab'].', '.$_POST['type'].')">Удалить</a><br>';

				}
				echo $rez;
			}else{
				echo '<span style="color: red;">никого</span>';
			}
		}
	}
?>