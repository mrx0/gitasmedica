<?php
	
//FastSearchNameW.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		if (isset($_POST['searchdata2'])){
			$searchdata = $_POST['searchdata2'];
		}elseif(isset($_POST['searchdata3'])){
			$searchdata = $_POST['searchdata3'];
		}elseif(isset($_POST['searchdata4'])){
			$searchdata = $_POST['searchdata4'];
		}
		if(($searchdata == '') || (strlen($searchdata) < 3)){
			//--
		}else{
			include_once 'DBWork.php';	
			$fast_search = SelForFastSearch ('spr_workers', $searchdata);
			if ($fast_search != 0){
				//var_dump ($fast_search);
				for ($i = 0; $i < count($fast_search); $i++){
					echo "\n<li>".$fast_search[$i]["full_name"]."</li>";
				}
			}
			
		}
	}

?>