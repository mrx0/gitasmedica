<?php

//stat_client_finance2.php
//Долги авансы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
		if (($report['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices_j = getAllFilials(true, false, true);
			//var_dump($offices_j );

			if ($_POST) {
            }else{
				echo '
					<header style="margin-bottom: 5px;">
						<h1>Открытые наряды</h1>
					</header>';

				echo '
						<div id="data">';
				echo '
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);">
								
								<li style="margin-bottom: 7px; display: none">
									Выберите условие
								</li>
								
								<!--<li class="filterBlock" style="display: none">
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
								</li>-->
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Филиал
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div class="wrapper-demo">
											<select id="filial" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
												<ul class="dropdown">
													<li><option value="99" selected>Все</option></li>';
														if (!empty($offices_j)){
															foreach ($offices_j as $filial_id => $filial_data){
																echo '<li><option value="'.$filial_id.'" class="icon-twitter icon-large">'.$filial_data['name'].'</option></li>';
															}
														}
											
				echo '
												</ul>
											</select>
										</div>
									</div>
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник, на кого был выписан наряд<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">';
                if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
                    echo '
										<input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="" class="who4" autocomplete="off">
										<ul id="search_result4" class="search_result4"></ul><br />';
                }else{
                    echo WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false).'
                                        <input type="hidden" id="search_client4" name="searchdata4" value="'.WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false).'">';
                }
                echo '
									</div>
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Пациент<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="30" name="searchdata" id="search_client" placeholder="Минимум три буквы для поиска" value="" class="who" autocomplete="off">
										<ul id="search_result" class="search_result"></ul><br />
									</div>
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник, который создал<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="30" name="searchdata2" id="search_worker" placeholder="Минимум три буквы для поиска" value="" class="who2" autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</li>
								
								';
				echo '
								<li class="cellsBlock" style="margin: 10px;">
									<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_client_finance2()">
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
					
					/*$("input[name=all_age]").change(function() {
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
					});*/
					
					/*$("input[name=wo_sex]").change(function() {
						wo_sex = $("input[name=wo_sex]:checked").val();
						//console.log(all_time);
						
						if (wo_sex === undefined){
							wo_sex = 0;
						}
					});*/
						
					
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