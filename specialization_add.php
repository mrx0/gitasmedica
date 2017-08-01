<?php

//specialization_add.php
//Добавить специализацию

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		
		include_once 'DBWork.php';
		
		echo '
			<div id="status">
				<header>
                    <div class="nav">
						<a href="specializations.php" class="b">Специализации</a>
					</div>
					<h2>Добавить специализацию</h2>
					Заполните поля
				</header>';

		echo '
				<div id="data">';
		echo '				
					<div id="errror"></div>';
		echo '
					<form action="specialization_add_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Название</div>
							<div class="cellRight">
								<input type="text" name="name" id="name" value="">
							</div>
						</div>

						<input type="button" class="b" value="Добавить" onclick="Ajax_specialization_add(\'add\')">
					</form>';	
			
		echo '
				</div>
			</div>';
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>