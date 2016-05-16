<?php

//add_task_soft.php
//Добавить задачу ПО

	require_once 'header.php';
	
	if ($enter_ok){
		if (($soft['add_new'] == 1) || ($soft['add_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить задачу</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';

			echo '
						<form action="add_task_soft_f.php">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Краткое описание</div>
								<div class="cellRight">
									<input type="text" class="filter"  size="40" name="description" id="description" value="" placeholder=""/>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Подробно опишите задачу</div>
								<div class="cellRight"><textarea name="full_description" id="full_description" cols="35" rows="10"></textarea></div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Раздел</div>
								<div class="cellRight">
									<select name="priority" id="priority">
										<option value="1" selected>Косметология</option>
										<option value="2">Стоматология</option>
										<option value="3">Другое</option>
									</select>
								</div>
							</div>
							<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
							<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
								ajax({
									url:"add_task_soft_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										description:document.getElementById("description").value,
										full_description:document.getElementById("full_description").value,
										priority:document.getElementById("priority").value,
										author:document.getElementById("author").value,
									},
									success:function(data){document.getElementById("status").innerHTML=data;}
								})\'
							>
						</form>';	
				
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