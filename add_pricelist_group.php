<?php

//add_pricelist_group.php
//Добавить новую группу/подгруппу

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		require_once 'functions.php';
		
		if (($items['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			//тип график (космет/стомат/...)
			if (isset($_GET['who'])){
				if ($_GET['who'] == 'stom'){
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
				}elseif($_GET['who'] == 'cosm'){
					$who = '&who=cosm';
					$whose = 'Косметология ';
					$selected_stom = ' ';
					$selected_cosm = ' selected';
					$datatable = 'scheduler_cosm';
					$kabsForDoctor = 'cosm';
					$type = 6;
				}else{
					$who = '&who=stom';
					$whose = 'Стоматология ';
					$selected_stom = ' selected';
					$selected_cosm = ' ';
					$datatable = 'scheduler_stom';
					$kabsForDoctor = 'stom';
					$type = 5;
					$_GET['who'] = 'stom';
				}
			}else{
				$who = '&who=stom';
				$whose = 'Стоматология ';
				$selected_stom = ' selected';
				$selected_cosm = ' ';
				$datatable = 'scheduler_stom';
				$kabsForDoctor = 'stom';
				$type = 5;
				$_GET['who'] = 'stom';
			}
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить новую группу/подгруппу<!--'.$whose.'--></h2>
					</header>
					<a href="pricelist.php" class="b">В прайс</a><br>
					Заполните поля';

			echo '
					<div id="data">';
			echo '
						<div id="errror"></div>';
			echo '
						<form action="add_pricelistgroup_f.php" style="font-size: 90%;" class="input_form">
					
							<div class="cellsBlock2" style="margin-bottom: 5px;">
								<div class="cellLeft">Название</div>
								<div class="cellRight">
									<textarea name="groupname" id="groupname" style="width:90%; overflow:auto; height: 50px;"></textarea>
									<label id="pricename_error" class="error"></label>
								</div>
							</div>
							<div class="cellsBlock2">
								<div class="cellLeft">Расположение</div>
								<div class="cellRight">';
			echo '
									<select name="group" id="group" size="6" style="width: 250px;">
										<option value="0">*</option>';
										showTree(0, '', 'select', 0, TRUE, 0, FALSE, 'spr_pricelist_template', 0);
			echo '
									</select>';
			echo '
								</div>
							</div>

							<input type="button" class="b" value="Добавить" onclick="Ajax_add_pricegroup('.$_SESSION['id'].')">
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