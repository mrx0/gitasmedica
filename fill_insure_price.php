<?php

//fill_insure_price.php
//Заполнить

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['close'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$pricelistgroup = SelDataFromDB('spr_pricelist_template', $_GET['id'], 'id');
			//var_dump($pricelistgroup);
			if ($pricelistgroup !=0){
				echo '
					<div id="status">
						<header>
							<h2>Удалить(заблокировать) позицию</h2>
						</header>
						<a href="pricelist.php" class="b">В прайс</a><br>';

				echo '
						<div id="data">';
				echo '
						<div id="errrror"></div>';

				echo '
							<form action="pricelistitem_del_f.php">
								<div class="cellsBlock2" style="">
									<div class="cellLeft">
										Название
									</div>
									<div class="cellRight">
										<a href="pricelistitem.php?id='.$_GET['id'].'" class="ahref">'.$pricelistgroup[0]['name'].'</a>
									</div>
								</div>';
								
					echo '				
								<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
								<div id="errror"></div>
								<input type="button" class="b" value="Удалить(заблокировать)" onclick="Ajax_del_pricelistitem('.$_GET['id'].')">';
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