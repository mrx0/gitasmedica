<?php

//stat_insure.php
//Статистика по записи

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
		if (($finances['see_all']  == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices_j = SelDataFromDB('spr_filials', '', '');
			$insure_j = SelDataFromDB('spr_insure', '', '');

			if ($_POST){
			}else{
				echo '
					<header style="margin-bottom: 5px;">
                        <div class="nav">
                            <a href="insure_xls.php" class="b">Выгрузки</a>
                        </div>
						<h1>Страховые</h1>
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
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
										</div>
										<div style="vertical-align: middle; color: #333;">
											<input type="checkbox" name="all_time" value="1"> <span style="font-size:80%;">За всё время</span>
										</div>
									</div>
								</li>';

				echo '				
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Страховая
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div class="wrapper-demo">';
				/*if (($finances['see_all'] == 1) || $god_mode){*/
                    echo '
											<select id="insure_sel" class="wrapper-dropdown-2 b2" tabindex="2" name="insure_sel">
												<ul class="dropdown">
													<li><option value="99" selected>Все</option></li>';
														if ($insure_j !=0){
															for ($i = 0; $i < count($insure_j); $i++){
																echo '<li><option value="'.$insure_j[$i]['id'].'" class="icon-twitter icon-large">'.$insure_j[$i]['name'].'</option></li>';
															}
														}
											
				    echo '
												</ul>
											</select>';
				/*}else{
                    $offices_j = SelDataFromDB('spr_filials', $_SESSION['filial'], 'offices');
                    if ($offices_j != 0) {
                        echo $offices_j[0]['name'].'
                                <input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                    }
                }*/

                echo '
										</div>
									</div>
								</li>';
				echo '
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Филиал
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div class="wrapper-demo">';
				if (!isset($_SESSION['filial'])){
                    echo '
											<select id="filial" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
												<ul class="dropdown">
													<li><option value="99" selected>Все</option></li>';
														if ($offices_j !=0){
															for ($i = 0; $i < count($offices_j); $i++){
																echo '<li><option value="'.$offices_j[$i]['id'].'" class="icon-twitter icon-large">'.$offices_j[$i]['name'].'</option></li>';
															}
														}
											
				    echo '
												</ul>
											</select>';
				}else{
                    $offices_j = SelDataFromDB('spr_filials', $_SESSION['filial'], 'offices');
                    if ($offices_j != 0) {
                        echo $offices_j[0]['name'].'
                                <input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                    }
                }

				echo '
								<li class="cellsBlock" style="margin: 10px;">
									<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_insure()">
									<input type="button" class="b" value="Создать *.xls для скачивания" onclick="Ajax_repare_insure_xls()">
									<input type="checkbox" name="showError" value="1" checked><span style="font-size:80%;">Показывать ошибки</span>
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
                    
					var all_time = 0;
					var showError = 1;
					
					$("input[name=all_time]").change(function() {
						all_time = $("input[name=all_time]:checked").val();
						
						if (all_time === undefined){
							all_time = 0;
						}
						
						if (all_time == 1){
							document.getElementById("datastart").disabled = true;
							document.getElementById("dataend").disabled = true;
						}
						if (all_time == 0){
							document.getElementById("datastart").disabled = false;
							document.getElementById("dataend").disabled = false;
						}
					});
					
					$("input[name=showError]").change(function() {
						showError = $("input[name=showError]:checked").val();
						
						if (showError === undefined){
							showError = 0;
						}
					});
				</script>';
			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>