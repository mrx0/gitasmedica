<?php
	
//returnIDbyName.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		//var_dump($_POST);
		
		if (isset($_POST['searchdata2'])){
			$searchdata = $_POST['searchdata2'];
		}
		if(($searchdata == '') || (strlen($searchdata) < 3)){
			//--
		}else{
			include_once 'DBWork.php';
			$fast_search = SelForFastSearchFullName ('spr_workers', $searchdata);
			//var_dump ($searchdata);
			
			if ($fast_search != 0){
				for ($i = 0; $i < count($fast_search); $i++){
					echo $fast_search[$i]['id'];
				}
			}
			
		}
	}

?>