<?php 

//edit_schedule_f.php
//Функция для редактирования расписания

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
			
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$query = "SELECT * FROM `$datatable` WHERE `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `kab` = '$kab' AND `office` = '$filial' AND `start_time` > '$start_time' ORDER BY `start_time` ASC LIMIT 1";
			$res = mysql_query($query) or die($query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($data, $arr);
				}
				$req = 1;
			}else{
				$req = 0;
			}
			mysql_close();
			if ($req != 0){
				$next_time_start = $data[0]['start_time'];
			}
			echo '{"req":"'.$req.'", "next_time_start":"'.$next_time_start.'"}';
			//var_dump($data);
			/*
			if ($_POST['worker'] !=0){
				if ($_POST['patient'] != ''){
					if ($_POST['contacts'] != ''){
						if ($_POST['contacts'] != ''){
							//запись в базу
							WriteToDB_EditZapis ($datatable, $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab'], $_POST['worker'], $_POST['author'], $_POST['patient'], $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt']);
							
							echo '
								Изменения в расписание внесены<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
							//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
						}else{
							echo '
								Не указано описание<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
						}
					}else{
						echo '
							Не указали контакты<br /><br />
							<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
					}
				}else{
					echo '
						Не указали пациента<br /><br />
						<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
				}
			}else{
				echo '
					Не выбрали врача<br /><br />
					<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
			}*/
		}
	}
?>