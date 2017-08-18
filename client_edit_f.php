<?php 

//client_edit_f.php
//Функция для редактирования карточки пациента

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			$echo_therapist = '';
			$echo_therapist2 = '';
			if ($_POST['therapist'] == ''){
				$therapist = 0;
				$echo_therapist .= 'Лечащий врач <b>[стоматология]</b> не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
			}else{
				$therapists = SelDataFromDB ('spr_workers', $_POST['therapist'], 'worker_full_name');
				if ($therapists != 0){
					$therapist = $therapists[0]['id'];
					//$echo_therapist = 'Лечащий врач: '.$_POST['therapist'];
					$echo_therapist .= '';
				}else{
					$therapist = 0;
					$echo_therapist .= 'Лечащий врач <b>[стоматология]</b> не назначен. <span style="color:red;">Такого врача нет в нашей базе</span>. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
				}
			}
			if ($_POST['therapist2'] == ''){
				$therapist2 = 0;
				$echo_therapist2 .= 'Лечащий врач <b>[косметология]</b> не назначен. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
			}else{
				$therapists2 = SelDataFromDB ('spr_workers', $_POST['therapist2'], 'worker_full_name');
				if ($therapists2 != 0){
					$therapist2 = $therapists2[0]['id'];
					//$echo_therapist2 = 'Лечащий врач: '.$_POST['therapist'];
					$echo_therapist2 .= '';
				}else{
					$therapist2 = 0;
					$echo_therapist2.= 'Лечащий врач <b>[косметология]</b> не назначен. <span style="color:red;">Такого врача нет в нашей базе</span>. Это можно указать в карточке пациента. Либо это произойдет автоматически при первом приёме.<br />';
				}
			}

			$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);

            $birthday2 = $_POST['sel_year'].'-'.$_POST['sel_month'].'-'.$_POST['sel_date'];
				
			$card = str_replace(" ","",$_POST['card']);
			$card = mb_strtoupper($card, "UTF-8");
			$card = str_replace(";","; ",$card);
			$card = str_replace(",",", ",$card);
			$card = str_replace("/","/ ",$card);
						
			WriteClientToDB_Update ($_POST['session_id'], $_POST['id'], $_POST['comment'], $card, $therapist, $therapist2, $birthday, $birthday2, $_POST['sex'], $_POST['telephone'], $_POST['passport'], $_POST['alienpassportser'], $_POST['alienpassportnom'], $_POST['passportvidandata'], $_POST['passportvidankem'], $_POST['address'], $_POST['polis'], $_POST['fo'], $_POST['io'], $_POST['oo'], $_POST['htelephone'], $_POST['telephoneo'], $_POST['htelephoneo'], $_POST['polisdata'], $_POST['insurecompany']);

			echo '
				<div class="query_ok">
					<h3>Карточка отредактирована.</h3>
					<div style="font-size: 80%; margin: 7px;">'.$echo_therapist.'</div>
					<div style="font-size: 80%; margin: 7px;">'.$echo_therapist2.'</div>
				</div>';	
		}

	}
	
?>