		
		//Скрываем меню со статусами
		$('.point').find('.close').live('click', function(){
			var t = $(this),
				parent = t.parent('.point');
			
			parent.stop( true , true ).fadeOut(function(){
				parent.remove();
			});
			return false;
		});
		
		//
		/*var overlay = $('#overlay'); // подложка, должна быть одна на странице
		$('.open_modal').live('click', function(event){
			event.preventDefault(); // вырубаем стандартное поведение
			var div = $(this).attr('href'); // возьмем строку с селектором у кликнутой ссылки
			overlay.fadeIn(400, //показываем оверлэй
			function(){ // после окончания показывания оверлэя
				$(div) // берем строку с селектором и делаем из нее jquery объект
				.css('display', 'block') 
				.animate({opacity: 1, top: '50%'}, 200); // плавно показываем
			});
		});*/
		
		function CompileMenu (func_n_zuba, func_surface){
			
			var m_menu = "";
			var t_menu = "";
			var r_menu = "";
			var s_menu = "";
			var first = "";			
			
			var menu_arr = {};
			
			//
			for (var tooth_status_key in tooth_status_arr) {
				if ((tooth_status_key != 6) && (tooth_status_key != 7)){
					t_menu += "<tr>";
					if ((tooth_status_key != 3) &&  (tooth_status_key != 22)){
						t_menu += "<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
								"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' /> "+tooth_status_arr[tooth_status_key]['descr']+
							"</a>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#modal1' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
						"</td>";
					}else{
						if (tooth_status_key == '3'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' /> "+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='implant' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal1' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
						if (tooth_status_key == '22'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' />"+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='zo' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal1' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
					}
				}
				t_menu += "</tr>";
			}
			//Про Чужого
			t_menu += "</tr>"+
				"<td class='cellsBlockHover'>"+
					"<img src='img/tooth_state/alien.png' border='0' />Чужой"+
				"</td>"+
				"<td class='cellsBlockHover'>"+
					"<input type='checkbox' name='alien' value='1'>"+
				"</td>"+
				"<td class='cellsBlockHover'>"+
					"<a href='#modal1' class='open_modal' id='alien'><img src='img/list.jpg' border='0'/></a>"+
				"</td>"+
			"</tr>";
					
			t_menu += "<tr>"+
				"<td class='cellsBlockHover'>"+
					"<a href='#' id='refresh' onclick=\"refreshTeeth(0, '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
						"<img src='img/tooth_state/reset.png' border='0' />Сбросить"+
					"</a>"+
				"</td>"+
				"<td class='cellsBlockHover'>"+
				"</td>"+
				"<td class='cellsBlockHover'>"+
					"<a href='#modal1' class='open_modal' id='reset'><img src='img/list.jpg' border='0'/></a>"+
				"</td>"+
			"</tr>";
			
			
			//
			for (var root_status_key in root_status_arr) {
				r_menu += "<tr>"+
					"<td class='cellsBlockHover'>"+
						"<a href='#' id='refresh' onclick=\"refreshTeeth("+root_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
							"<img src='img/root_state/"+root_status_arr[root_status_key]['img']+"' border='0' /> "+root_status_arr[root_status_key]['descr']+
						"</a>"+
					"</td>"+
					"<td class='cellsBlockHover'>"+
					"</td>"+
					"<td class='cellsBlockHover'>"+
						"<a href='#modal1' class='open_modal' id='"+root_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
					"</td>"+
				"</tr>";
			}

			//
			for (var surface_status_key in surface_status_arr) {
				//отказались от использования статуса Коронка (69) к поверхности
				if ((surface_status_key != 69) && (surface_status_key != 72) && (surface_status_key != 73) && (surface_status_key != 74) && (surface_status_key != 75) && (surface_status_key != 76)){
					s_menu += "<tr>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=\"refreshTeeth("+surface_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
								"<img src='img/surface_state/"+surface_status_arr[surface_status_key]['img']+"' border='0' /> "+surface_status_arr[surface_status_key]['descr']+
							"</a>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#modal1' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
						"</td>"+
					"</tr>";
				}
				if (((surface_status_key == 72)  || (surface_status_key == 73)) && (func_surface == 'surface1')){
					s_menu += "<tr>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=\"refreshTeeth("+surface_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
								"<img src='img/surface_state/"+surface_status_arr[surface_status_key]['img']+"' border='0' /> "+surface_status_arr[surface_status_key]['descr']+
							"</a>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#modal1' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
						"</td>"+
					"</tr>";
				}
				if (((surface_status_key == 74) || (surface_status_key == 75) || (surface_status_key == 76)) && ((func_surface == 'top1') || (func_surface == 'top2') || (func_surface == 'top12'))){
					s_menu += "<tr>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=\"refreshTeeth("+surface_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
								"<img src='img/surface_state/"+surface_status_arr[surface_status_key]['img']+"' border='0' /> "+surface_status_arr[surface_status_key]['descr']+
							"</a>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#modal1' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
						"</td>"+
					"</tr>";
				}
			}
			
			/*$actions_stomat = SelDataFromDB('actions_stomat', '', '');
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
			}*/
			menu_arr['t_menu'] = t_menu;
			menu_arr['r_menu'] = r_menu;
			menu_arr['s_menu'] = s_menu;
			menu_arr['m_menu'] = m_menu;
			
			//alert(t_menu);
			return menu_arr;
		}
		
		
		function DrawTeethMapMenu (param) {
			
			var rezult_menu = "<div class='cellsBlock4'>"+
				"<div class='cellLeftTF' style=vertical-align: top;>"+
					"<table>";
			//alert(param);
			
			var param_array = param.split(", ");

			//номер зуба
			var n_zuba = param_array[0];
			//поверхность
			var surface = param_array[1];
			//
			var menu = param_array[2];
			//
			var draw_t_surface_name = param_array[3];
			//
			var draw_t_surface_name_surface = param_array[4];
			//
			var draw_t_surface_name_sw = param_array[5];
			//
			var draw_t_surface_name_right = param_array[6];
			//
			var draw_t_surface_name_surface_right = param_array[7];
			//
			var draw_t_surface_name_sw_right = param_array[8];
			//
			var DrawMenu_right = param_array[9];
			//
			var DrawMenu_surface_right = param_array[10];
			//
			var DrawMenu_menu_right = param_array[11];
			
			//alert(menu);
			
			//тут !!! вставить название
			
			var res = CompileMenu(n_zuba, surface);
			
			if (menu == 't_menu'){
				rezult_menu += res['t_menu'];
			}
			if (menu == 'r_menu'){
				rezult_menu += res['r_menu'];
			}
			if (menu == 's_menu'){
				rezult_menu += res['s_menu'];
			}
			if (menu == 'first'){
				//$first = '';	
			}
			if (menu == 'm_menu'){
				rezult_menu += res['m_menu'];		
			}
			
			rezult_menu += "</table>"+
				"</div>";
			
			//правая колонка меню
			if (draw_t_surface_name_right != 'false'){
				rezult_menu += "<div class='cellRight' style='vertical-align: top;'>"+
						"<table>";
						
				//тут !!! вставить название
				
				if (DrawMenu_right != 'false'){		
				
					var menu_arr_right = CompileMenu(n_zuba, DrawMenu_surface_right);	
					
					if (DrawMenu_menu_right == 't_menu'){
						rezult_menu += menu_arr_right['t_menu'];
					}
					if(DrawMenu_menu_right == 'r_menu'){
						rezult_menu += menu_arr_right['r_menu'];
					}
					if(DrawMenu_menu_right == 's_menu'){
						rezult_menu += menu_arr_right['s_menu'];
					}
					if(DrawMenu_menu_right == 'first'){
						//first = '';			
					}
					if(DrawMenu_menu_right == 'm_menu'){
						rezult_menu += menu_arr_right['m_menu'];			
					}				
				}

				rezult_menu += "</table>"+
					"</div>";
			}
			
			
			return rezult_menu;
		}
		
		
		