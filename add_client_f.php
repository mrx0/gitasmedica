<?php 

//add_client_f.php
//Функция для добавления клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo 'Что-то не заполнено. Если у пациента нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
					<a href="add_client.php" class="b">Добавить</a>
					<a href="clients.php" class="b">К списку пациентов</a>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				$echo_therapist = '';
				$echo_therapist2 = '';
				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo 'В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
						<a href="add_client.php" class="b">Добавить</a>
						<a href="clients.php" class="b">К списку пациентов</a>';
				}else{
					$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Проверяем есть ли такой пациент
					if (isSameFullName('spr_clients', $full_name)){
						echo 'Такой пациент уже есть. Если тёзка, в конце поля "Отчество" поставьте символ "*"<br /><br />
							<a href="add_client.php" class="b">Добавить ещё</a>
							<a href="clients.php" class="b">К списку пациентов</a>';
					}else{
						//лечащий врач стоматология
						if ($_POST['therapist'] == ''){
							$therapist = 0;
							$echo_therapist .= 'Лечащий врач [стоматология] не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
						}else{
							$therapists = SelDataFromDB ('spr_workers', $_POST['therapist'], 'worker_full_name');
							if ($therapists != 0){
								$therapist = $therapists[0]['id'];
								$echo_therapist .= 'Лечащий врач [стоматология]: '.$_POST['therapist'];
							}else{
								$therapist = 0;
								$echo_therapist .= 'Лечащий врач [стоматология] не назначен. Такого врача нет в нашей базе. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
							}
						}
						//лечащий врач косметология
						if ($_POST['therapist2'] == ''){
							$therapist2 = 0;
							$echo_therapist2 .= 'Лечащий врач [косметология] не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
						}else{
							$therapists2 = SelDataFromDB ('spr_workers', $_POST['therapist2'], 'worker_full_name');
							if ($therapists2 != 0){
								$therapist2 = $therapists2[0]['id'];
								$echo_therapist2 .= 'Лечащий врач [косметология]: '.$_POST['therapist2'];
							}else{
								$therapist2 = 0;
								$echo_therapist2 .= 'Лечащий врач [косметология] не назначен. Такого врача нет в нашей базе. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
							}
						}
						
						$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
						//echo
						$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);
						
						$new_client = WriteClientToDB_Edit ($_POST['session_id'], $name, $full_name, $_POST['f'], $_POST['i'], $_POST['o'], $_POST['contacts'], $therapist, $therapist2, $birthday, $_POST['sex']);
						//var_dump($new_client);
						
						echo '
							<h1>Пациент добавлен в базу.</h1>
							ФИО: '.$full_name.'<br />
							'.$echo_therapist.'<br />
							<a href="add_task_stomat.php?client='.$new_client.'" class="b">Добавить посещение стоматолога</a><br /><br />'
							.$echo_therapist2.'<br />
							<a href="add_task_cosmet.php?client='.$new_client.'" class="b">Добавить посещение косметолога</a>
							<br /><br />
							<a href="add_client.php" class="b">Добавить ещё пациента</a>
							<a href="clients.php" class="b">К списку пациентов</a>
							';
					}
				}
			}
		}
	}
?>