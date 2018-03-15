<?php

//stat_cosm_ex2.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
		if (($report['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices_j = SelDataFromDB('spr_filials', '', '');

			if ($_POST){
			}else{
				echo '
					<header style="margin-bottom: 5px;">
						<h1>Статистика с фильтром</h1>
					</header>';

				echo '
						<div id="data">';
				echo '
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);">
								
								<li style="margin-bottom: 10px;">
									Выберите условие
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Выберите период
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div style="margin-bottom: 10px;">
											C <input type="text" id="datastart" name="datastart" class="dateс" value="'.date("01.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" disabled>
											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" disabled>
										</div>
										<div style="vertical-align: middle; color: #333;">
											<input type="checkbox" name="all_time" value="1" checked> <span style="font-size:80%;">За всё время</span>
										</div>
									</div>
								</li>
								
								<li class="filterBlock" style="display: none;">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Возраст пациентов<br>
										<span style="font-size: 80%; color: red;">не доступно</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div style="margin-bottom: 10px;">
											От <input type="number" value="18" min="1" max="99" size="2" id="agestart" name="agestart" class="dateс" disabled>
											 &bull;до <input type="number" value="45" min="1" max="99" size="2" id="ageend" name="ageend" class="dateс" disabled>
										</div>
										<div style="vertical-align: middle; color: #333;">
											<input type="checkbox" name="all_age" value="1" checked disabled> <span style="font-size:80%;">Любой возраст</span>
										</div>
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="35" name="searchdata2" id="search_worker" placeholder="Введите первые три буквы для поиска" value="" class="who2" autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Филиал
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div class="wrapper-demo">
											<select id="filial" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
												<ul class="dropdown">
													<li><option value="99" selected>Все</option></li>';
														if ($offices_j !=0){
															for ($i=0;$i<count($offices_j);$i++){
																echo '<li><option value="'.$offices_j[$i]['id'].'" class="icon-twitter icon-large">'.$offices_j[$i]['name'].'</option></li>';
															}
														}
											
				echo '
												</ul>
											</select>
										</div>
									</div>
								</li>
								
								<!--<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Первичные
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input name="pervich" value="0" type="radio" checked> Все<br>
										<input name="pervich" value="1" type="radio"> Только первичные<br>
										<input name="pervich" value="2" type="radio"> Только НЕ первичные<br>
									</div>
								</li>-->
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Пол
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input name="sex" value="0" type="radio" checked> Все<br>
										<input name="sex" value="1" type="radio"> М<br>
										<input name="sex" value="2" type="radio"> Ж<br><br>
										<span style="font-size:80%; color: #333;"><input type="checkbox" name="wo_sex" checked value="1"> Показывать тех, у кого не указан пол</span>
									</div>
								</li>';
								
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');	
				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
				
				echo '				
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 170px; min-width: 170px; vertical-align: top;">
										<div>
											<select name="from[]" id="multi_d" class="form-control" size="16" multiple="multiple" style="width: 167px;">';
											
				for ($k = 0; $k < count($actions_cosmet)-2; $k++) {
					//Не надо отмечать первичную консультацию, для этого есть отметка о первичном посещении
					//if ($k != 13){
						echo '
												<option value="'.$actions_cosmet[$k]['id'].'" style="margin-bottom: 2px; background-color:'.$actions_cosmet[$k]['color'].'">'.$actions_cosmet[$k]['full_name'].'</option>';
					//}
				}

				echo '
											</select>
										</div>
									</div>
									<div class="filtercellLeft" style="width: 20px; min-width: 20px; vertical-align: middle">  
										<div style="margin-bottom: 30px;  vertical-align: middle">
											<button type="button" id="multi_d_rightAll" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-double-right"></i></button>
											<button type="button" id="multi_d_rightSelected" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-right"></i></button>
											<button type="button" id="multi_d_leftSelected" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-left"></i></button>
											<button type="button" id="multi_d_leftAll" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-double-left"></i></button>
										</div>
										<div style=" vertical-align: middle">            
											<button type="button" id="multi_d_rightAll_2" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-double-right"></i></i></button>
											<button type="button" id="multi_d_rightSelected_2" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-right"></i></button>
											<button type="button" id="multi_d_leftSelected_2" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-left"></i></button>
											<button type="button" id="multi_d_leftAll_2" class="b" style="margin-bottom: 2px;"><i class="fa fa-angle-double-left"></i></button>
										</div>
									</div>
									
									<div class="filtercellRight" style="width: 170px; min-width: 170px; vertical-align: top;">    
										<div style="margin-bottom: 30px;">
											<div style="margin-bottom: 7px;">
												Процедура условие
											</div>
											<div>
												<select name="to[]" id="multi_d_to" class="form-control" size="7" multiple="multiple" style="width: 167px;"></select>
											</div>
										</div>
										<div>		
											<div style="margin-bottom: 7px;">
												Процедура следствие
											</div>
											<div>
												<select name="to_2[]" id="multi_d_to_2" class="form-control" size="7" multiple="multiple" style="width: 167px;"></select>
											</div>
										</div>
									</div>
								</li>
								
								
								

								';
				echo '
								<li class="cellsBlock" style="margin: 10px;">
									<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_cosm_ex2()">
								</li>';
				echo '
							</ul>
						</div>
						
						<div id="status">
							<ul style="border: 1px dotted #CCC; margin: 10px; width: auto;" id="qresult">
								Результат отобразится здесь
							<ul>
						</div>';
						
				echo '

				<script type="text/javascript">
					var all_time = 1;
					var all_age = 1;
					var wo_sex = 1;
					
					$("input[name=all_time]").change(function() {
						all_time = $("input[name=all_time]:checked").val();
						//console.log(all_time);
						
						if (all_time === undefined){
							all_time = 0;
						}
						//console.log(all_time);
						//alert(all_time);
						
						if (all_time == 1){
							document.getElementById("datastart").disabled = true;
							document.getElementById("dataend").disabled = true;
						}
						if (all_time == 0){
							document.getElementById("datastart").disabled = false;
							document.getElementById("dataend").disabled = false;
						}
					});
					
					$("input[name=all_age]").change(function() {
						all_age = $("input[name=all_age]:checked").val();
						//console.log(all_age);
						
						if (all_age === undefined){
							all_age = 0;
						}
						//console.log(all_age);
						//alert(all_age);
						
						if (all_age == 1){
							document.getElementById("agestart").disabled = true;
							document.getElementById("ageend").disabled = true;
						}
						if (all_age == 0){
							document.getElementById("agestart").disabled = false;
							document.getElementById("ageend").disabled = false;
						}
					});
					
					$("input[name=wo_sex]").change(function() {
						wo_sex = $("input[name=wo_sex]:checked").val();
						//console.log(all_time);
						
						if (wo_sex === undefined){
							wo_sex = 0;
						}
					});
						
					
				</script>';
			}

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>