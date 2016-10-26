<?php

//index.php
//Главная

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		include_once 'DBWork.php';
		include_once 'functions.php';
		$offices = SelDataFromDB('spr_office', '', '');

		echo '
			<header style="margin-bottom: 5px;">
				<h1>Главная</h1>';
			echo '
			</header>
		
				<div id="data">';
			echo '<a href="history.php" class="b3">История изменений и обновлений <b style="color: red;">'.$version.'</b></a><br>';
			echo '		
				</div>';
		
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>