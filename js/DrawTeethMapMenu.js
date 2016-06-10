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
							"<a href='#' id='refresh' onclick=refreshTeeth("+tooth_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
								"<a href='#' id='refresh' onclick=refreshTeeth("+tooth_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
								"<a href='#' id='refresh' onclick=refreshTeeth("+tooth_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
					"<a href='#' id='refresh' onclick=refreshTeeth(0, "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
						"<a href='#' id='refresh' onclick=refreshTeeth("+root_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
				if ((surface_status_key != 69) && (surface_status_key != 72) && (surface_status_key != 73) && (surface_status_key != 74) && (surface_status_key != 75)){
					s_menu += "<tr>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=refreshTeeth("+surface_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
							"<a href='#' id='refresh' onclick=refreshTeeth("+surface_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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
				if (((surface_status_key == 74) || (surface_status_key == 75)) && ((func_surface == 'top1') || (func_surface == 'top2') || (func_surface == 'top12'))){
					s_menu += "<tr>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=refreshTeeth("+surface_status_key+", "+func_n_zuba+", "+func_surface+") class='ahref'>"+
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

			var param_array = param.split(",");

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
			
			//alert(n_zuba);
			
			var res = CompileMenu(n_zuba, surface);
			
			return (res['t_menu']);
			
		}