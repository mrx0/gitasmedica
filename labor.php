<?php

//labor.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				//include_once 'functions.php';
				
				$labor_j = SelDataFromDB('spr_labor', $_GET['id'], 'id');
				//var_dump($labor_j);
				
				if ($labor_j != 0){
					echo '
						<div id="status">
							<header>
								<h2>
									Карточка лаборатории';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($labor_j[0]['status'] != 9){
							echo '
										<a href="labor_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($labor_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_labor('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($labor_j[0]['status'] != 9){
							echo '
										<a href="labor_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>';
								
					if ($labor_j[0]['status'] == 9){
						echo '<i style="color:red;">Лаборатория удалена (заблокирована).</i><br>';
					}
					
					echo '
							</header>
							<a href="laboratories.php" class="b">Все лаборатории</a><br>';

					echo '
							<div id="data">';
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$labor_j[0]['name'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Договор</div>
									<div class="cellRight">'.$labor_j[0]['contract'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$labor_j[0]['contacts'].'</div>
								</div>
							</div>';
							
					/*if ($labor_j[0]['status'] != 9){
						echo '	
							<a href="labor_price.php?id='.$labor_j[0]['id'].'" class="b">Прайс комании</a>';
					}*/
						
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