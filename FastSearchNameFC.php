<?php
	
//FastSearchName.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 3)){
			//--
		}else{
			include_once 'DBWork.php';	
			$fast_search = SelForFastSearchFullName ('spr_clients', $_POST['searchdata']);
			if ($fast_search != 0){
				//var_dump ($fast_search);
				for ($i = 0; $i < count($fast_search); $i++){
					echo "<div style='border-bottom: 1px #ccc solid; width: 350px;'>
					<a href='client.php?id=".$fast_search[$i]["id"]."' class='ahref' style='display: block; height: 100%;'>
					<span style='font-size: 80%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span><br />
					<span style='font-size: 70%'>Дата рождения: ", $fast_search[$i]['birthday'] == "-1577934000" ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ,"</span><br />
					<span style='font-size: 70%'>Контакты: ".$fast_search[$i]['contacts']."</span>
					</a>
					</div>";
				}
			}
			
		}
	}

?>