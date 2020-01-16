<?php

//scheduler5.php
//Расписание конкретного персонала, который не входит в другие группы

	require_once 'header.php';
	
	if ($enter_ok){
	    //var_dump($_SESSION);

		require_once 'header_tags.php';
        include_once 'variables.php';

//        //Массив типов сотрудников, которые никуда не входят
//        $workers_target_arr = [1, 9, 12, 777];

        if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'widget_calendar.php';


            $filials_j = getAllFilials(false, false, false);
            //var_dump ($filials_j);

            //обнулим сессионные данные для редактирования
            unset($_SESSION['scheduler3']);
            //var_dump ($_SESSION);

			$dop = '';
			$dopWho = '';
			$dopDate = '';
			$dopFilial = '';
			$di = 0;
			
			if (!isset($_GET['filial'])){
				//Филиал	
				if (isset($_SESSION['filial'])){
					$_GET['filial'] = $_SESSION['filial'];
				}else{
					$_GET['filial'] = 15;
				}
			}

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 11, array(4,7,13,14,15,11));
            }else{
                $getWho = returnGetWho(11, 11, array(4,7,13,14,15,11));
            }
            //var_dump($getWho);

            $who = $getWho['who'];
            $whose = $getWho['whose'];
            $selected_stom = $getWho['selected_stom'];
            $selected_cosm = $getWho['selected_cosm'];
            $datatable = $getWho['datatable'];
            $kabsForDoctor = $getWho['kabsForDoctor'];
            $type = $getWho['type'];

            $stom_color = $getWho['stom_color'];
            $cosm_color = $getWho['cosm_color'];
            $somat_color = $getWho['somat_color'];
            $admin_color = $getWho['admin_color'];
            $assist_color = $getWho['assist_color'];
            $sanit_color = $getWho['sanit_color'];
            $ubor_color = $getWho['ubor_color'];
            $dvornik_color = $getWho['dvornik_color'];
            $other_color = $getWho['other_color'];
            $all_color = $getWho['all_color'];

            //Тут по категории не будем брать. Ниже будем брать индивидуально для каждого
            //$normaHours = getNormaHours(0, true, $type);

			if (isset($_GET['m']) && isset($_GET['y'])){
				//операции со временем						
				$month = $_GET['m'];
				$year = $_GET['y'];
			}else{
				//операции со временем						
				$month = date('m');		
				$year = date('Y');
			}

			//Сегодняшняя дата
			$day = date("d");
            $cur_month = date("m");
            $cur_year = date("Y");
			
			$month_stamp = mktime(0, 0, 0, $month, 1, $year);
            //var_dump($month_stamp);

			//Дней в месяце
			$day_count = date("t", $month_stamp);
			//var_dump($day_count);

            //День недели начала месяца
			$weekday = date("w", $month_stamp);
			if ($weekday == 0){
				$weekday = 7;
			}
			//var_dump($weekday);

			$start = -($weekday-2);
			//var_dump($start);
			
			$last = ($day_count + $weekday - 1) % 7;
			//var_dump($last);

            //$somat_color = '';

			if ($last == 0){
				$end = $day_count; 
			}else{
				$end = $day_count + 7 - $last;
			}
            //var_dump($end);
			
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
				
			//$filial = SelDataFromDB('spr_filials', $_GET['filial'], 'offices');
			//var_dump($filial['name']);

            $msql_cnnct = ConnectToDB ();

            //Получаем календарь выходных на указанный год
            $holidays_arr = array();

            $query = "SELECT * FROM `spr_proizvcalendar_holidays` WHERE `year` = '$year' AND `month` = '$month'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    if (!isset($holidays_arr[$arr['month']])){
                        $holidays_arr[$arr['month']] = array();
                        array_push($holidays_arr[$arr['month']], $arr['day']);
                    }else{
                        array_push($holidays_arr[$arr['month']], $arr['day']);
                    }
                }
            }
            //var_dump($number);
            //var_dump($holidays_arr);

            //Рабочие дни месяца
            $work_days_norma = $day_count - $number;
            //var_dump($work_days_norma);

            //Получаем нормы смен для этого типа
            $arr = array();
            $normaSmen = array();

//            $query = "SELECT * FROM `fl_spr_normasmen` WHERE `type` = '$type'";
//            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//            $number = mysqli_num_rows($res);
//            if ($number != 0){
//                while ($arr = mysqli_fetch_assoc($res)){
//                    //Раскидываем в массив
//                    $normaSmen[$arr['month']] = $arr['count'];
//                }
//            }
            //var_dump($normaSmen);
            //$normaSmen[10] = 7;

            //Получаем сотрудников этого типа
            $workers_target_str = implode(',', $workers_target_arr);

            $arr = array();
            $filial_workers = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` IN ($workers_target_str) AND `status` = '0' ORDER BY `full_name` ASC";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    array_push($filial_workers, $arr);
                }
                //$markSheduler = 1;
            }
            //var_dump($filial_workers);

            //Получаем график факт этого филиала
			$arr = array();
            $schedulerFakt = array();

            $query = "SELECT `id`, `day`, `worker` FROM `scheduler` WHERE `type` IN ($workers_target_str) AND `month` = '$month' AND `year` = '$year' AND `filial`='{$_GET['filial']}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					//Раскидываем в массив
                    if (!isset($schedulerFakt[$arr['worker']])) {
                        $schedulerFakt[$arr['worker']] = array();
                    }
                    if (!isset($schedulerFakt[$arr['worker']][$arr['day']])) {
                        $schedulerFakt[$arr['worker']][$arr['day']] = array();
                    }
                    //array_push($schedulerFakt[$arr['worker']][$arr['day']], $arr);
                    $schedulerFakt[$arr['worker']][$arr['day']] = 1;
				}
			}
			//var_dump($query);
			
			//$schedulerFakt = $rez;
            //var_dump($schedulerFakt);

            //Получаем график факт с других филиалов
			$arr = array();
            $schedulerFaktOther = array();

            $query = "SELECT `id`, `day`, `worker`, `filial` FROM `scheduler` WHERE `type` IN ($workers_target_str) AND `month` = '$month' AND `year` = '$year' AND `filial` <> '{$_GET['filial']}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    if (!isset($schedulerFaktOther[$arr['worker']])) {
                        $schedulerFaktOther[$arr['worker']] = array();
                    }
                    if (!isset($schedulerFaktOther[$arr['worker']][$arr['day']])) {
                        $schedulerFaktOther[$arr['worker']][$arr['day']] = array();
                    }
                    //array_push($schedulerFakt[$arr['worker']][$arr['day']], $arr);
                    $schedulerFaktOther[$arr['worker']][$arr['day']] = $arr['filial'];
                }
            }
			//var_dump($query);

			//$schedulerFakt = $rez;
            //var_dump($schedulerFaktOther);

            //var_dump($schedulerFakt);

            //Пробежимся по сотрудникам НЕ из этого филиала
            //Если у них есть смены в этом филиале, поднимаем их вверх

            $filial_not_workers_temp = array();

//            foreach ($filial_not_workers as $workers_item){
//			    //var_dump($workers_item);
//
//
//                if (isset($schedulerFakt[$workers_item['id']])){
//                    //!!!Тест перемещение любого элемента ассоциативного массива в начало этого же массива
//                    //$filial_not_workers = array($workers_item['id'] => $filial_not_workers[$workers_item['id']]) + $filial_not_workers;
//                    $filial_not_workers_temp[$workers_item['id']] = $filial_not_workers[$workers_item['id']];
//                }
//            }

//            $filial_not_workers = $filial_not_workers_temp + $filial_not_workers;
            //var_dump($filial_not_workers );


            //Соберём уже указанные часы
            $arr = array();
            $hours_j = array();

            $workers_target_str = implode(',', $workers_target_arr);

            $query = "SELECT * FROM `fl_journal_scheduler_report` WHERE `type` IN ($workers_target_str) AND `month` = '$month' AND `year` = '$year'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    if (!isset($hours_j[$arr['worker_id']])) {
                        $hours_j[$arr['worker_id']] = array();
                    }
                    if (!isset($hours_j[$arr['worker_id']][$arr['day']])) {
                        $hours_j[$arr['worker_id']][$arr['day']] = array();
                    }
                    if (!isset($hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']])) {
                        $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = array();
                    }
                    //array_push($hours_j, $arr);
                    $hours_j[$arr['worker_id']][$arr['day']][$arr['filial_id']] = $arr['hours'];

                }
            }
//            var_dump($query);
//            var_dump($hours_j);

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
							<a href="scheduler_template.php" class="b">График план</a>
							<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
						</div>
						<!--<span style="color: red;">Тестовый режим</span>-->
						<h2>График Другие на ',$monthsName[$month],' ',$year,' филиал '.$filials_j[$_GET['filial']]['name'].'</h2>
					</header>
					<!--<a href="own_scheduler.php" class="b">График сотрудника</a>-->';
			echo '
					<!--Администраторы-->
					</div>';
			echo '
					<div id="data" style="margin-top: 5px;">
					    <input type="hidden" id="type" value="99">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
			if (($scheduler['edit'] == 1) || ($scheduler['add_worker'] == 1) || $god_mode){
				echo '
							<div class="no_print"> 
							<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
								<div style="cursor: pointer;" onclick="manageScheduler(\'scheduler\')">
									<span id="manageMessage" style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">', $displayBlock ? 'Управление <span style=\'color: green;\'>включено</span>' : 'Управление <span style=\'color: red;\'>выключено</span>' ,'</span> <i class="fa fa-cog" title="Настройки"></i>
								</div>
							</li>
							</div>';
			}
			echo '			
							<div class="no_print"> 
                                <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                                <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                    <a href="scheduler.php?'.$dopFilial.$dopDate.'&who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                    <a href="scheduler.php?'.$dopFilial.$dopDate.'&who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                    <a href="scheduler.php?'.$dopFilial.$dopDate.'&who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                                    <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                    <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                    <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                    <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                    <a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=15" class="b" style="'.$dvornik_color.'">Дворники</a>';

            if (($finances['see_all'] == 1) || $god_mode) {
                echo '
                                    <a href="scheduler4.php?' . $dopFilial . $dopDate . '&who=11" class="b" style="">Прочие</a>';

                if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
                    echo '
                                    <a href="scheduler5.php?' . $dopFilial . $dopDate . '&who=999" class="b" style="background-color: #fff261;">Другие</a>';
                }
            }

            echo '
                                </li>
                                <li style="width: auto; margin-bottom: 20px;">
                                    <div style="display: inline-block; margin-right: 20px;">
                                        <div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                            Филиалы
                                        </div>
                                        <div>
                                            <select name="SelectFilial" id="SelectFilial">
											';
            if (!empty($filials_j)) {
                foreach ($filials_j as $f_id => $filials_j_data) {
					$selected = '';
					if (isset($_GET['filial'])){
						if ($f_id == $_GET['filial']){
							$selected = 'selected';
						}
					}
					echo "<option value='".$f_id."' $selected>".$filials_j_data['name']."</option>";
				}
			}
			echo '
                                            </select>
                                        </div>
                                    </div>
                                    <div style="display: inline-block; margin-right: 20px;">
    
                                        <div style="display: inline-block; margin-right: 20px;">
                                            <a href="?'.$who.'" class="dotyel" style="font-size: 70%;">Сбросить</a>
                                        </div>
                                    </div>
                                </li>
                                
							</div>';
								
			echo '<div class="no_print">';
			echo widget_calendar ($month, $year, 'scheduler5.php', $dop);
			echo '</div>';
			
			echo '</ul>';

			//Календарная сетка
            echo '
                <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                    <tr class="<!--sticky f-sticky-->">
                        <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>ФИО</b></td>';

            //Всего
            echo '
                        <td style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: center;">
                            Часы<br><span style="color: rgb(158, 158, 158); font-size: 80%;">всего/ норма/ %</span>
                        </td>';

            $weekday_temp = $weekday;

            //Выведем даты месяца
            for ($i=1; $i <= $day_count; $i++){
                //var_dump($weekday_temp);

                //будни
                $BgColor = ' background-color: rgba(220, 220, 220, 0.5);';

                //суббота воскресение
                if (($weekday_temp == 6) || ($weekday_temp == 7)){
                    $BgColor = ' background-color: rgba(234, 123, 32, 0.33);';
                }else{
                    //Выделение цветом праздников и выходных
                    if (in_array(dateTransformation($i), $holidays_arr[dateTransformation($month)])) {
                        $BgColor = ' background-color: rgba(234, 123, 32, 0.33);';
                    }
                }
                //var_dump(dateTransformation($i));
                //var_dump(in_array(dateTransformation($i), $holidays_arr[dateTransformation($month)]));

                //Выделим, если сегодняшний день
                $Shtrih = '';
                $currentDayColor = '';

                if (($i == $day) && ($cur_month == $month) && ($cur_year == $year)) {
                    $currentDayColor = 'color: red; font-weight: bold;';

                    //суббота воскресение
                    if (($weekday_temp == 6) || ($weekday_temp == 7)){
                        //$Shtrih = 'background: linear-gradient(135deg, rgba(234, 123, 32, 0.33) 49.9%, rgba(179, 179, 179, 0.67) 49.9%, rgba(179, 179, 179, 0.67) 60%, rgba(234, 123, 32, 0.33) 60% ), linear-gradient(135deg, rgba(179, 179, 179, 0.67) 10%, rgba(234, 123, 32, 0.33) 10% ); background-size: 0.5em 0.5em;';
                    }else{
                        //будни
                        //$Shtrih = 'background: linear-gradient(135deg, rgba(220, 220, 220, 0.5) 49.9%, rgba(179, 179, 179, 0.67) 49.9%, rgba(179, 179, 179, 0.67) 60%, rgba(220, 220, 220, 0.5) 60% ), linear-gradient(135deg, rgba(179, 179, 179, 0.67) 10%, rgba(220, 220, 220, 0.5) 10% ); background-size: 0.5em 0.5em;';
                    }
                }

                $ii = dateTransformation($i);

                echo '
                        <td style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; '.$Shtrih.' padding: 5px; text-align: right; cursor: pointer;" onclick="window.location.href = \'fl_createSchedulerReport.php?filial_id='.$_GET['filial'].'&d='.$ii.'&m='.$month.'&y='.$year.'&type='.$type.'\';">
                            <b><i style="'.$currentDayColor.'" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-1\');">'.$ii.'</i></b><br><span>'.$dayWeek_arr[$weekday_temp].'</span>
                        </td>';

                //Если счетчик дней недели зашел за 7, возвращаем на понедельник
                $weekday_temp++;
                if ($weekday_temp > 7){
                    $weekday_temp = 1;
                }
            }
            echo '
                    </tr>';

            //Для сотрудников прикрепленных к этому филиалу выведем
            if (!empty($filial_workers)) {
                foreach ($filial_workers as $worker_data) {
                    //var_dump($worker_data);

                    //Если своё или отдельные другие личности (или ВВ (костыли пошли ппц))
//                    if (($_SESSION['id'] == 270) || $god_mode || ($worker_data['permissions'] == 777)
//                     || ($worker_data['id'] == 314) || ($worker_data['id'] == 299)) {

                        $normaHours = getNormaHours($worker_data['id']);

                        echo '
                        <tr class="cellsBlockHover workerItem" worker_id="' . $worker_data['id'] . '">
                            <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">
                                <b>' . $worker_data['full_name'] . '</b>';
                        //                    echo '
                        //                            <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                        //                                <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                        //                            </div>';
                        echo '
                            </td>';

                        //Всего
                        echo '
                            <td id="schedulerResult_' . $worker_data['id'] . '" class="hoverDate" style="width: 100px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;" title="">
                                <div id="allMonthHours_' . $worker_data['id'] . '" class="allMonthHours" style="display: inline;">0</div>/<div id="allMonthNorma_' . $worker_data['id'] . '" style="display: inline;">' . ($work_days_norma * $normaHours) . '</div>(<div id="hoursMonthPercent_' . $worker_data['id'] . '" style="display: inline;">0</div>%)
                            </td>';


                        $weekday_temp = $weekday;

                        //Выведем даты месяца
                        for ($i = 1; $i <= $day_count; $i++) {
                            //var_dump(isset($schedulerFakt[$worker_data['id']][$i]));

                            $title = '';

                            $selectedDate = 0;

                            //будни
                            $BgColor = ' background-color: rgba(255, 255, 255, 0.15);';

                            //Если нет сотрудника
                            //суббота воскресение
                            //                        if (($weekday_temp == 6) || ($weekday_temp == 7)){
                            //                            $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                            //                        }else{
                            //Выделение цветом праздников и выходных
                            if (in_array(dateTransformation($i), $holidays_arr[dateTransformation($month)])) {
                                $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                            }
                            //                        }

                            //Выделим, если сегодняшний день
                            $Shtrih = '';
                            $currentDayColor = '';

                            if (($i == $day) && ($cur_month == $month) && ($cur_year == $year)) {
                                $currentDayColor = 'color: red; font-weight: bold;';

                                //суббота воскресение
                                if (($weekday_temp == 6) || ($weekday_temp == 7)) {
                                    //$Shtrih = 'background: linear-gradient(135deg, rgba(234, 123, 32, 0.33) 49.9%, rgba(179, 179, 179, 0.67) 49.9%, rgba(179, 179, 179, 0.67) 60%, rgba(234, 123, 32, 0.33) 60% ), linear-gradient(135deg, rgba(179, 179, 179, 0.67) 10%, rgba(234, 123, 32, 0.33) 10% ); background-size: 0.5em 0.5em;';
                                } else {
                                    //будни
                                    //$Shtrih = 'background: linear-gradient(135deg, rgba(220, 220, 220, 0.5) 49.9%, rgba(179, 179, 179, 0.67) 49.9%, rgba(179, 179, 179, 0.67) 60%, rgba(220, 220, 220, 0.5) 60% ), linear-gradient(135deg, rgba(179, 179, 179, 0.67) 10%, rgba(220, 220, 220, 0.5) 10% ); background-size: 0.5em 0.5em;';
                                }
                            }

                            $worker_is_here = false;

                            //Если тут есть сотрудник по графику
                            if (isset($schedulerFakt[$worker_data['id']])) {
                                if (isset($schedulerFakt[$worker_data['id']][$i])) {

                                    $selectedDate = 1;
                                    $worker_is_here = true;

                                    //суббота воскресение
                                    if (($weekday_temp == 6) || ($weekday_temp == 7)) {
                                        $BgColor = ' background-color: rgba(24, 144, 54, 0.52) !important;';
                                    } else {
                                        //будни
                                        $BgColor = ' background-color: rgba(49, 239, 96, 0.52) !important;';
                                    }
                                }
                            }

                            //Если сотрудник по графику есть в другом филиале
                            if (isset($schedulerFaktOther[$worker_data['id']])) {
                                if (isset($schedulerFaktOther[$worker_data['id']][$i])) {

                                    $title = $filials_j[$schedulerFaktOther[$worker_data['id']][$i]]['name'];

                                    if (!$worker_is_here) {

                                        $selectedDate = 2;

                                        //суббота воскресение
                                        if (($weekday_temp == 6) || ($weekday_temp == 7)) {
                                            $BgColor = ' background-color: rgba(35, 137, 146, 0.52) !important;';
                                        } else {
                                            //будни
                                            $BgColor = ' background-color: rgba(49, 224, 239, 0.52) !important;';
                                        }
                                    } else {

                                        $selectedDate = 3;

                                        //суббота воскресение
                                        if (($weekday_temp == 6) || ($weekday_temp == 7)) {
                                            $BgColor = ' background-color: rgba(130, 34, 35, 0.52) !important;';
                                        } else {
                                            //будни
                                            $BgColor = ' background-color: rgba(236, 107, 107, 0.52) !important;';
                                        }
                                    }
                                }
                            }

                            $invoiceFreeAddStr = '';

                            //Для ассистентов
                            //                        if ($type == 7) {
                            //                            //Добавление нарядов "с улицы" если ассист на этом филиале
                            //                            if (($selectedDate == 1) || ($selectedDate == 3)) {
                            //                                $invoiceFreeAddStr .= 'else contextMenuShow(\''.$worker_data['id'].','.$type.','.$_GET['filial'].'\', \''.$i.'.'.$month.'.'.$year.'\', event, \'invoice_add_free\');';
                            //                            }
                            //                        }

                            //Есть ли уже указанные часы сотрудника
                            $hours = '';

                            if (!empty ($hours_j)) {

                                $ii = dateTransformation($i);

                                //Сумма часов только на этом филиале
                                /*if (isset($hours_j[$worker_data['id']][$ii][$_GET['filial']])){
                                    $hours = $hours_j[$worker_data['id']][$ii][$_GET['filial']];
                                }*/

                                //Сумма часов только на всех филиалах
                                if (isset($hours_j[$worker_data['id']][$ii])) {
                                    if (array_sum($hours_j[$worker_data['id']][$ii]) > 0) {
                                        if (($_SESSION['id'] == $worker_data['id']) || ($scheduler['see_all'] == 1) || $god_mode) {
                                            $hours = '<div id="" class="dayHours_' . $worker_data['id'] . '">' . array_sum($hours_j[$worker_data['id']][$ii]) . '</div>';
                                        } else {
                                            if ((date('d') == '28') || (date('d') == '29') || (date('d') == '30') || (date('d') == '31') || (date('d') == '01') || (date('d') == '02') || (date('d') == '03')) {
                                                $hours = '<i class="fa fa-plus-circle" style="color: rgb(72, 141, 16); font-size: 150%; text-shadow: 1px 1px 1px #999;"></i><div id="" class="dayHours_' . $worker_data['id'] . '" style="display: none;">' . array_sum($hours_j[$worker_data['id']][$ii]) . '</div>';
                                            } else {
                                                $hours = '<i class="fa fa-plus-circle" style="color: rgb(72, 141, 16); font-size: 150%; text-shadow: 1px 1px 1px #999;"></i>';
                                            }

                                        }
                                    }
                                }

                            }

                            if (($scheduler['edit'] == 1) || $god_mode) {
                                //часы только свои
                                if (($_SESSION['id'] == $worker_data['id']) || ($_SESSION['id'] == 270) || $god_mode) {
                                    echo '
                                    <td selectedDate="' . $selectedDate . '" class="hoverDate' . $i . ' schedulerItem" style="width: 20px; ' . $BgColor . ' ' . $Shtrih . ' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerSession(this, ' . $worker_data['id'] . ', ' . $_GET['filial'] . ', ' . $i . ', ' . $month . ', ' . $year . ', ' . $weekday_temp . '); ' . $invoiceFreeAddStr . '" onmouseover="/*SetVisible(this,true);*/ /*contextMenuShow(\'' . $ii . '.' . $month . '.' . $year . '\', 0, event, \'showCurDate\');*/ $(\'.hoverDate' . $i . '\').addClass(\'cellsBlockHover2\');" onmouseout="/*SetVisible(this,false);*/ $(\'.hoverDate' . $i . '\').removeClass(\'cellsBlockHover2\');" title="' . $title . '">
                                        ' . $hours . '
                                    </td>';
                                }else{
                                    echo '
                                    <td selectedDate="' . $selectedDate . '" class="hoverDate' . $i . ' schedulerItem" style="width: 20px; ' . $BgColor . ' ' . $Shtrih . ' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerSession(this, ' . $worker_data['id'] . ', ' . $_GET['filial'] . ', ' . $i . ', ' . $month . ', ' . $year . ', ' . $weekday_temp . '); ' . $invoiceFreeAddStr . '" onmouseover="/*SetVisible(this,true);*/ /*contextMenuShow(\'' . $ii . '.' . $month . '.' . $year . '\', 0, event, \'showCurDate\');*/ $(\'.hoverDate' . $i . '\').addClass(\'cellsBlockHover2\');" onmouseout="/*SetVisible(this,false);*/ $(\'.hoverDate' . $i . '\').removeClass(\'cellsBlockHover2\');" title="' . $title . '">
                                        
                                    </td>';
                                }
                            } elseif ($scheduler['add_worker'] == 1) {
                                if (($i == $day) && ($cur_month == $month) && ($cur_year == $year)) {
                                    echo '
                                    <td selectedDate="' . $selectedDate . '" class="hoverDate' . $i . ' schedulerItem" style="width: 20px; ' . $BgColor . ' ' . $Shtrih . ' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerSession(this, ' . $worker_data['id'] . ', ' . $_GET['filial'] . ', ' . $i . ', ' . $month . ', ' . $year . ', ' . $weekday_temp . '); ' . $invoiceFreeAddStr . '" onmouseover="/*SetVisible(this,true);*/ /*contextMenuShow(\'' . $ii . '.' . $month . '.' . $year . '\', 0, event, \'showCurDate\');*/ $(\'.hoverDate' . $i . '\').addClass(\'cellsBlockHover2\');" onmouseout="/*SetVisible(this,false);*/ $(\'.hoverDate' . $i . '\').removeClass(\'cellsBlockHover2\');" title="' . $title . '">
                                        ' . $hours . '
                                    </td>';
                                } else {
                                    echo '
                                    <td selectedDate="' . $selectedDate . '" class="hoverDate' . $i . ' schedulerItem" style="width: 20px; ' . $BgColor . ' ' . $Shtrih . ' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onmouseover="/*SetVisible(this,true);*/ /*contextMenuShow(\'' . $ii . '.' . $month . '.' . $year . '\', 0, event, \'showCurDate\');*/ $(\'.hoverDate' . $i . '\').addClass(\'cellsBlockHover2\');" onmouseout="/*SetVisible(this,false);*/ $(\'.hoverDate' . $i . '\').removeClass(\'cellsBlockHover2\');" title="' . $title . '">
                                        ' . $hours . '
                                    </td>';
                                }
                            } else {
                                echo '
                                <td selectedDate="' . $selectedDate . '" class="hoverDate' . $i . ' schedulerItem" style="width: 20px; ' . $BgColor . ' ' . $Shtrih . ' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onmouseover="/*SetVisible(this,true);*/ /*contextMenuShow(\'' . $ii . '.' . $month . '.' . $year . '\', 0, event, \'showCurDate\');*/ $(\'.hoverDate' . $i . '\').addClass(\'cellsBlockHover2\');" onmouseout="/*SetVisible(this,false);*/ $(\'.hoverDate' . $i . '\').removeClass(\'cellsBlockHover2\');" title="' . $title . '">
                                    <div id="" class="dayHours_' . $worker_data['id'] . '">' . $hours . '</div>
                                </td>';
                            }


                            //Если счетчик дней недели зашел за 7, возвращаем на понедельник
                            $weekday_temp++;
                            if ($weekday_temp > 7) {
                                $weekday_temp = 1;
                            }
                        }
                        echo '
                        </tr>';
//                    }
                }
            }



            echo '
                </table>';

    		echo '
						</div>
					</div>';

			if (($scheduler['edit'] == 1) || ($scheduler['add_worker'] == 1) ||  $god_mode) {
                echo '
					<div id="ShowSettingsSchedulerFakt" style="position: fixed; z-index: 105; right: 100px; top: 70px; background: rgb(224, 226, 226) none repeat scroll 0% 0%; display: none; padding: 10px; border: 1px rgba(146, 128, 128, 0.16) solid;">
					    <ul>
					        <li style="color: rgb(243, 0, 0); font-size: 80%; margin-bottom: 15px;">
					            Сохранить изменения графика
					        </li>
					        <li style="text-align: right;">
					            <input type="button" class="b" value="Сохранить" onclick="Ajax_tempScheduler_scheduler3_add('.$_GET['filial'].', '.$month.', '.$year.');">
                                <input type="button" class="b" value="Отмена" onclick="cancelChangeTempScheduler();">
                            </li>
                        </ul>
					</div>';
            }
			echo '

			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			echo '					
				<script>
				
				    //!!!Тест запретили контекстное меню правой кнопкой мышки
//                    $(".schedulerItem").bind("contextmenu", function(e) {
//                        return false;
//                    });
                    
//                    if (iCanManage){
//                        document.oncontextmenu = function() {return false;};
//                    }

                    //$("body").on("contextmenu", "td", function(e){ return false; });

                    //Клик на дате
                    $("body").on("mousedown", ".schedulerItem", function(event){
                        //console.log(event.which);
                        if (iCanManage){
//                            console.log(iCanManage);

                            document.oncontextmenu = function() {return false;};

                            //Проверяем нажата ли именно правая кнопка мыши:
                            if (event.which === 3){
                                
                                //Получаем элемент на котором был совершен клик:
                                var target = $(event.target);
                                //console.log(target.attr(\'status\'));                            
                                
                                contextMenuShow(target.attr(\'worker_id\'), target.attr(\'day\'), event, \'scheduler5\');
                            }
                        }else{
                            document.oncontextmenu = function() {};
                        }
                    });
                                
                    //Посчитаем кол-во всех часов за месяц для каждого
                    $(document).ready(function() {
                        //calculateWorkerDays();
                         calculateWorkerHours();
                    });
  

                                
				
					 /*<![CDATA[*/
					 /*!!! проверить надо ли это тут и в других местах*/
					 var s=[],s_timer=[];
					 function show(id,h,spd)
					 { 
						s[id]= s[id]==spd? -spd : spd;
						s_timer[id]=setTimeout(function() 
						{
							var obj=document.getElementById(id);
							if(obj.offsetHeight+s[id]>=h)
							{
								obj.style.height=h+"px";obj.style.overflow="auto";
							}
							else 
								if(obj.offsetHeight+s[id]<=0)
								{
									obj.style.height=0+"px";obj.style.display="none";
								}
								else 
								{
									obj.style.height=(obj.offsetHeight+s[id])+"px";
									obj.style.overflow="hidden";
									obj.style.display="block";
									setTimeout(arguments.callee, 10);
								}
						}, 10);
					 }
					 /*]]>*/

					
                    $(function() {
                        $("#SelectFilial").change(function(){
                            
                            blockWhileWaiting (true);
                            
                            //var dayW = document.getElementById("SelectDayW").value;
                            document.location.href = "?filial="+$(this).val()+"'.$who.'";
                        });
                        $("#SelectDayW").change(function(){
                        
                            blockWhileWaiting (true);
                            
                            var filial = document.getElementById("SelectFilial").value;
                            document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
                        });
                    });
						
					</script>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
		echo '
		    <div id="doc_title">График '.$whose.'/',$monthsName[$month],' ',$year,'/'.$filials_j[$_GET['filial']]['name'].' - Асмедика</div>';
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>