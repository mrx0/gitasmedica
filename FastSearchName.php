<?php
	
//FastSearchName.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 3)){
			//--
		}else{
			include_once 'DBWork.php';	
			$fast_search = SelForFastSearch ('spr_clients', $_POST['searchdata']);
			if ($fast_search != 0){
				//var_dump ($fast_search);
				for ($i = 0; $i < count($fast_search); $i++){
					echo "\n<li>".$fast_search[$i]["full_name"]."</li>";
				}
			}
			
		}
	}

?>