<?php

//add_filial.php
//Добавить филиал

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		include_once 'DBWork.php';
		
		echo '
			<div id="status">
				<header>
					<h2>Добавить филиал</h2>
					Заполните поля
				</header>';

		echo '
				<div id="data">';

		echo '
					<form action="add_filial_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Название</div>
							<div class="cellRight">
								<input type="text" name="name" id="name" value="">
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Адрес</div>
							<div class="cellRight">
								<textarea name="address" id="address" cols="35" rows="5"></textarea>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Контакты</div>
							<div class="cellRight">
								<textarea name="contacts" id="contacts" cols="35" rows="5"></textarea>
							</div>
						</div>

						<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
							ajax({
								url:"add_filial_f.php",
								statbox:"status",
								method:"POST",
								data:
								{
									name:document.getElementById("name").value,
									address:document.getElementById("address").value,
									contacts:document.getElementById("contacts").value,
									session_id:'.$_SESSION['id'].',
								},
								success:function(data){document.getElementById("status").innerHTML=data;}
							})\'
						>
					</form>';	
			
		echo '
				</div>
			</div>';
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>