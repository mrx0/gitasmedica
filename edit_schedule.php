<?php

//edit_schedule.php
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['add_own'] == 1) || $god_mode){
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
				if(isset($_GET['filial'])){
					$prev .= '&filial='.$_GET['filial']; 
					$next .= '&filial='.$_GET['filial'];
					$go_today .= '&filial='.$_GET['filial'];
					
					$selected_fil = $_GET['filial'];
				}
				$i = 0;
				
				
				
				
				$filial = SelDataFromDB('spr_office', $_GET['filial'], 'offices');
				//var_dump($filial['name']);
				
				if ($filial != 0){
					echo '
						<div id="status">
							<header>
								<h2>Расписание на ',$month_names[$m-1],' ',$y,' филиал '.$filial[0]['name'].'</h2>';
					echo '
								<form>
									<select name="SelectFilial" id="SelectFilial">
										<option value="0" selected>Выберите филиал</option>';
					if ($offices != 0){
						for ($off=0;$off<count($offices);$off++){
							echo "
										<option value='".$offices[$off]['id']."' ", $selected_fil == $offices[$off]['id'] ? "selected" : "" ,">".$offices[$off]['name']."</option>";
						}
					}

					echo '
									</select>
								</form>';	
					echo '			
							</header>';
							
					echo '
					
							<style>
								.label_desc{
									display: block;
								}
								.error{
									display: none;
								}
								.error_input{
									border: 2px solid #FF0000; 
								}
							</style>	
					
					
							<div id="data">';

					
					echo '
						<table style="border:1px solid #BFBCB5; height:600px;">
							<tr>
								<td colspan="7">
										<table width="100%" border=0 cellspacing=0 cellpadding=0> 
											<tr> 
												<td align="left"><a href="'.$prev.'">&lt;&lt; предыдущий</a></td> 
												<td align="center"><strong>',$month_names[$m-1],' ',$y,'</strong> (<a href="'.$go_today.'">текущий</a>)</td> 
												<td align="right"><a href="'.$next.'">следующий &gt;&gt;</a></td> 
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
						//все кабинеты свободны
						$ahtung = TRUE;
						$ahtung_smena1 = TRUE;	
						$ahtung_smena2 = TRUE;	
						$kabs = '
							<div class="smena_div">';
						//!!!по кабинетам бегаем
						for ($k = 1; $k <= 4; $k++){
							//смотрим че там в этом кабинете сегодня 
							$Kab_work_today = FilialKabSmenaWorker($y, $m, $d, $_GET['filial'], $k);
							$smena1_work = '
										<div class="smena">
											<br />
										</div>';
							$smena2_work = '
										<div class="smena">
											<br />
										</div>';
							if ($Kab_work_today !=0){
								//var_dump($Kab_work_today);
								
								for($t=0; $t<count($Kab_work_today); $t++){
									$worker_today = $Kab_work_today[$t]['worker'];
									if ($Kab_work_today[$t]['smena'] == 1){
										$smena1_work = '
													<div class="smena smena_work">
														1 см<br />
														'.$worker_today.'
													</div>';
										$ahtung_smena1 = FALSE;		
									}elseif ($Kab_work_today[$t]['smena'] == 2){
										$smena2_work = '
													<div class="smena smena_work">
														2 см<br />
														'.$worker_today.'
													</div>';
										$ahtung_smena2 = FALSE;	
									}elseif ($Kab_work_today[$t]['smena'] == 9){
										$smena1_work = '
													<div class="smena smena_work">
														1 см<br />
														'.$worker_today.'
													</div>';
										$smena2_work = '
													<div class="smena smena_work">
														2 см<br />
														'.$worker_today.'
													</div>';
										$ahtung_smena1 = FALSE;	
										$ahtung_smena2 = FALSE;	
									}
									if (!$ahtung_smena1 && !$ahtung_smena2)
										$ahtung = FALSE;
								}
								
							}
							
							$kabs .= '
										<div class="kab_filial" onclick="ShowSettingsScheduler('.$_GET['filial'].', \''.$filial[0]['name'].'\', '.$k.', '.$y.', '.$m.','.$d.')">
											<div class="n_kab_filial">
												№'.$k.'	
											</div>
											<div class="smena_div">
												'.$smena1_work.'
												'.$smena2_work.'
											</div>
										</div>';
						}
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
						
						if ($ahtung){
							$ahtung_color = 'id="blink2"';
						}else{
							$ahtung_color = '';
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
										<div style="vertical-align:top; text-align: right;'.$holliday_color.'" '.$ahtung_color.'>
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
				}
			}else{
				echo '
					<div id="status">
						<header>
							<h2>Расписание</h2>';
				echo '
					<form>
						<select name="SelectFilial" id="SelectFilial">
							<option value="0" selected>Выберите филиал</option>';
				if ($offices != 0){
					for ($i=0;$i<count($offices);$i++){
						echo "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
					}
				}
				echo '
						</select>
					</form>';
				echo '			
				</header>';
			}

			echo '
					</div>
				</div>';

			echo '
					<div id="ShowSettingsScheduler" style="position: absolute; z-index: 105; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsScheduler()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(../img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsScheduler">

							<div style="display:inline-block;">
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Число</div>
									<div class="cellRight" id="month_date">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight" id="filial_name">
									</div>
								</div>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">Кабинет №</div>
									<div class="cellRight" id="kab">
									</div>
								</div>

								<div class="cellsBlock2" style="font-size:80%; width:350px;">
									<div class="cellLeft" style="background: rgb(70, 250, 70) none repeat scroll 0% 0%;">1 смена</div>
									<div class="cellRight" style="background: rgb(70, 250, 70) none repeat scroll 0% 0%;">
										<input type="checkbox" name="smena1" id="smena1" value="1">
									</div>
								</div>
								<div class="cellsBlock2" style="font-size:80%; width:350px;">
									<div class="cellLeft" style="background: rgb(250, 100, 250) none repeat scroll 0% 0%;">2 смена</div>
									<div class="cellRight" style="background: rgb(250, 100, 250) none repeat scroll 0% 0%;">
										<input type="checkbox" name="smena2" id="smena2" value="1">
									</div>
								</div>';		
			foreach ($sheduler_times as $shedul_key => $shedul_value){
				if ($shedul_key < 13){
					$smena_class = ' class="smena1"';
					$smena_class2 = ' smena1';
				}else{
					$smena_class = ' class="smena2"';
					$smena_class2 = ' smena2';
				}
				//Для JS
				$js_data .= '
					var sh_value'.$shedul_key.' = $("input[name=sh_'.$shedul_key.']:checked").val();
				';
				$post_data .= '
					sh_'.$shedul_key.':sh_value'.$shedul_key.',';
				echo '
								<div class="cellsBlock2" style="font-size:70%; font-weight: bold; width:350px; display: none;">
									<div class="cellLeft'.$smena_class2.'" id="sh_'.$shedul_key.'_2">'.$shedul_value.'</div>
									<div class="cellRight">
										<input type="checkbox" name="sh_'.$shedul_key.'" id="sh_'.$shedul_key.'" value="1"'.$smena_class.' onclick="changeStyle(\'sh_'.$shedul_key.'\')">
									</div>
								</div>
								';
			}
			//Врачи
			echo '
							</div>
							<div style="display:inline-block; vertical-align: top; height: 350px; width: 340px; border: 1px solid #C1C1C1; overflow-x: hidden; overflow-y: scroll; ">
								<div id="ShowWorkersHere">
								</div>';

			echo '	
						</div>
					</div>';

			echo '
						<input type="hidden" id="day" name="day" value="0">
						<input type="hidden" id="month" name="month" value="0">
						<input type="hidden" id="year" name="year" value="0">
						<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
						<input type="hidden" id="filial" name="filial" value="0">
						<div id="errror"></div>
						<input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_Sheduler()>
					</div>';	
					
					
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			
			echo '
			
				<script>
				
					$(function() {
						$(\'#SelectFilial\').change(function(){
							//alert($(this).val());
							document.location.href = "?filial="+$(this).val();
						});
					});
					
					function ShowSettingsScheduler(filial, filial_name, kab, year, month, day){
						$(\'#ShowSettingsScheduler\').show();
						$(\'#overlay\').show();
						//alert(month_date);
						window.scrollTo(0,0)
						
						document.getElementById("filial").value=filial;
						document.getElementById("year").value=year;
						document.getElementById("month").value=month;
						document.getElementById("day").value=day;
						
						document.getElementById("filial_name").innerHTML=filial_name;
						document.getElementById("kab").innerHTML=kab;
						document.getElementById("month_date").innerHTML=day+\'.\'+month+\'.\'+year;
					}
					
					function HideSettingsScheduler(){
						$(\'#ShowSettingsScheduler\').hide();
						$(\'#overlay\').hide();
					}
					
					function ShowWorkersSmena(){
						var smena = 0;
						if ( $("#smena1").prop("checked")){
							if ( $("#smena2").prop("checked")){
								smena = 9;
							}else{
								smena = 1;
							}
						}else if ( $("#smena2").prop("checked")){
							smena = 2;
						}
						
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "show_workers_free.php",
							// какие данные будут переданы
							data: {
								day:$(\'#day\').val(),
								month:$(\'#month\').val(),
								year:$(\'#year\').val(),
								smena:smena,
							},
							// действие, при ответе с сервера
							success: function(workers){
								document.getElementById("ShowWorkersHere").innerHTML=workers;
							}
						});	
					}
					

				</script>
			
			
			
				<script>  
					function changeStyle(idd){
						if ( $("#"+idd).prop("checked"))
							document.getElementById(idd+"_2").style.background = \'#83DB53\';
						else
							document.getElementById(idd+"_2").style.background = \'#F0F0F0\';
					}

					$(document).ready(function() {
						$("#smena1").click(function() {
							var checked_status = this.checked;
							 $(".smena1").each(function() {
								this.checked = checked_status;
								if ( $(this).prop("checked"))
									this.style.background = \'#83DB53\';
								else
									this.style.background = \'#F0F0F0\';
							});
							
							var ShowWorkersSmena1 = ShowWorkersSmena();
						});
						$("#smena2").click(function() {
							var checked_status = this.checked;
							 $(".smena2").each(function() {
								this.checked = checked_status;
								if ( $(this).prop("checked"))
									this.style.background = \'#83DB53\';
								else
									this.style.background = \'#F0F0F0\';
							});
							
							var ShowWorkersSmena1 = ShowWorkersSmena();
						});
					}); 
									
				
					function Ajax_add_Sheduler() {
						 
						// получение данных из полей
						var filial = $(\'#filial\').val();
						var author = $(\'#author\').val();
						var year = $(\'#year\').val();
						var month = $(\'#month\').val();
						var day = $(\'#day\').val();
						
						var kab = document.getElementById("kab").innerHTML;

						
						var worker = $(\'input[name=worker]:checked\').val();
						if(typeof worker == "undefined") worker = 0;
						
						'.$js_data.'

						var smena1_val = $("input[name=smena1]:checked").val();
						var smena2_val = $("input[name=smena2]:checked").val();

						$.ajax({
							//statbox:SettingsScheduler,
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "edit_schedule_f.php",
							// какие данные будут переданы
							data: {
								type:"scheduler_stom",
								author:author,
								filial:filial,
								kab:kab,
								day:day,
								month:month,
								year:year,
								'.$post_data.'
								smena1:smena1_val,
								smena2:smena2_val,
								worker:worker,
							},
							// действие, при ответе с сервера
							success: function(data){
								document.getElementById("ShowSettingsScheduler").innerHTML=data;
								window.scrollTo(0,0)
							}
						});						
										
										

					};  
								  
				</script>
				
			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 10);
				 }
				 /*]]>*/
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