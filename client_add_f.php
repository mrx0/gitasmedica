<?php 

//client_add_f.php
//Функция для добавления Пациента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo '
					<div class="query_neok">
						Что-то не заполнено. Если у пациента нет отчества, поставьте в поле "Отчество" символ "*"<br><br>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				//Права
				$god_mode = FALSE;
		
				if ($_SESSION['permissions'] == '777'){
					$god_mode = TRUE;
				}else{
					//Получили список прав
					$permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');	
					//var_dump($permissions);
				}
				if (!$god_mode){
					if ($permissions != 0){
						$stom = json_decode($permissions[0]['stom'], true);
						$cosm = json_decode($permissions[0]['cosm'], true);
					}
				}else{
					//Видимость
					$stom['add_own'] = 0;
					$cosm['add_own'] = 0;
				}

				$echo_therapist = '';
				$echo_therapist2 = '';
				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo '
						<div class="query_neok">
							В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
						</div>';
				}else{
					
					$full_name = CreateFullName(firspUpperCase(trim($_POST['f'])), firspUpperCase(trim($_POST['i'])), firspUpperCase(trim($_POST['o'])));
					//Проверяем есть ли такой пациент
					if (isSameFullName('spr_clients', $full_name, 0)){
						//!!! Тупость, костыль.. то же самое делаем строчкой выше
						$rezult = SelDataFromDB('spr_clients', $full_name, 'full_name');
						//var_dump ($rezult);
					
						echo '
							<div class="query_neok">
								Такой пациент уже есть. <br>
								<a href="client.php?id='.$rezult[0]['id'].'" class="b">'.$rezult[0]['name'].'</a><br>
								Если тёзка, в конце отчества поставьте символ *<br /><br />
							</div>';
					}else{
						//лечащий врач стоматология
						if ($_POST['therapist'] == ''){
							$therapist = 0;
							$echo_therapist .= 'Лечащий врач <b>[стоматология]</b> не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
						}else{
							$therapists = SelDataFromDB ('spr_workers', $_POST['therapist'], 'worker_full_name');
							if ($therapists != 0){
								$therapist = $therapists[0]['id'];
								$echo_therapist .= 'Лечащий врач <b>[стоматология]</b>: '.$_POST['therapist'];
							}else{
								$therapist = 0;
								$echo_therapist .= 'Лечащий врач <b>[стоматология]</b> не назначен. <span style="color:red;">Такого врача нет в нашей базе</span>. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
							}
						}
						//лечащий врач косметология
						if ($_POST['therapist2'] == ''){
							$therapist2 = 0;
							$echo_therapist2 .= 'Лечащий врач <b>[косметология]</b> не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
						}else{
							$therapists2 = SelDataFromDB ('spr_workers', $_POST['therapist2'], 'worker_full_name');
							if ($therapists2 != 0){
								$therapist2 = $therapists2[0]['id'];
								$echo_therapist2 .= 'Лечащий врач <b>[косметология]</b>: '.$_POST['therapist2'];
							}else{
								$therapist2 = 0;
								$echo_therapist2 .= 'Лечащий врач <b>[косметология]</b> не назначен. <span style="color:red;">Такого врача нет в нашей базе</span>. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
							}
						}
						
						$name = CreateName(firspUpperCase(trim($_POST['f'])), firspUpperCase(trim($_POST['i'])), firspUpperCase(trim($_POST['o'])));
						
						//!!! Костыль для даты рождения
						//if (($_POST['sel_date'] = '00') || ($_POST['sel_month'] = '00') || ($_POST['sel_year'] = '00')){
							//$birthday = 0;
						//}else{
							$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);

							$birthday2 = $_POST['sel_year'].'-'.$_POST['sel_month'].'-'.$_POST['sel_date'];
						//}
						
						$card = str_replace(" ","",$_POST['card']);
						$card = mb_strtoupper($card, "UTF-8");
						$card = str_replace(";","; ",$card);
						$card = str_replace(",",", ",$card);
						$card = str_replace("/","/ ",$card);
						
						$new_client = WriteClientToDB_Edit ($_POST['session_id'], $name, $full_name, firspUpperCase(trim($_POST['f'])), firspUpperCase(trim($_POST['i'])), firspUpperCase(trim($_POST['o'])), firspUpperCase(trim($_POST['fo'])), firspUpperCase(trim($_POST['io'])), firspUpperCase(trim($_POST['oo'])), $_POST['comment'], $card, $therapist, $therapist2, $birthday, $birthday2, $_POST['sex'], $_POST['telephone'], $_POST['htelephone'], $_POST['telephoneo'], $_POST['htelephoneo'], $_POST['passport'], $_POST['alienpassportser'], $_POST['alienpassportnom'], $_POST['passportvidandata'], $_POST['passportvidankem'], $_POST['address'], $_POST['polis'], $_POST['polisdata'], $_POST['insurecompany']);
						//var_dump($new_client);
						
						echo '
							<div class="query_ok">
								<h3>Пациент добавлен в базу.</h3>
								<div>ФИО: <a href="client.php?id='.$new_client.'">'.$full_name.'</a></div>
								<div style="font-size: 80%; margin: 7px;">'.$echo_therapist.'</div>';
						/*if (($stom['add_own'] == 1) || $god_mode){
							echo '
								<div><a href="add_error.php" class="b" style="font-size: 70%;">Добавить посещение стоматолога</a></div>';
						}*/
						echo 
							'<div style="font-size: 80%; margin: 7px;">'.$echo_therapist2.'</div>';
						/*if (($cosm['add_own'] == 1) || $god_mode){
							echo '
								<div><a href="add_task_cosmet.php?client='.$new_client.'" class="b" style="font-size: 70%;">Добавить посещение косметолога</a></div>';
						}*/
						echo '
								<div stle="font-size: 70%; margin-top: 10px;"><a href="client_add.php" class="b">Добавить пациента</a>
								<a href="clients.php" class="b">К списку пациентов</a></div>
							</div>';
					}
				}
			}
		}
	}
?>