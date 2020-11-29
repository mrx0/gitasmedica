<?php 

//edit_task_stomat_f.php
//Функция для редактирования посещений стоматологов

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		//var_dump ($_SESSION['journal_tooth_status_temp']);
		if ($_POST){

            if (isset($_POST['id'])){

                $msql_cnnct = ConnectToDB ();
				
				//$create_time = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year'].' '.$_POST['sel_hours'].':'.$_POST['sel_minutes'].':'.$_POST['sel_seconds']);

				$t_f_data_temp = $_SESSION['journal_tooth_status_temp'][$_POST['client_id']];
						
				//$stat_time = time();

				$n_zuba = '';
				$stat_zuba = '';
				$for_query = '';
				$temp_n_zuba_stat_zuba = '';
				
				//для ЗО и остального
				$doppol_arr = array();
				
				foreach($t_f_data_temp as $key => $value){
					$n_zuba = "`{$key}` ";
					if (isset($value['zo'])){
						$doppol_arr[$key]['zo'] = $value['zo'];
						unset($value['zo']);
					}
					if (isset($value['shinir'])){
						$doppol_arr[$key]['shinir'] = $value['shinir'];
						unset($value['shinir']);
					}
					if (isset($value['podvizh'])){
						$doppol_arr[$key]['podvizh'] = $value['podvizh'];
						unset($value['podvizh']);
					}
					if (isset($value['retein'])){
						$doppol_arr[$key]['retein'] = $value['retein'];
						unset($value['retein']);
					}
					if (isset($value['skomplect'])){
						$doppol_arr[$key]['skomplect'] = $value['skomplect'];
						unset($value['skomplect']);
					}
					
					$rrr = implode(',', $value);
					$stat_zuba = " '{$rrr}', ";
					
					$temp_n_zuba_stat_zuba = $n_zuba.'='.$stat_zuba;
					
					$for_query .= $temp_n_zuba_stat_zuba;
				}
				

						
				//$for_query = substr($for_query, 0, -2);
				//$stat_zuba = substr($stat_zuba, 0, -2);
				
				//echo ($for_query);				
				//var_dump ($stat_zuba);				
						
				//Добавим данные в базу
//				require 'config.php';
//				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
//				mysql_select_db($dbName) or die(mysql_error());
//				mysql_query("SET NAMES 'utf8'");


				$time = time();
					
				//$query = "UPDATE `journal_tooth_status_temp` SET `{$key}` = '".(implode(',', $value))."' WHERE `id`='{$stat_id}'";
				
				$query = "
						UPDATE `journal_tooth_status` SET 
						{$for_query}
						`last_edit_time` = '{$time}',
						`last_edit_person` = '{$_SESSION['id']}',
						`comment` = '{$_POST['comment']}'
						WHERE `id`='{$_POST['id']}'";
				//echo $query.'<br />';

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
				
				//$task = mysql_insert_id();
				
				$for_query_update = '';
				$for_query_insert = '';
						
				if (!empty($doppol_arr)){
					$n_zuba_i = '';
					$stat_zuba_i = '';
					$n_zuba = '';
					$stat_zuba = '';
					foreach($doppol_arr as $key => $value){
						$n_zuba_i .= "`{$key}`, ";
						$rrr = json_encode($value, true);
						$stat_zuba_i .= "'{$rrr}', ";
						
						$n_zuba = "`{$key}`";
						$rrr = $n_zuba."='".json_encode($value, true)."'";
						$for_query_update .= $rrr.',';
					}
					//echo $for_query_update.'<br />';
					
					$for_query_update = substr($for_query_update, 0, -1);
					$n_zuba_i = substr($n_zuba_i, 0, -2);
					$stat_zuba_i = substr($stat_zuba_i, 0, -2);

					//Дополнительные отметки на зубах (зо, ... и т.д.)
					$query = "DELETE FROM `journal_tooth_status_temp` WHERE `id` = '{$_POST['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
					
					$query = "INSERT INTO `journal_tooth_status_temp` (
						`id`, {$n_zuba_i}) 
						VALUES (
							'{$_POST['id']}', {$stat_zuba_i})";
					//echo $query;

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
					//var_dump($stat_zuba);
				}
				
				//Первичка
//				if ($_POST['pervich'] == 1){
//					$pervich_status = 1;
//				}else{
//					$pervich_status = 0;
//				}
//				//Ночной
//				if ($_POST['noch'] == 1){
//					$noch_status = 1;
//				}else{
//					$noch_status = 0;
//				}
//				//Страховой
//				if ($_POST['insured'] == 1){
//					$insured_status = 1;
//				}else{
//					$insured_status = 0;
//				}
				
//				$query = "
//					INSERT INTO `journal_tooth_ex` (
//						`id`, `pervich` )
//					VALUES (
//						'{$_POST['id']}', '{$pervich_status}') ";

//				$query = "INSERT INTO `journal_tooth_ex` (
//					`id`, `pervich` )
//					VALUES (
//						'{$_POST['id']}', '{$pervich_status}')
//					ON DUPLICATE KEY UPDATE
//					`pervich` = '{$pervich_status}',
//					`noch` = '{$noch_status}',
//					`insured` = '{$insured_status}'
//					";

                $query = "
						UPDATE `journal_tooth_ex` SET 
						`complaints` = '{$_POST['complaints']}', 
						`objectively` = '{$_POST['objectively']}', 
						`diagnosis` = '{$_POST['diagnosis']}', 
						`therapy` = '{$_POST['therapy']}', 
						`recommended` = '{$_POST['recommended']}'
						WHERE `id`='{$_POST['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //unset($_SESSION['journal_tooth_status_temp'][$_POST['client_id']]);




				if ($_POST['notes'] == 1){
					if ($_POST['add_notes_type'] != 0){
						if (($_POST['add_notes_months'] != 0) || ($_POST['add_notes_days'] != 0)){

							$date = date_create(date('Y-m-d', time()));
							$dead_line_temp = date_add($date, date_interval_create_from_date_string($_POST['add_notes_months'].' months'));
							$dead_line = date_timestamp_get(date_add($dead_line_temp, date_interval_create_from_date_string($_POST['add_notes_days'].' days'))) + 60*60*8;
							
							//echo date('d.m.Y H:i', $dead_line);
							
									
							//Добавим данные в базу
							$time = time();
							$query = "
									INSERT INTO `notes` (
										`type`, `description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `dead_line`, `closed`) 
									VALUES (
										'5', '{$_POST['add_notes_type']}', 'journal_tooth_status', '{$_POST['client']}', '{$_POST['id']}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$dead_line}, 0) ";
							//echo $query.'<br />';

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

						}else{
							echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Вы не назначили срок напоминания</span>';
						}
					}else{
						echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Не выбран тип напоминания</span>';
					}
				}
				

                if ($_POST['remove'] == 1){
                    $removeAct = json_decode($_POST['removeAct'], true);
                    $removeWork = json_decode($_POST['removeWork'], true);
                    foreach($removeAct as $ind => $val){
                        if ($ind != 0){
                            if ($val != ''){
                                if ($removeWork[$ind] != ''){
                                    //Ищем к кому направляем
                                    $RemWorkers = SelDataFromDB ('spr_workers', $removeWork[$ind], 'full_name');
                                    //var_dump($clients);
                                    if ($RemWorkers != 0){
                                        $RemWorker = $RemWorkers[0]["id"];


                                        //Добавим данные в базу
                                        //require 'config.php';
                                        //mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                                        //mysql_select_db($dbName) or die(mysql_error());
                                        //mysql_query("SET NAMES 'utf8'");
                                        $time = time();
                                        $query = "
                                                INSERT INTO `removes` (
                                                    `type`, `description`, `dtable`, `client`, `task`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `whom`, `closed`) 
                                                VALUES (
                                                   '5', '{$val}', 'journal_tooth_status', '{$_POST['client']}', '{$_POST['id']}', '{$time}', '{$_SESSION['id']}', '{$time}', '{$_SESSION['id']}', {$RemWorker}, 0) ";
                                        //echo $query.'<br />';

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                        //удаление темповой записи
                                        //mysql_query("DELETE FROM `journal_tooth_status_temp` WHERE `id` = '$stat_id'");

                                        //mysql_close();

                                    }else{
                                        echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Не нашли в базе врача, к кому направляете.</span>';
                                    }
                                }else{
                                    echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Пустое значение врача, к кому направляете.</span>';
                                }
                            }else{
                                echo '<br><br><span style="color: red;"><i class="fa fa-warning" aria-hidden="true"></i> Пустое значение причины направления.</span>';
                            }
                        }
                    }
                }
                //echo($query2);

				echo '
                    <br><br>
					Посещение отредактировано.
					<br><br>
					<a href="task_stomat_inspection.php?id='.$_POST['id'].'" class="b">В посещение</a>
					';
							
			}else{
				echo '
					Что-то пошло не так<br /><br />
					<a href="task_stomat_inspection.php?id='.$_POST['id'].'" class="b">В посещение</a>';
			}
		}
	}
	
?>