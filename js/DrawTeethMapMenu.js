		
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
		var Status4All;
		
		/* засунем сразу все элементы в переменные, чтобы скрипту не приходилось их каждый раз искать при кликах */
		//var overlay = $('#overlay'); // подложка, должна быть одна на странице
		//var open_modal = $('.open_modal'); // все ссылки, которые будут открывать окна
		//var close = $('.modal_close, #overlay'); // все, что закрывает модальное окно, т.е. крестик и оверлэй-подложка
		//var modal = $('.modal_div'); // все скрытые модальные окна
		
		var overlay = $('#overlay'); // подложка, должна быть одна на странице
		$('.open_modal').live('click', function(event){
			event.preventDefault(); // вырубаем стандартное поведение
			var div = $(this).attr('href'); // возьмем строку с селектором у кликнутой ссылки
			
			Status4All = $(this).attr('id');
			ajax({
				url:"t_surface_status_post_ajax.php",
				statbox:"t_summ_status",
				method:"POST",
				data:
				{
					stat_id:$(this).attr('id'),
				},
				success:function(data){
					document.getElementById("t_summ_status").innerHTML=data;
				}
			});
			
			$('#overlay').fadeIn(400, //показываем оверлэй
			function(){ // после окончания показывания оверлэя
				$(div) // берем строку с селектором и делаем из нее jquery объект
				.css('display', 'block') 
				.animate({opacity: 1, top: '50%'}, 200); // плавно показываем
			});
		});
		
		/*$(document).ready(function() {

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
					if ((tooth_status_key != 3) &&  (tooth_status_key != 22) &&  (tooth_status_key != 23) &&  (tooth_status_key != 24) &&  (tooth_status_key != 25) &&  (tooth_status_key != 26)){
						t_menu += "<td class='cellsBlockHover'>"+
							"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
								"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' /> "+tooth_status_arr[tooth_status_key]['descr']+
							"</a>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
						"</td>"+
						"<td class='cellsBlockHover'>"+
							"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
						if (tooth_status_key == '23'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' />"+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='shinir' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
						if (tooth_status_key == '24'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' />"+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='podvizh' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
						if (tooth_status_key == '25'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' />"+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='retein' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
							"</td>";
						}
						if (tooth_status_key == '26'){
							t_menu += "<td class='cellsBlockHover'>"+
								"<a href='#' id='refresh' onclick=\"refreshTeeth("+tooth_status_key+", '"+func_n_zuba+"', '"+func_surface+"')\" class='ahref'>"+
									"<img src='img/tooth_state/"+tooth_status_arr[tooth_status_key]['img']+"' border='0' />"+tooth_status_arr[tooth_status_key]['descr']+
								"</a>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<input type='checkbox' name='skomplect' value='1'>"+
							"</td>"+
							"<td class='cellsBlockHover'>"+
								"<a href='#modal2' class='open_modal' id='"+tooth_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
					"<a href='#modal2' class='open_modal' id='alien'><img src='img/list.jpg' border='0'/></a>"+
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
					"<a href='#modal2' class='open_modal' id='reset'><img src='img/list.jpg' border='0'/></a>"+
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
						"<a href='#modal2' class='open_modal' id='"+root_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
							"<a href='#modal2' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
							"<a href='#modal2' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
							"<a href='#modal2' class='open_modal' id='"+surface_status_key+"'><img src='img/list.jpg' border='0'/></a>"+
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
							<a href='#modal2' class='open_modal' id='menu'><img src='img/list.jpg' border='0'/></a>
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
					
					var client = document.getElementById("client").value;
					
					//alert(client);
					
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
								
								client: client,

							},
						success: function(html){
							//alert(html);
							$.ajax({  
								url: "teeth_map_svg_edit.php",  
								method: "POST",
								cache: false,  
								data:
									{
										client: client,
									},
								success: function(html){  
									//скрываем модальные окна
									$("#modal1, #modal2") // все модальные окна
										.animate({opacity: 0, top: '45%'}, 50, // плавно прячем
											function(){ // после этого
												$(this).css('display', 'none');
												$('#overlay').fadeOut(50); // прячем подложку
											}
										);
									//отрисовка
									$("#teeth_map").html(html); 
									//снимаем галочки с чекбоксов
									$("input[name=t11]").removeAttr("checked");
									$("input[name=t12]").removeAttr("checked");
									$("input[name=t13]").removeAttr("checked");
									$("input[name=t14]").removeAttr("checked");
									$("input[name=t15]").removeAttr("checked");
									$("input[name=t16]").removeAttr("checked");
									$("input[name=t17]").removeAttr("checked");
									$("input[name=t18]").removeAttr("checked");
									
									$("input[name=t21]").removeAttr("checked");
									$("input[name=t22]").removeAttr("checked");
									$("input[name=t23]").removeAttr("checked");
									$("input[name=t24]").removeAttr("checked");
									$("input[name=t25]").removeAttr("checked");
									$("input[name=t26]").removeAttr("checked");
									$("input[name=t27]").removeAttr("checked");
									$("input[name=t28]").removeAttr("checked");
									
									$("input[name=t31]").removeAttr("checked");
									$("input[name=t32]").removeAttr("checked");
									$("input[name=t33]").removeAttr("checked");
									$("input[name=t34]").removeAttr("checked");
									$("input[name=t35]").removeAttr("checked");
									$("input[name=t36]").removeAttr("checked");
									$("input[name=t37]").removeAttr("checked");
									$("input[name=t38]").removeAttr("checked");
									
									$("input[name=t41]").removeAttr("checked");
									$("input[name=t42]").removeAttr("checked");
									$("input[name=t43]").removeAttr("checked");
									$("input[name=t44]").removeAttr("checked");
									$("input[name=t45]").removeAttr("checked");
									$("input[name=t46]").removeAttr("checked");
									$("input[name=t47]").removeAttr("checked");
									$("input[name=t48]").removeAttr("checked");
									
									$("input[name=implant]").removeAttr("checked");

								}  
							}); 
                        }  
                    }); 
				};
		