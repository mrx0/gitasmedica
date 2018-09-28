<?php

//stat_cashbox.php
//Касса

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
		if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			$offices_j = SelDataFromDB('spr_filials', '', '');

			if ($_POST){
			}else{
				echo '
					<header style="margin-bottom: 5px;">
						<h1>Касса</h1>';
                echo '
                        <div>
						    <a href="fl_give_out_cash_add.php" class="b4">Добавить расход</a>
						</div>';
                echo '		
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
											C <input type="text" id="datastart" name="datastart" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
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
				if (($finances['see_all'] == 1) || $god_mode){
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
										</div>
									</div>
								</li>';


                echo '				
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Вид оплаты
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input name="summType" value="0" type="radio" checked>Все<br>
										<input name="summType" value="1" type="radio">Наличные<br>
										<input name="summType" value="2" type="radio">Безналичные<br>
									</div>
								</li>
								
								<!--<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Вид услуг
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="zapisTypeAll" name="zapisTypeAll" class="zapisType" value="1" checked>Все<br>
										<input type="checkbox" id="zapisTypeStom" name="zapisTypeStom" class="zapisType" value="1" checked>Стоматология<br>
										<input type="checkbox" id="zapisTypeCosm" name="zapisTypeCosm" class="zapisType" value="1" checked>Косметология<br>
									</div>
								</li>-->
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Показывать проданные сертификаты
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="certificatesShow" name="certificatesShow" value="1" checked>
									</div>
								</li>
								
								</li>';

				echo '
								<li class="cellsBlock" style="margin: 10px;">
									<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_cashbox();">
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
				    //Проверка и установка checkbox
                    $(".zapisType").click(function() {
                        
					    var checked_status = $(this).is(":checked");
					    var thisId = $(this).attr("id");
					    var pin_status = false;
					    var allCheckStatus = false;
					    
                        if (thisId == "zapisTypeAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".zapisType").each(function() {
                                $(this).prop("checked", pin_status);
                            });
                        }else{
                            if (!checked_status){
                                $("#zapisTypeAll").prop("checked", false);
                            }else{
                                allCheckStatus = true; 
                                $(".zapisType").each(function() {
                                    if ($(this).attr("id") != "zapisTypeAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false; 
                                        }
                                    }
                                });
                                if (allCheckStatus){
                                    $("#zapisTypeAll").prop("checked", true);
                                }
                            }
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