<?php

//client_edit_fio.php
//Редактирование карточки пациента ФИО

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
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
								<form action="client_edit_f.php">
									<div class="cellsBlock2">
										<div class="cellLeft">
											Фамилия
										</div>
										<div class="cellRight">
											<input type="text" name="f" id="f" value="'.$client[0]['f'].'">
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Имя
										</div>
										<div class="cellRight">
											<input type="text" name="i" id="i" value="'.$client[0]['i'].'">
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">
											Отчество
										</div>
										<div class="cellRight">
											<input type="text" name="o" id="o" value="'.$client[0]['o'].'">
										</div>
									</div>';
									


									
					echo '					
						
												<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
												<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
												<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
													ajax({
														url:"client_edit_fio_f.php",
														statbox:"status",
														method:"POST",
														data:
														{
															id:'.$_GET['id'].',
															
															f:document.getElementById("f").value,
															i:document.getElementById("i").value,
															o:document.getElementById("o").value,
														},
														success:function(data){document.getElementById("status").innerHTML=data;}
													})\'
												>
											</form>
									</div>
								</div>';
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