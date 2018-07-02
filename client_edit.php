<?php

//client_edit.php
//Редактирование карточки пациента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
				//var_dump($_SESSION);
				
				if ($client !=0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать карточку пациента</h2>
							</header>';

					echo '
							<div id="data">';
					echo '
								<div id="errrror"></div>';

					echo '
								<form action="client_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">
											ФИО';
					if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
						echo '    <a href="client_edit_fio.php?id='.$_GET['id'].'"><i class="fa fa-cog" title="Редактировать ФИО"></i></a>';
					}
					echo '
										</div>
										<div class="cellRight">
											<a href="client.php?id='.$_GET['id'].'" class="ahref">'.$client[0]['full_name'].'</a>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">Дата рождения</div>
										<div class="cellRight">';
										
					if ($client[0]['birthday'] != 0){
						$bdate = getdate($client[0]['birthday']);
						$d = $bdate['mday'];
						$m = $bdate['mon'];
						$y = $bdate['year'];
					}else{
						$d = $m = $y = 0;
					}

					echo selectDate ($d, $m, $y);
					
					echo '
											<label id="sel_date_error" class="error"></label>
											<label id="sel_month_error" class="error"></label>
											<label id="sel_year_error" class="error"></label>
										</div>
									</div>

									<div class="cellsBlock2">
										<div class="cellLeft">Пол</div>
										<div class="cellRight">
											<input id="sex" name="sex" value="1" ', $client[0]['sex'] == 1 ? 'checked': '',' type="radio"> М
											<input id="sex" name="sex" value="2" ', $client[0]['sex'] == 2 ? 'checked': '',' type="radio"> Ж
											<label id="sex_error" class="error"></label>
										</div>
									</div>';
					echo '
									<div class="cellsBlock2">
										<div class="cellLeft">Телефон</div>
										<div class="cellRight">
											<div>
												<span style="font-size: 80%; color: #AAA">мобильный</span><br>
												<input type="text" name="telephone" id="telephone" value="'.mb_substr($client[0]['telephone'], 1).'">
											</div>
											<div>
												<span style="font-size: 80%; color: #AAA">домашний</span><br>
												<input type="text" name="htelephone" id="htelephone" value="'.$client[0]['htelephone'].'">
											</div>
										</div>
									</div>';
					echo '			
					
									<div id="toggleDiv1" class="cellsBlock2" style="margin-top: 2px; margin-bottom: -1px; display: block; margin: 3px 0;">
										<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
											Паспортные данные
										</div>
									</div>
									
									<div id="div1">
										<div class="cellsBlock2">
											<div class="cellLeft">Паспорт</div>
											<div class="cellRight">
												<div>
													<span style="font-size: 70%; color: #AAA">Серия номер</span><br>
													<input type="text" name="passport" id="passport" value="'.$client[0]['passport'].'" size="10"><br>
												</div>
												<div>
													<span style="font-size: 70%; color: #AAA">Серия номер (иностр.)</span><br>
													<input type="text" name="alienpassportser" id="alienpassportser" value="'.$client[0]['alienpassportser'].'" size="5">
													<input type="text" name="alienpassportnom" id="alienpassportnom" value="'.$client[0]['alienpassportnom'].'" size="10"><br>
												</div>
												<div>
													<span style="font-size: 70%; color: #AAA">Выдан когда</span><br>
													<input type="text" name="passportvidandata" id="passportvidandata" value="'.$client[0]['passportvidandata'].'" size="10">
												</div>
												<div>
													<span style="font-size: 70%; color: #AAA">Кем</span><br>
													<textarea name="passportvidankem" id="passportvidankem" cols="25" rows="2">'.$client[0]['passportvidankem'].'</textarea>
												</div>
											</div>
										</div>
										<div class="cellsBlock2">
											<div class="cellLeft">Адрес</div>
											<div class="cellRight"><textarea name="address" id="address" cols="35" rows="2">'.$client[0]['address'].'</textarea></div>
										</div>
									</div>';
					echo '
									<div id="toggleDiv2" class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block; margin: 3px 0;">
										<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
											Данные страховой компании
										</div>
									</div>
									
									<div id="div2">
										<div class="cellsBlock2">
											<div class="cellLeft">Полис
											</div>
											<div class="cellRight">
												<div>
													<span style="font-size: 80%; color: #AAA">Номер</span><br>
														<input type="text" name="polis" id="polis" value="'.$client[0]['polis'].'">
												</div>
												<div>
													<span style="font-size: 80%; color: #AAA">Дата</span><br>
														<input type="text" name="polisdata" id="polisdata" value="'.$client[0]['polisdata'].'">
												</div>
												<div>
													<span style="font-size: 80%; color: #AAA">Страховая компания</span><br>';
					echo '
													<select name="insurecompany" id="insurecompany">
														<option value="0">Выберите страховую</option>';
														
					/*if ($offices_j != 0){
						for ($i=0;$i<count($offices_j);$i++){
							$selected = '';
							if (isset($_GET['filial'])){
								if ($offices_j[$i]['id'] == $_GET['filial']){
									$selected = 'selected';
								}
							}
							echo "<option value='".$offices_j[$i]['id']."' $selected>".$offices_j[$i]['name']."</option>";
						}
					}*/

					$insures_j = SelDataFromDB('spr_insure', '', '');
						
					if ($insures_j != 0){
						for ($i=0;$i<count($insures_j);$i++){
							$selected = '';
							if ($insures_j[$i]['id'] == $client[0]['insure']){
								$selected = 'selected';
							}
							echo "<option value='".$insures_j[$i]['id']."' ".$selected.">".$insures_j[$i]['name']."</option>";
						}
					}
					echo '
													</select>
												</div>
											</div>
										</div>
									</div>';
					echo '			
									<div id="toggleDiv3" class="cellsBlock2" style="margin-top: 2px; margin-bottom: 0; display: block; margin: 3px 0;">
										<div class="cellLeft" style="font-weight: bold; width: 500px; cursor: pointer;">
											Опекун
										</div>
									</div>
									
									<div id="div3">
										<div class="cellsBlock2">
											<div class="cellLeft">Фамилия</div>
											<div class="cellRight">
												<input type="text" name="fo" id="fo" value="'.$client[0]['fo'].'">
												<label id="fname_error" class="error"></label>
											</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Имя</div>
											<div class="cellRight">
												<input type="text" name="io" id="io" value="'.$client[0]['io'].'">
												<label id="iname_error" class="error"></label>
											</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Отчество</div>
											<div class="cellRight">
												<input type="text" name="oo" id="oo" value="'.$client[0]['oo'].'">
												<label id="oname_error" class="error"></label>
											</div>
										</div>
										
										<div class="cellsBlock2">
											<div class="cellLeft">Телефон</div>
											<div class="cellRight">
												<div>
													<span style="font-size: 80%; color: #AAA">мобильный</span><br>
													<input type="text" name="telephoneo" id="telephoneo" value="'.$client[0]['telephoneo'].'">
												</div>
												<div>
													<span style="font-size: 80%; color: #AAA">домашний</span><br>
													<input type="text" name="htelephoneo" id="htelephoneo" value="'.$client[0]['htelephoneo'].'">
												</div>
											</div>
										</div>
									</div>';						
					echo '					
									<div class="cellsBlock2">
										<div class="cellLeft">Номер карты</div>
										<div class="cellRight">
											<input type="text" name="card" id="card" value="'.$client[0]['card'].'">
										</div>
									</div>
									
									<div class="cellsBlock2">
										<div class="cellLeft">Комментарий</div>
										<div class="cellRight">
											<textarea name="comment" id="comment" cols="35" rows="2">'.$client[0]['comment'].'</textarea>
										</div>
									</div>';
									
					//Для редактирования лечащих врачей в карточке
					if (($clients['add_own'] == 1) || $god_mode){
						$disabled_cosm = '';
						$disabled_stom = '';
					}else{
						$disabled_cosm = 'disabled';
						$disabled_stom = 'disabled';
					}
					//********************************************
					
					echo '								
									<div class="cellsBlock2">
										<div class="cellLeft">
											Лечащий врач<br />
											<span style="font-size: 70%">стоматология</span>
										</div>
										<div class="cellRight">
											<input type="text" size="50" name="searchdata2" '.$disabled_stom.' id="search_client2" placeholder="', $client[0]['therapist'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist'], 'user_full', false) : 'Введите первые три буквы для поиска' ,'" value="', $client[0]['therapist'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist'], 'user_full', false) : '' ,'" class="who2"  autocomplete="off">
											<ul id="search_result2" class="search_result2"></ul><br />
										</div>
									</div>';
									
					echo '				
									<div class="cellsBlock2">
										<div class="cellLeft">
											Лечащий врач<br />
											<span style="font-size: 70%">косметология</span>
										</div>
										<div class="cellRight">
											<input type="text" size="50" name="searchdata4"'.$disabled_cosm.' id="search_client4" placeholder="', $client[0]['therapist2'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist2'], 'user_full', false) : 'Введите первые три буквы для поиска' ,'" value="', $client[0]['therapist2'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist2'], 'user_full', false) : '' ,'" class="who4"  autocomplete="off">
											<ul id="search_result4" class="search_result4"></ul><br />
										</div>
									</div>';

					echo '				
									<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
									<div id="errror"></div>
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_client('.$_SESSION['id'].')">
								</form>';	
					echo '
							</div>
						</div>

											
					<script type="text/javascript">
						sex_value = '.$client[0]['sex'].';
						$("input[name=sex]").change(function() {
							sex_value = $("input[name=sex]:checked").val();
						});
						
						$("#passportvidandata").on("keyup", function(e) { 
						 
							var $this = $(this); 
							var val = $this.val(); 

							if ((val.length >= 10) && !isNaN(val[val.length - 1])){
								document.getElementById("passportvidankem").focus();
							}
						});

						jQuery(function($) {
							$.mask.definitions["~"]="[+-]";
							$("#passportvidandata").mask("99.99.9999");
							$("#telephone").mask("+7(999)999-9999");
							$("#passport").mask("9999 999999");
							$("#polisdata").mask("99.99.9999");
							$("#telephoneo").mask("+7(999)999-9999");
						});
						
					</script>';
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