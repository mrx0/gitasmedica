<?php

//insure.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				//include_once 'functions.php';
				
				$insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');
				//var_dump($insure_j);
				
				if ($insure_j != 0){
					echo '
						<div id="status">
							<header>
								<h2>
									Карточка страховой компании';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($insure_j[0]['status'] != 9){
							echo '
										<a href="insure_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($insure_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_insure('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($insure_j[0]['status'] != 9){
							echo '
										<a href="insure_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>';
								
					if ($insure_j[0]['status'] == 9){
						echo '<i style="color:red;">Страховая удалена (заблокирована).</i><br>';												
					}
					
					echo '
							</header>
							<a href="insurcompany.php" class="b">Все страховые</a><br>';

					echo '
							<div id="data">';
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$insure_j[0]['name'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Договор</div>
									<div class="cellRight">'.$insure_j[0]['contract'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">№ договора для док-ов</div>
									<div class="cellRight">'.$insure_j[0]['contract2'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$insure_j[0]['contacts'].'</div>
								</div>
							</div>';
							
					if ($insure_j[0]['status'] != 9){		
						echo '	
							<a href="insure_price.php?id='.$insure_j[0]['id'].'" class="b">Прайс комании</a>';
					}
						
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