<?php

//admin.php
//Админка

	require_once 'header.php';

	if ($enter_ok){
		if ($god_mode){
			include_once 'DBWork.php';
			//$offices = SelDataFromDB('spr_office', '', '');
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Логи</h1>
				</header>		
				<p style="margin: 5px 0; padding: 1px; font-size:80%;">
					Быстрый поиск по врачу: 
					<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
				</p>
				<div id="data">
					<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
				
			$logs = SelDataFromDB ('logs', '', '');
			if ($logs != 0){
				//var_dump ($logs);
				for ($i=0; $i<count($logs); $i++){
					echo '
						<li class="cellsBlock cellsBlockHover">
							<div class="cellCosmAct" style="background-color:'.$logs[$i]['id'].'"></div>
							<div class="cellTime">'.date('d.m.y H:i', $logs[$i]['date']).'</div>
							<div class="cellName"  id="4filter">'.$logs[$i]['creator'].'</div>
							<div class="cellOffice" style="text-align: center;">'.$logs[$i]['ip'].'</div>
							<div class="cellOffice" style="text-align: center;">'.$logs[$i]['mac'].'</div>
							<div class="cellText">'.$logs[$i]['description_new'].'<hr>
								<span style="background:#f0f0f0;">'.$logs[$i]['description_old'].'</span>
							</div>
						</li>';
					
				}
			}
			echo '	</ul>		
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>