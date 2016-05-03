<?php

//task_edit_stomat.php
//Редактирование посещения косметолога

	require_once 'header.php';
	
	if ($enter_ok){
		if (($stom['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'tooth_status.php';
				
				$task = SelDataFromDB('journal_tooth_status', $_GET['id'], 'task');
				//var_dump($task);
				
				$closed = FALSE;
				$dop = array();
				
				if ($task !=0){
					if ($task[0]['office'] == 99){
						$office = 'Во всех';
					}else{
						$offices = SelDataFromDB('spr_office', '', '');
						//var_dump ($offices);
						//$office = $offices[0]['name'];
					}	
					echo '
						<script src="js/init.js" type="text/javascript"></script>
						<div id="status">
							<header>
								<h2>Редактировать посещение</h2>
							</header>';

					echo '
							<div id="data">';
							
					/*if ($task[0]['end_time'] == 0){
						$ended = 'Нет';
						$closed = FALSE;
					}else{
						$ended = date('d.m.y H:i', $task[0]['end_time']);
						$closed = TRUE;
					}*/
					if (!$closed){
					
						echo '
									<form action="edit_task_stomat_f.php">
										
										<div class="cellsBlock2">
											<div class="cellLeft">Дата посещения</div>
											<div class="cellRight">';
				if ($task[0]['create_time'] != 0){
					//print_r  (getdate($client[0]['birthday']));
					$bdate = getdate($task[0]['create_time']);
					echo '
						<input type="hidden" id="sel_seconds" name="sel_seconds" value="'.$bdate['seconds'].'">
						<input type="hidden" id="sel_minutes" name="sel_minutes" value="'.$bdate['minutes'].'">
						<input type="hidden" id="sel_hours" name="sel_hours" value="'.$bdate['hours'].'">';
				}else{
					$bdate = 0;
				}
				echo '<select name="sel_date" id="sel_date">';
				$i = 1;
				while ($i <= 31) {
					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
					$i++;
				}
				echo "</select>";
				// Месяц
				echo "<select name='sel_month' id='sel_month'>";
				$month = array(
					"Январь",
					"Февраль",
					"Март",
					"Апрель",
					"Май",
					"Июнь",
					"Июль",
					"Август",
					"Сентябрь",
					"Октябрь",
					"Ноябрь",
					"Декабрь"
				);
				foreach ($month as $m => $n) {
					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
				}
				echo "</select>";
				// Год
				echo "<select name='sel_year' id='sel_year'>";
				$j = 1920;
				while ($j <= 2020) {
					echo "<option value='" . $j . "'", $bdate['year'] == $j ? ' selected':'' ,">$j</option>";
					$j++;
				}
				echo '</select>

											
											</div>
										</div>
																				
										<div class="cellsBlock2">
											<div class="cellLeft">Врач</div>
											<div class="cellRight">'.WriteSearchUser('spr_workers',$task[0]['worker'], 'user').'</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Пациент</div>
											<div class="cellRight">'.WriteSearchUser('spr_clients', $task[0]['client'], 'user').'</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Филиал</div>
											<div class="cellRight">
												<select name="filial" id="filial">
													<option value="0" selected>Выберите филиал</option>';
						if ($offices !=0){
							for ($i=0;$i<count($offices);$i++){
								echo "<option value='".$offices[$i]['id']."' ", $task[0]['office'] == $offices[$i]['id'] ? 'selected' : '' ,">".$offices[$i]['name']."</option>";
							}
						}
						echo '
												</select>
											</div>
										</div>

										<div class="cellsBlock2">
											<div class="cellLeft">Описание</div>
											<div class="cellRight">
											</div>
										</div>
										';
					
						$t_f_data_db_temp = array();
						
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$time = time();
						$query = "SELECT * FROM `journal_tooth_status` WHERE `id` = '{$_GET['id']}'";
						$res = mysql_query($query) or die($q);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($t_f_data_db_temp, $arr);
							}
							$t_f_data_db = $t_f_data_db_temp[0];
						}else
							$t_f_data_db = $t_f_data_db_first;
						
						
						//ЗО и тд						
						$query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$task[0]['id']}'";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}						
						


						
						$client = $t_f_data_db['client'];
						
						//var_dump ($t_f_data_db);
						
						unset($t_f_data_db['id']);
						unset($t_f_data_db['office']);
						unset($t_f_data_db['client']);
						unset($t_f_data_db['create_time']);
						unset($t_f_data_db['create_person']);
						unset($t_f_data_db['last_edit_time']);
						unset($t_f_data_db['last_edit_person']);
						unset($t_f_data_db['worker']);
						unset($t_f_data_db['comment']);
						
						
						//Разбиваем запись с ',' на массив и записываем в новый массив
						foreach ($t_f_data_db as $key => $value){
							$surfaces_temp = explode(',', $value);
							foreach ($surfaces_temp as $key1 => $value1){
								$t_f_data[$key][$surfaces[$key1]] = $value1;
							}
						}
						
						//var_dump ($t_f_data);
						/*unset($t_f_data['id']);
						unset($t_f_data['office']);
						unset($t_f_data['client']);
						unset($t_f_data['create_time']);
						unset($t_f_data['create_person']);
						unset($t_f_data['last_edit_time']);
						unset($t_f_data['last_edit_person']);
						unset($t_f_data['worker']);
						unset($t_f_data['comment']);*/
						
						unset($dop[0]['id']);
						
						if (!empty($dop[0])){
							//var_dump($dop[0]);
							foreach($dop[0] as $key => $value){
								if ($value != '0'){
									$dop_arr = json_decode($value, true);
									//var_dump($dop_arr);
									foreach ($dop_arr as $n_key => $n_value){
										if ($n_key == 'zo'){
											$t_f_data[$key]['zo'] = $n_value;
											//$t_f_data_draw[$key]['zo'] = $n_value;
										}
									}
								}
							}
						}
						
						//var_dump ($t_f_data);		
						
						//!!!Тест. Пробуем записать в сессию.
						$_SESSION['journal_tooth_status_temp'] = $t_f_data;
					
						echo '						
								<div class="cellsBlock3">
									<div class="cellRight">Зубная формула</div>
								</div>
								<div class="cellsBlock3">
									<div class="cellRight" id="teeth_map">';
									
						//рисуем зубную формулу						
						include_once 'teeth_map_svg.php';
						DrawTeethMap($t_f_data, 1, $tooth_status, $tooth_alien_status, $surfaces, '');
						
						echo '
									</div>
								</div>';
					
						echo '
										
										
										<div class="cellsBlock2">
											<div class="cellLeft">Комментарий</div>
											<div class="cellRight">
												<textarea name="comment" id="comment" cols="35" rows="10" style="vertical-align:top; text-align:left;">'
												.$task[0]['comment'].
												'</textarea>
											</div>
										</div>';
						//Напоминания / особые отметки				
						$notes = SelDataFromDB ('notes', $_GET['id'], 'task');
						if ($notes != 0){
							echo '
							<div class="cellsBlock2">
								<div class="cellRight">
									Напоминание уже создано
								</div>
							</div>';
						}else{
							echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Создать напоминание
									<input type="checkbox" name="add_notes_show" id="add_notes_show" value="1" onclick="Add_notes_stomat_show(this)">
								</div>
								
								<div class="cellRight">
									<table id="add_notes_here" style="display:block; display:none;">
										<tr>
											<td colspan="2">
										
												<select name="add_notes_type" id="add_notes_type">
													<option value="0" selected>Выберите</option>
													<option value="1">Каласепт, Метапекс, Септомиксин (Эндосольф)</option>
													<option value="2">Временная пломба</option>
													<option value="3">Открытый зуб</option>
													<option value="4">Депульпин</option>
													<option value="5">Распломбирован под вкладку (вкладка)</option>
													<option value="6">Имплантация (ФДМ ,  абатмент, временная коронка на импланте)</option>
													<option value="7">Временная коронка</option>
													<option value="10">Установлены брекеты</option>
													<option value="8">Санированные пациенты ( поддерживающее лечение через 6 мес)</option>
													<option value="9">Прочее</option>
												</select>
											
											</td>
										</tr>
										<tr>
											<td>Месяцев</td>
											<td>Дней</td>
										</tr>
										<tr>
											<td>
												<input type="number" size="2" name="add_notes_months" id="add_notes_months" min="0" max="12" value="0">
											</td>
											<td>
												<input type="number" size="2" name="add_notes_days" id="add_notes_days" min="0" max="31" value="0">
											</td>
										</tr>
										<!--<tr>
											<td>
												<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_notes_stomat()>
											</td>
										</tr>-->
									</table>
									
								</div>
							</div>
							';
						}
										
						$removes = SelDataFromDB ('removes', $task[0]['id'], 'task');
						include_once 'WriteRemoves.php';
						echo WriteRemoves($removes);
						echo '
							<div class="cellsBlock3">
								<div class="cellLeft">
									Добавить направление
									<input type="checkbox" name="add_remove_show" id="add_remove_show" value="1" onclick="Add_remove_stomat_show(this)">
								</div>
								<div class="cellRight">
									<div id="add_remove_here" style="display:block; display:none;">
										<table id="table_container">
										</table>
										<a href="#modal1" class="open_modal b" id="">Добавить направление</a>
										<!--<input type="button" class="b" value="Добавить поле" id="add" onclick="return add_new_image('.$_SESSION['id'].');">-->
										<div id="mini"></div>
									</div>
								</div>
							</div>
							';
						
						//Первичка
						$dop = array();
						$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$task[0]['id']}'";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}	
						
						if (!empty($dop) && ($dop[0]['pervich'] == 1)){
							$checked_pervich = ' checked';
						}else{
							$checked_pervich = '';
						}
						
						echo '
							<div class="cellsBlock3">
								<div class="cellLeft">Первичный?</div>
								<div class="cellRight">
									<input type="checkbox" name="pervich" id="pervich" value="1" '.$checked_pervich.'> да
								</div>
							</div>';
							
							
						mysql_close();	
						echo '
										<br />
										<div class="cellsBlock2">
											<div class="cellLeft">Подтвердить редактирование</div>
											<div class="cellRight">
												<input type="checkbox" name="change_true">
											</div>
										</div>	

										<div class="cellsBlock2">
											<span style="font-size:80%;">
												Создана: '.date('d.m.y H:i', $task[0]['create_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['create_person'], 'user').'<br />
												Последний раз редактировалось: '.date('d.m.y H:i', $task[0]['last_edit_time']).'<br />
												Кем: '.WriteSearchUser('spr_workers', $task[0]['last_edit_person'], 'user').'
											</span>
										</div>
										<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
										<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
										<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
											
											if ($("#add_notes_show").prop("checked")){
												notes_val = 1;
											}else{
												notes_val = 0;
											}
											
											if ($("#add_remove_show").prop("checked")){
												remove_val = 1;
											}else{
												remove_val = 0;
											}
												
											if ($("#pervich").prop("checked")){
												pervich_val = 1;
											}else{
												pervich_val = 0;
											}
											
											var arrayRemoveAct = new Array();
											var arrayRemoveWorker = new Array();
										
											$(".remove_add_search").each(function() {
												if (($(this).attr("id")).indexOf("td_title") != -1){
														var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
														arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
													}
													if (($(this).attr("id")).indexOf("td_worker") != -1){
														var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
														arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
													}
											});
											
											
											ajax({
												url:"edit_task_stomat_f.php",
												statbox:"status",
												method:"POST",
												data:
												{
													id:document.getElementById("id").value,
													filial:document.getElementById("filial").value,
													comment:document.getElementById("comment").value,
													
													
													sel_date:document.getElementById("sel_date").value,
													sel_month:document.getElementById("sel_month").value,
													sel_year:document.getElementById("sel_year").value,
													
													sel_seconds:document.getElementById("sel_seconds").value,
													sel_minutes:document.getElementById("sel_minutes").value,
													sel_hours:document.getElementById("sel_hours").value,
													
													notes:notes_val,
													remove:remove_val,
													
													pervich:pervich_val,
													
													removeAct:JSON.stringify(arrayRemoveAct),
													removeWork:JSON.stringify(arrayRemoveWorker),
													
													add_notes_type:document.getElementById("add_notes_type").value,
													add_notes_months:document.getElementById("add_notes_months").value,
													add_notes_days:document.getElementById("add_notes_days").value,
													
													
													client:'.$client.',
													
													
													';
													
					
					echo							'
												},
												success:function(data){document.getElementById("status").innerHTML=data;}
											})\'
										>
									</form>';	
					}else{
						echo '<h1>Задача закрыта. Редактировать нельзя</h1><a href="it.php">Вернуться в журнал</a>';
					}
					echo '
							</div>
						</div>
						';

			echo '
				<script type="text/javascript">

				
					document.getElementById(\'add_notes_show\').checked=false;
					document.getElementById(\'add_remove_show\').checked=false;
					
					function Add_notes_stomat_show(box) {
						var vis = (box.checked) ? "block" : "none";
						document.getElementById(\'add_notes_here\').style.display = vis;
					}
					function Add_remove_stomat_show(box) {
						var vis = (box.checked) ? "block" : "none";
						document.getElementById(\'add_remove_here\').style.display = vis;
					}
				</script>
			';
						
			echo '
			
			<!-- Модальные окна -->
			<div id="modal1" class="modal_div">
				<span class="modal_close">X</span>
				<table>
					<tr>
						<td>
							Причина направления
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="input_title" id="input_title" class="search_data"  autocomplete="off" style="width: 200px;">
						</td>
					</tr>
					<tr>
						<td>
							К кому направляем
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" size="50" name="searchdata3" id="search_client3" placeholder="Введите первые три буквы для поиска" value="" class="who3"  autocomplete="off" />
							<ul id="search_result3" class="search_result3"></ul>
						</td>
					</tr>
				</table>

				
				<a href="#" class="b" id="close_mdd" onclick="AddRemoveData()" style="bottom: 10px; position: absolute;">Направить</a>

			</div>
			<!-- Подложка только одна -->
			<div id="overlay"></div>
			
			
			
			
				<script type="text/javascript">
					$(document).ready(function() { // запускаем скрипт после загрузки всех элементов
						/* засунем сразу все элементы в переменные, чтобы скрипту не приходилось их каждый раз искать при кликах */
						var overlay = $(\'#overlay\'); // подложка, должна быть одна на странице
						var open_modal = $(\'.open_modal\'); // все ссылки, которые будут открывать окна
						var close = $(\'.modal_close, #overlay, #close_mdd\'); // все, что закрывает модальное окно, т.е. крестик и оверлэй-подложка
						var modal = $(\'.modal_div\'); // все скрытые модальные окна

						 open_modal.click( function(event){ // ловим клик по ссылке с классом open_modal
							 event.preventDefault(); // вырубаем стандартное поведение
							 var div = $(this).attr(\'href\'); // возьмем строку с селектором у кликнутой ссылки
							 
	 
							 overlay.fadeIn(400, //показываем оверлэй
								 function(){ // после окончания показывания оверлэя
									 $(div) // берем строку с селектором и делаем из нее jquery объект
										 .css(\'display\', \'block\') 
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
				function AddRemoveData(){
						
					var arrayRemoveAct = new Array();
					var arrayRemoveWorker = new Array();
					var maxIndex = 1;
					
					var input_title = document.getElementById("input_title").value;
					var search_client3 = document.getElementById("search_client3").value;
					//alert(input_title);
					
					$(".remove_add_search").each(function() {
						if (($(this).attr("id")).indexOf("td_title") != -1){
								var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
								arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
							}
							if (($(this).attr("id")).indexOf("td_worker") != -1){
								var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
								arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
							}
						maxIndex = Number(IndexArr)+1;
					});
					
					arrayRemoveAct[maxIndex] = input_title;
					arrayRemoveWorker[maxIndex] = search_client3;
					
					 $(document.getElementById("table_container")).empty();
					
					arrayRemoveAct.forEach(function(item, i, arrayRemoveAct){
						$(\'<tr>\')
						.attr(\'id\',\'tr_image_\'+i)
						.css({lineHeight:\'20px\'})
						.append (
							$(\'<td>\')
							.css({paddingRight:\'5px\',width:\'190px\'})
							.append(
								$(\'<input type="text" />\')
								.css({width:\'200px\'})
								.attr(\'id\',\'td_title_\'+i)
								.attr(\'class\',\'remove_add_search\')
								.attr(\'name\',\'input_title_\'+i)
								.attr(\'value\',item)
							)		
							
						)
						
						.append (
							$(\'<td>\')
							.css({paddingRight:\'5px\',width:\'200px\'})
							.append(
								$(\'<input type="text" size="50" name="" placeholder="" autocomplete="off" />\')
									.attr(\'id\',\'td_worker_\'+i)
									.attr(\'class\',\'remove_add_search\')
									.attr(\'value\',arrayRemoveWorker[i])
							)
						)	
						
						.append (
							$(\'<td>\')
							.css({width:\'60px\'})
							.append (
								$(\'<span id="progress_\'+i+\'"><a  href="#" onclick="$(\\\'#tr_image_\'+i+\'\\\').remove();" class="ico_delete"><img src="./img/delete.png" alt="del" border="0"></a></span>\')
							)
						)
						.appendTo(\'#table_container\');
						
						
						//$("#mini").append(this + "<br>");
					});
					
					
					
					
	
				};
					
				$(function(){
						
					//Живой поиск
					$(\'.who3\').bind("change keyup input click", function() {
						//alert(123);
						if(this.value.length > 2){
							$.ajax({
								url: "FastSearchNameW.php", //Путь к обработчику
								//statbox:"status",
								type:"POST",
								data:
								{
									\'searchdata3\':this.value
								},
								response: \'text\',
								success: function(data){
									$(".search_result3").html(data).fadeIn(); //Выводим полученые данные в списке
								}
							})
						}else{
							var elem1 = $("#search_result3"); 
							elem1.hide(); 
						}
					})
						
					$(".search_result3").hover(function(){
						$(".who3").blur(); //Убираем фокус с input
					})
						
					//При выборе результата поиска, прячем список и заносим выбранный результат в input
					$(".search_result3").on("click", "li", function(){
						s_user = $(this).text();
						$(".who3").val(s_user);
						//$(".who").val(s_user).attr(\'disabled\', \'disabled\'); //деактивируем input, если нужно
						$(".search_result3").fadeOut();
					})
					//Если click за пределами результатов поиска - убираем эти результаты
					$(document).click(function(e){
						var elem = $("#search_result3"); 
						if(e.target!=elem[0]&&!elem.has(e.target).length){
							elem.hide(); 
						} 
					})
				})
					
					
					
					
					
					
					
					
					
				</script>
			
			';

			
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>