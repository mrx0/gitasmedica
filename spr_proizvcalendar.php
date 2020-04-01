<?php

//spr_proizvcalendar.php
//Производственный календарь

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($scheduler['see_all'] == 1) /*|| ($scheduler['see_own'] == 1)*/ || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'widget_calendar.php';
			include_once 'variables.php';

            //Массив с месяцами
            $monthsName = array(
                '01' => 'Январь',
                '02' => 'Февраль',
                '03' => 'Март',
                '04' => 'Апрель',
                '05' => 'Май',
                '06' => 'Июнь',
                '07'=> 'Июль',
                '08' => 'Август',
                '09' => 'Сентябрь',
                '10' => 'Октябрь',
                '11' => 'Ноябрь',
                '12' => 'Декабрь'
            );

            $dop = '';
            $dopWho = '';
            $dopDate = '';
            $dopFilial = '';

			if (isset($_GET['y'])){
				//операции со временем						
				$year = $_GET['y'];
			}else{
				//операции со временем						
				$year = date('Y');
			}

            $month = date('m');

			//Сегодняшняя дата
			$day = date("d");
            $cur_month = date("m");
            $cur_year = date("Y");
			
			foreach ($_GET as $key => $value){
				if (($key == 'd') || ($key == 'm') || ($key == 'y'))
					$dopDate  .= '&'.$key.'='.$value;
				if ($key == 'filial'){
					$dopFilial .= '&'.$key.'='.$value;
					$dop .= '&'.$key.'='.$value;
				}
				if ($key == 'who'){
					$dopWho .= '&'.$key.'='.$value;
					$dop .= '&'.$key.'='.$value;
				}
			}

			$today = date("Y-m-d");

            $msql_cnnct = ConnectToDB ();

            //Получаем календарь выходных на указанный год
            $holidays_arr = array();

            $query = "SELECT * FROM `spr_proizvcalendar_holidays` WHERE `year` = '$year'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    if (!isset($holidays_arr[$arr['month']])) {
                        $holidays_arr[$arr['month']] = array();
                    }
                    array_push($holidays_arr[$arr['month']], $arr['day']);
                }
            }
            //var_dump($holidays_arr);

            //Получаем ДР сотрудников
            //Получаем календарь выходных на указанный год
            $birthdays_arr = array();

            $query = "SELECT `id`, `name`, `birth` FROM `spr_workers` WHERE `birth` <> '0000-00-00'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив

                    $bday_index = explode('-', $arr['birth'])[1].'-'.explode('-', $arr['birth'])[2];

                    if (!isset($birthdays_arr[$bday_index])) {
                        $birthdays_arr[$bday_index] = array();
                    }

                    array_push($birthdays_arr[$bday_index], $arr);
                }
            }
            //var_dump($birthdays_arr);


            //переменная, чтоб вкл/откл редактирование
            $iCanManage = 'false';
            $displayBlock = false;

            echo '
				<script>';
            if (isset($_SESSION['options'])){
                if (isset($_SESSION['options']['scheduler'])) {
                    $iCanManage = $_SESSION['options']['scheduler']['manage'];
                    if ($_SESSION['options']['scheduler']['manage'] == 'true') {
                        $displayBlock = true;
                    }
                }
            }else{
            }

            echo '
                    var iCanManage = '.$iCanManage.';';

            echo '
				</script>';

			echo '
			
				<div id="status">
					<div class="no_print"> 
					<header>
						<div class="nav">
                            <!-- ! -->
						</div>
						<!--<span style="color: red;">Тестовый режим</span>-->
						<h2>Производственный календарь</h2>
					</header>';
			echo '
					</div>';
			echo '
					<div id="data" style="margin-top: 5px;">
					    <input type="hidden" id="type" value="">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
			if (($scheduler['edit'] == 1) || ($scheduler['add_worker'] == 1) || $god_mode){
				echo '
							<!--<div class="no_print"> 
                                <li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
                                    <div style="cursor: pointer;" onclick="manageScheduler(\'scheduler\')">
                                        <span id="manageMessage" style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">', $displayBlock ? 'Управление <span style=\'color: green;\'>включено</span>' : 'Управление <span style=\'color: red;\'>выключено</span>' ,'</span> <i class="fa fa-cog" title="Настройки"></i>
                                    </div>
                                </li>
                            </div>-->';
			}

			echo '
                            <div class="no_print">';

            echo '
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                    <span style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="spr_proizvcalendar.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>
                                </li>
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
                                    <a href="spr_proizvcalendar.php?y='. ($year - 1) .'" class="cellTime ahref" style="text-align: center;">
                                        <span style="font-weight: normal; font-size: 80%;"><< '.($year - 1).'</span>
                                    </a>
                                    <div class="cellTime" style="text-align: center;">
                                        '.$year.'
                                    </div>
                                    <a href="spr_proizvcalendar.php?y='. ($year + 1) .'" class="cellTime ahref" style="text-align: center;">
                                        <span style="font-weight: normal; font-size: 80%;">'. ($year + 1) .' >></span>
                                    </a>
                                </li>';


			echo '
                            </div>';
			
			echo '
                        </ul>';

			//Календарная сетка
            //Проход по каждому месяцу
            for ($i=1; $i<=12; $i++) {

                $di = 0;

                $month_stamp = mktime(0, 0, 0, $i, 1, $year);
                //var_dump($month_stamp);

                //Дней в месяце
                $day_count = date("t", $month_stamp);
                //var_dump($day_count);

                //День недели по счёту
                $weekday = date("w", $month_stamp);
                if ($weekday == 0) {
                    $weekday = 7;
                }
                $start = -($weekday - 2);
                //var_dump($start);

                $last = ($day_count + $weekday - 1) % 7;
                //var_dump($last);

                if ($last == 0) {
                    $end = $day_count;
                } else {
                    $end = $day_count + 7 - $last;
                }
                //var_dump($end);

                echo '
                        <div style="display: inline-block; vertical-align: top; margin: 4px;">
							<table style="border:1px solid rgba(191, 188, 181, 0.9);">
							    <tr style="text-align:center; vertical-align: top;">
                                    <td colspan="7" style="">
										<i>'.$monthsName[dateTransformation($i)].'</i>
									</td>
							    </tr>
								<tr style="text-align: center; vertical-align: middle; font-size: 10px; font-weight: bold; height: 20px;">
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px;">
										Пн
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px;">
										Вт
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px;">
										Ср
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px;">
										Чт
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px;">
										Пт
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px; color: red;">
										Сб
									</td>
									<td style="border: 1px solid rgba(191, 188, 181, 0.7); padding: 3px; color: red;">
										Вс
									</td>
								</tr>';

                //Проход по дням месяца
                for($d = $start; $d <= $end; $d++){
                    if (!($di++ % 7)){
                        echo '
                                <tr style="height: 12px;">';
                    }

                    //выделение сегодня цветом
                    $now="$year-".dateTransformation($i)."-".sprintf("%02d", $d);
                    //var_dump($now);

                    if ($now == $today){
                        $today_color = 'outline: 1px solid red; background-color: rgba(7, 255, 60, 0.33); font-weight: bold;';
                    }else{
                        $today_color = 'border:1px solid rgba(191, 188, 181, 0.3);';
                    }

                    $holliday_color = '';

                    //Выделение цветом выходных
//                    if (($di % 7 == 0) || ($di % 7 == 6)){
//                        $holliday_color = 'font-size: 95%; font-weight: bold; color: red;';
//                    }else{
                        //Выделение цветом праздников и выходных
                        if (in_array(dateTransformation($d), $holidays_arr[dateTransformation($i)])){
                            $holliday_color = 'font-size: 95%; font-weight: bold; color: red;';
                        }
//                    }


                    //Метки ДР на днях
                    $day_marks = '';
                    if (array_key_exists(dateTransformation($i)."-".sprintf("%02d", $d), $birthdays_arr)) {
                        $day_marks = '<div style="position: absolute; color: red; font-size: 70%; top: -8px; right: -4px;">&#9679;</div>';
                    }



                    echo '
                                    <td class="cellsBlockHover" style="'.$today_color.' text-align: center; text-align: -moz-center; text-align: -webkit-center; vertical-align: top;">';
                    if ($d < 1 || $d > $day_count){
                        echo "&nbsp";
                    }else{

                        echo '
                                        <div style="vertical-align:top;'.$holliday_color.'" id="">
                                            <div>';
                        echo '				
                                                <div style="text-align: right; margin: 3px; position: relative;">
                                                    '.$d.'
                                                    '.$day_marks.'
                                                </div>
                                            </div>
                                        </div>';
                    }
                    echo '
                                    </td>';
                    if (!($di % 7)){
                        echo '
                                </tr>';
                    }
                }
                echo '
							</table>
                        </div>';
            }


    		echo '
                    </div>
                </div>';

			echo '

			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			echo '					
				<script>
				

						
                </script>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
		echo '
		    <div id="doc_title">Производственный календарь - Асмедика</div>';
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>