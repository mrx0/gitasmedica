<?php

//fl_report1.php
//Первый отчёт

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'ffun.php';

            require 'variables.php';

			//!!!Для теста ID филиала ПР54
            $filial_id = 13;

            $msql_cnnct = ConnectToDB ();

            if (isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $month = date('m');
                $year = date('Y');
            }
            $day = date("d");


            $month_stamp = mktime(0, 0, 0, $month, 1, $year);

            //Количество дней в месяце
            $day_count = date("t", $month_stamp);

            //или так
            //$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            echo '
			    <div id="data" class="report">';
            echo '
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px; background-color: #FFF;">';

            echo '
                        <li class="cellsBlock" style="font-weight:bold;">';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Дата
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Наличные
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                Безнал.
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Z-отчёт
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Аренда
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Серт-ты проданные за НАЛ
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Серт-ты проданные за БЕЗнал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                 ЛЕЧЕНИЕ<br>нал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  ЛЕЧЕНИЕ<br>б/н + серт
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                 ОРТО<br>нал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  ОРТО<br>б/н + серт
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  ОРТО<br>кол-во
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  нал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  безнал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  серт-ты
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Солярий<br>нал
                            </div>';
            echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                  Солярий<br>б/н + серт
                            </div>';
            echo '
                            <div class="cellText">
                            </div>';

            echo '
                        </li>';

            //С первого дня месяца по последний
            for($d = 1; $d <= $day_count; $d++){
                //приводим дату в норм вид
                $data = dateTransformation ($d).'.'.dateTransformation ($month).'.'.$year;
                //день недели
                $week_day = date("w", strtotime($year.'-'.$month.'-'.$d));
                //var_dump($dayWeek_arr[$week_day]);

                //цвет дня на выходных
                $weekend_block = 'cellsBlock';
                if (($week_day == 6) || ($week_day == 0)){
                    $weekend_block = 'cellsBlock6';
                }

                echo '
                        <li class="'.$weekend_block.' cellsBlockHover" style="font-weight:bold;">';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                '.$data.'
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                
                            </div>';
                echo '
                            <div class="cellText">
                            </div>';
                echo '
                        </li>';

            }

            echo '
                    </ul>
			    </div>';

			echo '

				<script type="text/javascript">
				
                    $(document).ready(function() {
                        $("#main").css({margin: \'0\', padding: \'10px 0 20px\'});                        
                        $("#data").css({margin: \'10px \'});                        
                        $("#livefilter-list").css({width: \'min-content\'});                        
                    });
				
                
				</script>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>