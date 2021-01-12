<?php

//zapis2.php
//Запись на филиале v2.0

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if (($zapis['see_all'] == 1) || ($zapis['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
            include_once 'variables.php';

			$filials_j = getAllFilials(false, false, false);
			//var_dump($filials_j);

			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			$dopWho = '';
			$dopDate = '';
			$dopFilial = '';
            $dopClient = '';

			$NextSmenaArr_Bool = FALSE;
			$NextSmenaArr_Zanimayu = 0;

			//Права для передачи в ф-ции
            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

            if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
                $finance_edit = true;
                $edit_options = true;
            }

            if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode){
                $stom_edit = true;
                $edit_options = true;
            }
            if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode){
                $cosm_edit = true;
                $edit_options = true;
            }

            if (($zapis['add_own'] == 1) || ($zapis['add_new'] == 1) || $god_mode) {
                $admin_edit = true;
                $edit_options = true;
            }

            if (($scheduler['see_all'] == 1) || $god_mode){
                $upr_edit = true;
                $edit_options = true;
            }

            if (isset($_SESSION['filial'])) {
                $contexMenuZapisMain_filial = $_SESSION['filial'];
            }else{
                $contexMenuZapisMain_filial = 0;
            }

			if (!isset($filial_id)){
				//Филиал	
				if (isset($_SESSION['filial'])){
					$filial_id = $_SESSION['filial'];
				}else{
                    $filial_id = 15;
				}
			}else{
                $filial_id = $filial_id;
            }
			
            //тип график (космет/стомат/...)
            if (isset($_GET['who'])){
                if ($_GET['who'] == 'stom'){
                    $who = '&who=stom';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                }elseif($_GET['who'] == 'cosm'){
                    $who = '&who=cosm';
                    $whose = 'Косметологи ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_cosm';
                    $kabsForDoctor = 'cosm';
                    $type = 6;

                    $stom_color = '';
                    $cosm_color = 'background-color: #fff261;';
                    $somat_color = '';
                }elseif($_GET['who'] == 'somat'){
                    $who = '&who=somat';
                    $whose = 'Специалисты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                }else{
                    $who = '&who=stom';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;
                    $_GET['who'] = 'stom';

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                }
            }else{
                $who = '&who=stom';
                $whose = 'Стоматологи ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_stom';
                $kabsForDoctor = 'stom';
                $type = 5;
                $_GET['who'] = 'stom';

                $stom_color = 'background-color: #fff261;';
                $cosm_color = '';
                $somat_color = '';
            }
				
            if (isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
                //операции со временем
                $day = $_GET['d'];
                $month = $_GET['m'];
                $year = $_GET['y'];
            }else{
                //операции со временем
                $day = date('d');
                $month = date('m');
                $year = date('Y');
            }

            if (!isset($day) || $day < 1 || $day > 31)
                $day = date("d");
            if (!isset($month) || $month < 1 || $month > 12)
                $month = date("m");
            if (!isset($year) || $year < 2010 || $year > 2037)
                $year = date("Y");

			foreach ($_GET as $key => $value){
				if (($key == 'd') || ($key == 'm') || ($key == 'y'))
					$dopDate  .= '&'.$key.'='.$value;
				if ($key == 'filial')
					$dopFilial .= '&'.$key.'='.$value;
				if ($key == 'who')
					$dopWho .= '&'.$key.'='.$value;
                if ($key == 'client_id')
					$dopClient .= '&'.$key.'='.$value;
			}

            $default_client_id = 0;
            $default_client_name = '';

            //Если мы пришли сюда поставить в запись определённого пациента
            if (isset($_GET['client_id'])){
                $default_client_id = $_GET['client_id'];
                $default_client_name = WriteSearchUser('spr_clients', $_GET['client_id'], 'user_full', false);
            }

            //Кабинеты в этом филиале
            $kabsInFilial_arr = SelDataFromDB('spr_kabs', $filial_id, 'office_kabs');
            if ($kabsInFilial_arr != 0){
                $kabsInFilial_json = $kabsInFilial_arr[0][$kabsForDoctor];
                //var_dump($kabsInFilial_json);

                if ($kabsInFilial_json != NULL){
                    $kabsInFilialExist = TRUE;
                    $kabsInFilial = json_decode($kabsInFilial_json, true);
                    //var_dump($kabsInFilial);
                    //echo count($kabsInFilial);

                }else{
                    $kabsInFilialExist = FALSE;
                }
            }
				
            //переменная, чтоб вкл/откл редактирование
            echo '
                <script>
                    var iCanManage = true;
                </script>
            ';
				
			//Поехали
            $msql_cnnct = ConnectToDB();

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="scheduler.php?'.$dopFilial.$dopWho.$dopDate.$dopClient.'" class="b">График</a>
                            <a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
                        </div>
                    
                        <h2>Запись '.$day.' ',$monthsName[$month],' ',$year,'</h2>
                        <b>Филиал</b> '.$filials_j[$filial_id]['name'].'<br>
                        <span style="color: green; font-size: 120%; font-weight: bold;">'.$whose.'</span><br>
                    </header>';

            //Быстрый поиск пациентов
            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 1;">';

            echo $block_fast_search_client;

            echo '
					</div>';

            if (($zapis['see_all'] == 1) || ($zapis['add_own'] == 1) || $god_mode) {
                if ($default_client_id > 0) {
                    echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 435px;">';
                    echo '
                        <div class="cellRight" style="box-shadow: -1px 1px 8px #333; background-color: rgba(249, 183, 183, 0.68);">
                            <span style="font-size: 70%;">Добавление записи пациенту:</span><br>
                            ' . $default_client_name . ' <a href="zapis.php?' . $dopWho . $dopDate . $dopFilial . '" class="ahref"><i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 130%;" title="Отменить"></i></a>
                        </div>';
                    echo '
					</div>';
                }
            }

            echo '
					<div id="data" style="margin: 0">
					    <ul style="margin-left: 6px; margin-bottom: 20px;">';

					if (($zapis['edit'] == 1) || $god_mode){
						echo '
                            <li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
                                <div style="cursor: pointer;" onclick="manageZapis()">
                                    <!--<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>-->
                                </div>
                            </li>';
					}


            if (isset($_SESSION['filial'])){

                $arr = array();
                $arr2 = array();
                $rez = array();
                $rez2 = array();

                $query = "SELECT * FROM `zapis` WHERE `office` = '{$_SESSION['filial']}' AND `add_from` <> '{$_SESSION['filial']}' AND `enter` <> '8'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rez, $arr);
                    }
                }

                $query = "SELECT * FROM `zapis` WHERE `add_from` = '{$_SESSION['filial']}' AND `office` <> '{$_SESSION['filial']}' AND `enter` <> '8'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr2 = mysqli_fetch_assoc($res)){
                        array_push($rez2, $arr2);
                    }
                }

                //mysql_close();
                //var_dump($rez);
                if (!empty($rez) || !empty($rez2)){
                    echo '
                        <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас есть неподтвёрждённые записи</span><br>';
                }

                if (!empty($rez)){
                    echo '
                        <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><b>Вам</b></span><br>';
                    foreach ($rez as $val){
                        if ($val['type'] == 5)
                            $who = '&who=stom';
                        if ($val['type'] == 6)
                            $who = '&who=cosm';
                        if ($val['type'] == 10)
                            $who = '&who=somat';

                        if ($val['day'] < 10) $val['day'] = '0'.$val['day'];
                        if ($val['month'] < 10) $val['month'] = '0'.$val['month'];

                        echo '
                        <li class="cellsBlock" style="width: auto; margin-bottom: 5px;">
                            <a href="zapis_full.php?filial='.$val['office'].''.$who.'&d='.$val['day'].'&m='.$val['month'].'&y='.$val['year'].'&kab='.$val['kab'].'" style="text-decoration: none; border-bottom: 1px dashed #000080;">'.$val['day'].'.'.$val['month'].'.'.$val['year'].' показать</a>
                        </li>';
                    }
                }
                if (!empty($rez2)){
                    echo '
                        <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><b>Ваши</b></span><br>';
                    foreach ($rez2 as $val){
                        if ($val['type'] == 5)
                            $who = '&who=stom';
                        if ($val['type'] == 6)
                            $who = '&who=cosm';
                        if ($val['type'] == 10)
                            $who = '&who=somat';
                        echo '
                        <li class="cellsBlock" style="width: auto; margin-bottom: 5px;">
                            <a href="zapis_full.php?filial='.$val['office'].''.$who.'&d='.$val['day'].'&m='.$val['month'].'&y='.$val['year'].'&kab='.$val['kab'].'" style="text-decoration: none; border-bottom: 1px dashed #000080;">'.$val['day'].'.'.$val['month'].'.'.$val['year'].' показать</a>
                        </li>';
                    }
                }
            }else{
                echo '
                        <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
            }
					
			echo '			
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="?'.$dopFilial.$dopDate.$dopClient.'&who=stom" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="?'.$dopFilial.$dopDate.$dopClient.'&who=cosm" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="?'.$dopFilial.$dopDate.$dopClient.'&who=somat" class="b" style="'.$somat_color.'">Специалисты</a>
							</li>
							<li class="cellsBlock" style="width: auto; margin-bottom: 20px;">
								<div style="display: inline-block; margin-right: 20px;">
									<div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
										Выберите филиал
									</div>
									<div>
										<select name="SelectFilial" id="SelectFilial">
											';
				if (!empty($filials_j)){
                    foreach ($filials_j as $f_id => $filials_j_data) {
                        $filialSelected = '';
                        if ($filials_j_data['id'] == $filial_id){
                            $filialSelected = 'selected';
                        }
                        echo "<option value='" . $filials_j_data['id'] . "' " . $filialSelected . ">" . $filials_j_data['name'] . "</option>";
                    }
				}
				echo '
										</select>
									</div>
								</div>
								<div style="display: inline-block; margin-right: 20px;">

									<div style="display: inline-block; margin-right: 20px;">
										<a href="?'.$who.$dopClient.'" class="dotyel" style="font-size: 80%;">Сбросить</a>
									</div>
								</div>
							</li>';
					//Календарик	
					echo '
	
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
									<div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?'.$dopFilial.$dopWho.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
									<div>
										<span style="color: rgb(125, 125, 125);">
											Изменить дату:
											<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
											<span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'zapis.php?'.$dopFilial.$dopWho.$dopClient.'\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
										</span>
									</div>
								</li>';
							
					
					if ($kabsInFilialExist){
						
						$Work_Today_arr = array();
						
						$Work_Today = FilialWorker	($type, $year, $month, $day, $filial_id);

						if ($Work_Today != 0){
							//var_dump($Work_Today);
							
							foreach($Work_Today as $Work_Today_value){
								//var_dump($Work_Today_value);
								//!!!Бля такой тут пиздец с этой 9ой сменой....
								//а, сука, потому что сразу надо было головой думать
								if ($Work_Today_value['smena'] == 9){
									$Work_Today_arr[$Work_Today_value['kab']][1] = $Work_Today_value;
									$Work_Today_arr[$Work_Today_value['kab']][2] = $Work_Today_value;
								}else{
									$Work_Today_arr[$Work_Today_value['kab']][$Work_Today_value['smena']] = $Work_Today_value;
								}
							}
						}else{
							//никто не работает тут сегодня
						}
						//var_dump($Work_Today_arr);
						
						echo '
							<div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
								<ul>
									<li><a href="#tabs-1" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-1\');">1 смена</a></li>
									<li><a href="#tabs-2" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-2\');">2 смена</a></li>
									<li><a href="#tabs-3" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-3\');">3 смена</a></li>
									<li><a href="#tabs-4" onclick="window.location.replace(\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#tabs-4\');">4 смена</a></li>
								</ul>';
						
						
						$NextSmenaArr = array();
						//$NextSmenaFill = FALSE;
						//$PrevSmenaZapis = array();
						
						//смена 1	
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;

						echo '
						<div id="tabs-1">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
                                    <td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
                                        <div class="" style="border: none; width: 100%;  height: 50px;">
                                            
                                        </div>
                                        <div style="position:relative; height: 720px;">';

						//Выводим время
						for ($wt=540; $wt < 900; $wt=$wt+30){
							
							echo '
                                            <div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
                                                '.$zapis_times[$wt].'
                                            </div>';

							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						echo '
                                        </div>
                                    </td>';


                        //Соберём всю запись за сегодня на филиале у этих специалистов
                        $ZapisHereQueryToday3 = FilialKabSmenaZapisToday3($datatable, $year, $month, $day, $filial_id, $type);
                        //var_dump ($ZapisHereQueryToday3);

                        //Проходим по всем кабинетам
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';

							//Врач по графику есть
							if (isset($Work_Today_arr[$k][1])){
								//var_dump($Kab_work_today_smena1);

								$worker_id = $Work_Today_arr[$k][1]['worker'];
								
								echo '
                                        <div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
                                            <div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
                                                1 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</i>
                                                <div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
                                            </div>
                                        </div>';
							}else{
							    //врача по графику нет
								
								$worker_id = 0;
								
								echo '
                                        <div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
                                            <div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
                                                1 смена каб '.$k.'<br>
                                                <span style="color:red;">нет врача по <a href="scheduler.php?filial='.$filial_id.''.$who.'">графику</a></span>
                                                <div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
                                            </div>
                                        </div>';
							}
							
							echo '
									    <div style="position:relative; height: 720px;">';

                            //Выбрать записи пациентов, если есть
                            //$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $filial_id, $k);
                            //var_dump ($ZapisHereQueryToday);

//                            $NextTime = FALSE;
//                            $ThatTimeFree = TRUE;
//                            $PeredannNextTime = FALSE;
//                            $NextTime_val = 0;
//                            //сдвиг для блоков времени
//                            $cellZapisTime_TopSdvig = 0;
//                            $cellZapisValue_TopSdvig = 0;
//                            $PrevZapis = array();
//                            $NextFill = FALSE;

                            //Отрисовка записи
                            echo prepareDrawZapisDay ($ZapisHereQueryToday3, 540, 900, $worker_id, $filials_j, $filial_id, $k, $year, $month, $day, $type, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);

//								for ($wt=540; $wt < 900; $wt=$wt+30){
//
//									if (isset($Work_Today_arr[$k][1])){
//										$bg_color = '';
//									}else{
//										$bg_color = ' background-color: #f0f0f0;';
//									}
//
//									$back_color = '';
//
//									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
//
//									//Выбрать записи пациентов, если есть
//									//$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $filial_id, $k, $wt, $type);
//									//var_dump ($ZapisHereQueryToday);
//
//
//
//
//									if ($ZapisHereQueryToday != 0){
//										//Если тут записей больше 1
//										if (count($ZapisHereQueryToday) > 1){
//											foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
//												if ($ZapisHereQueryToday_val['start_time'] < 900){
//													//вычисляем время начала приёма
//													$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
//													$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
//													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//													//вычисляем время окончания приёма
//													$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
//													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//													$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
//													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//													//Сдвиг для блока
//													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-540)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
//													//Высота блока
//													$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
//													//Если от начала работы окончание выходит за предел промежутка времени
//													if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//														$NextFill = TRUE;
//														//$PrevZapis = $ZapisHereQueryToday_val;
//													}
//
//													//Если перед первой работой от начала промежутка есть свободное время
//													if ($Zapis_key == 0){
//														if ($ZapisHereQueryToday_val['start_time'] > $wt){
//															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
//															$wt_start_FreeSpace = $wt;
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//															echo '
//																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																	';
//																//var_dump($Zapis_key);
//															echo '
//																</div>';
//														}
//													}else{
//														//если последняя запись
//														if ($Zapis_key == count($ZapisHereQueryToday)-1){
//															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//															$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//															echo '
//																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																	';
//																//var_dump($Zapis_key);
//															echo '
//																</div>';
//															//если есть свободное место после последней записи
//															if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
//																$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
//																$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//															//Если залазит на следующий отрезок времени
//															}else{
//																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//																	$NextFill = TRUE;
//																	//$PrevZapis = $ZapisHereQueryToday_val;
//																}
//															}
//
//														}else{
//															$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//															$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//															echo '
//																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																	.';
//																//var_dump($Zapis_key);
//															echo '
//																</div>';
//														}
//													}
//
//													//Если время выполнения работы больше чем осталось до конца смены
//													if ($wt == 870){
//														if ($cellZapisValue_Height > 60){
//															$cellZapisValue_Height = 60;
//														}
//													}
//													//Если пришёл
//													if ($ZapisHereQueryToday_val['enter'] == 1){
//														$back_color = 'background-color: rgba(119, 255, 135, 1);';
//													}else{
//														//Если оформлено не на этом филиале
//														if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
//															$back_color = 'background-color: rgb(119, 255, 250);';
//														}
//													}
//
//                                                    $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                                    $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
//                                                    $title_descr = $ZapisHereQueryToday_val['description'];
//                                                    $zapis_id = $ZapisHereQueryToday_val['id'];
//
//                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//
//												}else{
//												}
//												//Передать предыдущую запись
//												$PrevZapis = $ZapisHereQueryToday_val;
//											}
//										}elseif (count($ZapisHereQueryToday) == 1){
//											if ($ZapisHereQueryToday[0]['start_time'] < 900){
//												//вычисляем время начала приёма
//												$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
//												$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
//												if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//												//вычисляем время окончания приёма
//												$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
//												if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//												$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
//												if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//												//Сдвиг для блока
//												$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-540)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-540)%30*2);
//												//Высота блока
//												$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
//												//Если от начала работы окончание выходит за предел промежутка времени
//												if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
//													$NextFill = TRUE;
//													//$PrevZapis = $ZapisHereQueryToday[0];
//												}
//												//Если работа закончится до окончания периода
//												if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
//													$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
//													$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
//													$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//													echo '
//														<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//															';
//														//var_dump($NextFill);
//													echo '
//														</div>';
//												}
//												//Если работа начнется не с начала периода
//												if ($ZapisHereQueryToday[0]['start_time'] > $wt){
//													if (!$NextFill){
//														$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//														$wt_start_FreeSpace = $wt;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//													}else{
//														$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//														$wt_start_FreeSpace = $wt;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//
//														/*if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
//															$NextFill = FALSE;
//															$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
//															$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//															echo '
//																<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', 'add')">
//																	4';
//																//var_dump($NextFill);
//															echo '
//																</div>';
//														}*/
//
//													}
//													echo '
//														<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//															';
//														//var_dump($NextFill);
//													echo '
//														</div>';
//												}
//
//												//Если время выполнения работы больше чем осталось до конца смены
//												if ($wt == 870){
//													if ($cellZapisValue_Height > 60){
//														$cellZapisValue_Height = 60;
//														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
//													}
//													//добавлено 2016-12-23
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > 900){
//														$cellZapisValue_Height = (900-$ZapisHereQueryToday[0]['start_time'])*2;
//														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
//														if ($NextSmenaArr[$k]['OstatokVremeni'] > 720){
//															$NextSmenaArr[$k]['OstatokVremeni'] = 720;
//														}
//													}
//												}else{
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > 900){
//														$cellZapisValue_Height = (900-$ZapisHereQueryToday[0]['start_time'])*2;
//														$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//														$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//														$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 900)*2;
//														if ($NextSmenaArr[$k]['OstatokVremeni'] > 720){
//															$NextSmenaArr[$k]['OstatokVremeni'] = 720;
//														}
//													}
//												}
//												if ($ZapisHereQueryToday[0]['enter'] == 1){
//													$back_color = 'background-color: rgba(119, 255, 135, 1);';
//												}else{
//													//Если оформлено не на этом филиале
//													if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
//														$back_color = 'background-color: rgb(119, 255, 250);';
//													}
//												}
//
//                                                $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                                $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
//                                                $title_descr = $ZapisHereQueryToday[0]['description'];
//                                                $zapis_id = $ZapisHereQueryToday[0]['id'];
//
//                                                echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//											}else{
//											}
//											//Передать предыдущую запись
//											$PrevZapis = $ZapisHereQueryToday[0];
//										}
//									//если тут вообще нет записи
//									}else{
//										if (!$NextFill){
//											$wt_FreeSpace = 30;
//											$wt_start_FreeSpace = $wt;
//											$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//											$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//											echo '
//												<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//													';
//												//var_dump($NextFill);
//											echo '
//												</div>';
//										}
//									}
//									//Если предыдущее время залазит на это
//									if ($NextFill){
//										if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
//											$NextFill = FALSE;
//											$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
//											$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
//											$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//											$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-540)*2;
//											echo '
//												<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//													';
//												//var_dump($NextFill);
//											echo '
//												</div>';
//										}
//									}
//								}
								echo '
                                        </div>';
							/*}else{
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none;">
												1 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$filial_id.'&who='.$who.'">графику</a></span>
											</div>
										</div>';
							}*/

							echo '
									</td>';
						}
						echo '
                                </tr>
                            </table>
                        </div>';
								
						//смена 2
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;

						echo '
						<div id="tabs-2">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
                                    <td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
                                        <div class="" style="border: none; width: 100%;  height: 50px;">
                                            
                                        </div>
                                        <div style="position:relative; height: 720px;">';

                        //Выводим время
						for ($wt=900; $wt < 1260; $wt=$wt+30){
							
							echo '
                                            <div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
                                                '.$zapis_times[$wt].'
                                            </div>';

							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}

						echo '
                                        </div>
                                    </td>';

                        //Проходим по всем кабинетам
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
									<td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';

                            //Врач по графику есть
							if (isset($Work_Today_arr[$k][2])){
								//var_dump($Kab_work_today_smena1);

								$worker_id = $Work_Today_arr[$k][2]['worker'];
								
								echo '
                                        <div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
                                            <div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
                                                2 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</i>
                                                <div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
                                            </div>
                                        </div>';
							}else{
                                //врача по графику нет

								$worker_id = 0;
								
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
												2 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$filial_id.''.$who.'">графику</a></span>
												<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
											</div>
										</div>';
							}
							echo'
										<div style="position:relative; height: 720px;">';
								//Выбрать записи пациентов, если есть
								//$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $filial_id, $k);
								//var_dump ($ZapisHereQueryToday);
								
//								$NextTime = FALSE;
//								$ThatTimeFree = TRUE;
//								$PeredannNextTime = FALSE;
//								$NextTime_val = 0;
//								//сдвиг для блоков времени
//								$cellZapisTime_TopSdvig = 0;
//								$cellZapisValue_TopSdvig = 0;
//								$PrevZapis = array();
//								$NextFill = FALSE;

                            //Отрисовка записи
                            echo prepareDrawZapisDay ($ZapisHereQueryToday3, 900, 1260, $worker_id, $filials_j, $filial_id, $k, $year, $month, $day, $type, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);
								
//								for ($wt=900; $wt < 1260; $wt=$wt+30){
//									if (isset($Work_Today_arr[$k][2])){
//										$bg_color = '';
//									}else{
//										$bg_color = ' background-color: #f0f0f0;';
//									}
//
//									$back_color = '';
//									/*echo '
//										<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;" onclick="window.location.href = \'zapis_full.php?filial='.$filial_id.'&who='.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'\'">
//											'.$zapis_times[$wt].'';
//									//var_dump($NextFill);
//									echo '
//										</div>';*/
//									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
//
//									//Выбрать записи пациентов, если есть
//									$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $filial_id, $k, $wt, $type);
//									//var_dump ($ZapisHereQueryToday);
//
//									if (isset($NextSmenaArr[$k]['NextSmenaFill']) && !$NextSmenaArr_Bool){
//										//var_dump(4);
//										if($NextSmenaArr[$k]['NextSmenaFill']){
//											//var_dump(5);
//											//надо перескочить промежуток
//											$NextSmenaArr_Bool = TRUE;
//											$NextSmenaArr_Zanimayu = $NextSmenaArr[$k]['OstatokVremeni']/2;
//
//											//$NextFill = TRUE;
//											$PrevZapis = $NextSmenaArr[$k]['ZapisHereQueryToday'];
//
//											//вычисляем время начала приёма
//											$TempStartWorkTime_h = floor($PrevZapis['start_time']/60);
//											$TempStartWorkTime_m = $PrevZapis['start_time']%60;
//											if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//											//вычисляем время окончания приёма
//											$TempEndWorkTime_h = floor(($PrevZapis['start_time']+$PrevZapis['wt'])/60);
//											if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//											$TempEndWorkTime_m = ($PrevZapis['start_time']+$PrevZapis['wt'])%60;
//											if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//											//Сдвиг для блока
//											$cellZapisValue_TopSdvig = 0;
//											//Высота блока
//											$cellZapisValue_Height = $NextSmenaArr[$k]['OstatokVremeni'];
//											if ($cellZapisValue_Height > 720)
//												$cellZapisValue_Height = 720;
//
//											if ($NextSmenaArr[$k]['ZapisHereQueryToday']['enter'] == 1){
//												$back_color = 'background-color: rgba(119, 255, 135, 1);';
//											}else{
//												//Если оформлено не на этом филиале
//												if($NextSmenaArr[$k]['ZapisHereQueryToday']['office'] != $NextSmenaArr[$k]['ZapisHereQueryToday']['add_from']){
//													$back_color = 'background-color: rgb(119, 255, 250);';
//												}
//											}
//
//                                            $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                            $title_client = WriteSearchUser('spr_clients', $NextSmenaArr[$k]['ZapisHereQueryToday']['patient'], 'user', false);
//                                            $title_descr = $NextSmenaArr[$k]['ZapisHereQueryToday']['description'];
//                                            $zapis_id = $NextSmenaArr[$k]['ZapisHereQueryToday']['id'];
//
//                                            echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//										}
//									}
//
//
//										if ($ZapisHereQueryToday != 0){
//											//var_dump(2);
//
//											//Если тут записей больше 1
//											if (count($ZapisHereQueryToday) > 1){
//												foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
//													if ($ZapisHereQueryToday_val['start_time'] < 1260){
//														//вычисляем время начала приёма
//														$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
//														$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
//														if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//														//вычисляем время окончания приёма
//														$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
//														if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//														$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
//														if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//														//Сдвиг для блока
//														$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-900)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
//														//Высота блока
//														$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
//														//Если от начала работы окончание выходит за предел промежутка времени
//														if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//															$NextFill = TRUE;
//															//$PrevZapis = $ZapisHereQueryToday_val;
//														}
//														//Если перед первой работой от начала промежутка есть свободное время
//														if ($Zapis_key == 0){
//															if ($ZapisHereQueryToday_val['start_time'] > $wt){
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
//																$wt_start_FreeSpace = $wt;
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//															}
//														}else{
//															//если последняя запись
//															if ($Zapis_key == count($ZapisHereQueryToday)-1){
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//																//если есть свободное место после последней записи
//																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
//																	$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
//																	$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
//																	$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																	$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//																	echo '
//																		<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', '.$type.', \'add\')">
//																			';
//																		//var_dump($Zapis_key);
//																	echo '
//																		</div>';
//																//Если залазит на следующий отрезок времени
//																}else{
//																	if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//																		$NextFill = TRUE;
//																		//$PrevZapis = $ZapisHereQueryToday_val;
//																	}
//																}
//
//															}else{
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//															}
//														}
//
//														//Если время выполнения работы больше чем осталось до конца смены
//														if ($wt == 1230){
//															if ($cellZapisValue_Height > 60){
//																$cellZapisValue_Height = 60;
//															}
//														}else{
//															if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1260){
//																$cellZapisValue_Height = (1260-$ZapisHereQueryToday[0]['start_time'])*2;
//																/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//																$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//																$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
//																if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
//																	$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
//																}*/
//															}
//														}
//														if ($ZapisHereQueryToday_val['enter'] == 1){
//															$back_color = 'background-color: rgba(119, 255, 135, 1);';
//														}else{
//															//Если оформлено не на этом филиале
//															if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
//																$back_color = 'background-color: rgb(119, 255, 250);';
//															}
//														}
//
//                                                        $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                                        $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
//                                                        $title_descr = $ZapisHereQueryToday_val['description'];
//                                                        $zapis_id = $ZapisHereQueryToday_val['id'];
//
//                                                        echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//													}else{
//													}
//													//Передать предыдущую запись
//													$PrevZapis = $ZapisHereQueryToday_val;
//												}
//											}elseif (count($ZapisHereQueryToday) == 1){
//												if ($ZapisHereQueryToday[0]['start_time'] < 1260){
//													//вычисляем время начала приёма
//													$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
//													$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
//													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//													//вычисляем время окончания приёма
//													$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
//													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//													$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
//													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//													//Сдвиг для блока
//													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-900)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-900)%30*2);
//													//Высота блока
//													$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
//													//Если от начала работы окончание выходит за предел промежутка времени
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
//														$NextFill = TRUE;
//														//$PrevZapis = $ZapisHereQueryToday[0];
//													}
//													//Если работа закончится до окончания периода
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
//														$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
//														$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//														echo '
//															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																';
//															//var_dump($NextFill);
//														echo '
//															</div>';
//													}
//
//													//Если работа начнется не с начала периода
//													if ($ZapisHereQueryToday[0]['start_time'] > $wt){
//														if (!$NextFill){
//															$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//															$wt_start_FreeSpace = $wt;
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//														}else{
//															//var_dump($PrevZapis);
//															if (isset($PrevZapis['start_time'])){
//																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - ($PrevZapis['start_time'] + $PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//															}else{
//																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//																$wt_start_FreeSpace = $wt;
//															}
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//														}
//														echo '
//															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																';
//															//var_dump($NextSmenaArr);
//														echo '
//															</div>';
//													}
//
//													//Если время выполнения работы больше чем осталось до конца смены
//													if ($wt == 1230){
//														if ($cellZapisValue_Height > 60){
//															$cellZapisValue_Height = 60;
//														}
//													}else{
//														if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1260){
//															$cellZapisValue_Height = (1260-$ZapisHereQueryToday[0]['start_time'])*2;
//															/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//															$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//															$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
//															if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
//																$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
//															}*/
//														}
//													}
//													if ($ZapisHereQueryToday[0]['enter'] == 1){
//														$back_color = 'background-color: rgba(119, 255, 135, 1);';
//													}else{
//														//Если оформлено не на этом филиале
//														if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
//															$back_color = 'background-color: rgb(119, 255, 250);';
//														}
//													}
//
//													$title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//													$title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
//													$title_descr = $ZapisHereQueryToday[0]['description'];
//                                                    $zapis_id = $ZapisHereQueryToday[0]['id'];
//
//                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//												}else{
//												}
//												//Передать предыдущую запись
//												$PrevZapis = $ZapisHereQueryToday[0];
//											}
//										//если тут вообще нет записи
//										}else{
//											//var_dump(1);
//											$mark = '';
//											if (!$NextFill){
//												//($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))
//												if (!$NextSmenaArr_Bool){
//													$wt_FreeSpace = 30;
//													$wt_start_FreeSpace = $wt;
//													$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
//													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//													//$mark = 777;
//													//var_dump($wt);
//												}else{
//													if ((900+$NextSmenaArr_Zanimayu)< $wt){
//														$wt_FreeSpace = 30;
//														$wt_start_FreeSpace = $wt;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//														//$mark = 888;
//													}else{
//														$wt_FreeSpace = $wt+30-(900+$NextSmenaArr_Zanimayu);
//														$wt_start_FreeSpace = 900+$NextSmenaArr_Zanimayu;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//														//$mark = $NextSmenaArr_Zanimayu;
//													}
//												}
//												echo '
//													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//														'.$mark.'';
//													//var_dump($NextFill);
//												echo '
//													</div>';
//											}
//										}
//										//Если предыдущее время залазит на это
//										if ($NextFill){
//											//var_dump(3);
//											if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
//												$NextFill = FALSE;
//												$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
//												$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
//												$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//												$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-900)*2;
//												echo '
//													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//														';
//													//var_dump($NextFill);
//												echo '
//													</div>';
//											}
//										}
//									//}
//
//								}
								echo '
                                        </div>';


							echo '
									</td>';

//							$NextSmenaArr[$k] = array();
//							$NextSmenaArr_Bool = FALSE;

						}
						echo '
								</tr>
							</table>
						</div>';
						
						//смена 3
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;

						echo '
						<div id="tabs-3">
							<table style="border:1px solid #BFBCB5; /*width: 100%;*/ background: #fff;">
								<tr>';
						echo '
                                    <td style="border:1px solid grey; vertical-align: top; width: 50px; min-width: 50px; max-width: 50px;">
                                        <div class="" style="border: none; width: 100%;  height: 50px;">
                                            
                                        </div>
                                        <div style="position:relative; height: 720px;">';

						//for ($wt=1260; $wt < 1620; $wt=$wt+30){

						//до полуночи
                        //Выводим время
						for ($wt=1260; $wt < 1440; $wt=$wt+30){	
							echo '
                                            <div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
                                                '.$zapis_times[$wt].'
                                            </div>';

							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}

						//после полуночи
                        //Выводим время
						for ($wt=0; $wt < 180; $wt=$wt+30){	
							echo '
                                            <div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;">
                                                '.$zapis_times[$wt].'
                                            </div>';

							$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
						}
						echo '
                                        </div>
                                    </td>';

                        //Проходим по всем кабинетам
						for ($k = 1; $k <= count($kabsInFilial); $k++){
							echo '
							        <td style="border:1px solid grey; vertical-align: top; width: 180px; min-width: 180px; max-width: 180px;">';

                            //Врач по графику есть
							if (isset($Work_Today_arr[$k][3])){
								//var_dump($Kab_work_today_smena1);

								$worker_id = $Work_Today_arr[$k][3]['worker'];
								
								echo '
                                        <div class="cellsBlock5 ahref" style="border: none; font-weight: bold; font-size:80%; margin-bottom: -1px;" >
                                            <div class="cellRight" id="month_date_worker" style="border: none; background-color:rgba(39, 183, 127, .5); height: 40px; outline: none; position: relative;">
                                                3 смена каб '.$k.'<br><i>'.WriteSearchUser('spr_workers', $worker_id, 'user', false).'</i>
                                                <div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
                                            </div>
                                        </div>';
							}else{
                                //врача по графику нет

								$worker_id = 0;
								
								echo '
										<div class="cellsBlock5" style="font-weight: bold; font-size:80%; margin-bottom: -1px;">
											<div class="cellRight" id="month_date_worker" style="border: none; height: 40px; outline: none; position: relative;">
												3 смена каб '.$k.'<br>
												<span style="color:red;">нет врача по <a href="scheduler.php?filial='.$filial_id.''.$who.'">графику</a></span>
												<div class="b" style="position: absolute; top: 0; right: 0; color: #0C0C0C; margin: 0; padding: 1px 5px;"><a href="zapis_full.php?filial='.$filial_id.''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="ahref" style="border: none; font-weight: bold; font-size:80%;" title="Подробно">Подробно</a></div>
											</div>
										</div>';
							}
							echo'
										<div style="position:relative; height: 720px;">';
								//Выбрать записи пациентов, если есть
								//$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $y, $m, $d, $filial_id, $k);
								//var_dump ($ZapisHereQueryToday);
								
//								$NextTime = FALSE;
//								$ThatTimeFree = TRUE;
//								$PeredannNextTime = FALSE;
//								$NextTime_val = 0;
//								//сдвиг для блоков времени
//								$cellZapisTime_TopSdvig = 0;
//								$cellZapisValue_TopSdvig = 0;
//								$PrevZapis = array();
//								$NextFill = FALSE;
								
								//for ($wt=900; $wt < 1260; $wt=$wt+30){
								//for ($wt=1260; $wt < 1620; $wt=$wt+30){
									
							//до полуночи
                            echo prepareDrawZapisDay ($ZapisHereQueryToday3, 1260, 1440, $worker_id, $filials_j, $filial_id, $k, $year, $month, $day, $type, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit);

//								for ($wt=1260; $wt < 1440; $wt=$wt+30){
//									if (isset($Work_Today_arr[$k][3])){
//										$bg_color = '';
//									}else{
//										$bg_color = ' background-color: #f0f0f0;';
//									}
//
//									$back_color = '';
//									/*echo '
//										<div class="cellZapisTime" style="text-align: -moz-center; text-align: center; text-align: -webkit-center; top: '.$cellZapisTime_TopSdvig.'px;" onclick="window.location.href = \'zapis_full.php?filial='.$filial_id.'&who='.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'\'">
//											'.$zapis_times[$wt].'';
//									//var_dump($NextFill);
//									echo '
//										</div>';*/
//									$cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;
//
//									//Выбрать записи пациентов, если есть
//									$ZapisHereQueryToday = FilialKabSmenaZapisToday2($datatable, $year, $month, $day, $filial_id, $k, $wt, $type);
//									//var_dump ($ZapisHereQueryToday);
//
//									if (isset($NextSmenaArr[$k]['NextSmenaFill']) && !$NextSmenaArr_Bool){
//										//var_dump(4);
//										if($NextSmenaArr[$k]['NextSmenaFill']){
//											//var_dump(5);
//											//надо перескочить промежуток
//											$NextSmenaArr_Bool = TRUE;
//											$NextSmenaArr_Zanimayu = $NextSmenaArr[$k]['OstatokVremeni']/2;
//
//											//$NextFill = TRUE;
//											$PrevZapis = $NextSmenaArr[$k]['ZapisHereQueryToday'];
//
//											//вычисляем время начала приёма
//											$TempStartWorkTime_h = floor($PrevZapis['start_time']/60);
//											$TempStartWorkTime_m = $PrevZapis['start_time']%60;
//											if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//											//вычисляем время окончания приёма
//											$TempEndWorkTime_h = floor(($PrevZapis['start_time']+$PrevZapis['wt'])/60);
//											if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//											$TempEndWorkTime_m = ($PrevZapis['start_time']+$PrevZapis['wt'])%60;
//											if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//											//Сдвиг для блока
//											$cellZapisValue_TopSdvig = 0;
//											//Высота блока
//											$cellZapisValue_Height = $NextSmenaArr[$k]['OstatokVremeni'];
//											if ($cellZapisValue_Height > 720)
//												$cellZapisValue_Height = 720;
//
//											if ($NextSmenaArr[$k]['ZapisHereQueryToday']['enter'] == 1){
//												$back_color = 'background-color: rgba(119, 255, 135, 1);';
//											}else{
//												//Если оформлено не на этом филиале
//												if($NextSmenaArr[$k]['ZapisHereQueryToday']['office'] != $NextSmenaArr[$k]['ZapisHereQueryToday']['add_from']){
//													$back_color = 'background-color: rgb(119, 255, 250);';
//												}
//											}
//
//                                            $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                            $title_client = WriteSearchUser('spr_clients', $NextSmenaArr[$k]['ZapisHereQueryToday']['patient'], 'user', false);
//                                            $title_descr = $NextSmenaArr[$k]['ZapisHereQueryToday']['description'];
//                                            $zapis_id = $NextSmenaArr[$k]['ZapisHereQueryToday']['id'];
//
//                                            echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($NextSmenaArr[$k]['ZapisHereQueryToday'], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//
//										}
//									}
//
//										if ($ZapisHereQueryToday != 0){
//											//var_dump(2);
//
//											//Если тут записей больше 1
//											if (count($ZapisHereQueryToday) > 1){
//												foreach ($ZapisHereQueryToday as $Zapis_key => $ZapisHereQueryToday_val){
//													if ($ZapisHereQueryToday_val['start_time'] < 1440){
//														//вычисляем время начала приёма
//														$TempStartWorkTime_h = floor($ZapisHereQueryToday_val['start_time']/60);
//														$TempStartWorkTime_m = $ZapisHereQueryToday_val['start_time']%60;
//														if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//														//вычисляем время окончания приёма
//														$TempEndWorkTime_h = floor(($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])/60);
//														if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//														$TempEndWorkTime_m = ($ZapisHereQueryToday_val['start_time']+$ZapisHereQueryToday_val['wt'])%60;
//														if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//														//Сдвиг для блока
//														$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday_val['start_time']-1260)/30)*60 + ($ZapisHereQueryToday_val['start_time']-540)%30*2);
//														//Высота блока
//														$cellZapisValue_Height = $ZapisHereQueryToday_val['wt']*2;
//														//Если от начала работы окончание выходит за предел промежутка времени
//														if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//															$NextFill = TRUE;
//															//$PrevZapis = $ZapisHereQueryToday_val;
//														}
//														//Если перед первой работой от начала промежутка есть свободное время
//														if ($Zapis_key == 0){
//															if ($ZapisHereQueryToday_val['start_time'] > $wt){
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - $wt;
//																$wt_start_FreeSpace = $wt;
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//															}
//														}else{
//															//если последняя запись
//															if ($Zapis_key == count($ZapisHereQueryToday)-1){
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//																//если есть свободное место после последней записи
//																if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] < $wt+30){
//																	$wt_FreeSpace = $wt+30-($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt']);
//																	$wt_start_FreeSpace = $ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'];
//																	$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																	$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//																	echo '
//																		<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																			';
//																		//var_dump($Zapis_key);
//																	echo '
//																		</div>';
//																//Если залазит на следующий отрезок времени
//																}else{
//																	if ($ZapisHereQueryToday_val['start_time'] + $ZapisHereQueryToday_val['wt'] > $wt+30){
//																		$NextFill = TRUE;
//																		//$PrevZapis = $ZapisHereQueryToday_val;
//																	}
//																}
//
//															}else{
//																$wt_FreeSpace = $ZapisHereQueryToday_val['start_time'] - ($PrevZapis['start_time']+$PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//																$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//																$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//																echo '
//																	<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																		';
//																	//var_dump($Zapis_key);
//																echo '
//																	</div>';
//															}
//														}
//
//														//Если время выполнения работы больше чем осталось до конца смены
//														if ($wt == 1230){
//															if ($cellZapisValue_Height > 60){
//																$cellZapisValue_Height = 60;
//															}
//														}else{
//															if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1440){
//																$cellZapisValue_Height = (1440-$ZapisHereQueryToday[0]['start_time'])*2;
//																/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//																$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//																$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
//																if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
//																	$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
//																}*/
//															}
//														}
//														if ($ZapisHereQueryToday_val['enter'] == 1){
//															$back_color = 'background-color: rgba(119, 255, 135, 1);';
//														}else{
//															//Если оформлено не на этом филиале
//															if($ZapisHereQueryToday_val['office'] != $ZapisHereQueryToday_val['add_from']){
//																$back_color = 'background-color: rgb(119, 255, 250);';
//															}
//														}
//
//                                                        $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                                        $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday_val['patient'], 'user', false);
//                                                        $title_descr = $ZapisHereQueryToday_val['description'];
//                                                        $zapis_id = $ZapisHereQueryToday_val['id'];
//
//                                                        echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday_val, $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//													}else{
//													}
//													//Передать предыдущую запись
//													$PrevZapis = $ZapisHereQueryToday_val;
//												}
//											}elseif (count($ZapisHereQueryToday) == 1){
//												//var_dump(2);
//												//var_dump($ZapisHereQueryToday[0]['start_time']);
//												//var_dump($ZapisHereQueryToday[0]['wt']);
//												if ($ZapisHereQueryToday[0]['start_time'] < 1440){
//													//вычисляем время начала приёма
//													$TempStartWorkTime_h = floor($ZapisHereQueryToday[0]['start_time']/60);
//													$TempStartWorkTime_m = $ZapisHereQueryToday[0]['start_time']%60;
//													if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;
//													//вычисляем время окончания приёма
//													$TempEndWorkTime_h = floor(($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])/60);
//													if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
//													$TempEndWorkTime_m = ($ZapisHereQueryToday[0]['start_time']+$ZapisHereQueryToday[0]['wt'])%60;
//													if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;
//													//Сдвиг для блока
//													$cellZapisValue_TopSdvig = (floor(($ZapisHereQueryToday[0]['start_time']-1260)/30)*60 + ($ZapisHereQueryToday[0]['start_time']-1260)%30*2);
//													//Высота блока
//													$cellZapisValue_Height = $ZapisHereQueryToday[0]['wt']*2;
//													//Если от начала работы окончание выходит за предел промежутка времени
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] > $wt+30){
//														$NextFill = TRUE;
//														//$PrevZapis = $ZapisHereQueryToday[0];
//													}
//													//Если работа закончится до окончания периода
//													if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] < $wt+30){
//														$wt_FreeSpace = $wt+30-($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt']);
//														$wt_start_FreeSpace = $ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'];
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//														echo '
//															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																';
//															//var_dump($NextFill);
//														echo '
//															</div>';
//													}
//
//													//Если работа начнется не с начала периода
//													if ($ZapisHereQueryToday[0]['start_time'] > $wt){
//														if (!$NextFill){
//															$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//															$wt_start_FreeSpace = $wt;
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//														}else{
//															//var_dump($PrevZapis);
//															if (isset($PrevZapis['start_time'])){
//																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - ($PrevZapis['start_time'] + $PrevZapis['wt']);
//																$wt_start_FreeSpace = $PrevZapis['start_time'] + $PrevZapis['wt'];
//															}else{
//																$wt_FreeSpace = $ZapisHereQueryToday[0]['start_time'] - $wt;
//																$wt_start_FreeSpace = $wt;
//															}
//															$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//															$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//														}
//														echo '
//															<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//																';
//															//var_dump($NextSmenaArr);
//														echo '
//															</div>';
//													}
//
//													//Если время выполнения работы больше чем осталось до конца смены
//													if ($wt == 1230){
//														if ($cellZapisValue_Height > 60){
//															$cellZapisValue_Height = 60;
//														}
//													}else{
//														if ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] >= 1440){
//															$cellZapisValue_Height = (1440-$ZapisHereQueryToday[0]['start_time'])*2;
//															/*$NextSmenaArr[$k]['NextSmenaFill'] = TRUE;
//															$NextSmenaArr[$k]['ZapisHereQueryToday'] = $ZapisHereQueryToday[0];
//															$NextSmenaArr[$k]['OstatokVremeni'] = ($ZapisHereQueryToday[0]['start_time'] + $ZapisHereQueryToday[0]['wt'] - 1260)*2;
//															if ($NextSmenaArr[$k]['OstatokVremeni'] > 1260){
//																$NextSmenaArr[$k]['OstatokVremeni'] = 1260;
//															}*/
//														}
//													}
//													if ($ZapisHereQueryToday[0]['enter'] == 1){
//														$back_color = 'background-color: rgba(119, 255, 135, 1);';
//													}else{
//														//Если оформлено не на этом филиале
//														if($ZapisHereQueryToday[0]['office'] != $ZapisHereQueryToday[0]['add_from']){
//															$back_color = 'background-color: rgb(119, 255, 250);';
//														}
//													}
//
//                                                    $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
//                                                    $title_client = WriteSearchUser('spr_clients', $ZapisHereQueryToday[0]['patient'], 'user', false);
//                                                    $title_descr = $ZapisHereQueryToday[0]['description'];
//                                                    $zapis_id = $ZapisHereQueryToday[0]['id'];
//
//                                                    echo drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($ZapisHereQueryToday[0], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
//
//												}else{
//												}
//												//Передать предыдущую запись
//												$PrevZapis = $ZapisHereQueryToday[0];
//											}
//										//если тут вообще нет записи
//										}else{
//											//var_dump(1);
//											$mark = '';
//											if (!$NextFill){
//												//var_dump(7);
//												//($NextSmenaArr_Bool && (((900+$NextSmenaArr_Zanimayu)-$wt)%30 != 0))
//												if (!$NextSmenaArr_Bool){
//													//var_dump(8);
//													$wt_FreeSpace = 30;
//													$wt_start_FreeSpace = $wt;
//													$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
//													$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//													//$mark = 777;
//													//var_dump($wt);
//												}else{
//													if ((1260+$NextSmenaArr_Zanimayu)< $wt){
//														$wt_FreeSpace = 30;
//														$wt_start_FreeSpace = $wt;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace * 2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//														//$mark = 888;
//													}else{
//														$wt_FreeSpace = $wt+30-(1260+$NextSmenaArr_Zanimayu);
//														$wt_start_FreeSpace = 1260+$NextSmenaArr_Zanimayu;
//														$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//														$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//														//$mark = $NextSmenaArr_Zanimayu;
//													}
//												}
//												echo '
//													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//														'.$mark.'';
//													//var_dump($NextFill);
//												echo '
//													</div>';
//											}
//										}
//										//Если предыдущее время залазит на это
//										if ($NextFill){
//											//var_dump(3);
//											if ($PrevZapis['start_time'] + $PrevZapis['wt'] < $wt+30){
//												$NextFill = FALSE;
//												$wt_FreeSpace = $wt+30-($PrevZapis['start_time'] + $PrevZapis['wt']);
//												$wt_start_FreeSpace = ($PrevZapis['start_time'] + $PrevZapis['wt'])%30+$wt;
//												$cellZapisFreeSpace_Height = $wt_FreeSpace*2;
//												$cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-1260)*2;
//												echo '
//													<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filials_j[$filial_id]['name'].'\', '.$k.', '.$year.', '.$month.','.$day.', 2, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker.', \''.WriteSearchUser('spr_workers', $worker, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">
//														';
//													//var_dump($NextFill);
//												echo '
//													</div>';
//											}
//										}
//									//}
//
//								}
							echo '
                                        </div>
                                    </td>';
//							$NextSmenaArr[$k] = array();
//							$NextSmenaArr_Bool = FALSE;
						}
						echo '
								</tr>
							</table>
						</div>';

						
						//смена 4
						
						//сдвиг для блоков времени
						$cellZapisTime_TopSdvig = 0;

						//В четвертой смене ничего нету, не заполняем
						echo '
						<div id="tabs-4">
						</div>';
						
					}else{
						echo '<h1>В этом филиале нет кабинетов такого типа.</h1>';
					}

			echo '
					</div>
				</div>';

            echo $block_show_settings_add_temp_zapis;

            echo '
                <div id="Ajax_add_TempZapis_button" style="display: inline;"></div>
                <input type="button" class="b" value="Отмена" onclick="HideSettingsAddTempZapis()">
                 </div>';

			echo '
						<input type="hidden" id="zapis_id" name="zapis_id" value="0">
						
						<input type="hidden" id="day" name="day" value="0">
						<input type="hidden" id="month" name="month" value="0">
						<input type="hidden" id="year" name="year" value="0">
						<input type="hidden" id="author" name="author" value="'.$_SESSION['id'].'">
						<input type="hidden" id="filial" name="filial" value="0">
						<input type="hidden" id="start_time" name="start_time" value="0">
						<input type="hidden" id="wt" name="wt" value="0">
						<input type="hidden" id="worker_id" name="worker_id" value="0">
						<input type="hidden" id="type" name="type" value="'.$type.'">
						
						<input type="hidden" id="default_client_id" value="'.$default_client_id.'">
						<input type="hidden" id="default_client_name" value="'.$default_client_name.'">
						<!--<input type="button" class="b" value="Добавить" id="Ajax_add_TempZapis" onclick="Ajax_add_TempZapis('.$type.')">-->';


			echo '	
						
					</div>
					<div id="doc_title">Запись '.$whose.'/'.$day.' ',$monthsName[$month],' ',$year,'/'.$filials_j[$filial_id]['name'].' - Асмедика</div>';
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			/*echo '
			<div id="ShowDescrTempZapis" style="display: none; position: fixed; padding: 10px; margin: 10px; top: 40px; left: 20px; background-color: rgba(255,255,0, .7); border: 1px dotted red; font-size: 75%;">

			</div>
			';*/
			
			echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
								//var dayW = document.getElementById("SelectDayW").value;
								document.location.href = "?filial="+$(this).val()+"'.$who.$dopClient.'";
							});
							/*$("#SelectDayW").change(function(){
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
							});*/
						});
						
					</script>';
					
			/*echo '
					<script>';*/
			
			//если есть права или бог или костыль (ассисенты ночью)
			if (($zapis['add_new'] == 1) || $god_mode || (($_SESSION['permissions'] == 7) && (date("H", time()-60*60) > 16))){
				echo '
				<script src="js/zapis.js"></script>';

			}	
			echo '	
				<!--</script>-->
			
			
			
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
			if (($zapis['add_new'] == 1) || $god_mode){					
				echo '';
			}

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

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>