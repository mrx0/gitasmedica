<?php

//directory.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($spravka['see_all'] == 1) || ($spravka['see_own'] == 1) || $god_mode){
			echo '
				<header>
					<h1>Справочники</h1>
				</header>';

				echo '<a href="contacts.php" class="b3" title="Сотрудники">Сотрудники</a><br>';

				echo '<a href="filials.php" class="b3" title="Филиалы">Филиалы</a><br>';

				echo '<a href="insurcompany.php" class="b3" title="Страховые компании">Страховые компании</a><br>';
				
				echo '<a href="services.php" class="b3" title="Справочник услуг">Справочник услуг</a><br>';
				
				//echo '<a href="pricelist.php" class="b3" title="Прайс">Прайс</a><br>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>