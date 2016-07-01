<?php

//t_context_menu_ajax.php
//Зубная карта меню, вызов из Аякса

	if($_POST){
		//var_dump($_POST);
		
		$_POST['param'] = str_replace(' ', '', $_POST['param']);
		$data_arr = explode(',', $_POST['param']);
		//var_dump($data_arr);
		
		$n_zuba = $data_arr[0];
		$surface = $data_arr[1];
		$menu = $data_arr[2];
		
		$draw_t_surface_name = $data_arr[3];
		$draw_t_surface_name_surface = $data_arr[4];
		$draw_t_surface_name_sw = $data_arr[5];
		
		$draw_t_surface_name_right = $data_arr[6];
		$draw_t_surface_name_surface_right = $data_arr[7];
		$draw_t_surface_name_sw_right = $data_arr[8];
		
		$DrawMenu_right = $data_arr[9];
		$DrawMenu_surface_right = $data_arr[10];
		$DrawMenu_menu_right = $data_arr[11];
		
		
		//echo $DrawMenu_right;
		
		
		include_once 'DBWork.php';
		include_once 't_surface_name.php';
		

		
		function CompileMenu ($func_n_zuba, $func_surface){
			require 'tooth_status.php';
			require 'root_status.php';
			require 'surface_status.php';

			$m_menu = '';
			$t_menu = '';
			$r_menu = '';
			$s_menu = '';
			$first = '';			
			//var_dump($tooth_status);
			
			$menu_arr = array();
			
			foreach ($tooth_status as $key => $value){
				if (($key != '6') && ($key != '7')){
					$t_menu .= "<tr>";
					if (($key != '3') &&  ($key != '22')){
						$t_menu .= "
						<td class='cellsBlockHover'>
							<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
								<img src='img/tooth_state/{$value['img']}' border='0' />{$value['descr']}
							</a>
						</td>
						<td class='cellsBlockHover'>
						</td>
						<td class='cellsBlockHover'>
							<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
						</td>
						";
					}else{
						if ($key == '3'){
							$t_menu .= "
							<td class='cellsBlockHover'>
								<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
									<img src='img/tooth_state/{$value['img']}' border='0' />{$value['descr']}
								</a>
							</td>
							<td class='cellsBlockHover'>
								<input type='checkbox' name='implant' value='1'>
							</td>
							<td class='cellsBlockHover'>
								<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
							</td>
							";
						}elseif ($key == '22'){
							$t_menu .= "
							<td class='cellsBlockHover'>
								<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
									<img src='img/tooth_state/{$value['img']}' border='0' />{$value['descr']}
								</a>
							</td>
							<td class='cellsBlockHover'>
								<input type='checkbox' name='zo' value='1'>
							</td>
							<td class='cellsBlockHover'>
								<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
							</td>
							";
						}
					}
				}
				$t_menu .= "</tr>";
			}
			//Про Чужого
			$t_menu .= "
			</tr>
				<td class='cellsBlockHover'>
					<!--<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>-->
						<img src='img/tooth_state/alien.png' border='0' />Чужой 
					<!--</a>-->
				</td>
				<td class='cellsBlockHover'>
					<input type='checkbox' name='alien' value='1'>
				</td>
				<td class='cellsBlockHover'>
					<a href='#modal1' class='open_modal' id='alien'><img src='img/list.jpg' border='0'/></a>
				</td>
			</tr>
			";
					
			$t_menu .= "
			<tr>
				<td class='cellsBlockHover'>
					<a href='#' id='refresh' onclick=refreshTeeth(0,'{$func_n_zuba}','{$func_surface}') class='ahref'>
						<img src='img/tooth_state/reset.png' border='0' />Сбросить
					</a>
				</td>
				<td class='cellsBlockHover'>
				</td>	
				<td class='cellsBlockHover'>
					<a href='#modal1' class='open_modal' id='reset'><img src='img/list.jpg' border='0'/></a>
				</td>
			</tr>
			";
			
			foreach ($root_status as $key => $value){
				$r_menu .= "
				<tr>
					<td class='cellsBlockHover'>
						<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
							<img src='img/root_state/{$value['img']}' border='0' />{$value['descr']}
						</a>
					</td>
					<td class='cellsBlockHover'>
					</td>
					<td class='cellsBlockHover'>
						<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
					</td>
				</tr>
				";
			}

			foreach ($surface_status as $key => $value){
				//отказались от использования статуса Коронка (69) к поверхности
				if (($key != 69) && ($key != 72) && ($key != 73) && ($key != 74) && ($key != 75) && ($key != 76)){
					$s_menu .= "
					<tr>
						<td class='cellsBlockHover'>
							<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
								<img src='img/surface_state/{$value['img']}' border='0' />{$value['descr']}
							</a>
						</td>
						<td class='cellsBlockHover'>
						</td>
						<td class='cellsBlockHover'>
							<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
						</td>
					</tr>
					";
				}
				if ((($key == 72)  || ($key == 73)) && ($func_surface == 'surface1')){
					$s_menu .= "
					<tr>
						<td class='cellsBlockHover'>
							<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
								<img src='img/surface_state/{$value['img']}' border='0' />{$value['descr']}
							</a>
						</td>
						<td class='cellsBlockHover'>
						</td>
						<td class='cellsBlockHover'>
							<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
						</td>
					</tr>
					";
				}
				if ((($key == 74) || ($key == 75)) || ($key == 76)) && (($func_surface == 'top1') || ($func_surface == 'top2') || ($func_surface == 'top12'))){
					$s_menu .= "
					<tr>
						<td class='cellsBlockHover'>
							<a href='#' id='refresh' onclick=refreshTeeth({$key},'{$func_n_zuba}','{$func_surface}') class='ahref'>
								<img src='img/surface_state/{$value['img']}' border='0' />{$value['descr']}
							</a>
						</td>
						<td class='cellsBlockHover'>
						</td>
						<td class='cellsBlockHover'>
							<a href='#modal1' class='open_modal' id='{$key}'><img src='img/list.jpg' border='0'/></a>
						</td>
					</tr>
					";
				}
			}
			
			$actions_stomat = SelDataFromDB('actions_stomat', '', '');
			//var_dump ($actions_stomat);
			if ($actions_stomat != 0){
				for ($i = 0; $i < count($actions_stomat); $i++){
					$m_menu .= " 
					<tr>
						<td class='cellsBlockHover'>
							".$actions_stomat[$i]['full_name']."
						</td>
						<td class='cellsBlockHover'>
							<input type='checkbox' name='action{$actions_stomat[$i]['id']}' value='1'>
						</td>
						<td class='cellsBlockHover'>
							<a href='#modal1' class='open_modal' id='menu'><img src='img/list.jpg' border='0'/></a>
						</td>
					</tr>
					";
				}
			}
			$menu_arr['t_menu'] = $t_menu;
			$menu_arr['r_menu'] = $r_menu;
			$menu_arr['s_menu'] = $s_menu;
			$menu_arr['m_menu'] = $m_menu;
			
			
			return $menu_arr;
		}
		
		$menu_arr = CompileMenu($n_zuba, $surface);
		
		echo '
			<div class=\'cellsBlock4\'>
				<div class=\'cellLeftTF\' style="vertical-align: top;">
					<table>';
					
		if ($draw_t_surface_name != 'false'){
			
			echo '
						<tr>
							<td colspan="3" style="border: 2px solid #BEBEBE; padding:2px;">
								'.t_surface_name($n_zuba.$draw_t_surface_name_surface, $draw_t_surface_name_sw).'
							</td>
						</tr>';
		}

		if ($menu == 't_menu'){
			echo $menu_arr['t_menu'];
		}elseif($menu == 'r_menu'){
			echo $menu_arr['r_menu'];
		}elseif($menu == 's_menu'){
			echo $menu_arr['s_menu'];
		}elseif($menu == 'first'){
			$first = '';			
		}elseif($menu == 'm_menu'){
			echo $menu_arr['m_menu'];			
		}
	}

		echo '
					</table>
				</div>';
				
		if ($draw_t_surface_name_right != 'false'){
			echo '
				<div class=\'cellRight\' style="vertical-align: top;">
					<table>';
			echo '
						<tr>
							<td colspan="3" style="border: 2px solid #BEBEBE; padding:2px;">
								'.t_surface_name($n_zuba.$draw_t_surface_name_surface_right, $draw_t_surface_name_sw_right).'
							</td>
						</tr>';
			if ($DrawMenu_right != 'false'){		
				$menu_arr_right = CompileMenu($n_zuba, $DrawMenu_surface_right);	
				if ($DrawMenu_menu_right == 't_menu'){
					echo $menu_arr_right['t_menu'];
				}elseif($DrawMenu_menu_right == 'r_menu'){
					echo $menu_arr_right['r_menu'];
				}elseif($DrawMenu_menu_right == 's_menu'){
					echo $menu_arr_right['s_menu'];
				}elseif($DrawMenu_menu_right == 'first'){
					$first = '';			
				}elseif($DrawMenu_menu_right == 'm_menu'){
					echo $menu_arr_right['m_menu'];			
				}				
			}
			echo '
					</table>
				</div>';
		}
		
		echo '
			</div>
			<!-- Модальные окна -->
			<div id="modal1" class="modal_div">
				<span class="modal_close">X</span>
					
						<h3>Выбор нескольких сегментов зубной формулы</h3>
						<b>Статус: </b>
						<div id="t_summ_status"></div>


							<table>
								<tr>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													18
												</td>
												<td style="border: 1px solid #BEBEBE;">
													17
												</td>
												<td style="border: 1px solid #BEBEBE;">
													16
												</td>
												<td style="border: 1px solid #BEBEBE;">
													15
												</td>
												<td style="border: 1px solid #BEBEBE;">
													14
												</td>
												<td style="border: 1px solid #BEBEBE;">
													13
												</td>
												<td style="border: 1px solid #BEBEBE;">
													12
												</td>
												<td style="border: 1px solid #BEBEBE;">
													11
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t18" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t17" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t16" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t15" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t14" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t13" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t12" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t11" value="1">
												</td>
											</tr>
										</table>
									</td>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													21
												</td>
												<td style="border: 1px solid #BEBEBE;">
													22
												</td>
												<td style="border: 1px solid #BEBEBE;">
													23
												</td>
												<td style="border: 1px solid #BEBEBE;">
													24
												</td>
												<td style="border: 1px solid #BEBEBE;">
													25
												</td>
												<td style="border: 1px solid #BEBEBE;">
													26
												</td>
												<td style="border: 1px solid #BEBEBE;">
													27
												</td>
												<td style="border: 1px solid #BEBEBE;">
													28
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t21" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t22" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t23" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t24" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t25" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t26" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t27" value="1">
												</td>
													<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t28" value="1">
												</td>
											</tr>
										</table>
										</td>
								</tr>
								<tr>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													48
												</td>
												<td style="border: 1px solid #BEBEBE;">
													47
												</td>
												<td style="border: 1px solid #BEBEBE;">
													46
												</td>
												<td style="border: 1px solid #BEBEBE;">
													45
												</td>
												<td style="border: 1px solid #BEBEBE;">
													44
												</td>
												<td style="border: 1px solid #BEBEBE;">
													43
												</td>
												<td style="border: 1px solid #BEBEBE;">
													42
												</td>
												<td style="border: 1px solid #BEBEBE;">
													41
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t48" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t47" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t46" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t45" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t44" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t43" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t42" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t41" value="1">
												</td>
											</tr>
										</table>
									</td>
									<td>
										<table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													31
												</td>
												<td style="border: 1px solid #BEBEBE;">
													32
												</td>
												<td style="border: 1px solid #BEBEBE;">
													33
												</td>
												<td style="border: 1px solid #BEBEBE;">
													34
												</td>
												<td style="border: 1px solid #BEBEBE;">
													35
												</td>
												<td style="border: 1px solid #BEBEBE;">
													36
												</td>
												<td style="border: 1px solid #BEBEBE;">
													37
												</td>
												<td style="border: 1px solid #BEBEBE;">
													38
												</td>
											</tr>
											<tr>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t31" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t32" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t33" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t34" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t35" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t36" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t37" value="1">
												</td>
												<td style="border: 1px solid #BEBEBE;">
													<input type="checkbox" name="t38" value="1">
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<input type="checkbox" name="implant" value="1"> + имплант
									</td>
								</tr>
							</table>
						<a href="#" class="b" onclick="refreshAllTeeth()">Применить</a>
					
			</div>


			<!-- Подложка только одна -->
			<div id="overlay"></div>

			<script type="text/javascript">
			 var Status4All;
				$(document).ready(function() { // запускаем скрипт после загрузки всех элементов
					/* засунем сразу все элементы в переменные, чтобы скрипту не приходилось их каждый раз искать при кликах */
					var overlay = $(\'#overlay\'); // подложка, должна быть одна на странице
					var open_modal = $(\'.open_modal\'); // все ссылки, которые будут открывать окна
					var close = $(\'.modal_close, #overlay\'); // все, что закрывает модальное окно, т.е. крестик и оверлэй-подложка
					var modal = $(\'.modal_div\'); // все скрытые модальные окна

					 open_modal.click( function(event){ // ловим клик по ссылке с классом open_modal
						 event.preventDefault(); // вырубаем стандартное поведение
						 var div = $(this).attr(\'href\'); // возьмем строку с селектором у кликнутой ссылки
						 
						 //alert($(this).attr(\'id\')); //!!!
						 
						//document.getElementById("t_summ_status").innerHTML=$(this).attr(\'id\'); //!!!
						 Status4All = $(this).attr(\'id\');
											ajax({
												url:"t_surface_status_post_ajax.php",
												statbox:"t_summ_status",
												method:"POST",
												data:
												{
													stat_id:$(this).attr(\'id\'),
												},
												success:function(data){
													document.getElementById("t_summ_status").innerHTML=data;
												}
											});
						 
						 overlay.fadeIn(400, //показываем оверлэй
							 function(){ // после окончания показывания оверлэя
								 $(div) // берем строку с селектором и делаем из нее jquery объект
									 .css(\'display\', \'block\') 
									 .css(\'z-index\', \'103\') 
									 .animate({opacity: 1, top: \'50%\'}, 200); // плавно показываем
						 });
					 });

					 close.click( function(){ // ловим клик по крестику или оверлэю
							modal // все модальные окна
							 .animate({opacity: 0, top: \'45%\'}, 200, // плавно прячем
								 function(){ // после этого
									 $(this).css(\'display\', \'none\');
									 overlay.fadeOut(400); // прячем подложку
								 }
							 );
					 });
				});
				
				function refreshAllTeeth(){
					var t_stat_value11 = $("input[name=t11]:checked").val();
					var t_stat_value12 = $("input[name=t12]:checked").val();
					var t_stat_value13 = $("input[name=t13]:checked").val();
					var t_stat_value14 = $("input[name=t14]:checked").val();
					var t_stat_value15 = $("input[name=t15]:checked").val();
					var t_stat_value16 = $("input[name=t16]:checked").val();
					var t_stat_value17 = $("input[name=t17]:checked").val();
					var t_stat_value18 = $("input[name=t18]:checked").val();
					
					var t_stat_value21 = $("input[name=t21]:checked").val();
					var t_stat_value22 = $("input[name=t22]:checked").val();
					var t_stat_value23 = $("input[name=t23]:checked").val();
					var t_stat_value24 = $("input[name=t24]:checked").val();
					var t_stat_value25 = $("input[name=t25]:checked").val();
					var t_stat_value26 = $("input[name=t26]:checked").val();
					var t_stat_value27 = $("input[name=t27]:checked").val();
					var t_stat_value28 = $("input[name=t28]:checked").val();
					
					var t_stat_value31 = $("input[name=t31]:checked").val();
					var t_stat_value32 = $("input[name=t32]:checked").val();
					var t_stat_value33 = $("input[name=t33]:checked").val();
					var t_stat_value34 = $("input[name=t34]:checked").val();
					var t_stat_value35 = $("input[name=t35]:checked").val();
					var t_stat_value36 = $("input[name=t36]:checked").val();
					var t_stat_value37 = $("input[name=t37]:checked").val();
					var t_stat_value38 = $("input[name=t38]:checked").val();
					
					var t_stat_value41 = $("input[name=t41]:checked").val();
					var t_stat_value42 = $("input[name=t42]:checked").val();
					var t_stat_value43 = $("input[name=t43]:checked").val();
					var t_stat_value44 = $("input[name=t44]:checked").val();
					var t_stat_value45 = $("input[name=t45]:checked").val();
					var t_stat_value46 = $("input[name=t46]:checked").val();
					var t_stat_value47 = $("input[name=t47]:checked").val();
					var t_stat_value48 = $("input[name=t48]:checked").val();
					
					
					var implant = $("input[name=implant]:checked").val();
					
					//alert(Status4All);
					
					$.ajax({  
                        url: "teeth_map_svg_edit_status_all.php",  
						method: "POST",
                        cache: false,  
						data:
							{
								t_stat_value11:t_stat_value11,
								t_stat_value12:t_stat_value12,
								t_stat_value13:t_stat_value13,
								t_stat_value14:t_stat_value14,
								t_stat_value15:t_stat_value15,
								t_stat_value16:t_stat_value16,
								t_stat_value17:t_stat_value17,
								t_stat_value18:t_stat_value18,
								
								t_stat_value21:t_stat_value21,
								t_stat_value22:t_stat_value22,
								t_stat_value23:t_stat_value23,
								t_stat_value24:t_stat_value24,
								t_stat_value25:t_stat_value25,
								t_stat_value26:t_stat_value26,
								t_stat_value27:t_stat_value27,
								t_stat_value28:t_stat_value28,
								
								t_stat_value31:t_stat_value31,
								t_stat_value32:t_stat_value32,
								t_stat_value33:t_stat_value33,
								t_stat_value34:t_stat_value34,
								t_stat_value35:t_stat_value35,
								t_stat_value36:t_stat_value36,
								t_stat_value37:t_stat_value37,
								t_stat_value38:t_stat_value38,
								
								t_stat_value41:t_stat_value41,
								t_stat_value42:t_stat_value42,
								t_stat_value43:t_stat_value43,
								t_stat_value44:t_stat_value44,
								t_stat_value45:t_stat_value45,
								t_stat_value46:t_stat_value46,
								t_stat_value47:t_stat_value47,
								t_stat_value48:t_stat_value48,
								
								implant:implant,
								
								status_all:Status4All,

							},
                       success: function(html){
							$.ajax({  
								url: "teeth_map_svg_edit.php",  
								method: "POST",
								cache: false,  
								data:
									{

									},
								success: function(html){  
									$("#teeth_map").html(html);  
								}  
							}); 
                        }  
                    }); 
				};
			</script>

';

?>