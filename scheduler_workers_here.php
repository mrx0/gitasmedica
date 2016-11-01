<?php 

//scheduler_workers_here.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			//var_dump($_POST);
			
			include_once 'functions.php';
			
			//получаем шаблон графика из базы
			$query = "SELECT `worker` FROM `sheduler_template` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `kab`='{$_POST['kabN']}' AND `type`='{$_POST['type']}'";
			
			$shedTemplate = 0;
			
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
				$shedTemplate = $rez;
			}else{
				$shedTemplate = 0;
			}
			
			$rez = '';
			
			//var_dump ($shedTemplate);
						
			if ($shedTemplate != 0){
				//var_dump ($shedTemplate);
				
				foreach ($shedTemplate as $value){
					//var_dump ($value);
					
					$rez .= WriteSearchUser('spr_workers', $value['worker'], 'user', false).' <a href="#" class="b" onclick="DeleteWorkersSmena('.$value['worker'].', '.$_POST['filial'].', '.$_POST['dayW'].', '.$_POST['smenaN'].', '.$_POST['kabN'].', '.$_POST['type'].')">Удалить</a><br>';

				}
				echo $rez;
			}else{
				echo '<span style="color: red;">никого</span>';
			}
			
		}
	}
?>