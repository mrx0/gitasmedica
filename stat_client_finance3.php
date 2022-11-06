<?php

//stat_client_finance3.php
//Пациенты, которые потратили до указанной суммы за период

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
        if (($finances['see_all'] == 1) || $god_mode) {
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
						<h1>Пациенты по сумме ордеров</h1>
					</header>';

				echo '
						<div id="data">';
				echo '
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);">
								
								<li style="margin-bottom: 7px; display: none">
									Выберите условие
								</li>
								
								<li class="filterBlock" style="">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Выберите период
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div style="margin-bottom: 10px;">
											Год: <input id="orderYear" type="number" value="' . date('Y') . '" min="2000" max="2030" size="4" style="width: 60px;">
										</div>
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
										Сумма<br>
										<span style="font-size:80%; color: #999; ">до 999 999</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
									    <input id="orderSumm" type="number" value="10000" min="0" max="999999" size="10" style="width: 80px;">
									</div>
								</li>
								';
				echo '
								<li class="cellsBlock" style="margin: 10px;">
									<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_client_finance3()">
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

					$("#orderSumm").change(function() {
						let orderSumm = $("#orderSumm").val();
//						console.log(orderSumm);
						
						if (orderSumm > 999999){
							$("#orderSumm").val(999999);
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