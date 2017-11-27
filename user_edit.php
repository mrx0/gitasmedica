<?php

//user_edit.php
//Редактирование пользователя

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($workers['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$user = SelDataFromDB('spr_workers', $_GET['id'], 'user');
			//var_dump($user);
			$arr_orgs = SelDataFromDB('spr_org', '', '');
			//var_dump($orgs);
			$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump($permissions);
			$permissions = SearchInArray($arr_permissions, $user[0]['permissions'], 'name');
			//var_dump($permissions);
            $specializations = workerSpecialization($_GET['id']);
            //var_dump($specializations);

            $specialization_j = SelDataFromDB('spr_specialization', '', '');
            //var_dump($specialization_j);
			$org = SearchInArray($arr_orgs, $user[0]['org'], 'name');
			//var_dump($org);
			
			
			if ($user !=0){
				echo '
					<div id="status">
						<header>
							<h2>Редактировать карточку пользователя</h2>
						</header>';

				echo '
						<div id="data">';

				echo '
							<form action="user_edit_f.php">
								<div class="cellsBlock2">
									<div class="cellLeft">ФИО';
				if ($god_mode || ($workers['edit'] == 1)){
					echo '    <a href="user_edit_fio.php?id='.$_GET['id'].'"><i class="fa fa-cog" title="Редактировать ФИО"></i></a>';
				}
				echo '
									</div>
									<div class="cellRight">'.$user[0]['full_name'].'</div>
								</div>
								
						<!--		<div class="cellsBlock2">
									<div class="cellLeft">Организация</div>
									<div class="cellRight">	
										<select name="org" id="org">
											<option value="0">Выбери</option>';
										for ($i=0;$i<count($arr_orgs);$i++){
											if ($arr_orgs[$i]['name'] == $org){
												$slctd = 'selected';
											}else{
												$slctd = '1';
											}
											echo "<option value='".$arr_orgs[$i]['id']."' $slctd>".$arr_orgs[$i]['name']."</option>";
										}
										echo '
										</select>
									</div>
								</div>
							-->	
								<div class="cellsBlock2">
									<div class="cellLeft">Должность</div>
									<div class="cellRight">';
											if ((($arr_permissions[$i]['id'] != 1) && ($arr_permissions[$i]['id'] != 2) && ($arr_permissions[$i]['id'] != 3) && ($arr_permissions[$i]['id'] != 8)) || ($god_mode)){
												echo '
													<select name="permissions" id="permissions">
														<option value="0">Нажми и выбери</option>';
												for ($i=0;$i<count($arr_permissions);$i++){
													if ((($arr_permissions[$i]['id'] != 1) && ($arr_permissions[$i]['id'] != 2) && ($arr_permissions[$i]['id'] != 3) && ($arr_permissions[$i]['id'] != 8)) || ($god_mode)){
														if ($arr_permissions[$i]['name'] == $permissions){
															$slctd = 'selected';
														}else{
															$slctd = '1';
														}
														echo "<option value='".$arr_permissions[$i]['id']."' $slctd>".$arr_permissions[$i]['name']."</option>";
													}
												}
												echo "</select>";
											}else{
												echo $permissions.'<input type="hidden" id="permissions" name="permissions" value="'.$user[0]['permissions'].'">';
											}
										echo '
										
									</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">Специализация</div>
									<div class="cellRight">';

                                            $specializations_temp = array();

											//Преобразуем массив чтоб id стали ключами
                                            if ($specializations != 0) {
                                                foreach ($specializations as $data) {
                                                    $specializations_temp[$data['id']] = $data;
                                                }
                                            }

                                                foreach ($specialization_j as $data) {
                                                    $chckd = '';
                                                    if (!empty($specializations_temp)) {
                                                        if (isset($specializations_temp[$data['id']])) {
                                                            $chckd = 'checked';
                                                        }
                                                    }

                                                    echo '<input type="checkbox" name="specializations[]" value="' . $data['id'] . '" ' . $chckd . '> ' . $data['name'] . '<br>';
                                                }
                                            //}

										echo '
										
									</div>
								</div>
								';

				echo '								
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">
										<textarea name="contacts" id="contacts" cols="35" rows="5">'.$user[0]['contacts'].'</textarea>
									</div>
								</div>	
								
								<div class="cellsBlock2">
									<div class="cellLeft">Уволен</div>
									<div class="cellRight">';
				if ($user[0]['fired'] == '1'){
					$chkd = 'checked';
				}else{
					$chkd = '';
				}
				echo '
										<input type="checkbox" name="fired" id="fired" value="1" '.$chkd.'>
									</div>
								</div>
											<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
											<!--<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">-->
											<input type=\'button\' class="b" value="Применить" onclick=Ajax_user_edit('.$_GET['id'].')>
										</form>';	

						echo '
						
								</div>
							</div>';
							
			//Фунция JS для проверки не нажаты ли чекбоксы + AJAX
			
			echo '
				<script>  

 
					  
				</script> 
			';	
							
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