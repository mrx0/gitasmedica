<?php

//pricelist.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($pricelist['see_all'] == 1) || ($pricelist['see_own'] == 1) || $god_mode){
			
			echo '
				<header>
					<h1>Прайс лист</h1>
				</header>';
		if (($pricelist['add_new'] == 1) || $god_mode){
				echo '
					<a href="add_priceitem.php" class="b">Добавить</a>';
			}
			echo '
						<p style="margin: 5px 0; padding: 2px;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						</p>
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
								<li class="cellsBlock" style="font-weight:bold; width: auto; margin-bottom: 10px;">
									<div class="cellPriority" style="text-align: center"></div>
									<div class="cellName" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Наименование услуг</div>
									<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">Цена, руб.</div>
								</li>';
			
			include_once 'DBWork.php';
			$pricelist_j = SelDataFromDB('journal_pricelist', '', '');
			//var_dump ($pricelist_j);
			
			if ($pricelist_j !=0){
				for ($i = 0; $i < count($pricelist_j); $i++) {
					echo '
							<li class="cellsBlock">
								<div class="cellPriority" style="background-color:"></div>
								<div class="cellOffice" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$pricelist_j[$i]['name'].'</div>
								<div class="cellText" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;">'.$pricelist_j[$i]['contacts'].'</div>
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