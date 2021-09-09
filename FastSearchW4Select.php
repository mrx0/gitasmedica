<?php
	
//FastSearchW4Select.php
//тестовый поиск

	//var_dump ($_POST);
	if ($_POST){

		$table = 'spr_workers';

		$res = array();
		
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
                    //echo "\n<option value=".$fast_search[$i]["id"].">".$fast_search[$i]["full_name"]." [".$fast_search[$i]["type_name"]."]</option>";
                    $res['id'] = $fast_search[$i]['id'];
                    $res['name'] = $fast_search[$i]['full_name'];
                    $res['type'] = $fast_search[$i]['type_name'];
                }
//                for ($i = 0; $i < count($fast_search); $i++){
//                    echo "\n<li style='position: relative;'>".$fast_search[$i]["full_name"]."<span style='position: absolute; font-size: 80%; right: 5px; top: 4px; color: red;'>".$fast_search[$i]["type_name"]."</span></li>";
//                }

			}

            echo json_encode(array('result' => 'success', 'data' => $res));
		}
	}

?>