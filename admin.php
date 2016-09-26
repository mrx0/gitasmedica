<?php

//admin.php
//Админка

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){
			include_once 'DBWork.php';
			//$offices = SelDataFromDB('spr_office', '', '');
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Админка</h1>
				</header>		
				<div id="data">';
				
			//echo '<a href="shed_temlates.php" class="b">Шаблоны графиков</a>';
			echo '<a href="settins.php" class="b">Настройки</a>';
			echo '<a href="logs.php" class="b">LOGS</a>';
			echo '<a href="wrights.php" class="b">Права</a>';
			echo '<a href="/sxd" class="b">SXD</a>';			

				
			echo '			
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>