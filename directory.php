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

            //echo '<a href="equipment.php" class="b3" title="Номенлатура">Номенлатура</a>';

            echo '<a href="abonements.php" class="b3" style="width: 230px;" title="Абонементы солярия">Абонементы</a>';

            echo '<a href="laboratories.php" class="b3" style="width: 230px;" title="Лаборатории">Лаборатории</a>';

            echo '<a href="orgs.php" class="b3" style="width: 230px;" title="Организации">Организации</a>';

            echo '<a href="pricelist.php" class="b3" style="width: 230px;" title="Прайс">Прайс</a>';

            echo '<a href="certificates.php" class="b3" style="width: 230px;" title="Сертификаты">Сертификаты</a>';

            echo '<a href="certificates_name.php" class="b3" style="width: 230px;" title="Сертификаты именные">Сертификаты именные</a>';

            echo '<a href="contacts.php" class="b3" style="width: 230px;" title="Сотрудники">Сотрудники</a>';

            echo '<a href="specializations.php" class="b3" style="width: 230px;" title="Специализация">Специализация</a>';

            echo '<a href="insurcompany.php" class="b3" style="width: 230px;" title="Страховые компании">Страховые компании</a>';

            //echo '<a href="cashout_types.php" class="b3" style="width: 230px;" title="Типы расходов из кассы">Типы расходов</a>';

            echo '<a href="filials.php" class="b3" style="width: 230px;" title="Филиалы">Филиалы</a>';


		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>