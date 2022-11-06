<?php

//reports.php
//Отчёты

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($report['see_all'] == 1) || ($report['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'widget_calendar.php';
			
			$filter = FALSE;
			$dop = '';

            //Опция доступа к филиалам конкретных сотрудников
            $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
            //var_dump($optionsWF);

            //Для доступа по специализациям (костыль?)
            $specialization_j = SelDataFromDB('spr_specialization', $_SESSION['permissions'], 'permission');
            //var_dump($specialization_j);
            $specializations = array();

            if ($specialization_j != 0) {
                foreach ($specialization_j as $spec_data) {
                    array_push($specializations, $spec_data['id']);
                }
                //var_dump($specializations);
                //var_dump(in_array(11, $specializations));
            }
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Статистика и отчёты</h1>
				</header>';




			echo '
					<div id="data">';
			echo '
						<ul class="reportBlock" style="">
							<h2>Стоматология</h2>';
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat2.php" class="b3 reportElement" style="">Пропавшая первичка</a>
							</li>';

			/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat3.php" class="b3 reportElement" style="">Выборка</a>
							</li>';*/
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat4.php" class="b3 reportElement" style="">Отсутствующие зубы</a>
							</li>';

//			echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_lab_order.php" class="b3 reportElement" style="">Лабораторные работы</a>
//							</li>';

//            echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_labor.php" class="b3 reportElement" style="">Заказы в лабораторию</a>
//							</li>';

			/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat5.php" class="b3 reportElement" style="">Все просроченные незакрытые напоминания стоматологов</a>
							</li>';*/

			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="temp_notes_cert.php" class="b3 reportElement" style="">Выданные сертификаты</a>
							</li>';
			echo '
						</ul>
						<ul class="reportBlock" style="">
							<h2>Косметология</h2>';
				echo '							
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm.php" class="b3 reportElement" style="">Статистика</a>
							</li>';
				/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm_ex.php" class="b3 reportElement" style="">Статистика с фильтром (старая нерабочая)</a>
							</li>';*/
				echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm_ex2.php" class="b3 reportElement" style="">Статистика с фильтром</a>
							</li>';

			echo '
						</ul>
						<ul class="reportBlock" style="">
								<h2>Запись и график</h2>';
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_zapis.php" class="b3 reportElement" style="">Запись</a>
							</li>';

			/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_createSchedulerReport.php" class="b3 reportElement" style="">Ежедневный отчёт по часам</a>
							</li>';*/

            echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_add_clients.php" class="b3 reportElement" style="">Добавление пациентов</a>
							</li>';

            echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="phone_calls.php" class="b3 reportElement" style="">Статистика звонков</a>
							</li>';

            if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_lost_pervich.php" class="b3 reportElement" style="">Пропавшая первичка v2.0</a>
							</li>';
            }

            echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportCategory.php" class="b3 reportElement" style="">Отчёт по категориям</a>
							</li>';


            echo '
						</ul>';
            if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || in_array(11, $specializations) || $god_mode) {


                echo '
						<ul class="reportBlock" style="">
								<h2>Финансы</h2>';

                if (!in_array(11, $specializations)) {
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="report_zapis_daily.php" class="b3 reportElement" style="">Ведомость</a>
							</li>';

                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cashbox.php" class="b3 reportElement" style="">Касса</a>
							</li>';
                }

                if (($finances['see_all'] == 1) || in_array(11, $specializations) || $god_mode){
                    if (!in_array(11, $specializations)) {
                        echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_installments.php" class="b3 reportElement" style="">Открытые рассрочки (старое)</a>
							</li>';
                    }
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_installments2.php" class="b3 reportElement" style="">Открытые рассрочки (новое)</a>
							</li>';
                }

                if (!in_array(11, $specializations)) {
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="giveout_cash_all.php" class="b3 reportElement" style="">Расходные ордеры</a>
							</li>';
                }

                if (($finances['see_all'] == 1) || $god_mode){
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_giveout_cash.php" class="b3 reportElement" style="">Расходы по филиалам</a>
							</li>';
                }

                if (!in_array(11, $specializations)) {
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_consolidated_report_admin.php" class="b3 reportElement" style="">Сводный отчёт по филиалу</a>
							</li>';
                }

                if (($_SESSION['permissions'] == 3) || $god_mode){
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_main_report_average.php" class="b3 reportElement" style="">Усреднённый отчёт</a>
							</li>';
                }

                if (!in_array(11, $specializations)) {
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_client_finance2.php" class="b3 reportElement" style="">Открытые наряды</a>
							</li>';

                    /*echo '
                                    <li class="cellsBlock" style="margin: 1px;">
                                        <a href="stat_client_finance3.php" class="b3 reportElement">Свободные средства на счетах пациентов</a>
                                    </li>';*/

                    echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_invoice.php" class="b3 reportElement" style="">Наряды</a>
							</li>';

//                if (($finances['see_all'] == 1) || $god_mode) {
//                    echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="fl_in_bank_add.php" class="b3 reportElement" style="">В банк</a>
//							</li>';
//                }
                }

                if (($finances['see_all'] == 1) || $god_mode) {
                    echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_client_finance3.php" class="b3 reportElement" style="">Пациенты по сумме ордеров</a>
							</li>';
                }

                echo '
						</ul>';
            }

            if (($finances['see_all'] == 1) /*|| ($finances['see_own'] == 1)*/ || $god_mode    || ($_SESSION['id'] == 719)) {
                echo '
						<ul class="reportBlock" style="">
                            <h2>Страховые</h2>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_insure.php" class="b3 reportElement" style="">Страховые</a>
							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="insure_xls.php" class="b3 reportElement" style="">Страховые выгрузки</a>
							</li>';

                echo '
						</ul>';
            }
            /*echo '
						<ul class="reportBlock" style="">
								<h2>Работы</h2>';
			echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_invoices.php" class="b3 reportElement" style="">Наряды</a>
							</li>';

			echo '
						</ul>';*/
            if (($finances['see_all'] == 1) || $god_mode) {
                echo '
						<ul class="reportBlock" style="">
								<h2>Управление</h2>';
                /*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="calculates.php" class="b3 reportElement" style="">-%-</a>
							</li>';*/

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_tabels.php" class="b3 reportElement" style="">Важный отчёт</a>
							</li>';

//                if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
//                if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
//                    echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="fl_main_report2.php" class="b3 reportElement" style=">Финальный отчёт</a>
//							</li>';
//                }

                echo '
                            <li class="cellsBlock" style="margin: 1px;">
                                <a href="fl_tabels2.php" class="b3 reportElement" style="">Отчёт по часам</a>
                            </li>';

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportCategory2.php" class="b3 reportElement" style="">По категориям (общ. соотношение)</a>
							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportCategory3.php" class="b3 reportElement" style="">Отчёт по категориям 3</a>
							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="worker_zapis_report2.php" class="b3 reportElement" style="">Отчёт посещений по врачу</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportZapis.php" class="b3 reportElement" style="">Отчёт по записи</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_percents.php" class="b3 reportElement" style="">Отчёт по скидкам и наценкам</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="material_costs_test.php" class="b3 reportElement" style="">Расходы на материалы</a>
							</li>';
                /*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="absents.php" class="b3 reportElement" style="">Отпуск/больничный</a>
							</li>';*/

//                echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_invoice2.php" class="b3 reportElement" style="">Отчёт по оплатам (выручка)</a>
//							</li>';

                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="remarks_to_employees.php" class="b3 reportElement" style="">Замечания сотрудникам</a>
							</li>';

                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="reviews.php" class="b3 reportElement" style="">Отзывы</a>
							</li>';


                //if (($finances['see_all'] == 1) || $god_mode) {



//                echo '
//                            <li class="cellsBlock" style="margin: 1px;">
//                                <a href="fl_report_noch.php" class="b3">Ночь</a>
//                            </li>';


                //}
                echo '
						</ul>';
            }

            if (($finances['see_all'] == 1) || $god_mode) {
                echo '
						<ul class="reportBlock" style="">
								<h2>Учёт</h2>';


                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="reportLamps.php" class="b3 reportElement" style="">Отчет по лампам</a>
							</li>';

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="sclad.php" class="b3 reportElement" style="">Склад (тест)</a>
							</li>';

                echo '
						</ul>';
            }
            echo '
                    <div id="doc_title">Отчёты - Асмедика</div>
					</div>';

		}else{
			echo '<h2>Не хватает прав доступа.</h2><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>