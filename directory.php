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

            //echo '<a href="stocks.php" class="b3" title="Акции">Акции</a>';
            if (($finances['see_all'] == 1) || $god_mode) {
                echo '<a href="fl_percent_cats.php" class="b3" title="Категории процентов">Категории процентов</a>';
            }

            echo '<a href="laboratories.php" class="b3" title="Лаборатории">Лаборатории</a>';

            echo '<a href="pricelist.php" class="b3" title="Прайс">Прайс</a>';

            echo '<a href="certificates.php" class="b3" title="Сертификаты">Сертификаты</a>';

            echo '<a href="contacts.php" class="b3" title="Сотрудники">Сотрудники</a>';

            echo '<a href="specializations.php" class="b3" title="Специализация">Специализация</a>';

            echo '<a href="insurcompany.php" class="b3" title="Страховые компании">Страховые компании</a>';

            //echo '<a href="cashout_types.php" class="b3" title="Типы расходов из кассы">Типы расходов</a>';

            echo '<a href="filials.php" class="b3" title="Филиалы">Филиалы</a>';


		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>