<?php

//stat_invoice.php
//Статистика по нарядам

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//var_dump($_SESSION);
		if (($zapis['see_all'] == 1) || ($zapis['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
            include_once 'ffun.php';

            $clientInvoices = array();

            $filials_j = getAllFilials(false, true);

			if ($_POST){
			}else{
				echo '
					<header style="margin-bottom: 5px;">
						<h1>Наряды</h1>
						
						<a href="invoice_add_free.php" class="b">Добавить новый (без записи)</a>
						
					</header>';

				/*echo '
						<div id="data">';
				echo '
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
								
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
								</li>
								<!--<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник, к кому была запись<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="" class="who4" autocomplete="off">
										<ul id="search_result4" class="search_result4"></ul><br />
									</div>
								</li>-->
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
										Сотрудник, который добавил наряд<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="30" name="searchdata2" id="search_worker" placeholder="Минимум три буквы для поиска" value="" class="who2" autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</li>
								';


                echo '				
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Состояние
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="paidAll" name="paidAll" class="paidType" value="1" checked> Все<br>
										<input type="checkbox" id="paidTrue" name="paidTrue" class="paidType" value="1" checked> Не оплаченные<br>
										<input type="checkbox" id="paidNot" name="paidNot" class="paidType" value="1" checked> Оплаченные<br>
										<input type="checkbox" id="insureTrue" name="insureTrue" class="paidType" value="1" checked> Страховые<br>
										<!--<input type="checkbox" id="zapisError" name="zapisError" class="zapisType" value="1" checked> Ошибочные<br>-->
									</div>
								</li>';

				echo '
							</ul>
							<!--<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);display: inline-table;">
							    
								<li style="margin-bottom: 10px;">
									Дополнительные условия
								</li>
							    
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Тип
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input id="typeW" name="typeW" value="0" type="radio" checked>Все<br />
										<input id="typeW" name="typeW" value="5" type="radio">Стоматологи<br />
										<input id="typeW" name="typeW" value="6" type="radio">Косметологи<br />
										<input id="typeW" name="typeW" value="10" type="radio">Специалисты<br />
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Заполненность
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="fullAll" name="fullAll" class="fullType" value="1" checked> Все<br>
										<input type="checkbox" id="fullWOInvoice" name="fullWOInvoice" class="fullType" value="1" checked> Без нарядов<br>
										<input type="checkbox" id="fullWOTask" name="fullWOTask" class="fullType" value="1" checked> Без посещений<br>
										<input type="checkbox" id="fullOk" name="fullOk" class="fullType" value="1" checked> Заполненные полностью<br>
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Статус
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="statusAll" name="statusAll" class="statusType" value="1" checked> Все<br>
										<input type="checkbox" id="statusPervich" name="statusPervich" class="statusType" value="1" checked> Первичные<br>
										<input type="checkbox" id="statusInsure" name="statusInsure" class="statusType" value="1" checked> Страховые<br>
										<input type="checkbox" id="statusNight" name="statusNight" class="statusType" value="1" checked> Ночные<br>
										<input type="checkbox" id="statusAnother" name="statusAnother" class="statusType" value="1" checked> Все остальные<br>
									</div>
								</li>
								
							</ul>-->
						</div>';
				
				echo '
						<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_invoice()">';

                echo '
						<div id="status">
							<ul style="border: 1px dotted #CCC; margin: 10px; width: auto;" id="qresult">
								Результат отобразится здесь
							<ul>
						</div>';*/


				//!!!Временно!!! будем показывать тут последние N нарядов

                $msql_cnnct = ConnectToDB ();

                $query = '';

                //Соберем (неудаленные) наряды
                if (($finances['see_all'] == 1) || $god_mode) {

                }else {
                    if (isset($_SESSION['id'])) {
                        $query .= " AND jinv.create_person = '" . $_SESSION['id'] . "'";
                    }
                }

                $query = "SELECT jinv.*, scli.full_name FROM `journal_invoice` jinv
                                LEFT JOIN `spr_clients` scli
                                ON scli.id = jinv.client_id
                                WHERE jinv.status <> '9' AND jinv.create_time > '2017-12-01 08:30:00' ".$query." ORDER BY `create_time` DESC LIMIT 20";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($clientInvoices, $arr);
                    }
                }else{
                }

                //Выводим результат
                if (!empty($clientInvoices)){
                    include_once 'functions.php';

                    echo '
					<div id="data">';

                    foreach ($clientInvoices as $data) {
                        echo '
									<li class="cellsBlock" style="font-weight: bold; width: auto; background-color: rgba(255, 255, 0, 0.3); margin: 2px">	
										<a href="invoice.php?id=' . $data['id'] . '"    class="cellTime ahref" style="text-align: center; ">Наряд #'.$data['id'].' от '. $data['create_time'] . '</a>
										    <div class="cellName" style="text-align: right; ">Сумма наряда: ' . $data['summ'] . ' руб.</div>
										    <div class="cellName" style="text-align: right; ">Не оплачено: <span style="color:', ($data['summ']-$data['paid'] > 0) ? 'red' : 'green' ,'"><BR>' . ($data['summ']-$data['paid']) . '</span> руб.</div>
										    <a href="invoice.php?id='.$data['client_id'].'" class="ahref cellText" style="max-width: 250px;">'.$data['full_name'].'<br><br>
										    '.$filials_j[$data['office_id']]['name2'].'
										    </a>
										    <div class="cellName" style="text-align: left; width: 160px; min-width: 160px;">
										        автор: '.WriteSearchUser('spr_workers', $data['create_person'], 'user', true).'<br>
										        исп-ль: '.WriteSearchUser('spr_workers', $data['worker_id'], 'user', true).'
										    </div>
										
									</li>';
                    }

                }else{
                    echo '<span style="color: red;">Ничего не найдено</span>';
                }

                CloseDB($msql_cnnct);



						
				echo '

				<script type="text/javascript">
				    //Проверка и установка checkbox
                    $(".paidType").click(function() {
                        
					    var checked_status = $(this).is(":checked");
					    var thisId = $(this).attr("id");
					    var pin_status = false;
					    var allCheckStatus = false;
					    
                        if (thisId == "paidAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".paidType").each(function() {
                                $(this).prop("checked", pin_status);
                            });
                        }else{
                            if (!checked_status){
                                $("#paidAll").prop("checked", false);
                            }else{
                                allCheckStatus = true; 
                                $(".paidType").each(function() {
                                    if ($(this).attr("id") != "paidAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false; 
                                        }
                                    }
                                });
                                if (allCheckStatus){
                                    $("#paidAll").prop("checked", true);
                                }
                            }
                        }
					});
                    
                    $(".fullType").click(function() {
                        
					    var checked_status = $(this).is(":checked");
					    var thisId = $(this).attr("id");
					    var pin_status = false;
					    var allCheckStatus = false;
					    
                        if (thisId == "fullAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".fullType").each(function() {
                                $(this).prop("checked", pin_status);
                            });
                        }else{
                            if (!checked_status){
                                $("#fullAll").prop("checked", false);
                            }else{
                                allCheckStatus = true; 
                                $(".fullType").each(function() {
                                    if ($(this).attr("id") != "fullAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false; 
                                        }
                                    }
                                });
                                if (allCheckStatus){
                                    $("#fullAll").prop("checked", true);
                                }
                            }
                        }
					});
                    
                    $(".statusType").click(function() {
                        
					    var checked_status = $(this).is(":checked");
					    var thisId = $(this).attr("id");
					    var pin_status = false;
					    var allCheckStatus = false;
					    
                        if (thisId == "statusAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".statusType").each(function() {
                                $(this).prop("checked", pin_status);
                            });
                        }else{
                            if (!checked_status){
                                $("#statusAll").prop("checked", false);
                            }else{
                                allCheckStatus = true; 
                                $(".statusType").each(function() {
                                    if ($(this).attr("id") != "statusAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false; 
                                        }
                                    }
                                });
                                if (allCheckStatus){
                                    $("#statusAll").prop("checked", true);
                                }
                            }
                        }
					});
                    
                    
					var all_time = 1;
					
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