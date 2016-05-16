<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'tooth_status.php';
			
			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			//var_dump($user);
			if ($client != 0){
				echo '
					<script src="js/init2.js" type="text/javascript"></script>
					<div id="status">
						<header>
							<h2>Карточка пациента #'.$client[0]['id'].'</h2>
						</header>';

				echo '
						<div id="data">';

				echo '

								<div class="cellsBlock2">
									<div class="cellLeft">ФИО</div>
									<div class="cellRight">'.$client[0]['full_name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">', $client[0]['birthday'] == '-1577934000' ? 'не указана' : date('d.m.Y', $client[0]['birthday']) ,'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">';
				if ($client[0]['sex'] != 0){
					if ($client[0]['sex'] == 1){
						echo 'М';
					}
					if ($client[0]['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo 
									'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Лечащий врач</div>';
				//Надо найти имя клиента
				/*$therapists = SelDataFromDB ('spr_workers', $client[0]['therapist'], 'worker_id');
				if ($therapists != 0){
					$therapist = $therapists[0]["name"];
				}else{
					$therapist = 'unknown';
				}*/								
				echo '
									<div class="cellRight">'.WriteSearchUser('spr_workers',$client[0]['therapist'], 'user').'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$client[0]['contacts'].'</div>
								</div>	

								<div class="cellsBlock2">
									<a href="#" id="showDiv1" class="b">Стоматология</a>
									<a href="#" id="showDiv2" class="b">Косметология</a>';
				if (($clients['edit'] == 1) || $god_mode){
					echo '
									<a href="client_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
				}
				echo '
								</div>';
	

				echo '<div id="div1">';
				echo '	<a href="add_task_stomat.php?client='.$client[0]['id'].'" class="b">Добавить осмотр</a>';

				
				//Выберем из базы последнюю запись
				$t_f_data_db = array();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` DESC LIMIT 3";
				$res = mysql_query($query) or die($q);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($t_f_data_db, $arr);
					}
				}else
					$t_f_data_db = 0;
				mysql_close();
				
				//Выберем из базы первую запись
				$t_f_data_db_first = array();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` ASC LIMIT 1";
				$res = mysql_query($query) or die($q);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($t_f_data_db_first, $arr);
					}
				}else
					$t_f_data_db_first = 0;
				mysql_close();
				
				//$t_f_data_db = SelDataFromDB('journal_tooth_status', $_GET['id'], 'id');								
				//var_dump ($t_f_data_db);
				//var_dump ($t_f_data_db_first);
				
				$t_f_data_db[count($t_f_data_db)-1] = $t_f_data_db_first[0];
				
							
				if ($t_f_data_db !=0){
					for ($z = 0; $z < count ($t_f_data_db); $z++){
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
						
						
						echo '<div class="map'.$n.'" id="map'.$n.'">';

						

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
						
						
						
						
						echo '</div></div></div>';
								
					}
					
					
					/*echo '
								<div class="cellLeft">
									Зубная формула<br />на '.date('d.m.y H:i', $t_f_data_db[0]['create_time']).'<br />пока показывается только самая последняя
								</div>
								<div class="cellRight">';
				
					//рисуем зубную формулу						
					//include_once 'teeth_map_svg.php';
					//DrawTeethMap($t_f_data_db[0], 0, $tooth_status, $tooth_alien_status, $surfaces, '');
					
					
					//var_dump ($t_f_data_temp);
					//function DrawTeethMap($t_f_data_temp, $sw, $tooth_status, $tooth_alien_status, $surfaces, $n){
					//include_once 'DBWork.php';
					include_once 'teeth_map_db.php';
					include_once 't_surface_name.php';
					include_once 't_surface_status.php';

					include_once 'root_status.php';
					include_once 'surface_status.php';
					include_once 't_context_menu.php';
					
					$t_f_data = array();
					$n = '';
					$sw = 0;
					$stat_id = $t_f_data_db[0]['id'];
					
					unset($t_f_data_db[0]['id']);
					unset($t_f_data_db[0]['create_time']);
					//echo "echo$sw";
					//var_dump ($surfaces);
					$t_f_data_temp_refresh = '';
					
					foreach ($t_f_data_db[0] as $key => $value){
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
					
					
					echo '<div class="map'.$n.'" id="map'.$n.'">';

					

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
											data-path="'.$coordinates.'"
											fill-color=\'"fill": "'.$color.'"\'
											t_menu = "
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
								/*	if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
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
												data-path="'.$coordinates.'"
												fill-color=\'"fill": "'.$color.'"\'
												t_menu = "
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
										data-path="'.$coordinates.'"
										fill-color=\'"fill": "'.$color.'"\'
										t_menu = "
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
					
					
					
					
					echo '</div></div></div>';
					
					
/**********************************************

					echo '<div class="cellsBlock3">
								<div class="cellLeft">
									Зубная формула<br />на '.date('d.m.y H:i', $t_f_data_db[1]['create_time']).'<br />пока показывается только самая последняя
								</div>
								<div class="cellRight">';

		$t_f_data = array();
		$n = '1';
		$sw = 0;
		$stat_id = $t_f_data_db[1]['id'];
		
		unset($t_f_data_db[1]['id']);
		unset($t_f_data_db[1]['create_time']);
		//echo "echo$sw";
		//var_dump ($surfaces);
		$t_f_data_temp_refresh = '';
		
		foreach ($t_f_data_db[1] as $key => $value){
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
		
		
		echo '<div class="map'.$n.'" id="map'.$n.'">';

		

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
								data-path="'.$coordinates.'"
								fill-color=\'"fill": "'.$color.'"\'
								t_menu = "
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
						/*if ((array_key_exists($t_f_data[$i.$j]['status'], $tooth_status)) && ($t_f_data[$i.$j]['status'] != '19')){
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
									data-path="'.$coordinates.'"
									fill-color=\'"fill": "'.$color.'"\'
									t_menu = "
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
							data-path="'.$coordinates.'"
							fill-color=\'"fill": "'.$color.'"\'
							t_menu = "
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
		
		
		
		
		echo '</div>';					
					
					
/////////////************************************************//////////////////////////					
					
				/*	echo '
								</div>';*/
				}else{
					echo '
								<div class="cellLeft">
									Не было посещений стоматолога
								</div>';
				}
					
				/*echo '			
							</div>';*/
////////////////////////
				/*echo '
							<div class="cellsBlock3">';
				if ($t_f_data_db !=0){
					echo '
								<div class="cellLeft">
									Зубная формула<br />на '.date('d.m.y H:i', $t_f_data_db[1]['create_time']).'<br />пока показывается только самая последняя
								</div>
								<div class="cellRight">';
				
					//рисуем зубную формулу						
					//include_once 'teeth_map_svg.php';
					DrawTeethMap($t_f_data_db[1], 0, $tooth_status, $tooth_alien_status, $surfaces, 1);
					
					echo '
								</div>';
				}else{
					echo '
								<div class="cellLeft">
									Не было посещений стоматолога
								</div>';
				}
					
				echo '			
							</div>';	*/						
							
				echo '
					</div>

					<div id="div2">';
			echo '	<a href="add_task_cosmet.php?client='.$client[0]['id'].'" class="b">Добавить посещение</a>';		
								
			$cosmet_task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'client_cosm_id');
			//var_dump ($cosmet_task);
			$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
			
			if ($cosmet_task != 0){
				for ($i=0; $i < count($cosmet_task); $i++){
					//!а если нет офиса или работника??
					$worker = SelDataFromDB('spr_workers', $cosmet_task[$i]['worker'], 'worker_id');
					$offices = SelDataFromDB('spr_office', $cosmet_task[$i]['office'], 'offices');
					echo '
						<div class="cellsBlock3">
							<div class="cellLeft">
								<a href="task_cosmet.php?id='.$cosmet_task[$i]['id'].'" class="ahref">
									'.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'
									<br />
									'.$worker[0]['name'].'
									<br />
									'.$offices[0]['name'].'
								</a>
							</div>';
					
					$decription = array();
					$decription_temp_arr = array();
					$decription_temp = '';
					
					/*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
					foreach($cosmet_task[$i] as $key => $value){
						if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
							$decription_temp_arr[mb_substr($key, 1)] = $value;
						}
					}
						
						//var_dump ($decription_temp_arr);
						
						$decription = $decription_temp_arr;

					
					
					
					
					
					
					/*$decription = array();
					$decription = json_decode($cosmet_task[$i]['description'], true);
					//var_dump ($decription);	*/	
					
					echo '<div class="cellLeft">';
					
					for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						$action = '';
						if (isset($decription[$j])){
							if ($decription[$j] != 0){
								$action = $actions_cosmet[$j-1]['full_name'].'<br />';
							}else{
								$action = '';
							}
							echo $action;
						}else{
							echo '';
						}
					}
					
					echo '
							</div>
							<div class="cellRight">';
					//!!!!!!if ($SESION_ID == )
					echo $cosmet_task[$i]['comment'];
					echo '
							</div>
						</div>';
					
					//echo ''.date('d.m.y H:i', $cosmet_task[$i]['create_time']).'<br />';
				}
			}
			
								
								
								
			echo '					
								</div>
							</div>
					</div>

								
								
								
								';	
					
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
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>