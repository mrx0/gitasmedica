<?php 

//show_some_stomat_f.php
//

if ($_POST){	

		include_once 'DBWork.php';
		include_once 'teeth_map_db.php';
		include_once 't_surface_name.php';
		include_once 't_surface_status.php';
		include_once 'tooth_status.php';
		include_once 'root_status.php';
		include_once 'surface_status.php';
		include_once 't_context_menu.php';

		echo '					<script src="js/init.js" type="text/javascript"></script>
					<script src="js/init2.js" type="text/javascript"></script>';
		$temp_arr = array();
		
			$client_id = $_POST['client_id'];
			unset($_POST['ajax']);
			//var_dump ($_POST);
			foreach($_POST as $key => $value){
				if ($value == '1'){
					$temp_arr[mb_substr($key, 2)] = $value;
				}
			}
			//var_dump ($temp_arr);
			//echo  min(array_keys($temp_arr));
			if (count($temp_arr) > 0){
				
				
				
				
//!!!!!!!!!!!!!!!!!!!!!!!!!*************************************
				

				
			//Выберем из базы последнюю запись
				$t_f_data_db = array();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '".max(array_keys($temp_arr))."'";
				$res = mysql_query($query) or die($q);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($t_f_data_db, $arr);
					}
				}else
					$t_f_data_db = 0;
				mysql_close();
				
				//var_dump ($t_f_data_db);
				
				if ($t_f_data_db !=0){
					
					//$t_f_data_db_max_id = $t_f_data_db[0]['id'];
					
					//echo '							<script src="js/init.js" type="text/javascript"></script>';
					if (count($temp_arr) > 1){
						//Выберем из базы первую запись
						$t_f_data_db_first = array();
						
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$time = time();
						$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '".min(array_keys($temp_arr))."'";
						$res = mysql_query($query) or die($q);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($t_f_data_db_first, $arr);
							}
						}else
							$t_f_data_db_first = 0;
						mysql_close();
						
						//$t_f_data_db = SelDataFromDB('journal_tooth_status', $_GET['client'], 'id');								
						//var_dump ($t_f_data_db);
						//var_dump ($t_f_data_db_first);
						if ($t_f_data_db_first !=0){
							$t_f_data_db_min_id = $t_f_data_db_first[0]['id'];
							if ($t_f_data_db_first[0]['id'] != $t_f_data_db[0]['id']){
								$t_f_data_db[count($t_f_data_db)] = $t_f_data_db_first[0];
							}
						}
					}	
					
					//var_dump ($t_f_data_db);
					
					for ($z = 0; $z < count ($t_f_data_db); $z++){
						echo '
							<div class="cellsBlock3">';
						echo '
								<div class="cellLeft">
									<a href="task_stomat_inspection.php?id='.$t_f_data_db[$z]['id'].'" class="ahref">'.date('d.m.y H:i', $t_f_data_db[$z]['create_time']).'</a>
								</div>
								<div class="cellRight">';
									
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
								$t_f_data[$key][$surfaces[$key1]] = $value1;
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
									
									$s_stat = $t_f_data[$i.$j][$surface];
									
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
												$DrawRoots = TRUE;
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
										
										//!Костыль для радикса(корень)/статус 34
										/*if (($t_f_data[$i.$j]['root1'] == '34') || ($t_f_data[$i.$j]['root2'] == '34') || ($t_f_data[$i.$j]['root3'] == '34')){
											$surface = 'NONE';
											$color = '#FF0000';
											$coordinates = $teeth_map_d[$n_zuba];								
										}*/
										
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
							}
						}
						
						
						
						
						echo '
								</div>
							</div>
						</div>';
						echo '
						<div class="cellsBlock3" style="font-size:80%;">
							<div class="cellLeft">';

						$decription = $t_f_data_db[$z];
						
						unset($decription['id']);
						unset($decription['office']);
						unset($decription['client']);
						unset($decription['create_time']);
						unset($decription['create_person']);
						unset($decription['last_edit_time']);
						unset($decription['last_edit_person']);
						unset($decription['worker']);
						unset($decription['comment']);
						
						$t_f_data = array();
			
						//собрали массив с зубами и статусами по поверхностям
						foreach ($decription as $key => $value){
							$surfaces_temp = explode(',', $value);
							foreach ($surfaces_temp as $key1 => $value1){
								$t_f_data[$key][$surfaces[$key1]] = $value1;
							}
						}
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
				
				

				
				
//!!!!!!!!!!!!!!!!!!!!!!!!!*************************************

				
				
				
				
			}else{
				echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Ничего не выбрано
								</div>
							</div>';
			}
		}

?>