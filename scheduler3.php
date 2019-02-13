<?php

//scheduler3.php
//Расписание администраторов и ассистентов v2.0










//!!!!! продолжай с $_SESSION['scheduler3'] добавление и так далее


	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'widget_calendar.php';
			include_once 'variables.php';

            $filials_j = getAllFilials(false, false);
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
			
			//тип график (космет/стомат/...)
            $who = '&who=4';
            $whose = 'Администраторов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_admin';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])){
                if ($_GET['who'] == 5){
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 6){
                    $who = '&who=6';
                    $whose = 'Косметологи ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_cosm';
                    $kabsForDoctor = 'cosm';
                    $type = 6;

                    $stom_color = '';
                    $cosm_color = 'background-color: #fff261;';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 10){
                    $who = '&who=10';
                    $whose = 'Специалистов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;


                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 4){
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 7){
                    $who = '&who=7';
                    $whose = 'Ассистенты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 7;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = 'background-color: #fff261;';
                    $other_color = '';
                    $all_color = '';
                }elseif($_GET['who'] == 11){
                    $who = '&who=11';
                    $whose = 'Прочее ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 11;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = 'background-color: #fff261;';
                    $all_color = '';
                }else{
                    $who = '&who=4';
                    $whose = 'Администраторов ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_admin';
                    $kabsForDoctor = 'admin';
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                    $all_color = '';
                }
            }else{
//                $who = '';
//                $whose = 'Все ';
//                $selected_stom = ' selected';
//                $selected_cosm = ' ';
//                $datatable = 'scheduler_stom';
//                $kabsForDoctor = 'stom';
//                $type = 0;
//
//                $stom_color = '';
//                $cosm_color = '';
//                $somat_color = '';
//                $admin_color = '';
//                $assist_color = '';
//                $other_color = '';
//                $all_color = 'background-color: #fff261;';

                $who = '&who=4';
                $whose = 'Администраторов ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_admin';
                $kabsForDoctor = 'admin';
                $type = 4;

                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = 'background-color: #fff261;';
                $assist_color = '';
                $other_color = '';
                $all_color = '';
            }
			
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

            $somat_color = '';
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

            //Получаем сотрудников этого филиала
            $arr = array();
            $filial_workers = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '$type' AND `filial_id` = '{$_GET['filial']}' AND `status` <> '9' ORDER BY `full_name` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    array_push($filial_workers, $arr);
                }
                //$markSheduler = 1;
            }

            //Получаем сотрудников не из этого филиала
            $arr = array();
            $filial_not_workers = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '$type' AND `filial_id` <> '{$_GET['filial']}' AND `status` <> '9' ORDER BY `full_name` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    array_push($filial_not_workers, $arr);
                }
                //$markSheduler = 1;
            }

            //Получаем график факт
			//$markSheduler = 0;

			$arr = array();
			$rez = array();

            $query = "SELECT `id`, `day`, `worker` FROM `scheduler` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year' AND `filial`='{$_GET['filial']}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
			$number = mysqli_num_rows($res);
			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					//Раскидываем в массив
                    if (!isset($rez[$arr['day']])) {
                        $rez[$arr['day']] = array();
                    }
                    array_push($rez[$arr['day']], $arr);
				}
				//$markSheduler = 1;
			}
			//var_dump($query);
			
			$schedulerFakt = $rez;
            //var_dump($schedulerFakt);

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
						
						<h2>График '.$whose.' на ',$monthsName[$month],' ',$year,' филиал '.$filials_j[$_GET['filial']]['name'].'</h2>
					</header>
					<!--<a href="own_scheduler.php" class="b">График сотрудника</a>-->';
			echo '
					Администраторы
					</div>';
			echo '
					<div id="data">
					    <input type="hidden" id="type" value="'.$type.'">
						<ul style="margin-left: 6px; margin-bottom: 20px;">';
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
							<div class="no_print"> 
							<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
								<div style="cursor: pointer;" onclick="manageScheduler(\'scheduler\')">
									<span id="manageMessage" style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">', $displayBlock ? 'Управление выключить' : 'Управление включить' ,'</span> <i class="fa fa-cog" title="Настройки"></i>
								</div>
							</li>
							</div>';
			}
			echo '			
							<div class="no_print"> 
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="scheduler.php?'.$dopFilial.$dopDate.'&who=stom" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="scheduler.php?'.$dopFilial.$dopDate.'&who=cosm" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="scheduler.php?'.$dopFilial.$dopDate.'&who=somat" class="b" style="'.$somat_color.'">Специалисты</a>
								<a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=4" class="b" style="'.$admin_color.'">Администраторы</a>
								<a href="scheduler3.php?'.$dopFilial.$dopDate.'&who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
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
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">';
//			if ($zapis['see_own'] == 1){
//				if ($type == $_SESSION['permissions']){
//					echo '
//								<a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$day.'&worker='.$_SESSION['id'].'" class="b">Ваша запись сегодня</a>';
//				}
//			}
			if (($zapis['add_new'] == 1) || $god_mode){
				echo '
								<a href="zapis.php?y='.$year.'&m='.$month.'&d='.$day.'&filial='.$_GET['filial'].''.$who.'" class="b">Запись сегодня</a>';
                //if (isset($_SESSION['filial'])) {
                    //if ($_SESSION['filial'] == 15) {
                        echo '
								<a href="zapis_online.php" class="b" style="position: relative">Запись онлайн<div class="have_new-zapis notes_count" style="display: none;" title="Есть необработанные"></div></a>';
                    //}
                //}
				/*echo '
								<a href="zapis_full.php?y='.$year.'&m='.$month.'&d='.$day.'&filial='.$_GET['filial'].''.$who.'" class="b">Подробно</a>';*/
			}
			echo '
							</li>
							</div>';
								
			echo '<div class="no_print">';
			echo widget_calendar ($month, $year, 'scheduler3.php', $dop);
			echo '</div>';
			
			echo '</ul>';





			//Новый календарик

            $weekday_temp = $weekday;

            echo '
                <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                    <tr class="<!--sticky f-sticky-->">
                        <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>ФИО</b><br><span style="color: rgb(35, 175, 53); font-size: 80%;">прикреплены к филиалу</span></td>';

            //Выведем даты месяца
            for ($i=1; $i<=$day_count; $i++){

                //суббота воскресение
                if (($weekday_temp == 6) || ($weekday_temp == 7)){
                    $BgColor = ' background-color: rgba(234, 123, 32, 0.33);';
                }else{
                    //будни
                    $BgColor = ' background-color: rgba(220, 220, 220, 0.5);';
                }

                echo '
                        <td style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;"><b><i>'.$i.'</i></b></td>';

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
                    echo '
                    <tr class="cellsBlockHover">
                        <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>'.$worker_data['full_name'].'</b></td>';

                    //Выведем даты месяца
                    for ($i=1; $i<=$day_count; $i++){

                        //суббота воскресение
                        if (($weekday_temp == 6) || ($weekday_temp == 7)){
                            $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                        }else{
                            //будни
                            $BgColor = ' background-color: rgba(255, 255, 255, 0.15);';
                        }

                        echo '
                            <td selectedDate="0" style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerTempSession(this, '.$worker_data['id'].', '.$_GET['filial'].', '.$i.', '.$month.', '.$year.', '.$weekday_temp.');" onmouseover="SetVisible(this,true);" onmouseout="SetVisible(this,false);">
                                <div style="display: none;"><i>'.$i.'</i></div>
                            </td>';

                        //Если счетчик дней недели зашел за 7, возвращаем на понедельник
                        $weekday_temp++;
                        if ($weekday_temp > 7){
                            $weekday_temp = 1;
                        }
                    }
                    echo '
                    </tr>';
                }
            }

            echo '
                    <tr>
                        <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>ФИО</b><br><span style="color: rgb(243, 0, 0); font-size: 80%;">не прикреплены к филиалу</span></td>';

            for ($i=1; $i<=$day_count; $i++){

                //суббота воскресение
                //if (($weekday_temp == 6) || ($weekday_temp == 7)){
                //    $BgColor = ' background-color: rgba(234, 123, 32, 0.33);';
                //}else{
                    //будни
                    $BgColor = ' background-color: rgba(255, 255, 255, 1);';
                //}

                echo '
                        <td style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; /*border-left: 1px solid #BFBCB5;*/ padding: 5px; text-align: right;"></td>';

                //Если счетчик дней недели зашел за 7, возвращаем на понедельник
//                $weekday_temp++;
//                if ($weekday_temp > 7){
//                    $weekday_temp = 1;
//                }
            }

            //Для сотрудников НЕ прикрепленных к этому филиалу выведем
            if (!empty($filial_not_workers)) {
                foreach ($filial_not_workers as $worker_data) {
                    echo '
                    <tr class="cellsBlockHover">
                        <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>'.$worker_data['full_name'].'</b></td>';

                    //Выведем даты месяца
                    for ($i=1; $i<=$day_count; $i++){

                        //суббота воскресение
                        if (($weekday_temp == 6) || ($weekday_temp == 7)){
                            $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                        }else{
                            //будни
                            $BgColor = ' background-color: rgba(255, 255, 255, 0.15);';
                        }

                        echo '
                            <td selectedDate="0" style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerTempSession(this, '.$worker_data['id'].', '.$_GET['filial'].', '.$i.', '.$month.', '.$year.', '.$weekday_temp.');" onmouseover="SetVisible(this,true);" onmouseout="SetVisible(this,false);">
                                <div style="display: none;"><i>'.$i.'</i></div>
                            </td>';

                        //Если счетчик дней недели зашел за 7, возвращаем на понедельник
                        $weekday_temp++;
                        if ($weekday_temp > 7){
                            $weekday_temp = 1;
                        }
                    }
                    echo '
                    </tr>';
                }
            }

            echo '
                </table>';




//						echo '
//							<table style="border:1px solid #BFBCB5;">
//								<tr style="text-align:center; vertical-align:top; font-weight:bold; height:20px;">
//									<td style="border:1px solid #BFBCB5;">
//										Понедельник
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Вторник
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Среда
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Четверг
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Пятница
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Суббота
//									</td>
//									<td style="border:1px solid #BFBCB5;">
//										Воскресенье
//									</td>
//								</tr>';
//
//						//отсутствие сотрудников в клинике
//						$now_ahtung = TRUE;
//						$ahtung = TRUE;
//
//						for($d = $start; $d <= $end; $d++){
//							if (!($di++ % 7)){
//								echo '
//									<tr style="height: 142px;">';
//							}
//
//							$kabsNone = '';
//							$kabs = '
//								<div class="cellTime" style="padding: 0; text-align: center; background-color: #FEFEFE; border: 0; width: 150px; min-width: 125px; max-width: 150px;">';
//
//							//Проверяем, есть ли сегодня тут кто.
//							if (isset($schedulerFakt[$d])){
//                                //var_dump($schedulerFakt[$d]);
//
//                                //отсутствие врачей в клинике
//                                $now_ahtung = TRUE;
//                                $ahtung = TRUE;
//
//                                $kabs .= '
//                                        <div style="width: 100%; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">';
//
//                                //переменная для вывода
//                                $resEcho2 = '';
//
//                                //Отсортируем
//                                ksort ($schedulerFakt[$d]);
//
//                                foreach($schedulerFakt[$d] as $worker_val){
//                                    //var_dump($worker_val);
//
//                                    $resEcho = '';
//                                    $resEcho = WriteSearchUser('spr_workers', $worker_val['worker'], 'user', false).' <a href="scheduler_own.php?id='.$worker_val['worker'].'" class="info"><i class="fa fa-info-circle" title="График администратора"></i></a>';
//                                    $ahtung = FALSE;
//                                    $fontSize = 'font-size: 70%;';
//                                    $resEcho2 .= '
//                                            <div style="box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);" onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$_GET['filial'].', \''.$filials_j[$_GET['filial']]['name'].'\', 0, '.$year.', '.$month.','.$d.', 0)">
//                                                <div style="text-align: right; color: #555;">
//
//                                                </div>
//                                                <div style="text-align: left; padding: 6px;">';
//                                    $resEcho2 .= $resEcho;
//                                    $resEcho2 .= '
//                                                </div>
//                                            </div>';
//
//                                }
//
//                                $BgColor = ' background-color: rgba(171, 254, 213, 0.59);';
//
//                                $kabs .= '
//                                        <div style="text-align: center; display: table-cell !important; width: 100%;'.$BgColor.'">';
//                                $kabs .= $resEcho2;
//
//                                $kabs .= '<div class="manageScheduler" style="';
//
//                                if ($displayBlock){
//                                    $kabs .= 'display: block;';
//                                }
//
//                                $kabs .= 'background-color: #FEEEEE; box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);">
//                                                    <div style="text-align: center; padding: 4px; margin: 1px;">';
//
//                                $kabs .= '
//                                                <div style="display: inline-block; bottom: 0; font-size: 110%; cursor: pointer; border: 1px dotted #9F9D9D; width: 15px; margin-right: 2px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$_GET['filial'].', \''.$filials_j[$_GET['filial']]['name'].'\', 0, '.$year.', '.$month.','.$d.', 0)"><span style="color: #333;"></span><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
//
//                                $kabs .= '
//                                        </div>
//                                    </div>';
//
//							}else{
//								//Если вообще никого целый день
//								$ahtung = TRUE;
//
//                                $kabsNone .= '
//                                        <div style="width: 100%; height: 35px; min-height: 35px; outline: 1px solid  #BBB; display: table; margin-bottom: 3px; font-size: 70%;">
//
//                                            <div style="width: 100%; vertical-align: middle; display: table; margin-bottom: 3px; color: red; background-color: rgba(255, 0, 0, 0.33);">
//                                                <div style="margin-bottom: 7px;">никого нет</div>
//                                                <div class="manageScheduler" style="';
//
//                                if ($displayBlock){
//                                    $kabsNone .= 'display: block;';
//                                }
//
//                                $kabsNone .= '">';
//
//                                $kabsNone .= '<div style="display: inline-block; bottom: 0; font-size: 120%; cursor: pointer; border: 1px dotted #9F9D9D; width: 20px; margin-right: 3px;" title="Добавить сотрудника"  onclick="if (iCanManage) ShowSettingsSchedulerFakt('.$_GET['filial'].', \''.$filials_j[$_GET['filial']]['name'].'\', 0, '.$year.', '.$month.','.$d.', 0)"><span style="color: #333;"></span><span style="color: green;"><i class="fa fa-plus-square"></i></span></div>';
//
//                                $kabsNone .= '
//                                                </div>
//                                            </div>
//                                        </div>';
//
//							}
//							$kabsNone .= '
//											</div>';
//							//выделение сегодня цветом
//							$now="$year-$month-".sprintf("%02d",$d);
//							if ($now == $today){
//                                $today_color = 'border: 1px solid red; outline: 2px solid red;';
//                            }else{
//                                $today_color = 'border:1px solid #BFBCB5;';
//                            }
//							//Выделение цветом выходных
//							if (($di % 7 == 0) || ($di % 7 == 6)){
//								$holliday_color = 'color: red;';
//							}else{
//								$holliday_color = '';
//							}
//
//
//							echo '
//										<td style="'.$today_color.' text-align: center; text-align: -moz-center; text-align: -webkit-center; vertical-align: top;">';
//							if ($d < 1 || $d > $day_count){
//								echo "&nbsp";
//							}else{
//
//								echo '
//											<div style="vertical-align:top;'.$holliday_color.'" id="blink2">
//												<!--<div><span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b"  onclick="document.location.href = \'scheduler_day.php?y='.$year.'&m='.$month.'&d='.$d.'&filial='.$_GET['filial'].$who.'\'">запись</span>-->
//												<div>';
//								if ($zapis['see_own'] == 1){
//									if ($type == $_SESSION['permissions']){
//										echo '
//													<span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b">
//														<div class="no_print"> <a href="zapis_own.php?y='.$year.'&m='.$month.'&d='.$d.'&worker='.$_SESSION['id'].'" class="ahref">ваша запись</a></div>
//													</span>';
//									}
//								}
//								if (($zapis['add_new'] == 1) || $god_mode){
//									echo '
//													<span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b">
//														<div class="no_print">
//															<a href="zapis.php?y='.$year.'&m='.$month.'&d='.$d.'&filial='.$_GET['filial'].$who.'" class="ahref">запись</a>
//														</div>
//													</span>
//													<span style="font-size:70%; color: #0C0C0C; float:left; margin: 0; padding: 1px 5px;" class="b">
//														<div class="no_print">
//															<a href="zapis_full.php?y='.$year.'&m='.$month.'&d='.$d.'&filial='.$_GET['filial'].$who.'" class="ahref">под-но</a>
//														</div>
//													</span>';
//								}
//								echo '
//													<div style="text-align: right;">
//														<strong>'.$d.'</strong>
//													</div>
//												</div>
//											</div>
//											<div style="text-align: middle; display: table-cell !important; width: 100%;">';
//
//								echo $kabs;
//
//								if (!$ahtung OR !$now_ahtung){
//									//echo $kabs;
//								}else{
//									echo $kabsNone;
//								}
//								echo '
//										</div>';
//							}
//													/*}else
//														echo '
//																<td style="border:1px solid #BFBCB5; vertical-align:top;">&nbsp;</td>';*/
//									//			}
//							echo '
//										</td>';
//							if (!($di % 7)){
//								echo '
//									</tr>';
//							}
//						}
//						echo '
//							</table>';
				echo '
						</div>
					</div>';
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
					<div id="ShowSettingsSchedulerFakt" style="position: absolute; z-index: 105; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsSchedulerFakt()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsSchedulerFakt">
                            <label id="smena_error" class="error"></label><br>
                            <label id="worker_error" class="error"></label>
                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellLeft">Число</div>
                                <div class="cellRight" id="month_date"></div>
                                <div style="display: none;" id="day"></div>
                                <div style="display: none;" id="month"></div>
                                <div style="display: none;" id="year"></div>
                            </div>
                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellLeft">Филиал</div>
                                <div class="cellRight" id="filial_name">					
                                </div>
                                <div style="display: none;" id="filial_value"></div>
                            </div>
                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellLeft">Кабинет №</div>
                                <div class="cellRight" id="kabN">
                                </div>
                            </div>

                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellLeft">Смена</div>
                                <div class="cellRight" id="smenaN">
                                </div>
                            </div>
                            <div id="schedulerType" class="cellsBlock2" style="display: none; font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellLeft">Тип графика</div>
                                <div class="cellRight">
                                    <input type="checkbox" id="twobytwo" name="twobytwo" value="1"> 2 через 2<br>
                                </div>
                            </div>
                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                <div class="cellRight">
                                    <div id="workersTodayDelete"></div>
                                    <div id="errrror"></div>
                                </div>
                            </div>';

				//Врачи
				echo '
								<div id="ShowWorkersHere" style="vertical-align: top; height: 200px; border: 1px solid #C1C1C1; overflow-x: hidden; overflow-y: scroll;">
								</div>';

				echo '	
						</div>';
			}
			echo '
						<input type="button" class="b" value="OK" onclick="if (iCanManage) ChangeWorkerShedulerFakt()" id="changeworkersheduletbutton">
						<input type="button" class="b" value="Отмена" onclick="HideSettingsSchedulerFakt()">
					</div>';
	
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';


				echo '	
		
				
				
					<script>  
						function changeStyle(idd){
							if ( $("#"+idd).prop("checked"))
								document.getElementById(idd+"_2").style.background = \'#83DB53\';
							else
								document.getElementById(idd+"_2").style.background = \'#F0F0F0\';
						}

						$(document).ready(function() {
							$("#smena1").click(function() {
								var checked_status = this.checked;
								 $(".smena1").each(function() {
									this.checked = checked_status;
									if ( $(this).prop("checked"))
										this.style.background = \'#83DB53\';
									else
										this.style.background = \'#F0F0F0\';
								});
								
								var ShowWorkersSmena1 = ShowWorkersSmena();
							});
							$("#smena2").click(function() {
								var checked_status = this.checked;
								 $(".smena2").each(function() {
									this.checked = checked_status;
									if ( $(this).prop("checked"))
										this.style.background = \'#83DB53\';
									else
										this.style.background = \'#F0F0F0\';
								});
								
								var ShowWorkersSmena1 = ShowWorkersSmena();
							});
						});';

			echo '					
			</script>
					
				<script>
					 /*<![CDATA[*/
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
				 </script>
					
					';	

			echo '
					<script>
					
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