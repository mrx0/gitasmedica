<?php

//add_worker.php
//Добавить сотрудника

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($workers['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			
			$orgs = SelDataFromDB('spr_org', '', '');
			$permissions = SelDataFromDB('spr_permissions', '', '');
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить пользователя</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';

			echo '
						<form action="add_worker_f.php">
					
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									<input type="text" name="f" id="f" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									<input type="text" name="i" id="i" value="">
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									<input type="text" name="o" id="o" value="">
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Контакты</div>
								<div class="cellRight"><textarea name="contacts" id="contacts" cols="35" rows="5"></textarea></div>
							</div>
							
							<!--<div class="cellsBlock2">
								<div class="cellLeft">Организация</div>
								<div class="cellRight">
									<select name="org" id="org">
										<option value="0" selected>Нажми и выбери</option>';
									if ($orgs !=0){
										for ($i=0;$i<count($orgs);$i++){
											echo "<option value='".$orgs[$i]['id']."'>".$orgs[$i]['name']."</option>";
										}
									}
									echo '
									</select>
								</div>
							</div>-->
							
							<div class="cellsBlock2">
								<div class="cellLeft">Должность</div>
								<div class="cellRight">
									<select name="permissions" id="permissions">
										<option value="0" selected>Нажми и выбери</option>';
									if ($permissions !=0){
										//if ($god_mode){
											for ($i=0;$i<count($permissions);$i++){
												if ((($permissions[$i]['id'] != 1) && ($permissions[$i]['id'] != 2) && ($permissions[$i]['id'] != 3) && ($permissions[$i]['id'] != 8)) || ($god_mode)){
													echo "<option value='".$permissions[$i]['id']."'>".$permissions[$i]['name']."</option>";
												}
											}
										//}else{
										//	echo "<option value='0'>По умолчанию</option>";
										//}
									}
									echo '
									</select>
								</div>
							</div>
							
							<input type=\'button\' class="b" value=\'Добавить\' onclick=\'
								ajax({
									url:"add_worker_f.php",
									statbox:"status",
									method:"POST",
									data:
									{
										f:document.getElementById("f").value,
										i:document.getElementById("i").value,
										o:document.getElementById("o").value,
										
										contacts:document.getElementById("contacts").value,
										//org:document.getElementById("org").value,
										org:0,
										permissions:document.getElementById("permissions").value,
										
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>