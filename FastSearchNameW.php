<?php
	
//FastSearchNameW.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		
		$table = 'spr_workers';
		
		if (isset($_POST['searchdata2'])){
			$searchdata = $_POST['searchdata2'];
		}elseif(isset($_POST['searchdata3'])){
			$searchdata = $_POST['searchdata3'];
		}elseif(isset($_POST['searchdata4'])){
			$searchdata = $_POST['searchdata4'];
		}elseif(isset($_POST['searchdata5'])){
			$searchdata = $_POST['searchdata5'];
			$table = 'spr_pricelist_template';
		}
		if(($searchdata == '') || (strlen($searchdata) < 3)){
			//--
		}else{
			include_once 'DBWork.php';

			$fast_search = SelForFastSearch ($table, $searchdata);

			if ($fast_search != 0){
				//var_dump ($fast_search);
				
				//Если ищем не в прайсе
				if ($table != 'spr_pricelist_template'){
				    //var_dump($fast_search);

					for ($i = 0; $i < count($fast_search); $i++){
						echo "\n<li style='position: relative;'>".$fast_search[$i]["full_name"]."<span style='position: absolute; font-size: 80%; right: 5px; top: 4px; color: red;'>".$fast_search[$i]["type_name"]."</span></li>";
					}
				}else{
					for ($i = 0; $i < count($fast_search); $i++){
						echo "\n<li>".$fast_search[$i]["name"]."</li>";
					}
				}
			}
		}
	}

?>