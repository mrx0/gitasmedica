<?php

//teeth_map_svg.php
//Зубная карта отрисовка с меню

	function DrawTeethMap($t_f_data_temp, $sw, $tooth_status, $tooth_alien_status, $surfaces, $n){
		//var_dump ($t_f_data_temp);
		//var_dump ($dop);
		
		include_once 'DBWork.php';
		include_once 'teeth_map_db.php';
		include_once 't_surface_name.php';
		include_once 't_surface_status.php';

		include_once 'root_status.php';
		include_once 'surface_status.php';
		include_once 't_context_menu.php';
		
		
		//var_dump ($t_f_data_temp);
		if ($n == '') $nm = '\'\'';
		else $nm = $n;

		$tooth_error = '';
		
		$t_f_data = array();
		
		$dop_arr = array();
		
		//$stat_id = $t_f_data_temp['id'];
		
		unset($t_f_data_temp['id']);
		unset($t_f_data_temp['create_time']);
		//echo "echo$sw";
		//var_dump ($surfaces);
		$t_f_data_temp_refresh = '';
		
		/*foreach ($t_f_data_temp as $key => $value){
			//$t_f_data_temp_refresh .= $key.'+'.$value.':';
			
			
			//var_dump(json_decode($value, true));
			$surfaces_temp = explode(',', $value);
			//var_dump ($surfaces_temp);
			foreach ($surfaces_temp as $key1 => $value1){
				//$t_f_data[$key] = json_decode($value, true);
				$t_f_data[$key][$surfaces[$key1]] = $value1;
			}
		}*/
		
		$t_f_data = $t_f_data_temp;
		
		
		//$t_f_data_temp_refresh = json_encode($t_f_data_temp, true);
		//$t_f_data_temp_refresh = json_encode($t_f_data_temp, true);
		//var_dump($t_f_data);
		//echo $t_f_data_temp_refresh;
		
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
		
		//<div class="text_in_map_dop" style="left: 15px; top: -9px">А</div>
		
		echo '<div class="map'.$n.'" id="map'.$n.'">
			
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
		
		/*$teeth_map_zo_temp = $teeth_map_zo_db;
		foreach ($teeth_map_zo_temp as $value){
			$teeth_map_zo[$value['tooth']]=$value['coord'];
		}*/
		
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
						//штифт имплантант
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
								t_menu = "'.$n_zuba.', '.$surface.', t_menu, true, '.$surface.', 2, false, \'\', \'\', false, \'\', \'\'"';
								//для DrawTeethMapMenu 
								//$n_zuba, $surface, 't_menu'   
								//если true то t_surface_name
								//$n_zuba, NONE, 1
								//Если true то t_surface_name
								//$n_zuba, $surface, 0
								//Если $DrawMenu true то DrawTeethMapMenu
								//$n_zuba, $surface, $menu
								
/*								<div class=\'cellsBlock4\'>
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
							//если корень и статусы кое какие плохие, где ничего нет или молочный
							if  ((mb_strstr($surface, 'root') == TRUE) && 
								(($t_f_data[$i.$j]['status'] == '1') || ($t_f_data[$i.$j]['status'] == '2') || 
								($t_f_data[$i.$j]['status'] == '18') || ($t_f_data[$i.$j]['status'] == '19') || 
								($t_f_data[$i.$j]['status'] == '9'))){
								$DrawRoots = FALSE;
							}else{
								//var_dump($i.$j.'=>'.$t_f_data[$i.$j][$surface]);
								if (isset($t_f_data[$i.$j][$surface])){
									if  ((mb_strstr($surface, 'root') == TRUE) && ($t_f_data[$i.$j][$surface] != '0')){
										if (isset($root_status[$t_f_data[$i.$j][$surface]]['color'])){
											$color = $root_status[$t_f_data[$i.$j][$surface]]['color'];
										}else{
											$tooth_error .= '<i>Ошибка на зубе <b>'.$i.$j.'</b></i><br />';
										}
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
							//Если в массиве натыкаемся НЕ на корни или если чужой, то корни не рисуем, а рисуем кружок просто
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
										//цвет для корня
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
							class="mapArea'.$n.'" 
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
							
							/*
								<div class=\'cellsBlock4\'>
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
							class="mapArea'.$n.'" 
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
		
				
		if ($tooth_error != ''){
			echo '
					<span style="background: rgba(0,255,255,0.9); color: #ff0000; padding: 3px;">
						Если вы видете это сообщение, то произошла ошибка.<br />
						Отредактируйте формулу в этом посещении<br />
						Сбросьте статусы следующих зубов и сохраните<br />
							'.$tooth_error.'
					</span>';
		}
				
		
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
								n:'.$nm.',
							},
                        success: function(html){  
                            $("#teeth_map").html(html);  
                        }  
                    });  
                };  
                  
			</script> 

		';			
	
		
	}
	
?>