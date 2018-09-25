<?php

//insurcompany.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){
			
			echo '
				<header>
					<h1>Страховые компании</h1>
				</header>';
		if (($spravka['add_new'] == 1) || $god_mode){
				echo '
					<a href="add_insure.php" class="b">Добавить</a>';
			}
			echo '

						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
			echo '
						<li class="cellsBlock" style="font-weight:bold;">	
							<div class="cellPriority" style="text-align: center"></div>
							<div class="cellOffice" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">
							    Название';
            echo $block_fast_filter;
            echo '
							</div>
							<div class="cellText" style="text-align: center">Договор</div>
							<div class="cellName" style="text-align: center">№ договора для док-ов</div>
							<div class="cellText" style="text-align: center; width: 350px; min-width: 350px; max-width: 350px;">Контакты</div>
						</li>';
			
			include_once 'DBWork.php';
			$insure_j = SelDataFromDB('spr_insure', '', '');
			//var_dump ($insure_j);
			
			if ($insure_j !=0){
				for ($i = 0; $i < count($insure_j); $i++) {
					if ($insure_j[$i]['status'] == 9){
						$bgcolor = 'background-color: rgba(161,161,161,1);';
					}else{
						$bgcolor = '';
					}
					echo '
							<li class="cellsBlock" style="'.$bgcolor.'">
								<div class="cellPriority"></div>
								<a href="insure.php?id='.$insure_j[$i]['id'].'" class="cellOffice ahref 4filter" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px; font-weight: bold;" id="4filter">'.$insure_j[$i]['name'].'</a>
								<div class="cellText" style="text-align: left">'.$insure_j[$i]['contract'].'</div>
								<div class="cellName" style="text-align: left">'.$insure_j[$i]['contract2'].'</div>
								<div class="cellText" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;">'.$insure_j[$i]['contacts'].'</div>
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