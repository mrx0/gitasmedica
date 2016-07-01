<?php

//teeth_map_svg_edit_status_all.php
//

	session_start();
	
	if($_GET){
		
		//var_dump($_GET);
		//var_dump($_SESSION);
		
		include_once 'tooth_status.php';
		include_once 'root_status.php';
		include_once 'surface_status.php';
		
		$arr = array();
		
		foreach ($_GET as $key => $value){
			if (mb_strstr($key, 't_stat_value') != FALSE){
				//array_push ($arr, $value);
				$key = str_replace('t_stat_value', '', $key);
				//echo $key.'<br />';
				$arr[$key] = $value;
			}				
		}
		//var_dump($arr);
		
		if (!empty($arr)){
			$t_f_data = $_SESSION['journal_tooth_status_temp'];
			foreach ($arr as $key => $value){
				if (array_key_exists($_GET['status_all'], $tooth_status) || ($value == '0')){
					//Если не ЗО
					if (($_GET['status_all'] != '22') && ($_GET['status_all'] != '23') && ($_GET['status_all'] != '24')){
						$t_f_data[$key]['status'] = $_GET['status_all'];
						//Чистим радиксы (пока только их)  место для взаимоисключающих статусов
						if ($t_f_data[$key]['root1'] == '34')
							$t_f_data[$key]['root1'] = 0;
						if ($t_f_data[$key]['root2'] == '34')
							$t_f_data[$key]['root2'] = 0;
						if ($t_f_data[$key]['root3'] == '34')
							$t_f_data[$key]['root3'] = 0;
							
					}else{
						
						if ($_GET['status_all'] == '22'){
							if (isset($t_f_data[$key]['zo'])){
								if ($t_f_data[$key]['zo'] == '1'){
									$t_f_data[$key]['zo'] = '0';
								}elseif ($t_f_data[$key]['zo'] == '0'){
									unset($t_f_data[$key]['zo']);
								}
							}else{
								$t_f_data[$key]['zo'] = '1';
							}
						}
						
						if ($_GET['status_all'] == '23'){
							if (isset($t_f_data[$key]['shinir'])){
								unset($t_f_data[$key]['shinir']);
							}else{
								$t_f_data[$key]['shinir'] = '1';
							}
						}
						
						if ($_GET['status_all'] == '24'){
							if (isset($t_f_data[$key]['podvizh'])){
								unset($t_f_data[$key]['podvizh']);
							}else{
								$t_f_data[$key]['podvizh'] = '1';
							}
						}
						
					}
				}elseif (array_key_exists($_GET['status_all'], $surface_status)){
					$t_f_data[$key]['surface1'] = $_GET['status_all'];
					$t_f_data[$key]['surface2'] = $_GET['status_all'];
					$t_f_data[$key]['surface3'] = $_GET['status_all'];
					$t_f_data[$key]['surface4'] = $_GET['status_all'];
					$t_f_data[$key]['top1'] = $_GET['status_all'];
					$t_f_data[$key]['top2'] = $_GET['status_all'];
					$t_f_data[$key]['top12'] = $_GET['status_all'];
				}
				
				//сбросить статус зуба до полностью здорового
				if (isset($_GET['status_all']) && ($_GET['status_all'] == 'reset')){
					foreach($t_f_data[$key] as $key_2 => $value){
						$t_f_data[$key][$key_2] = '0';
						//echo $key_2.':'.$value.'<br />';
					}
					$t_f_data[$key]['alien'] = '0';
					$t_f_data[$key]['pin'] = '0';
					unset($t_f_data[$key]['zo']);
					unset($t_f_data[$key]['shinir']);
					unset($t_f_data[$key]['podvizh']);
				}
				
				//имплантант (может быть с чем-то)
				if (isset($_GET['implant']) && ($_GET['implant'] == '1') && ($value != '0')){
					$t_f_data[$key]['pin'] = '1';
				}
				//один только имплант
				if ($value == '3'){
					$t_f_data[$key]['pin'] = '1';
					$t_f_data[$key]['status'] = '3';
				}
				//Чужой (исправлено 20160528)
				//было if (isset($_GET['alien']) && ($_GET['alien'] == '1') && ($value != '0')){ 
				if (isset($_GET['status_all']) && ($_GET['status_all'] == 'alien') && ($value != '0')){
					$t_f_data[$key]['alien'] = '1';
				}else{
					$t_f_data[$key]['alien'] = '0';
				}
			}
			
			//var_dump($t_f_data);
			$_SESSION['journal_tooth_status_temp'] = $t_f_data;
		}

		//var_dump($_SESSION);
	}

?>