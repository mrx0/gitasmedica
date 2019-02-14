<?php

//scheduler3.php
//Расписание администраторов и ассистентов v2.0

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

            //Получаем сотрудников НЕ из этого филиала
            $arr = array();
            $filial_not_workers = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '$type' AND `filial_id` <> '{$_GET['filial']}' AND `status` <> '9' ORDER BY `full_name` ASC";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //Раскидываем в массив
                    //array_push($filial_not_workers, $arr);
                    $filial_not_workers[$arr['id']] = $arr;
                }
            }
            //var_dump($filial_not_workers);

            //Получаем график факт этого филиала
			$arr = array();
            $schedulerFakt = array();

            $query = "SELECT `id`, `day`, `worker` FROM `scheduler` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year' AND `filial`='{$_GET['filial']}'";
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

            $query = "SELECT `id`, `day`, `worker`, `filial` FROM `scheduler` WHERE `type` = '$type' AND `month` = '$month' AND `year` = '$year' AND `filial` <> '{$_GET['filial']}'";
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

            foreach ($filial_not_workers as $workers_item){
			    //var_dump($workers_item);


                if (isset($schedulerFakt[$workers_item['id']])){
                    //!!! Тест перемещение любого элемента ассоциативного массива в начало этого же массива
                    //$filial_not_workers = array($workers_item['id'] => $filial_not_workers[$workers_item['id']]) + $filial_not_workers;
                    $filial_not_workers_temp[$workers_item['id']] = $filial_not_workers[$workers_item['id']];
                }
            }

            $filial_not_workers = $filial_not_workers_temp + $filial_not_workers;
            //var_dump($filial_not_workers );

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
					<!--Администраторы-->
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





			//Календарная сетка
            echo '
                <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                    <tr class="<!--sticky f-sticky-->">
                        <td style="width: 260px; border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>ФИО</b><br><span style="color: rgb(35, 175, 53); font-size: 80%;">прикреплены к филиалу</span></td>';

            $weekday_temp = $weekday;

            //Выведем даты месяца
            for ($i=1; $i<=$day_count; $i++){
                //var_dump($weekday_temp);

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

                    $weekday_temp = $weekday;

                    //Выведем даты месяца
                    for ($i=1; $i<=$day_count; $i++){
                        //var_dump(isset($schedulerFakt[$worker_data['id']][$i]));

                        $title = '';

                        $selectedDate = 0;

                        //Если нет сотрудника
                        //суббота воскресение
                        if (($weekday_temp == 6) || ($weekday_temp == 7)){
                            $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                        }else{
                            //будни
                            $BgColor = ' background-color: rgba(255, 255, 255, 0.15);';
                        }

                        $worker_is_here = false;

                        //Если тут есть сотрудник по графику
                        if (isset($schedulerFakt[$worker_data['id']])){
                            if (isset($schedulerFakt[$worker_data['id']][$i])){

                                $selectedDate = 1;
                                $worker_is_here = true;

                                //суббота воскресение
                                if (($weekday_temp == 6) || ($weekday_temp == 7)){
                                    $BgColor = ' background-color: rgba(24, 144, 54, 0.52) !important;';
                                }else{
                                    //будни
                                    $BgColor = ' background-color: rgba(49, 239, 96, 0.52) !important;';
                                }
                            }
                        }

                        //Если сотрудник по графику есть в другом филиале
                        if (isset($schedulerFaktOther[$worker_data['id']])){
                            if (isset($schedulerFaktOther[$worker_data['id']][$i])){

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
                                }else{

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

                        echo '
                            <td selectedDate="'.$selectedDate.'" class="hoverDate'.$i.'" style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerSession(this, '.$worker_data['id'].', '.$_GET['filial'].', '.$i.', '.$month.', '.$year.', '.$weekday_temp.');" onmouseover="SetVisible(this,true); $(\'.hoverDate'.$i.'\').addClass(\'cellsBlockHover2\');" onmouseout="SetVisible(this,false); $(\'.hoverDate'.$i.'\').removeClass(\'cellsBlockHover2\');" title="'.$title.'">
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

                    $weekday_temp = $weekday;

                    //Выведем даты месяца
                    for ($i=1; $i<=$day_count; $i++){

                        $title = '';

                        $selectedDate = 0;

                        //Если нет сотрудника
                        //суббота воскресение
                        if (($weekday_temp == 6) || ($weekday_temp == 7)){
                            $BgColor = ' background-color: rgba(234, 123, 32, 0.15);';
                        }else{
                            //будни
                            $BgColor = ' background-color: rgba(255, 255, 255, 0.15);';
                        }

                        $worker_is_here = false;

                        //Если тут есть сотрудник по графику
                        if (isset($schedulerFakt[$worker_data['id']])){
                            if (isset($schedulerFakt[$worker_data['id']][$i])){

                                $selectedDate = 1;
                                $worker_is_here = true;

                                //суббота воскресение
                                if (($weekday_temp == 6) || ($weekday_temp == 7)){
                                    $BgColor = ' background-color: rgba(24, 144, 54, 0.52) !important;';
                                }else{
                                    //будни
                                    $BgColor = ' background-color: rgba(49, 239, 96, 0.52) !important;';
                                }
                            }
                        }


                        //Если сотрудник по графику есть в другом филиале
                        if (isset($schedulerFaktOther[$worker_data['id']])){
                            if (isset($schedulerFaktOther[$worker_data['id']][$i])){

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
                                }else{

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

                        echo '
                            <td selectedDate="'.$selectedDate.'" class="hoverDate'.$i.'" style="width: 20px; '.$BgColor.' border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right; cursor: pointer;" onclick="if (iCanManage) changeTempSchedulerSession(this, '.$worker_data['id'].', '.$_GET['filial'].', '.$i.', '.$month.', '.$year.', '.$weekday_temp.');" onmouseover="SetVisible(this,true); /*$(\'.hoverDate'.$i.'\').addClass(\'cellsBlockHover2\');*/" onmouseout="SetVisible(this,false); /*$(\'.hoverDate'.$i.'\').removeClass(\'cellsBlockHover2\');*/" title="'.$title.'">
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

    		echo '
						</div>
					</div>';

			if (($scheduler['edit'] == 1) || $god_mode) {
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