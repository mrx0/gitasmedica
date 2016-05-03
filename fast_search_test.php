<?php

//add_task.php
//Добавить задачу

	require_once 'header.php';
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//$offices = SelDataFromDB('spr_office', '', '');
		//$post_data = '';
		//$js_data = '';

		
		echo '
			    <input type="text" name="searchdata" placeholder="Введите имя" value="" class="who"  autocomplete="off">
				<ul id="search_result" class="search_result"></ul><br />
				1235456
		';
			
			
	}	
		
	require_once 'footer.php';

?>