<?php

//add_task.php
//Добавить задачу IT

	require_once 'header.php';
	
	if ($enter_ok){
		if (($it['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			$offices = SelDataFromDB('spr_office', '', '');
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить задачу</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';

			echo '
						<form action="add_task_f.php">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">
									<select name="filial" id="filial">
										<option value="99" selected>Выберите или не меняйте если для всех</option>';
									if ($offices !=0){
										for ($i=0;$i<count($offices);$i++){
											echo "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
										}
									}
									echo '
									</select>
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Подробно опишите проблему</div>
								<div class="cellRight"><textarea name="description" id="description" cols="35" rows="10"></textarea></div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Приоритет</div>
								<div class="cellRight">';
			if (($it['edit'] == 1) || $god_mode){					
				echo '					
									<select name="priority" id="priority">
										<option value="1" selected>Низкий</option>
										<option value="2">Средний</option>
										<option value="3">Высокий</option>
									</select>';
			}else{
				echo '					
					Низкий по умолчанию.
					<input type="hidden" id="priority" name="priority" value="1">';
			}
			echo '
								</div>
							</div>
							<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
							<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
								ajax({
									url:"add_task_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										filial:document.getElementById("filial").value,
										description:document.getElementById("description").value,
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