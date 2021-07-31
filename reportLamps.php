<?php

//reportLamps.php
//Отчет по лампам

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

        include_once('DBWorkPDO.php');
        include_once 'DBWork.php';
        include_once 'functions.php';
        //include_once 'widget_calendar.php';
        include_once 'ffun.php';
        require 'variables.php';

        //Опция доступа к филиалам конкретных сотрудников
        //$optionsWF = getOptionsWorkerFilial($_SESSION['id']);
        //var_dump($optionsWF);

        //if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){
        if (($finances['see_all'] == 1) || $god_mode){


            //$permissions_sort_method = [5,6,10,7,4,13,14,15,9,12,11,777];

            $filials_j = getAllFilials(true, true, false);
            //var_dump($filials_j);


            //Даты
            if (isset($_GET['m_start']) && isset($_GET['y_start'])){
                //операции со временем
                $month_start = $_GET['m_start'];
                $year_start = $_GET['y_start'];
            }else{
                //операции со временем
                $month_start = date('m');
                $year_start = date('Y');
            }

            if (isset($_GET['m_end']) && isset($_GET['y_end'])){
                //операции со временем
                $month_end = $_GET['m_end'];
                $year_end = $_GET['y_end'];
            }else{
                //операции со временем
                $month_end = date('m');
                $year_end = date('Y');
            }

            $day_start = date("d");


            echo '
                <div id="status">
                    <header id="header">
                        <div class="nav">
                        </div>
                        <h2 style="padding: 0;">Отчёт по Лампам</h2>
                    </header>';



            echo '
                    <div id="data">';

            echo '				
                        <div id="errrror"></div>';

            echo '
                        <div class="no_print">';

            echo '
                            <ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
								<li style="margin-bottom: 10px;">
                                    Выберите условие
                                </li>
								
								<li class="filterBlock">

									<div class="filtercellRight" style="width: 378px; min-width: 378px;">';

            echo '
                                        Выберите период: <br>от <select name="month_start" id="month_start" style="margin-right: 5px;">';

            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == $month_start){
                    $selected = 'selected';
                }
                echo '
                                            <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
                                        </select>
                                        <select name="year_start" id="year_start">';

            for ($i = (int)date('Y')-2; $i <= (int)date('Y')+1; $i++){
                $selected = '';
                if ($i == $year_start){
                    $selected = 'selected';
                }
                echo '
                                            <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
                                        </select>';
            echo '
                                        и до <select name="month_end" id="month_end" style="margin-right: 5px;">';

            foreach ($monthsName as $mNumber => $mName){
                $selected = '';
                if ((int)$mNumber == $month_end){
                    $selected = 'selected';
                }
                echo '
            <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
            }
            echo '
                                        </select>
                                        <select name="year_end" id="year_end">';

            for ($i = (int)date('Y')-2; $i <= (int)date('Y')+1; $i++){
                $selected = '';
                if ($i == $year_end){
                    $selected = 'selected';
                }
                echo '
            <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            echo '
                                        </select>
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
                                        Филиал
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">';
            echo '	
                                        <select name="SelectFilial" id="SelectFilial">';

            foreach ($filials_j as $filial_item) {

                    echo '
                                            <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name'] . '</option>';
            }

            echo '
                                        </select>';

            echo '
									</div>
								</li>

							</ul>';

            echo '<div><span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick="reportLamps();"><i class="fa fa-check-square" style=" color: green;"></i> Применить</span></div>';

            echo '
                        </div>';

            //Табличка с результатами
            echo '
                        <div id="res_table_tmpl" style="margin-top: 10px;"></div>';



            echo '
                    </div>
                </div>';

            echo '
                <div class="no_print" style="position: fixed; top: 45px; right: 10px; border: 1px solid #0C0C0C; border-radius: 5px; padding: 5px 5px; background-color: #FFFFFF">
                    <div class="cellCosmAct b" style="text-align: center; display: inline-block !important; vertical-align: middle; height: auto; border-radius: 3px;"
                    onclick="window.print();">
                        <i class="fa fa-print" aria-hidden="true"></i>
                    </div>
                </div>';

            echo '    
                <div id="doc_title">Отчёт по Лампам - Асмедика</div>';

            echo '	
			    <!-- Подложка только одна -->
			    <div id="overlay"></div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}

	require_once 'footer.php';

?>