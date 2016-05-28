<?php 

//client_edit_fio_f.php
//Изменение ФИО клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo 'Что-то не заполнено. Если у пациента нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
					<a href="client_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
					<a href="clients.php" class="b">К списку пациентов</a>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				$echo_therapist = '';
				$echo_therapist2 = '';
				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo 'В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
						<a href="client_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
						<a href="clients.php" class="b">К списку пациентов</a>';
				}else{
					$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Проверяем есть ли такой пациент
					if (isSameFullName('spr_clients', $full_name)){
						echo 'Такой пациент уже есть. Если тёзка, в конце поля "Отчество" поставьте символ "*"<br /><br />
							<a href="client_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
							<a href="clients.php" class="b">К списку пациентов</a>';
					}else{
						$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
						
						WriteFIOClientToDB_Update ($_SESSION['id'], $_POST['id'], $name, $full_name, $_POST['f'], $_POST['i'], $_POST['o']);
					
						echo '
							<h1>ФИО пациента изменены</h1>
							ФИО: '.$full_name.'<br />
							<br /><br />
							<a href="client.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
							<a href="clients.php" class="b">К списку пациентов</a>
							';
					}
				}
			}
		}
	}
?>