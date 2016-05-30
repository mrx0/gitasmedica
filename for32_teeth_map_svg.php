<?php

//for32_teeth_map_svg.php
//Зубная карта собственно отрисовка 

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
								t_menuA = "
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
									t_menuA = "
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
							t_menuA = "
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
		
?>