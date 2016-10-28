<?php 

//scheduler_worker_edit_fakt_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if ($_POST['worker'] != 0){
				$workerBusy = FALSE;
				
				$request = '';
				
				$workers = array();
				$arr = array();
				
				//надо посмотреть, а не работает ли этот врач еще где-то в эту смену в этот день
				$query = "SELECT `id`, `filial`, `day`, `month`, `year`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$_POST['worker']}' AND `type` = '{$_POST['type']}' AND `day` =  '{$_POST['day']}' AND `month` =  '{$_POST['month']}' AND `year` =  '{$_POST['year']}' AND `smena` =  '{$_POST['smena']}'";

				require 'config.php';
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$res = mysql_query($query) or die(mysql_error().' -> '.$query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($workers, $arr);
					}
					$workerBusy = TRUE;
				}else{
					$workers = 0;
				}
				//var_dump ($workers);
				//var_dump ($query);
				
				//Если есть уже в графике, то удаляем оттуда
				if ($workers != 0){
					foreach ($workers as $value){
						$query = "DELETE FROM `scheduler` WHERE `id`='{$value['id']}'";
						
						mysql_query($query) or die($query.' -> '.mysql_error());
						
					}
					//логирование
					AddLog ('0', $_SESSION['id'], '', '[ПЕРЕНОС ИЗ ДРУГОЙ ФАКТИЧЕСКОЙ СМЕНЫ] ['.$_POST['worker'].'] удален из смены  Графика ['.$_POST['smena'].']. Филиал ['.$_POST['filial'].']. Кабинет ['.$_POST['kab'].']. День ['.$_POST['day'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']');	
				}
			
				//Надо посмотреть, есть ли кто уже именно тут, в этом каб, смене, дне, филиале и удалить его потом
				$query = "SELECT `id` FROM `scheduler` WHERE `type` = '{$_POST['type']}' AND `day` =  '{$_POST['day']}' AND `month` =  '{$_POST['month']}' AND `year` =  '{$_POST['year']}' AND `smena` =  '{$_POST['smena']}' AND `filial` =  '{$_POST['filial']}' AND `kab` =  '{$_POST['kab']}'";				
				$workers = array();
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$res = mysql_query($query) or die(mysql_error().' -> '.$query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($workers, $arr);
					}
				}else{
					$workers = 0;
				}
				//var_dump ($workers);
				
				//Если есть уже в графике, то удаляем оттуда
				if ($workers != 0){
					foreach ($workers as $value){
						$query = "DELETE FROM `scheduler` WHERE `id`='{$value['id']}'";
						
						mysql_query($query) or die($query.' -> '.mysql_error());
					}
						
					//логирование
					AddLog ('0', $_SESSION['id'], '', '[ЗАМЕНА НА ДРУГОГО В ФАКТИЧЕСКОМ ГРАФИКЕ] ['.$_POST['worker'].'] удален из смены Графика ['.$_POST['smena'].']. Филиал ['.$_POST['filial'].']. Кабинет ['.$_POST['kab'].']. День ['.$_POST['day'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']');	
				}

				//Добавляем новую запись
				$query = "INSERT INTO `scheduler` (`filial`, `day`, `month`, `year`, `smena`, `kab`, `worker`, `type`) VALUES ('{$_POST['filial']}', '{$_POST['day']}', '{$_POST['month']}', '{$_POST['year']}', '{$_POST['smena']}', '{$_POST['kab']}', '{$_POST['worker']}', '{$_POST['type']}')";
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				mysql_query($query) or die($query.' -> '.mysql_error());
				
				mysql_close();
				
				//логирование
				AddLog ('0', $_SESSION['id'], '', 'Добавили сотрудника ['.$_POST['worker'].'] в смену Фактического графика ['.$_POST['smena'].']. Филиал ['.$_POST['filial'].']. Кабинет ['.$_POST['kab'].']. День ['.$_POST['day'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']');	

				if ($workerBusy){
					$request = 'Переместили сотрудника в смену';
				}else{
					$request = 'Поставили сотрудника в смену';
				}
				
				echo '{"req": "ok", "text":"'.$request.'"}';
			}
		}
	}
?>