<?php

//add_insure_price_item.php
//Добавить услугу в прайс страховой из общего
//Нигде не используется, удаляю 2018-08-31

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');
			
			//if ($insure_j != 0){
				
				//операции со временем						
				$day = date('d');		
				$month = date('m');		
				$year = date('Y');

				echo '
					<div id="status">
						<header>
							<h2>Добавить позицию</h2>
							<a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a>
						</header>';
			
				echo '
						<a href="insure_price.php?id='.$_GET['id'].'" class="b">В прайс</a><br>';
				echo '
						<div id="data">';
						
				echo '
							<div id="errror"></div>';
				echo '
							<form action="add_pricelistitem_f.php" style="font-size: 90%;" class="input_form">
						
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight" id="worker_name">
										<input type="text" size="30" name="searchdata5" id="search_client5" placeholder="Введите название позиции" value="" class="who5"  autocomplete="off" style="width: 90%;">
										<ul id="search_result5" class="search_result5"></ul><br />
									</div>
								</div>';

				echo '
							<input type="button" class="b" value="Добавить" disabled onclick="Ajax_add_insure_priceitem()">
						</form>
					</div>';	
				
			echo '
					</div>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>