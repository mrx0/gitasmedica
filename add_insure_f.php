<?php 
	//var_dump ($_POST);
	if ($_POST){
		if (($_POST['name'] == '')||($_POST['contract'] == '')){
			echo '
				<div class="query_neok">
					Что-то не заполнено
				</div>';
		}else{
			include_once 'DBWork.php';
			
			WriteInsureToDB_Edit ($_POST['session_id'], $_POST['name'], $_POST['contract'], $_POST['contacts']);
		
			echo '
				<div class="query_ok">
					Страховая добавлена в справочник.
				</div>
				';
		}
	}
	
?>