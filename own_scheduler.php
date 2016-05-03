<?php

//scheduler.php
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			$offices = SelDataFromDB('spr_office', '', '');
			//var_dump ($offices);
			
			$post_data = '';
			$js_data = '';
			
			$sheduler_times = array (
				1 => '9:00 - 9:30',
				2 => '9:30 - 10:00',
				3 => '10:00 - 10:30',
				4 => '10:30 - 11:00',
				5 => '11:00 - 11:30',
				6 => '11:30 - 12:00',
				7 => '12:00 - 12:30',
				8 => '12:30 - 13:00',
				9 => '13:00 - 13:30',
				10 => '13:30 - 14:00',
				11 => '14:00 - 14:30',
				12 => '14:30 - 15:00',
				13 => '15:00 - 15:30',
				14 => '15:30 - 16:00',
				15 => '16:00 - 16:30',
				16 => '16:30 - 17:00',
				17 => '17:00 - 17:30',
				18 => '17:30 - 18:00',
				19 => '18:00 - 18:30',
				20 => '18:30 - 19:00',
				21 => '19:00 - 19:30',
				22 => '19:30 - 20:00',
				23 => '20:00 - 20:30',
				24 => '20:30 - 21:00',
			);
			
			$who = '&who=stom';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';
			
			if ($_GET){
				//var_dump ($_GET);
				
				$month_names=array(
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
				if (isset($_GET['y']))
					$y = $_GET['y'];
				if (isset($_GET['m']))
					$m = $_GET['m']; 
				if (isset($_GET['date']) && strstr($_GET['date'],"-"))
					list($y,$m) = explode("-",$_GET['date']);
				if (!isset($y) || $y < 1970 || $y > 2037)
					$y = date("Y");
				if (!isset($m) || $m < 1 || $m > 12)
					$m = date("m");
				$month_stamp = mktime(0, 0, 0, $m, 1, $y);
				$day_count = date("t",$month_stamp);
				$weekday = date("w", $month_stamp);
				if ($weekday == 0)
					$weekday = 7;
				$start = -($weekday-2);
				$last = ($day_count + $weekday - 1) % 7;
				if ($last == 0) 
					$end = $day_count; 
				else 
					$end = $day_count + 7 - $last;
				$today = date("Y-m-d");
				$go_today = date('?\m=m&\y=Y', mktime (0, 0, 0, date("m"), 1, date("Y"))); 
				
				$prev = date('?\m=m&\y=Y', mktime (0, 0, 0, $m-1, 1, $y));  
				$next = date('?\m=m&\y=Y', mktime (0, 0, 0, $m+1, 1, $y));
				
				
				$worker = SelDataFromDB('spr_workers', $_GET['id'], 'user');
				//var_dump($worker);
				
				//
				if ($worker != 0){
					$prev .= '&id='.$_GET['id']; 
					$next .= '&id='.$_GET['id']; 
					$go_today .= '&id='.$_GET['id']; 
					//Определим, откуда брать данные по правам воркера
					if ($worker[0]['permissions'] == 5){
						$datatable = 'scheduler_stom';
					}elseif ($worker[0]['permissions'] == 6){
						$datatable = 'scheduler_cosm';
					}
					$i = 0;
					
					
					echo '
						<div id="status">
							<header>
								<h2>График работы '.$worker[0]['name'].'на ',$month_names[$m-1],' ',$y,'</h2>
								<a href="scheduler.php" class="b">График работы врачей в филиалах</a>
							</header>
							<div id="data">';
					echo '
								<table style="border:1px solid #BFBCB5; height:600px;">
									<tr>
										<td colspan="7">
												<table width="100%" border=0 cellspacing=0 cellpadding=0> 
													<tr> 
														<td align="left"><a href="'.$prev.$who.'">&lt;&lt; предыдущий</a></td> 
														<td align="center"><strong>',$month_names[$m-1],' ',$y,'</strong> (<a href="'.$go_today.$who.'">текущий</a>)</td> 
														<td align="right"><a href="'.$next.$who.'">следующий &gt;&gt;</a></td> 
													</tr> 
												</table> 
										</td>
									</tr>
									<tr style="text-align:center; vertical-align:top; font-weight:bold; height:20px;">
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px; text-align:center; ">
											Понедельник
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Вторник
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Среда
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Четверг
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Пятница
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Суббота
										</td>
										<td style="border:1px solid #BFBCB5; width:180px; min-width:180px;">
											Воскресенье
										</td>
									</tr>';
									
					for($d = $start; $d <= $end; $d++){
						if (!($i++ % 7)){
							echo '
								<tr>';
						}
						$kabs = '
							<div class="smena_div">';

						//смотрим че работаем или нет
						$Work_today = FilialSmenaWorker($datatable, $y, $m, $d, $_GET['id']);
						//var_dump($Work_today);
						
						$smena1_work = '
									<div class="smena">
										<br />
									</div>';
						$smena2_work = '
									<div class="smena">
										<br />
									</div>';
						if ($Work_today != 0){
							//var_dump($Work_today);
							
							for($t=0; $t<count($Work_today); $t++){
								$worker_today = $Work_today[$t]['worker'];
								$filial = SelDataFromDB('spr_office', $Work_today[$t]['office'], 'offices');
								if ($Work_today[$t]['smena'] == 1){
									$smena1_work = '
												<div class="smena smena_work">
													1 см<br />
													'.$filial[0]['name'].'<br />
													Кабинет '.$Work_today[$t]['kab'].'<br />
												</div>';
								}elseif ($Work_today[$t]['smena'] == 2){
									$smena2_work = '
												<div class="smena smena_work">
													2 см<br />
													'.$filial[0]['name'].'<br />
													Кабинет '.$Work_today[$t]['kab'].'<br />
												</div>';
								}elseif ($Work_today[$t]['smena'] == 9){
									$smena1_work = '
												<div class="smena smena_work">
													1 см<br />
													'.$filial[0]['name'].'<br />
													Кабинет '.$Work_today[$t]['kab'].'<br />
												</div>';
									$smena2_work = '
												<div class="smena smena_work">
													2 см<br />
													'.$filial[0]['name'].'<br />
													Кабинет '.$Work_today[$t]['kab'].'<br />
											</div>';
								}
							}
						}	

							
						$kabs .= '
									<div class="kab_filial">
										<div class="smena_div">
											'.$smena1_work.'
											'.$smena2_work.'
										</div>
									</div>';

						$kabs .= '
									</div>';
						//выделение сегодня цветом
						$now="$y-$m-".sprintf("%02d",$d);
						if ($now == $today){
							$today_color = 'border:1px solid red;';
						}else{
							$today_color = 'border:1px solid #BFBCB5;';
						}
						//Выделение цветом выходных
						if (($i % 7 == 0) || ($i % 7 == 6)){
							$holliday_color = 'color: red;';
						}else{
							$holliday_color = '';
						}
						
						echo '
									<td style="'.$today_color.' text-align: center; text-align: -moz-center; text-align: -webkit-center;">';
						if ($d < 1 || $d > $day_count){
							echo "&nbsp";
						}else{
															/*echo '		$now="$y-$m-".sprintf("%02d",$d);
															<td style="border:1px solid #BFBCB5; text-align: center; text-align: -moz-center; text-align: -webkit-center;">
																<div style="vertical-align:top; text-align: right;">
																	<font color=red>
																		<strong>'.$week[$i][$j].'</strong>
																	</font>
																</div>
																'.$kabs.'
															</td>';*/
													/*}else*/
							echo '
										<div style="vertical-align:top; text-align: right;'.$holliday_color.'">
											<strong>'.$d.'</strong>
										</div>
										'.$kabs.'';
						}
												/*}else 
													echo '
															<td style="border:1px solid #BFBCB5; vertical-align:top;">&nbsp;</td>';*/
								//			}
						echo '
									</td>';
						if (!($i % 7)){
							echo '
								</tr>';
						}
					} 
					echo '
						</table>';
				
					//$filial = SelDataFromDB('spr_office', $_GET['filial'], 'offices');
					//var_dump($filial['name']);
				}
			}else{
				echo '
					<div id="status">
						<header>
							<h2>График работы</h2>
							<a href="scheduler.php" class="b">График работы филиалов</a><br /><br />';

				echo '								
								<div class="cellsBlock2">
									<div class="cellLeft">
										Выберите сотрудника<br />
										<span style="font-size: 70%">и нажмите <Далее></span>
									</div>
									<div class="cellRight">
										<input type="text" size="50" name="searchdata2" id="search_client2" placeholder="Введите первые три буквы для поиска" value="" class="who2"  autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
											<input type=\'button\' class="b" value=\'Далее\' onclick=\'
												ajax({
													url:"returnIDbyName.php",
													//statbox:"status",
													method:"POST",
													data:
													{
														searchdata2:document.getElementById("search_client2").value,
													},
													success:function(data){
														//alert(data);
														//document.getElementById("status").innerHTML=data;
														window.location.href = "own_scheduler.php?&id="+data;
													}
												})\'>
									</div>
								</div>';

				echo '			
				</header>';
			}

			echo '
					</div>
				</div>';


			echo '
			
				<script>
				
					$(function() {
						$(\'#SelectWho\').change(function(){
							if (document.getElementById("SelectFilial").value != 0)
								document.location.href = "?filial="+document.getElementById("SelectFilial").value+"&who="+$(this).val();	
						});
						$(\'#SelectFilial\').change(function(){
							document.location.href = "?filial="+$(this).val()+"&who="+document.getElementById("SelectWho").value;
						});
					});
					
					

					

				</script>
				';	

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>