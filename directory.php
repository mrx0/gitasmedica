<?php

//directory.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		echo '
			<header>
				<h1>Справочники</h1>
			</header>';

		if (($workers['see_all'] == 1) || ($workers['see_own'] == 1) || $god_mode){
			echo '<a href="contacts.php" class="b3" title="Сотрудники">Сотрудники</a><br>';
		}
		
		if (($offices['see_all'] == 1) || ($offices['see_own'] == 1) || $god_mode){
			echo '<a href="filials.php" class="b3" title="Филиалы">Филиалы</a><br>';
		}

		if (($offices['see_all'] == 1) || ($offices['see_own'] == 1) || $god_mode){
			echo '<a href="insurcompany.php" class="b3" title="Страховые компании">Страховые компании</a><br>';
		}

		
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>