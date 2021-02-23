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

            //echo '<a href="stocks.php" class="b3" title="Акции">Акции</a>';
            if (($finances['see_all'] == 1) || $god_mode) {
                echo '<a href="fl_percent_cats.php" class="b3" style="width: 230px;" title="Категории процентов">Категории процентов</a>';
            }

            echo '<a href="laboratories.php" class="b3" style="width: 230px;" title="Лаборатории">Лаборатории</a>';

            if (($finances['see_all'] == 1) || $god_mode) {

                echo '<a href="fl_taxes.php" class="b3" style="width: 230px;" title="Налоги">Налоги</a>';

                echo '<a href="fl_salaries.php" class="b3" style="width: 230px;" title="Оклады сотрудников">Оклады сотрудников</a>';

                echo '<a href="fl_salaries_category.php" class="b3" style="width: 230px;" title="Оклады по должностям">Оклады по должностям</a>';

                //echo '<a href="spr_proizvcalendar.php" class="b3" style="width: 230px;" title="Производственный календарь">Производственный календарь</a>';

                echo '<a href="fl_spr_revenue_percent.php" class="b3" style="width: 230px;" title="Проценты от выручки">Проценты от выручки</a>';

                if (($_SESSION['permissions'] == 3) || $god_mode) {
                    echo '<a href="fl_surcharges.php" class="b3" style="width: 230px;" title="Прочие доплаты сотрудникам">Прочие доплаты сотрудникам</a>';
                }

                echo '<a href="orgs.php" class="b3" style="width: 230px;" title="Организации">Организации</a>';
            }

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