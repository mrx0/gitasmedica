<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		if ($_GET){
			//var_dump($_GET);
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'tooth_status.php';
			
			$id_zf = FALSE;
			
			$t_f_data_db_max_id = 0;
			$t_f_data_db_min_id = 0;

			$temp_arr = array();
			
			$text_tooth_status = array(
				'up' => -9,
				'down' => 138,
				'left' => array (
					1 => 268,
					2 => 231,
					3 => 196,
					4 => 159,
					5 => 123,
					6 => 87,
					7 => 52,
					8 => 15,						
				),
				'right' => array (
					1 => 321,
					2 => 360,
					3 => 396,
					4 => 432,
					5 => 469,
					6 => 505,
					7 => 539,
					8 => 576,			
				),
			);
			
			
			if (count($_GET) > 1){
				$id_zf = TRUE;
				
	
				//$client = $_GET['client'];
				unset($_GET['ajax']);

				foreach($_GET as $key => $value){
					if ($value == '1'){
						$temp_arr[mb_substr($key, 2)] = $value;
					}
				}
			}
			
			$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
			
			
			//var_dump($client);
			if ($client != 0){
				echo '
					<script src="js/init.js" type="text/javascript"></script>
					<script src="js/init2.js" type="text/javascript"></script>
						<header>
							<h2>История [стоматология] пациент #'.$_GET['client'].'</h2>
						</header>';
						
				echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">
						<div class="cellRight">
							<span style="font-size: 70%;">Быстрый поиск пациента</span><br />
							<input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите первые три буквы для поиска" value="" class="who_fc"  autocomplete="off">
							<!--<ul id="search_result_fc" class="search_result_fc"></ul><br />-->
							<div id="search_result_fc2"></div>
						</div>
					</div>';

				echo '
						<div id="data">';

				echo '

								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">'.$client[0]['full_name'].'</div>
								</div>';
								
	

				echo '
								<div>';
				if (($stom['add_own'] == 1) || ($god_mode)){
					echo '	
									<a href="add_task_stomat.php?client='.$_GET['client'].'" class="b">Добавить осмотр</a>';
				}
				
				if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
					echo '	
									<a href="client.php?id='.$_GET['client'].'" class="b">Карточка пациента</a>';
				}
				
//!!!!!!!!!!!!!!!!!!!!!!!!!*************************************
				
			echo '<div id="status">	';
				
			//Выберем из базы последнюю запись
				$t_f_data_db = array();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				if ($id_zf){
					$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '".max(array_keys($temp_arr))."'";
				}else{
					$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['client']}' ORDER BY `create_time` DESC LIMIT 1";
				}
				$res = mysql_query($query) or die($q);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($t_f_data_db, $arr);
					}
				}else
					$t_f_data_db = 0;
				
				if ($t_f_data_db !=0){
					
					$t_f_data_db_max_id = $t_f_data_db[0]['id'];
					
					//echo '							<script src="js/init.js" type="text/javascript"></script>';
					//Выберем из базы первую запись
					$t_f_data_db_first = array();
					

					$time = time();
					if ($id_zf){
						$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '".min(array_keys($temp_arr))."'";
					}else{
						$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['client']}' ORDER BY `create_time` ASC LIMIT 1";
					}
					$res = mysql_query($query) or die($q);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($t_f_data_db_first, $arr);
						}
					}else
						$t_f_data_db_first = 0;
					
					
					//$t_f_data_db = SelDataFromDB('journal_tooth_status', $_GET['client'], 'id');								
					//var_dump ($t_f_data_db);
					//var_dump ($t_f_data_db_first);
					if ($t_f_data_db_first !=0){
						$t_f_data_db_min_id = $t_f_data_db_first[0]['id'];
						if ($t_f_data_db_first[0]['id'] != $t_f_data_db[0]['id']){
							$t_f_data_db[count($t_f_data_db)] = $t_f_data_db_first[0];
						}
					}
							
					for ($z = 0; $z < count ($t_f_data_db); $z++){
						$dop = array();
						
						//ЗО и тд
						$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$t_f_data_db[$z]['id']}'";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}
						
						
						echo '
							<div class="cellsBlock3">';
						echo '
								<div class="cellLeft">
									<a href="task_stomat_inspection.php?id='.$t_f_data_db[$z]['id'].'" class="ahref">'.date('d.m.y H:i', $t_f_data_db[$z]['create_time']).'</a>
								</div>
								<div class="cellRight">';
								
						include_once 'teeth_map_db.php';
						include_once 't_surface_name.php';
						include_once 't_surface_status.php';

						include_once 'root_status.php';
						include_once 'surface_status.php';
						include_once 't_context_menu.php';
									
						$t_f_data = array();
						
						if ($z == 0){
							$n = '';
						}else{
							$n = $z;
						}
						
						$sw = 0;
						$stat_id = $t_f_data_db[$z]['id'];
						
						unset($t_f_data_db[$z]['id']);
						unset($t_f_data_db[$z]['create_time']);
						unset($t_f_data_db[$z]['comment']);
						//echo "echo$sw";
						//var_dump ($surfaces);
						$t_f_data_temp_refresh = '';
						
						foreach ($t_f_data_db[$z] as $key => $value){
							//$t_f_data_temp_refresh .= $key.'+'.$value.':';
							
							
							//var_dump(json_decode($value, true));
							$surfaces_temp = explode(',', $value);
							//var_dump ($surfaces_temp);
							foreach ($surfaces_temp as $key1 => $value1){
								//$t_f_data[$key] = json_decode($value, true);
								///!!!Еба костыль
								if ($key1 < 13){
									$t_f_data[$key][$surfaces[$key1]] = $value1;
								}
							}
						}
						
						unset($t_f_data['id']);
						unset($t_f_data['office']);
						unset($t_f_data['client']);
						unset($t_f_data['create_time']);
						unset($t_f_data['create_person']);
						unset($t_f_data['last_edit_time']);
						unset($t_f_data['last_edit_person']);
						unset($t_f_data['worker']);
						unset($t_f_data['comment']);
							
						//var_dump ($dop[0]);
						
						if (!empty($dop[0])){
							//var_dump($dop[0]);
							unset($dop[0]['id']);
							//var_dump($dop[0]);
							foreach($dop[0] as $key => $value){
								//var_dump($value);
								if ($value != '0'){
									//var_dump($value);
									$dop_arr = json_decode($value, true);
									//var_dump($dop_arr);
									foreach ($dop_arr as $n_key => $n_value){
										if ($n_key == 'zo'){
											$t_f_data[$key]['zo'] = $n_value;
											//$t_f_data_draw[$key]['zo'] = $n_value;
										}
										if ($n_key == 'shinir'){
											$t_f_data[$key]['shinir'] = $n_value;
											//$t_f_data_draw[$key]['shinir'] = $n_value;
										}
										if ($n_key == 'podvizh'){
											$t_f_data[$key]['podvizh'] = $n_value;
											//$t_f_data_draw[$key]['podvizh'] = $n_value;
										}
									}
								}
							}
						}
					
						//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
						//$t_f_data_temp_refresh = json_encode($t_f_data_db[0], true);
						//var_dump($t_f_data);
						//echo $t_f_data_temp_refresh;
						
						
						echo '
									<div class="map'.$n.'" id="map'.$n.'">
										<div class="text_in_map" style="left: 15px">8</div>
										<div class="text_in_map" style="left: 52px">7</div>
										<div class="text_in_map" style="left: 87px">6</div>
										<div class="text_in_map" style="left: 123px">5</div>
										<div class="text_in_map" style="left: 159px">4</div>
										<div class="text_in_map" style="left: 196px">3</div>
										<div class="text_in_map" style="left: 231px">2</div>
										<div class="text_in_map" style="left: 268px">1</div>
										
										<div class="text_in_map" style="left: 321px">1</div>
										<div class="text_in_map" style="left: 360px">2</div>
										<div class="text_in_map" style="left: 396px">3</div>
										<div class="text_in_map" style="left: 432px">4</div>
										<div class="text_in_map" style="left: 469px">5</div>
										<div class="text_in_map" style="left: 505px">6</div>
										<div class="text_in_map" style="left: 539px">7</div>
										<div class="text_in_map" style="left: 576px">8</div>
										';

						

						//var_dump ($teeth_map_temp);	
						
						//!!!ТЕСТ ИНКЛУДА ОТРИСОВКИ ЗФ
						//require_once 'for32_teeth_map_svg.php';
						
						
						//$teeth_map_temp = SelDataFromDB('teeth_map', '', '');
						$teeth_map_temp = $teeth_map_db;
						foreach ($teeth_map_temp as $value){
							$teeth_map[mb_substr($value['tooth'], 0, 3)][mb_substr($value['tooth'], 3, strlen($value['tooth'])-3)]=$value['coord'];
						}
						//$teeth_map_d_temp = SelDataFromDB('teeth_map_d', '', '');
						$teeth_map_d_temp = $teeth_map_d_db;
						foreach ($teeth_map_d_temp as $value){
							$teeth_map_d[$value['tooth']]=$value['coord'];
						}
						//$teeth_map_pin_temp = SelDataFromDB('teeth_map_pin', '', '');
						$teeth_map_pin_temp = $teeth_map_pin_db;
						foreach ($teeth_map_pin_temp as $value){
							$teeth_map_pin[$value['tooth']]=$value['coord'];
						}
						
						for ($i=1; $i <= 4; $i++){
							for($j=1; $j <= 8; $j++){
								
								$DrawRoots = TRUE;				
								$menu = 't_menu';
								if (isset($sw)){
									if ($sw == '1'){
										$t_status = 'yes';
									}else{
										$t_status = 'no';
									}
								}else{
									$t_status = 'yes';
								}
								//$t_status = 'yes';
								$color = "#fff";
								$color_stroke = '#74675C';
								$stroke_width = 1;
								$n_zuba = 't'.$i.$j;
								//echo $n_zuba.'<br />';
								if ($t_f_data[$i.$j]['alien'] == '1'){
									$color_stroke = '#F7273F';
									$stroke_width = 3;
								}
								
								foreach($teeth_map[$n_zuba] as $surface => $coordinates){
									
									$color = "#fff";
									//!!!! попытка с молочным зубом
									if ($t_f_data[$i.$j]['status'] == '19'){
										$color_stroke = '#FF9900';
									}
									$DrawMenu = TRUE;
									
									if (isset($t_f_data[$i.$j][$surface])){
										$s_stat = $t_f_data[$i.$j][$surface];
									}
									
									//!!! надо как-то получать статус в строку, чтоб писать в описании
									//t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
									
									if ($t_f_data[$i.$j]['status'] == '3'){
										//штифт
										$surface = 'NONE';
										$color = "#9393FF";
										$color_stroke = '#5353FF';
										$coordinates = $teeth_map_pin[$n_zuba];
										$stroke_width = 1;
															
										echo '
											<div id="'.$n_zuba.$surface.'"
												status-path=\'
												"stroke": "'.$color_stroke.'", 
												"stroke-width": '.$stroke_width.', 
												"fill-opacity": "1"\' 
												class="mapArea'.$n.'" 
												t_status = "'.$t_status.'"
												data-path'.$n.'="'.$coordinates.'"
												fill-color'.$n.'=\'"fill": "'.$color.'"\'
												t_menu'.$n.' = "
														<div class=\'cellsBlock4\'>
															<div class=\'cellLeft\'>
																'.t_surface_name($n_zuba.$surface, 2).'<br />';
												
										DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
										
										echo '
															</div>
														</div>';
										echo					
												'"
												t_menuA'.$n.' = "
														'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
												
										//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
												
										echo					
												'"
											>
											</div>
										';
									}else{
									
									
										//Если надо рисовать корень, но в бд написано, что тут имплант
										if (($t_f_data[$i.$j]['pin'] == '1') && (mb_strstr($surface, 'root') != FALSE)){
											$DrawRoots = FALSE;
										}else{
											if  ((mb_strstr($surface, 'root') == TRUE) && 
												(($t_f_data[$i.$j]['status'] == '1') || ($t_f_data[$i.$j]['status'] == '2') || 
												($t_f_data[$i.$j]['status'] == '18') || ($t_f_data[$i.$j]['status'] == '19') || 
												($t_f_data[$i.$j]['status'] == '9'))){
												$DrawRoots = FALSE;
											}else{
												if (isset($t_f_data[$i.$j][$surface])){
													if  ((mb_strstr($surface, 'root') == TRUE) && ($t_f_data[$i.$j][$surface] != '0')){
														$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
													}
													$DrawRoots = TRUE;
												}
											}
										}
										//!!!!учим рисовать корни с коронками - начало  - кажется, это все говно. надо иначе
										/*if ($t_f_data[$i.$j]['status'] == '19'){
											$DrawRoots = TRUE;
										}*/
										if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
											//Если в массиве натыкаемся не на корни или если чужой, то корни не рисуем, а рисум кружок просто
											if ((($surface != 'root1') && ($surface != 'root2') && ($surface != 'root3')) || ($t_f_data[$i.$j]['alien'] == '1')){
												//без корней + коронки и всякая херня
												$surface = 'NONE';
												$color = $tooth_status[$t_f_data[$i.$j]['status']]['color'];
												$coordinates = $teeth_map_d[$n_zuba];								
											}
										}else{
											//Если у какой-то из областей зуба есть статус в бд.
											if (isset($t_f_data[$i.$j][$surface])){
											if ($t_f_data[$i.$j][$surface] != '0'){
												if (array_key_exists($t_f_data[$i.$j][$surface], $root_status)){
													$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
												}elseif(array_key_exists($t_f_data[$i.$j][$surface], $surface_status)){
													$color = $surface_status[$t_f_data[$i.$j][$surface]]['color'];
												}else{
													$color = "#fff";
												}
											}
											}
										}
										
						//!Костыль для радикса(корень)/статус 34
						if ((($t_f_data[$i.$j]['root1'] == '34') || ($t_f_data[$i.$j]['root2'] == '34') || ($t_f_data[$i.$j]['root3'] == '34')) && 
								(($t_f_data[$i.$j]['status'] != '1') && ($t_f_data[$i.$j]['status'] != '2') && 
								($t_f_data[$i.$j]['status'] != '18') && ($t_f_data[$i.$j]['status'] != '19') &&
								($t_f_data[$i.$j]['status'] != '9')))
						{
											$surface = 'NONE';
											$color = '#FF0000';
											$coordinates = $teeth_map_d[$n_zuba];								
										}
										
										
										if (mb_strstr($surface, 'root') != FALSE){
											$menu = 'r_menu';
										}elseif((mb_strstr($surface, 'surface') != FALSE) || (mb_strstr($surface, 'top') != FALSE)){
											$menu = 's_menu';
										}else{
											$DrawMenu = FALSE;
										}
										
										if ($DrawRoots){
											echo '
												<div id="'.$n_zuba.$surface.'"
													status-path=\'
													"stroke": "'.$color_stroke.'", 
													"stroke-width": '.$stroke_width.', 
													"fill-opacity": "1"\' 
													class="mapArea'.$n.'" 
													t_status = "'.$t_status.'"
													data-path'.$n.'="'.$coordinates.'"
													fill-color'.$n.'=\'"fill": "'.$color.'"\'
													t_menu'.$n.' = "
														<div class=\'cellsBlock4\'>
															<div class=\'cellLeft\'>
																'.t_surface_name($n_zuba.'NONE', 1).'<br />';
														
											DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
													echo '
															</div>
															<div class=\'cellRight\'>
																'.t_surface_name($n_zuba.$surface, 0).'<br />';
											if ($DrawMenu){ DrawTeethMapMenu($key, $n_zuba, $surface, $menu);}	
											echo '
															</div>
														</div>';	
											echo
													'"
													t_menuA'.$n.' = "
														'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
													
											//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
											
											echo					
													'"
													>
													</div>
													';
										}
									}
								}
								
								if ($t_f_data[$i.$j]['pin'] == '1'){
									//штифт
									$surface = 'NONE';
									$color = "#9393FF";
									$color_stroke = '#5353FF';
									$coordinates = $teeth_map_pin[$n_zuba];
									$stroke_width = 1;
									if ($t_f_data[$i.$j]['alien'] == '1'){
										$color_stroke = '#F7273F';
										$stroke_width = 3;
									}				
									echo '
										<div id="'.$n_zuba.$surface.'"
											status-path=\'
											"stroke": "'.$color_stroke.'", 
											"stroke-width": '.$stroke_width.', 
											"fill-opacity": "1"\' 
											class="mapArea'.$n.'" 
											t_status = "'.$t_status.'"
											data-path'.$n.'="'.$coordinates.'"
											fill-color'.$n.'=\'"fill": "'.$color.'"\'
											t_menu'.$n.' = "
												<div class=\'cellsBlock4\'>
													<div class=\'cellLeft\'>
														'.t_surface_name($n_zuba.$surface, 2).'<br />';
											
									DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
									echo '
													</div>
												</div>';
									echo					
											'"
											t_menuA'.$n.' = "
														'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
											
									//DrawTeethMapMenu($key, $n_zuba, $surface, 't_menu');	
									
									echo					
											'"
											>
											</div>
											';
								}
								
								//Для ЗО и дополнительно
								if (isset($t_f_data[$i.$j]['zo'])){
									$surface = 'NONE';
									if ($t_f_data[$i.$j]['zo'] == '1'){
										$color = "#FF0000";
									}else{
										$color = "#FFF";
									}
									$color_stroke = '#5353FF';
									$coordinates = $teeth_map_zo_db[$i.$j];
									$stroke_width = 1;
								
									echo '
										<div id="'.$n_zuba.$surface.'"
											status-path=\'
											"stroke": "'.$color_stroke.'", 
											"stroke-width": '.$stroke_width.', 
											"fill-opacity": "1"\' 
											class="mapArea'.$n.'" 
											t_status = "'.$t_status.'"
											data-path'.$n.'="'.$coordinates.'"
											fill-color'.$n.'=\'"fill": "'.$color.'"\'
											t_menu'.$n.' = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
									echo					
												'
											t_menuA'.$n.' = "
														'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
								
									echo					
											'"
											>
											</div>
											';
								}
								
								$text_status_div = '';
								$text_status_div_shinir = '';
								$text_status_div_podvizh = '';
								
								//Для Шинирования и дополнительно
								if (isset($t_f_data[$i.$j]['shinir'])){
									$text_status_div_shinir = 'ш';
									if ($i == 1){
										$top_tts = $text_tooth_status['up'];
										$left_tts = $text_tooth_status['left'][$j];
									}
									if ($i == 2){
										$top_tts = $text_tooth_status['up'];
										$left_tts = $text_tooth_status['right'][$j];
									}
									if ($i == 3){
										$top_tts = $text_tooth_status['down'];
										$left_tts = $text_tooth_status['right'][$j];
									}
									if ($i == 4){
										$top_tts = $text_tooth_status['down'];
										$left_tts = $text_tooth_status['left'][$j];
									}
								}
								//Для Подвижности и дополнительно
								if (isset($t_f_data[$i.$j]['podvizh'])){
									$text_status_div_podvizh = 'A';
									if ($i == 1){
										$top_tts = $text_tooth_status['up'];
										$left_tts = $text_tooth_status['left'][$j];
									}
									if ($i == 2){
										$top_tts = $text_tooth_status['up'];
										$left_tts = $text_tooth_status['right'][$j];
									}
									if ($i == 3){
										$top_tts = $text_tooth_status['down'];
										$left_tts = $text_tooth_status['right'][$j];
									}
									if ($i == 4){
										$top_tts = $text_tooth_status['down'];
										$left_tts = $text_tooth_status['left'][$j];
									}
									$text_status_div .= '
										<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">';
								}
								if ((isset($t_f_data[$i.$j]['shinir'])) || (isset($t_f_data[$i.$j]['podvizh']))){
									echo '<div class="text_in_map_dop" style="left: '.$left_tts.'px; top: '.$top_tts.'px">'.$text_status_div_shinir.' '.$text_status_div_podvizh.'</div>';
								}
								
							}
						}
						
						
						
						
						echo '
								</div>
							</div>
						</div>';
						echo '
						<div class="cellsBlock3" style="font-size:80%;">
							<div class="cellLeft">';

						//$decription = $t_f_data_db[$z];
						
						//$t_f_data = array();
			
						//собрали массив с зубами и статусами по поверхностям
						/*foreach ($decription as $key => $value){
							$surfaces_temp = explode(',', $value);
							foreach ($surfaces_temp as $key1 => $value1){
								$t_f_data[$key][$surfaces[$key1]] = $value1;
							}
						}*/
						//var_dump ($t_f_data);			
			
						$descr_rez = '';
						echo '<div><a href="#open1" onclick="show(\'hidden_'.$z.'\',200,5)">Подробно</a></div>';	
						echo '<div id=hidden_'.$z.' style="display:none;">';		
						foreach($t_f_data as $key => $value){
							//var_dump ($value);
							foreach ($value as $key1 => $value1){
								
								if ($key1 == 'status'){
									//var_dump ($value1);	
									if ($value1 != 0){
										//$descr_rez .= 
										echo t_surface_name('t'.$key.'NONE', 1).' '.t_surface_status($value1, 0).'';
									}
								}elseif($key1 == 'pin'){
									if ($value1 != 0){
										echo t_surface_status(3, 0);
									}
								}elseif($key1 == 'alien'){
									
								}elseif($key1 == 'zo'){
									
								}else{
									if ($value1 != 0){
										echo t_surface_name('t'.$key.$key1, 1).' '.t_surface_status(0, $value1);
									}
								}
							}
				
						}
						echo '</div>';				
								
						echo '					
								</div>
							</div>';
									
					}
					
				}
			mysql_close();	
				
				
				
			echo '</div>';	
				
				
//!!!!!!!!!!!!!!!!!!!!!!!!!*************************************

				
				if (($stom['see_all'] == 1) || ($stom['see_own'] == 1) || $god_mode){
					/*echo '	
									<input type=\'button\' class="b" value=\'Обновить\' onclick=Some_ZF_Refresh()>';*/
					echo '	
									<input type=\'button\' class="b" value=\'Обновить\' onclick="document.getElementById(\'target\').submit()" >';
				}		
				
				//echo $t_f_data_db_min_id.'<br />';
				//echo $t_f_data_db_max_id;
				
				$js_data = '';
				$post_data = '';
				
				$stom_journal = array();

				$stom_journal = SelDataFromDB('journal_tooth_status', $_GET['client'], 'client_stom_id');
				//var_dump($stom_journal);
				if ($stom_journal !=0){
					//var_dump($stom_journal);
					echo '<form id="target" action="stom_history.php">';
					foreach($stom_journal as $value){
						
						$js_data .= '
							var id'.$value['id'].' = $("input[name=id'.$value['id'].']:checked").val();
						';
						$post_data .= '
							id'.$value['id'].':id'.$value['id'].',';
						
						if (($value['id'] == $t_f_data_db_min_id) || ($value['id'] == $t_f_data_db_max_id)){
							$checked = 'checked';
						}else{
							$checked = '';
						}
						//var_dump($value);
						echo '
							<div class="cellsBlock">
								<div class="cellCosmAct">
									<input type="checkbox" name="id'.$value['id'].'" value="1" id="id'.$value['id'].' "'.$checked.'>
								</div>
								<div class="cellCosmAct">
									+
								</div>
								<div class="cellTime">';
						echo '
									<a href="task_stomat_inspection.php?id='.$value['id'].'" class="ahref">'.date('d.m.y H:i', $value['create_time']).'</a>';
						echo '		
								</div>
								<div class="cellName">
									'.WriteSearchUser('spr_workers', $value['worker'], 'user').'
								</div>
								<div class="cellText">';
						//if (($stom['see_all'] == 1) || (($stom['see_own'] == 1) && (($_SESSION['id'] == $value['worker']) || ($_SESSION['id'] == $value['worker']))) || $god_mode){
						if (($stom['see_all'] == 1) || (($stom['see_own'] == 1) && (($_SESSION['id'] == $value['worker']) || ($_SESSION['id'] == $value['worker']))) || $god_mode){
							echo $value['comment'];
						}else{
							echo '';
						}							
						echo '			
								</div>
							</div>
						';
					}
					
					
					echo '<input type="hidden" name="client" value="'.$_GET['client'].'"></form>';
				}else{
					echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Не было посещений стоматолога
								</div>
							</div>';
				}
					
							
				echo '
					</div>';
								
								
								
			echo '					
					</div>
				</div>
			</div>
			
			

			
			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
			 </script>
								
				<script>  

					function Some_ZF_Refresh() {
						'.$js_data.'
								ajax({
									url:"show_some_stomat_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										client_id:'.$_GET['client'].',';
			echo $post_data;
			echo '
									},
									success:function(data){
										document.getElementById("status").innerHTML=data;
									}
								})
					};  
					  
				</script> 
			';					
								
					
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>