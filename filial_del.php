<?php

//filial_del.php
//Удаление(блокирование) филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';

				$filial_j = SelDataFromDB('spr_filials', $_GET['id'], 'id');
				//var_dump($filial_j);
				
				if ($filial_j !=0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="filials.php" class="b">Филиалы</a>
                                </div>
								<h2>Удалить(заблокировать) филиал <a href="filial.php?id='.$_GET['id'].'" class="ahref">'.$filial_j[0]['name'].'</a></h2>
							</header>';

					echo '
							<div id="data">';
					echo '
							<div id="errrror"></div>';

					echo '
								<form action="labor_del_f.php">
									<div class="cellsBlock2" style="">
										<div class="cellLeft">
											Название
										</div>
										<div class="cellRight">
											<a href="labor.php?id='.$_GET['id'].'" class="ahref">'.$filial_j[0]['name'].'</a>
										</div>
									</div>
									';
									
					echo '				
									<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
									<div id="errror"></div>
									<input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_filial('.$_GET['id'].')">';

					echo '				
								</form>';	
					echo '
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