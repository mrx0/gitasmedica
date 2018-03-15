<?php

//fast_search_test.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		include_once 'DBWork.php';
		//$offices = SelDataFromDB('spr_filials', '', '');
		//$post_data = '';
		//$js_data = '';

		
		echo '
			    <input type="text" name="searchdata" placeholder="Введите имя" value="" class="who"  autocomplete="off">
				<ul id="search_result" class="search_result"></ul><br />
				1235456
		';
			
			
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>