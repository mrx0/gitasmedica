<?php

//fl_percent_cats.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finances['see_all'] == 1) || $god_mode){
			
			echo '
				<header>
					<h1>Категории процентов</h1>
				</header>';
		    if (($finances['add_new'] == 1) || $god_mode){
				echo '
					<a href="fl_percent_cat_add.php" class="b">Добавить</a>';
			}
			echo '
						<p style="margin: 5px 0; padding: 2px;">
							Быстрый поиск: 
							<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						</p>
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock2" style="font-weight: bold; font-size: 11px;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellTime" style="text-align: center;">Название</div>
							<div class="cellTime" style="text-align: center">Процент за работу (общий)</div>
							<div class="cellTime" style="text-align: center;">Процент за материал (общий)</div>
							<div class="cellText" style="text-align: center;">Персонал</div>
						</li>';
			
			include_once 'DBWork.php';
			$percents_j = SelDataFromDB('fl_spr_percents', '', '');
			var_dump ($percents_j);
			
			if ($percents_j !=0){
				for ($i = 0; $i < count($percents_j); $i++) {
					if ($percents_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = '';
					}
					echo '
							<li class="cellsBlock2" style="font-weight: bold; font-size: 11px;'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="fl_percent_cat.php?id='.$percents_j[$i]['id'].'" class="cellTime ahref" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px; font-weight: bold;" id="4filter">'.$percents_j[$i]['name'].'</a>
                                <div class="cellTime" style="text-align: center">Процент за работу (общий)</div>
                                <div class="cellTime" style="text-align: center;">Процент за материал (общий)</div>
                                <div class="cellText" style="text-align: center;">Персонал</div>
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