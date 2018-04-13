<?php
	
//FastSearchW4Select.php
//тестовый поиск

	//var_dump ($_POST);
	if ($_POST){

		$table = 'spr_workers';
		
		if (isset($_POST['search_param'])){
			$searchdata = $_POST['search_param'];
		}

		if(($searchdata == '') || (strlen($searchdata) < 2)){
			//--
		}else{
			include_once 'DBWork.php';	
			$fast_search = SelForFastSearch ($table, $searchdata);
			if ($fast_search != 0){
				//var_dump ($fast_search);
				
                for ($i = 0; $i < count($fast_search); $i++){
                    echo "\n<option value=".$fast_search[$i]["id"].">".$fast_search[$i]["full_name"]."</option>";
                }
			}
		}
	}

?>