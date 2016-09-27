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
					echo '    <a href="client_edit_fio.php?id='.$_GET['id'].'"><img src="img/change.png" title="Редактировать ФИО"></a>';
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
										<input type="text" size="50" name="searchdata2" '.$disabled_stom.' id="search_client2" placeholder="', $client[0]['therapist'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist'], 'user_full') : 'Введите первые три буквы для поиска' ,'" value="', $client[0]['therapist'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist'], 'user_full') : '' ,'" class="who2"  autocomplete="off">
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
										<input type="text" size="50" name="searchdata4"'.$disabled_cosm.' id="search_client4" placeholder="', $client[0]['therapist2'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist2'], 'user_full') : 'Введите первые три буквы для поиска' ,'" value="', $client[0]['therapist2'] != 0 ? WriteSearchUser('spr_workers',$client[0]['therapist2'], 'user_full') : '' ,'" class="who4"  autocomplete="off">
										<ul id="search_result4" class="search_result4"></ul><br />
									</div>
								</div>';
								
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">
										<textarea name="contacts" id="contacts" cols="35" rows="5">'.$client[0]['contacts'].'</textarea>
									</div>
								</div>	
								
								<div class="cellsBlock2">
									<div class="cellLeft">Номер карты</div>
									<div class="cellRight">
										<input type="text" name="card" id="card" value="'.$client[0]['card'].'">
									</div>
								</div>
								
								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Редактировать" onclick="Ajax_edit_client('.$_SESSION['id'].')">
							</form>';	
				echo '
						</div>
					</div>

										
				<script type="text/javascript">
					sex_value = '.$client[0]['sex'].';
					$("input[name=sex]").change(function() {
						sex_value = $("input[name=sex]:checked").val();
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