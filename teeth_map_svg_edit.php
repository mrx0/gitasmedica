<?php

//teeth_map_svg_edit.php
//Зубная карта отрисовка с меню по POST запросу
	//$time_arr[] = microtime(true);
	session_start();
	
	echo '

<script src="js/raphael.js" type="text/javascript"></script>
<script src="js/init.js" type="text/javascript"></script>

	
	';
	
	if($_GET){
		
		//var_dump($_GET);
		//var_dump($_SESSION);
		
		include_once 'DBWork.php';
		include_once 'teeth_map_db.php';
		include_once 't_surface_name.php';
		include_once 't_surface_status.php';
		include_once 'tooth_status.php';
		include_once 'root_status.php';
		include_once 'surface_status.php';
		include_once 't_context_menu.php';
		
		$t_f_data = $_SESSION['journal_tooth_status_temp'];
		
		//Если был изменен статус зуба, есть номер зуба и номер зуба != ''
		if (isset($_GET['status']) && ($_GET['status'] != '') && isset($_GET['n_zuba']) && ($_GET['n_zuba'] != '')){
			
			if (($_GET['status'] != '22') && ($_GET['status'] != '23') && ($_GET['status'] != '24')){
				if (array_key_exists($_GET['status'], $tooth_status) || ($_GET['status'] == '0')){
					if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] == $_GET['status']){
						$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = '0';
					}else{
						$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = $_GET['status'];
						//Чистим радиксы (пока только их)  место для взаимоисключающих статусов
						if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['root1'] == '34')
							$t_f_data[mb_substr($_GET['n_zuba'], 1)]['root1'] = 0;
						if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['root2'] == '34')
							$t_f_data[mb_substr($_GET['n_zuba'], 1)]['root2'] = 0;
						if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['root3'] == '34')
							$t_f_data[mb_substr($_GET['n_zuba'], 1)]['root3'] = 0;
							
					}
				}elseif (array_key_exists($_GET['status'], $root_status)){
					if ($t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] == $_GET['status']){
						$t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] = '0';
					}else{
						$t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] = $_GET['status'];
					}
				}elseif (array_key_exists($_GET['status'], $surface_status)){
					if ($t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] == $_GET['status']){
						$t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] = '0';
					}else{
						$t_f_data[mb_substr($_GET['n_zuba'], 1)][$_GET['surface']] = $_GET['status'];
					}
				}
			}
			//сбросить статус зуба + ЗО до полностью здорового
			if ($_GET['status'] == '0'){
				foreach($t_f_data[mb_substr($_GET['n_zuba'], 1)] as $key => $value){
					$t_f_data[mb_substr($_GET['n_zuba'], 1)][$key] = '0';
					//echo $key.':'.$value.'<br />';
				}
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['pin'] = '0';
				unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo']);
				unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir']);
				unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh']);
			}
			
			//имплантант (может быть с чем-то)
			if (isset($_GET['implant']) && ($_GET['implant'] == '1') && ($_GET['status'] != '0')){
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['pin'] = '1';
			}
			//один только имплант
			if ($_GET['status'] == '3'){
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['pin'] = '1';
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = '3';
			}
			//Чужой
			if (isset($_GET['alien']) && ($_GET['alien'] == '1') && ($_GET['status'] != '0')){
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['alien'] = '1';
			}else{
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['alien'] = '0';
			}
			//один только ЗО
			if ($_GET['status'] == '22'){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'])){
					if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] == '1'){
						$t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] = '0';
					}elseif ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] == '0'){
						unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo']);
					}
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] = '1';
				}
				//$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = '22';
			}
			//ЗО с чем-то
			if (isset($_GET['zo']) && ($_GET['zo'] == '1')){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'])){
					if ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] == '1'){
						$t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] = '0';
					}elseif ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] == '0'){
						unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo']);
					}
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] = '1';
				}
			}
			//один только Шинирование
			if ($_GET['status'] == '23'){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir'])){
					unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir']);
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir'] = '1';
				}
				//$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = '22';
			}
			//Шинирование с чем-то
			if (isset($_GET['shinir']) && ($_GET['shinir'] == '1')){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir'])){
					unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir']);
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['shinir'] = '1';
				}
			}
			//один только Подвижность
			if ($_GET['status'] == '24'){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh'])){
					unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh']);
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh'] = '1';
				}
				//$t_f_data[mb_substr($_GET['n_zuba'], 1)]['status'] = '22';
			}
			//Подвижность с чем-то
			if (isset($_GET['podvizh']) && ($_GET['podvizh'] == '1')){
				if (isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh'])){
					unset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh']);
				}else{
					$t_f_data[mb_substr($_GET['n_zuba'], 1)]['podvizh'] = '1';
				}
			}
			/*if (!isset($_GET['zo']) && ((isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'])) && ($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] != 1) || (!isset($t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'])))){
				$t_f_data[mb_substr($_GET['n_zuba'], 1)]['zo'] = '0';
			}*/
		}

		//var_dump ($t_f_data);
		$_SESSION['journal_tooth_status_temp'] = $t_f_data;
		
		//Обновим данные в базе
		//require 'config.php';
		//mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		//mysql_select_db($dbName) or die(mysql_error()); 
		//mysql_query("SET NAMES 'utf8'");
		
		//foreach($t_f_data as $key => $value){
			//$query = "UPDATE `journal_tooth_status_temp` SET `{$key}` = '".(implode(',', $value))."' WHERE `id`='{$stat_id}'";
			//mysql_query($query) or die(mysql_error());
		//}
		//mysql_close();
		
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
		
		echo '<div class="map" id="map">
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


		
		//Зубная карта собственно отрисовка 

		$teeth_map_temp = $teeth_map_db;
		foreach ($teeth_map_temp as $value){
			$teeth_map[mb_substr($value['tooth'], 0, 3)][mb_substr($value['tooth'], 3, strlen($value['tooth'])-3)]=$value['coord'];
		}

		$teeth_map_d_temp = $teeth_map_d_db;
		foreach ($teeth_map_d_temp as $value){
			$teeth_map_d[$value['tooth']]=$value['coord'];
		}

		$teeth_map_pin_temp = $teeth_map_pin_db;
		foreach ($teeth_map_pin_temp as $value){
			$teeth_map_pin[$value['tooth']]=$value['coord'];
		}
		
		/*$teeth_map_zo_temp = $teeth_map_zo_db;
		foreach ($teeth_map_zo_temp as $value){
			$teeth_map_zo[$value['tooth']]=$value['coord'];
		}*/
		
	//$time_arr[] = microtime(true);	
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
								class="mapArea" 
								t_status = "'.$t_status.'"
								data-path="'.$coordinates.'"
								fill-color=\'"fill": "'.$color.'"\'
								t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
								//для DrawTeethMapMenu 
								//$n_zuba, $surface, 't_menu'   
								//если true то t_surface_name
								//$n_zuba, NONE, 1
								//Если true то t_surface_name
								//$n_zuba, $surface, 0
								//Если $DrawMenu true то DrawTeethMapMenu
								//$n_zuba, $surface, $menu
								
						/*				<div class=\'cellsBlock4\'>
											<div class=\'cellLeft\'>
												'.t_surface_name($n_zuba.$surface, 2).'<br />';
								
						DrawTeethMapMenu($n_zuba, $surface, 't_menu');	
						
						echo '
											</div>
										</div>';*/
						echo					
								'
								t_menuA = "
										'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
								
						//DrawTeethMapMenu($n_zuba, $surface, 't_menu');	
								
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
									class="mapArea" 
									t_status = "'.$t_status.'"
									data-path="'.$coordinates.'"
									fill-color=\'"fill": "'.$color.'"\'
									t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, NONE, 1, true, '.$surface.', 0, ', $DrawMenu ? 'true' : 'false' ,', '.$surface.', '.$menu.'"';
									//для DrawTeethMapMenu 
									//$n_zuba, $surface, 't_menu'   
									//если true то t_surface_name
									//$n_zuba, NONE, 1
									//Если true то t_surface_name
									//$n_zuba, $surface, 0
									//Если $DrawMenu true то DrawTeethMapMenu
									//$n_zuba, $surface, $menu
									
									/*		<div class=\'cellRight\'>
												'.t_surface_name($n_zuba.$surface, 0).'<br />';
							if ($DrawMenu){ DrawTeethMapMenu($n_zuba, $surface, $menu);}	
							echo '
											</div>
										</div>';	*/
							echo
									'
									t_menuA = "
										'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
									
							//DrawTeethMapMenu($n_zuba, $surface, 't_menu');	
							
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
							class="mapArea" 
							t_status = "'.$t_status.'"
							data-path="'.$coordinates.'"
							fill-color=\'"fill": "'.$color.'"\'
							t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
							//для DrawTeethMapMenu 
							//$n_zuba, $surface, 't_menu'   
							//если true то t_surface_name
							//$n_zuba, NONE, 1
							//Если true то t_surface_name
							//$n_zuba, $surface, 0
							//Если $DrawMenu true то DrawTeethMapMenu
							//$n_zuba, $surface, $menu
							
							/*	<div class=\'cellsBlock4\'>
									<div class=\'cellLeft\'>
										'.t_surface_name($n_zuba.$surface, 2).'<br />';
							
					DrawTeethMapMenu($n_zuba, $surface, 't_menu');	
					echo '
									</div>
								</div>';*/
					echo					
							'
							t_menuA = "
										'.t_surface_name($n_zuba.$surface, 1).'<br />'.t_surface_status($t_f_data[$i.$j]['status'], $s_stat);
							
					//DrawTeethMapMenu($n_zuba, $surface, 't_menu');	
					
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
							class="mapArea" 
							t_status = "'.$t_status.'"
							data-path="'.$coordinates.'"
							fill-color=\'"fill": "'.$color.'"\'
							t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
					echo					
								'
							t_menuA = "
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
//$time_arr[] = microtime(true);
	
		//Пробуем отрисовать менюшку
		$color = "#000";
		$color_stroke = '#74675C';

		/*echo '
			<div id="'.$n_zuba.$surface.'"
				status-path=\'
				"stroke": "'.$color_stroke.'", 
				"stroke-width": '.$stroke_width.', 
				"fill-opacity": "1"\' 
				class="mapArea" 
				t_status = "'.$t_status.'"
				data-path="M 288 71.5L 288 74L 298.5 74L 309 74L 309 71.5L 309 69L 298.5 69L 288 69L 288 71.5z"
				fill-color=\'"fill": "'.$color.'"\'
				t_menu = "'.$n_zuba.', '.$surface.', m_menu, false, \'\', \'\', false, \'\', \'\', false, \'\', \'\'"';
				//для DrawTeethMapMenu 
				//$n_zuba, $surface, 't_menu'   
				//если true то t_surface_name
				//$n_zuba, NONE, 1
				//Если true то t_surface_name
				//$n_zuba, $surface, 0
				//Если $DrawMenu true то DrawTeethMapMenu
				//$n_zuba, $surface, $menu
				
				/*	<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';*/
		/*echo					
				'
				t_menuA = "Меню"
				>
				</div>
				';
		echo '
			<div id="'.$n_zuba.$surface.'"
				status-path=\'
				"stroke": "'.$color_stroke.'", 
				"stroke-width": '.$stroke_width.', 
				"fill-opacity": "1"\' 
				class="mapArea" 
				t_status = "'.$t_status.'"
				data-path="M 288 63.5L 288 66L 298.5 66L 309 66L 309 63.5L 309 61L 298.5 61L 288 61L 288 63.5z"
				fill-color=\'"fill": "'.$color.'"\'
				t_menu = "'.$n_zuba.', '.$surface.', m_menu, false, \'\', \'\', false, \'\', \'\', false, \'\', \'\'"';
				//для DrawTeethMapMenu 
				//$n_zuba, $surface, 't_menu'   
				//если true то t_surface_name
				//$n_zuba, NONE, 1
				//Если true то t_surface_name
				//$n_zuba, $surface, 0
				//Если $DrawMenu true то DrawTeethMapMenu
				//$n_zuba, $surface, $menu
				
/*				<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';	*/			
		/*echo					
				'
				t_menuA = "Меню"
				>
				</div>
				';
		echo '
			<div id="'.$n_zuba.$surface.'"
				status-path=\'
				"stroke": "'.$color_stroke.'", 
				"stroke-width": '.$stroke_width.', 
				"fill-opacity": "1"\' 
				class="mapArea" 
				t_status = "'.$t_status.'"
				data-path="M 288 79.5L 288 82L 298.5 82L 309 82L 309 79.5L 309 77L 298.5 77L 288 77L 288 79.5z"
				fill-color=\'"fill": "'.$color.'"\'
				t_menu = "'.$n_zuba.', '.$surface.', m_menu, false, \'\', \'\', false, \'\', \'\', false, \'\', \'\'"';
				//для DrawTeethMapMenu 
				//$n_zuba, $surface, 't_menu'   
				//если true то t_surface_name
				//$n_zuba, NONE, 1
				//Если true то t_surface_name
				//$n_zuba, $surface, 0
				//Если $DrawMenu true то DrawTeethMapMenu
				//$n_zuba, $surface, $menu
				
				/*	<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';*/	
		/*echo					
				'
				t_menuA = "Меню"
				>
				</div>
				';*/
		
		
		echo '</div>';
		

		echo '
			<script>  

				function refreshTeeth(stat, nzuba, surf) {
					var implant = $("input[name=implant]:checked").val();
					var alien = $("input[name=alien]:checked").val();
					var zo = $("input[name=zo]:checked").val();
					var shinir = $("input[name=shinir]:checked").val();
					var podvizh = $("input[name=podvizh]:checked").val();
                    $.ajax({  
                        url: "teeth_map_svg_edit.php",  
						method: "POST",
                        cache: false,  
						data:
							{
								status:stat,
								n_zuba:nzuba,
								surface:surf,

								implant:implant,
								
								alien:alien,
								zo:zo,
								shinir:shinir,
								podvizh:podvizh,
							},
                        success: function(html){  
                            $("#teeth_map").html(html);  
                        }  
                    });  
                };  
                  
			</script> 
		';			
	
//$time_arr[] = microtime(true);
//for ($i=1; $i<count($time_arr); $i++){
	//print_r ("Время $i = ".(round(($time_arr[$i]-$time_arr[$i-1]), 4))." секунд(ы)...<br />");	
//}
	}

?>