<?php 

//ajax_show_result_stat_cosm_ex2_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		var_dump ($_POST);
		if ($_POST){
			$workerExist = false;
			$queryDopExist = false;
			$queryDopExExist = false;
			$queryDopClientExist = false;
			$query = '';
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';
			
			$queryConditionExist = true;
			$queryCondition = '';
			
			if ($_POST['worker'] != ''){
				include_once 'DBWork.php';
				$workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
				
				if ($workerSearch == 0){
					$workerExist = false;
				}else{
					$workerExist = true;
					$worker = $workerSearch[0]['id'];
				}
			}else{
				$workerExist = true;
				$worker = 0;
			}	
			
			if ($workerExist){
				$query .= "SELECT * FROM `journal_cosmet1`";
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				//$time = time();
				
				//Дата/время
				if ($_POST['all_time'] != 1){
					$queryDop .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
					$queryDopExist = true;
				}
				
				//!!! Тут возраст, пока не готово
				
				//Сотрудник
				if ($worker != 0){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`worker` = '".$worker."'";
					$queryDopExist = true;
				}
				
				//Филиал
				if ($_POST['filial'] != 99){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`office` = '".$_POST['filial']."'";
					$queryDopExist = true;
				}
				
				//Пол
				if ($_POST['sex'] != 0){
					if ($queryDopClientExist){
						$queryDopClient .= ' AND';
					}
					$queryDopClient .= "`sex` = '".$_POST['sex']."'";
					$queryDopClientExist = true;
					
					//Без пола
					if ($_POST['wo_sex'] == 1){
						if ($queryDopClientExist){
							$queryDopClient .= ' OR';
						}
						$queryDopClient .= "`sex` = '0'";
						$queryDopClient = "(".$queryDopClient.")";
						$queryDopClientExist = true;
					}
				}
				

				
				//Первичка
				/*if ($_POST['pervich'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['pervich'] == 1){
						$queryDopEx .= "`pervich` = '1'";
					}else{
						$queryDopEx .= "`pervich` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Страховые
				if ($_POST['insured'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['insured'] == 1){
						$queryDopEx .= "`insured` = '1'";
					}else{
						$queryDopEx .= "`insured` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Ночные
				if ($_POST['noch'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['noch'] == 1){
						$queryDopEx .= "`noch` = '1'";
					}else{
						$queryDopEx .= "`noch` <> '1'";
					}
					$queryDopExExist = true;
				}*/
				
				//По процедурам
				if (isset($_POST['condition'])){
					for($i=0; $i<count($_POST['condition']); $i++){
						$queryCondition .= "`c".$_POST['condition'][$i]."`='1'";
						if ($i < count($_POST['condition']) - 1){
							$queryCondition .= ' AND ';
						}
					}
					$queryConditionExist = true;
				}
				
				if (isset($_POST['effect'])){
					
				}
				
				if ($queryDopExist){
					$query .= ' WHERE '.$queryDop;

					if ($queryConditionExist){
						//var_dump($queryCondition);

						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= $queryCondition;
						$queryDopExist = true;
					}
					
					if ($queryDopClientExist){
						$queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= "`client` IN (".$queryDopClient.")";
					}
					
					$query = $query." ORDER BY `create_time` DESC";
					//var_dump($query);					
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					$arr = array();
					$rez = array();
					
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
						$journal = $rez;
					}else{
						$journal = 0;
					}
					var_dump($journal);
					
					//Выводим результат
					if ($journal != 0){
						include_once 'functions.php';


					}else{
						echo '<span style="color: red;">Ничего не найдено</span>';
					}				
					
				}else{
					echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
				}
				
				//var_dump($query);
				//var_dump($queryDopEx);
				//var_dump($queryDopClient);
				
				mysql_close();
			}else{
				echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
			}
		}
	}
?>