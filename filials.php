<?php

//filials.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		echo '
			<header>
				<h1>Координаты филиалов</h1>
			</header>';

		echo '
					<p style="margin: 5px 0; padding: 2px;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
		echo '
					<li class="cellsBlock" style="font-weight:bold;">	
						<div class="cellPriority" style="text-align: center"></div>
						<div class="cellOffice" style="text-align: center">Филиал</div>
						<div class="cellAddress" style="text-align: center">Адрес</div>
						<div class="cellText" style="text-align: center">Контакты</div>
					</li>';
		
		include_once 'DBWork.php';
		$offices = SelDataFromDB('spr_office', '', '');
		//var_dump ($offices);
		
		if ($offices !=0){
			for ($i = 0; $i < count($offices); $i++) { 
				echo '
						<li class="cellsBlock">
							<div class="cellPriority" style="background-color:"></div>
							<div class="cellOffice" style="text-align: center" id="4filter">'.$offices[$i]['name'].'</div>
							<div class="cellAddress" style="text-align: left">'.$offices[$i]['address'].'</div>
							<div class="cellText" style="text-align: left">'.$offices[$i]['contacts'].'</div>
						</li>';
			}
		}

		echo '
				</ul>
			</div>';
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>