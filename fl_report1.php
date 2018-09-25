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
                    <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';

            echo '
                        <li class="cellsBlock cellsBlockHover" style="font-weight:bold;">';
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
                            <div class="cellText">
                            </div>';

            echo '
                        </li>';

            //С первого дня месяца по последний
            for($d = 1; $d <= $day_count; $d++){
                $data = dateTransformation ($d).'.'.dateTransformation ($month).'.'.$year;

                echo '
                        <li class="cellsBlock cellsBlockHover" style="font-weight:bold;">';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                '.$data.'
                            </div>';
                echo '
                            <div class="cellTime cellsTimereport" style="text-align: center">
                                '.$data.'
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
                        //console.log(798798);
                        
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