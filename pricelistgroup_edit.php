<?php

//pricelistgroup_edit.php
//Редактирование 

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if ($god_mode || $_SESSION['permissions'] == 3 || ($clients['add_own'] == 1)){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$pricelistgroup_j = SelDataFromDB('spr_storagegroup', $_GET['id'], 'id');
				//var_dump($pricelistgroup_j);
				
				if ($pricelistgroup_j !=0){
					echo '
						<div id="status">
							<header>
								<h2>Редактировать <a href="pricelistgroup.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							</header>
							<a href="pricelist.php" class="b">В прайс</a><br>';

					echo '
							<div id="data">';
					echo '
								<div id="errror"></div>';
					echo '
								<form action="pricelistgroup_edit_f.php">
					
									<div class="cellsBlock2">
										<div class="cellLeft">Название</div>
										<div class="cellRight">
											<textarea name="pricelistgroupname" id="pricelistgroupname" style="width:90%; overflow:auto; height: 50px;">'.$pricelistgroup_j[0]['name'].'</textarea>
											<label id="pricelistgroupname_error" class="error"></label>
										</div>
									</div>
									<div class="cellsBlock2">
										<div class="cellLeft">Расположение</div>
										<div class="cellRight">';
					//$itemsingroups_j = SelDataFromDB('spr_storagegroup', $_GET['level'], 'level');
					//var_dump($itemsingroups_j);
					
					if ($pricelistgroup_j[0]['level'] != 0){
						$selected = $pricelistgroup_j[0]['level'];
					}else{
						$selected = 0;
					}
					
					//var_dump($selected);
					echo '
											<select name="group" id="group" size="6" style="width: 250px;">
												<option value="0" ',$selected == 0 ? 'selected' : '','>*</option>';
					showTree(0, '', 'select', $selected, TRUE, 0, FALSE, 'spr_pricelist_template', 0);
					echo '
											</select>';
					echo '
										</div>
									</div>
									<input type="button" class="b" value="Применить" onclick="Ajax_edit_pricelistgroup('.$_GET['id'].', '.$_SESSION['id'].')">
								</form>
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