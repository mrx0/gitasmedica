<?php

//laboratiries.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){
			
			echo '
				<header>
					<h1>Лаборатории</h1>
				</header>';
		if (($spravka['add_new'] == 1) || $god_mode){
				echo '
					<a href="labor_add.php" class="b">Добавить</a>';
			}
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
							<div class="cellOffice" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Название</div>
							<div class="cellText" style="text-align: center">-</div>
							<div class="cellText" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Контакты</div>
						</li>';
			
			include_once 'DBWork.php';
			$labor_j = SelDataFromDB('spr_labor', '', '');
			//var_dump ($labor_j);
			
			if ($labor_j !=0){
				for ($i = 0; $i < count($labor_j); $i++) {
					if ($labor_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = '';
					}
					echo '
							<li class="cellsBlock" style="'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="labor.php?id='.$labor_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px; font-weight: bold;" id="4filter">'.$labor_j[$i]['name'].'</a>
								<div class="cellText" style="text-align: left">'.$labor_j[$i]['contract'].'</div>
								<div class="cellText" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;">'.$labor_j[$i]['contacts'].'</div>
							</li>';
				}
			}

			echo '
					</ul>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>