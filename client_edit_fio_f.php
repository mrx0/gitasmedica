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
				echo '
					<div class="query_neok">
						Что-то не заполнено. Если у пациента нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
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
					if (isSameFullName('spr_clients', $full_name, $_POST['id'])){
						echo '
							<div class="query_neok">
								Такой пациент уже есть. Если тёзка, в конце отчества поставьте символ *<br /><br />
							</div>';
					}else{
						$name = CreateName(firspUpperCase(trim($_POST['f'])), firspUpperCase(trim($_POST['i'])), firspUpperCase(trim($_POST['o'])));
						
						WriteFIOClientToDB_Update ($_SESSION['id'], $_POST['id'], $name, $full_name, $_POST['f'], $_POST['i'], $_POST['o']);
					
						echo '
							<div class="query_ok">
								<h3>ФИО клиента изменены</h3>
								ФИО: '.$full_name.'<br>
								<br><br>
								<a href="client.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
								<a href="clients.php" class="b">К списку клиентов</a>
							</div>
							';
					}
				}
			}
		}
	}
?>