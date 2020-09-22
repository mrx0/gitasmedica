<?php

//reports.php
//

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
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Статистика и отчёты</h1>
				</header>';
				
							

				
			echo '
					<div id="data">';
			echo '
						<ul class="reportBlock" style="">
							<h1>Стоматология</h1>';
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat2.php" class="b3">Пропавшая первичка</a>
							</li>';
			/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat3.php" class="b3">Выборка</a>
							</li>';*/
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat4.php" class="b3">Отсутствующие зубы</a>
							</li>';

//			echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_lab_order.php" class="b3">Лабораторные работы</a>
//							</li>';

//            echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_labor.php" class="b3">Заказы в лабораторию</a>
//							</li>';

			/*echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_stomat5.php" class="b3">Все просроченные незакрытые напоминания стоматологов</a>
							</li>';*/
			echo '
						</ul>
						<ul class="reportBlock" style="">
							<h1>Косметология</h1>';
				echo '							
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm.php" class="b3">Статистика</a>
							</li>';
				/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm_ex.php" class="b3">Статистика с фильтром (старая нерабочая)</a>
							</li>';*/
				echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cosm_ex2.php" class="b3">Статистика с фильтром</a>
							</li>';

			echo '
						</ul>
						<ul class="reportBlock" style="">
								<h1>Запись и график</h1>';
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_zapis.php" class="b3">Запись</a>
							</li>';

			/*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_createSchedulerReport.php" class="b3">Ежедневный отчёт по часам</a>
							</li>';*/

            echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_add_clients.php" class="b3">Добавление пациентов</a>
							</li>';

            echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="phone_calls.php" class="b3">Статистика звонков</a>
							</li>';

			echo '
						</ul>';
            if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {

                echo '
						<ul class="reportBlock" style="">
								<h1>Финансы</h1>';

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="report_zapis_daily.php" class="b3">Ведомость</a>
							</li>';

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_cashbox.php" class="b3">Касса</a>
							</li>';

                if (($finances['see_all'] == 1) || $god_mode){
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_installments.php" class="b3">Открытые рассрочки (старое)</a>
							</li>';
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_installments2.php" class="b3">Открытые рассрочки (новое)</a>
							</li>';
                }

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="giveout_cash_all.php" class="b3">Расходные ордеры</a>
							</li>';

                if (($finances['see_all'] == 1) || $god_mode){
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_giveout_cash.php" class="b3">Расходы по филиалам</a>
							</li>';
                }

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_consolidated_report_admin.php" class="b3">Сводный отчёт по филиалу</a>
							</li>';

                if (($_SESSION['permissions'] == 3) || $god_mode){
                    echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_main_report_average.php" class="b3">Усреднённый отчёт</a>
							</li>';
                }

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_client_finance2.php" class="b3">Открытые наряды</a>
							</li>';

                /*echo '
                                <li class="cellsBlock" style="margin: 1px;">
                                    <a href="stat_client_finance3.php" class="b3">Свободные средства на счетах пациентов</a>
                                </li>';*/

                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_invoice.php" class="b3">Наряды</a>
							</li>';

//                if (($finances['see_all'] == 1) || $god_mode) {
//                    echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="fl_in_bank_add.php" class="b3">В банк</a>
//							</li>';
//                }
                echo '
						</ul>';
            }

            if (($finances['see_all'] == 1) || ($finances['see_own'] == 1) || $god_mode) {
                echo '
						<ul class="reportBlock" style="">
                            <h1>Страховые</h1>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_insure.php" class="b3">Страховые</a>
							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="insure_xls.php" class="b3">Страховые выгрузки</a>
							</li>';

                echo '
						</ul>';
            }
            /*echo '
						<ul class="reportBlock" style="">
								<h1>Работы</h1>';
			echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_invoices.php" class="b3">Наряды</a>
							</li>';

			echo '
						</ul>';*/
            if (($finances['see_all'] == 1) || $god_mode) {
                echo '
						<ul class="reportBlock" style="">
								<h1>Управление</h1>';
                /*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="calculates.php" class="b3">-%-</a>
							</li>';*/

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_tabels.php" class="b3">Важный отчёт</a>
							</li>';

//                if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
//                if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
//                    echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="fl_main_report2.php" class="b3">Финальный отчёт</a>
//							</li>';
//                }

                echo '
                            <li class="cellsBlock" style="margin: 1px;">
                                <a href="fl_tabels2.php" class="b3">Отчёт по часам</a>
                            </li>';

//                echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="fl_mainReportCategory.php" class="b3">Отчёт по категориям</a>
//							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportCategory2.php" class="b3">Отчёт по категориям</a>
							</li>';
                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="worker_zapis_report2.php" class="b3">Отчёт посещений по врачу</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="fl_mainReportZapis.php" class="b3">Отчёт по записи</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="stat_percents.php" class="b3">Отчёт по скидкам и наценкам</a>
							</li>';
                echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="material_costs_test.php" class="b3">Расходы на материалы</a>
							</li>';
                /*echo '
							<li class="cellsBlock" style="margin: 1px;">
								<a href="absents.php" class="b3">Отпуск/больничный</a>
							</li>';*/

//                echo '
//							<li class="cellsBlock" style="margin: 1px;">
//								<a href="stat_invoice2.php" class="b3">Отчёт по оплатам (выручка)</a>
//							</li>';

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
								<h1>Складской учёт [ТЕСТ]</h1>';

                echo '				
							<li class="cellsBlock" style="margin: 1px;">
								<a href="sclad.php" class="b3">Склад (тест)</a>
							</li>';

                echo '
						</ul>';
            }
            echo '
                    <div id="doc_title">Отчёты - Асмедика</div>
					</div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>