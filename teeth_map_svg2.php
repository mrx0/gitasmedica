<?php

//teeth_map_svg.php
//Зубная карта отрисовка с меню

	function DrawTeethMap($t_f_data_temp, $sw){
		//var_dump ($t_f_data_temp);
		include_once 'DBWork.php';
		include_once 'teeth_map_db.php';
		include_once 't_surface_name.php';
		include_once 't_surface_status.php';
		include_once 'tooth_status.php';
		include_once 'root_status.php';
		include_once 'surface_status.php';
		include_once 't_context_menu.php';
		
		$t_f_data = array();
		
		$stat_id = $t_f_data_temp['id'];
		
		unset($t_f_data_temp['id']);
		unset($t_f_data_temp['create_time']);
		//echo "echo$sw";
		//var_dump ($surfaces);
		$t_f_data_temp_refresh = '';
		
		foreach ($t_f_data_temp as $key => $value){
			//$t_f_data_temp_refresh .= $key.'+'.$value.':';
			
			
			//var_dump(json_decode($value, true));
			$surfaces_temp = explode(',', $value);
			//var_dump ($surfaces_temp);
			foreach ($surfaces_temp as $key1 => $value1){
				//$t_f_data[$key] = json_decode($value, true);
				$t_f_data[$key][$surfaces[$key1]] = $value1;
			}
		}
		//$t_f_data_temp_refresh = json_encode($t_f_data_temp, true);
		//$t_f_data_temp_refresh = json_encode($t_f_data_temp, true);
		//var_dump($t_f_data);
		//echo $t_f_data_temp_refresh;
		
		
		echo '
			<div class="map" id="map">
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
		//var_dump ($teeth_map_temp);	
		
		//!!!ТЕСТ ИНКЛУДА ОТРИСОВКИ ЗФ
		include_once 'for32_teeth_map_svg.php';
		
		//Пробуем отрисовать менюшку !!! Она тут нужна???
		/*$color = "#000";
		$color_stroke = '#74675C';

		echo '
			<div id="'.$n_zuba.$surface.'"
				status-path=\'
				"stroke": "'.$color_stroke.'", 
				"stroke-width": '.$stroke_width.', 
				"fill-opacity": "1"\' 
				class="mapArea" 
				t_status = "'.$t_status.'"
				data-path="M 288 71.5L 288 74L 298.5 74L 309 74L 309 71.5L 309 69L 298.5 69L 288 69L 288 71.5z"
				fill-color=\'"fill": "'.$color.'"\'
				t_menu = "
					<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($key, $n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';
		echo					
				'"
				t_menu2 = "Меню"
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
				t_menu = "
					<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($key, $n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';				
		echo					
				'"
				t_menu2 = "Меню"
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
				t_menu = "
					<div class=\'cellsBlock4\'>
						<div class=\'cellLeft\'>';
				
		DrawTeethMapMenu($key, $n_zuba, $surface, 'm_menu');	
		echo '
						</div>
					</div>';	
		echo					
				'"
				t_menu2 = "Меню"
				>
				</div>
				';
		
		*/
		echo '</div>';
		
		
		echo '
			<script>  

				function refreshTeeth(stat, nzuba, surf) {
					var implant = $("input[name=implant]:checked").val();
					var alien = $("input[name=alien]:checked").val();
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
								stat_id:'.$stat_id.',
								alien:alien,
							},
                        success: function(html){  
                            $("#teeth_map").html(html);  
                        }  
                    });  
                };  
                  
			</script> 
		';			
	
		
	}

			/*Старое меню
			<a href='it.php' class='ahref'><img src='img/tooth_state/1.png' border='0' />Отсутствует</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/2.png' border='0' />Молочный</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/3.png' border='0' />Удалён</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/4.png' border='0' />Имплантант</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/5.png' border='0' />Форм. десны</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/6.png' border='0' />Коронка</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/7.png' border='0' />Культ. вкладка</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/8.png' border='0' />Бюгел. протез</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/9.png' border='0' />Мост</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/10.png' border='0' />Искусственный</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/11.png' border='0' />Чужая коронка</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/12.png' border='0' />Чужой мост</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/13.png' border='0' />Чужой бюгел.</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/14.png' border='0' />Полный съем.</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/15.png' border='0' />Частич. съем.</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/16.png' border='0' />Чужой пол.</a><br />
			<a href='it.php' class='ahref'><img src='img/tooth_state/17.png' border='0' />Чужой час.</a><br />";*/
	
	
?>