<?php

//task_stomat_inspection.php
//Описание осмотра стоматолога

	require_once 'header.php';
	
	if ($enter_ok){
		//var_dump($permissions);
		if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'tooth_status.php';
				
				$task = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');
				//var_dump($task);
				
				$closed = FALSE;
				$dop = array();
				
				if ($task !=0){
					echo '
						<script src="js/init.js" type="text/javascript"></script>
						<script>
							$(".h").css({
								display: "none"
							});
						</script>
						<div id="status">
					';
					//Дополнительно
		
					$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$task[0]['id']}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($dop, $arr);
						}
						
					}

					echo '			
							</header>';

					echo '
							<div id="data">';
							

					echo '
								<form>

									<div class="cellsBlock2">
										<div class="cellLeft">
											Пациент:<br /><i><b>'.WriteSearchUser('spr_clients', $task[0]['client'], 'user').'</b></i>
										</div>
										<div class="cellRight">
											Посещение #'.$task[0]['id'].'
										</div>
										
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">';
										
				
					
					//ЗО и тд	
					$dop = array();							
					$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$task[0]['id']}'";
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($dop, $arr);
						}
						
					}
											
											
					include_once 't_surface_name.php';
					include_once 't_surface_status.php';
					
					
					$arr = array();
					$decription = $task[0];
					
					//var_dump($decription);
					
					unset($decription['id']);
					unset($decription['office']);
					unset($decription['client']);
					unset($decription['create_time']);
					unset($decription['create_person']);
					unset($decription['last_edit_time']);
					unset($decription['last_edit_person']);
					unset($decription['worker']);
					
					unset($decription['comment']);

					unset($dop[0]['id']);
					
					$t_f_data = array();
					
					//собрали массив с зубами и статусами по поверхностям
					foreach ($decription as $key => $value){
						$surfaces_temp = explode(',', $value);
						//var_dump($surfaces_temp);
						foreach ($surfaces_temp as $key1 => $value1){
							$t_f_data[$key][$surfaces[$key1]] = $value1;
						}
					}
					//var_dump ($t_f_data);
					if (!empty($dop[0])){
						//var_dump($dop[0]);
						foreach($dop[0] as $key => $value){
							if ($value != '0'){
								$dop_arr = json_decode($value, true);
								//var_dump($dop_arr);
								foreach ($dop_arr as $n_key => $n_value){
									if ($n_key == 'zo'){
										$t_f_data[$key]['zo'] = $n_value;
										//$t_f_data_draw[$key]['zo'] = $n_value;
									}
								}
							}
						}
					}
					
					//var_dump ($t_f_data);		


					//рисуем зубную формулу						
					include_once 'teeth_map_svg.php';
					DrawTeethMap($t_f_data, 0, $tooth_status, $tooth_alien_status, $surfaces, '');

									
									

					echo '
								</form>';	
					

					
					echo '
							</div>
						</div>';
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>