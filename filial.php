<?php

//filial.php
//Карточка филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				//include_once 'functions.php';
				
				$filial_j = SelDataFromDB('spr_filials', $_GET['id'], 'id');
				//var_dump($filial_j);
				
				if ($filial_j != 0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="filials.php" class="b">Филиалы</a>
                                </div>
								<h2>
									Карточка филиала';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($filial_j[0]['status'] != 9){
							echo '
										<a href="filial_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($filial_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_filial('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($filial_j[0]['status'] != 9){
							echo '
										<a href="filial_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>';
								
					if ($filial_j[0]['status'] == 9){
						echo '<i style="color:red;">Филиал удален (заблокирован).</i><br>';
					}
					
					echo '
							</header>';

					echo '
							<div id="data">';
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$filial_j[0]['name'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">'.$filial_j[0]['address'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$filial_j[0]['contacts'].'</div>
								</div>
							</div>';

                    echo '
                    <div id="doc_title">Карточка филиала '.$filial_j[0]['name'].'</div>';
						
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