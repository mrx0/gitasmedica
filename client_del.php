<?php

//client_del.php
//Удаление(блокирование) карточки пациента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($clients['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			//var_dump($_SESSION);
			if ($client !=0){
				echo '
					<div id="status">
						<header>
							<h2>Удалить(заблокировать) карточку пациента</h2>
						</header>';

				echo '
						<div id="data">';
				echo '
						<div id="errrror"></div>';

				echo '
							<form action="client_edit_f.php">
								<div class="cellsBlock2" style="margin-bottom: 20px;">
									<div class="cellLeft">
										ФИО';
				echo '
									</div>
									<div class="cellRight">
										<a href="client.php?id='.$_GET['id'].'" class="ahref">'.$client[0]['full_name'].'</a>
									</div>
								</div>';
								
				//Выберем из базы последнюю запись
				$t_f_data_db = array();

                $time = time();

                $msql_cnnct = ConnectToDB ();

				$query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$_GET['id']}' ORDER BY `create_time` DESC LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					while ($arr = mysqli_fetch_assoc($res)){
						array_push($t_f_data_db, $arr);
					}
				}else
					$t_f_data_db = 0;
				}

                CloseDB ($msql_cnnct);

				//Косметология					
				$cosmet_task = SelDataFromDB('journal_cosmet1', $_GET['id'], 'client_cosm_id');
				//var_dump ($cosmet_task);
				
				//Долги/авансы					
				$clientDP = DebtsPrepayments ($_GET['id']);
				//var_dump ($clientDP);
				
				if (($t_f_data_db != 0) || ($cosmet_task != 0) || ($clientDP != 0)){
					echo '<i style="color:red;">У пациента есть посещения или открыт счёт. Удалять нельзя.</i>';
				}else{
					echo '				
								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_client('.$_SESSION['id'].')">';
				}


				echo '				
							</form>';	
				echo '
						</div>
					</div>';

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