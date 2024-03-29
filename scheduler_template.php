<?php

//scheduler_template.php v 2.0
//Расписание кабинетов филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        require 'variables.php';

		//var_dump ($_GET);
		//var_dump ($_SESSION);

		//$get_link = '';
		
		//Если есть GET
		/*if ($_GET){
			foreach ($_GET as $key => $value){
				$get_link .= '&'.$key.'='.$value;
			}
		}*/
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

			$dopQuery = '';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 5, array(5,6,10));
            }else{
                $getWho = returnGetWho(5, 5, array(5,6,10));
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
			
			//Филиал

            $filials_j = getAllFilials(true, false, false);
			//var_dump($filials_j);

            //Сколько смен отображать (раньше было 4, потому что работали ночью)
            $maxSmens = 2;

            $filials_j2 = getAllFilials(false, false, false);

            if (isset($_GET['filial'])){
                if ($_GET['filial'] > 0) {
                    $dopQuery .= " AND `filial`='{$_GET['filial']}'";
                }
            }else{
                if (isset($_SESSION['filial'])) {
                    $dopQuery .= " AND `filial`='{$_SESSION['filial']}'";
                }
            }

			$weekDays = '
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПН</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СР</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ЧТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ПТ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>СБ</b></div>
						<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>ВС</b></div>';
			
			//День недели
			if (isset($_GET['dayw'])){
				if ($_GET['dayw'] != 0){
					$dopQuery .= " AND `day`='{$_GET['dayw']}'";
				}
			}

            $msql_cnnct = ConnectToDB();

			//получаем шаблон графика из базы
			$query = "SELECT `filial`, `day`, `smena`, `kab`, `worker` FROM `sheduler_template` WHERE `type` = '$type'".$dopQuery;
			//var_dump($query);
			
			$shedTemplate = array();

			$arr = array();
			//$rez = array();

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

			$number = mysqli_num_rows($res);
			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
                    $shedTemplate[$arr['filial']][$arr['day']][$arr['smena']][$arr['kab']] = $arr['worker'];
				}
			}
            //$shedTemplate = $rez;
			//var_dump($shedTemplate[12]);
			
			//есть ли кабинеты в филиале
			$kabsInFilialExist = FALSE;
			//какие есть кабинеты в филиале
			$kabsInFilial = array();
			
			//переменная, чтоб вкл/откл редактирование
            $iCanManage = 'false';
            $displayBlock = false;

			echo '
				<script>';
            if (isset($_SESSION['options'])){
                if (isset($_SESSION['options']['scheduler_template'])) {
                    $iCanManage = $_SESSION['options']['scheduler_template']['manage'];
                    if ($_SESSION['options']['scheduler_template']['manage'] == 'true') {
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
				<header style="margin-bottom: 5px;">
					<h1>Текущий график план</h1>
					'.$whose.'
				</header>
				<a href="scheduler.php" class="b">График </a>
				<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
				<!--<a href="scheduler_own.php" class="b">График сотрудника</a>-->';
			echo '
				<ul style="margin-left: 6px; margin-bottom: 10px;">
					<li style="width: auto; color:#777; font-size: 70%;">
					Примечание к графику:
						<ul>
							<li>1 смена 9:00 - 15:00</li>
							<li>2 смена 15:00 - 21:00</li>
							<li>3 смена 21:00 - 3:00</li>
							<li>4 смена 3:00 - 9:00</li>
						</ul>
					</li>
				</ul>';
			echo '
				<div id="data">
					<ul style="margin-left: 6px; margin-bottom: 20px;">';
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
						<li class="cellsBlock" style="width: auto; margin-bottom: 10px;">
							<div id="showDiv1" style="cursor: pointer;" onclick="manageScheduler(\'scheduler_template\')">
								<!--<span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">Управление</span> <i class="fa fa-cog" title="Настройки"></i>-->
								<span id="manageMessage" style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">', $displayBlock ? 'Управление <span style=\'color: green;\'>включено</span>' : 'Управление <span style=\'color: red;\'>выключено</span>' ,'</span> <i class="fa fa-cog" title="Настройки"></i>
							</div>
							<div id="div1" style="', $displayBlock ? 'display: block;' : '' ,'  width: 400px; margin-bottom: 10px; border: 1px dotted #BFBCB5; padding: 20px 10px 10px; background-color: #EEE;">
								<div id="changeShedOptionsReq"></div>';
                if (empty($shedTemplate)){
                    if ($_GET['filial'] > 0) {
                        echo '
                        <span style="color: red; font-size: 100%;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%"></i> Шаблон пустой </span><br>
							<input type="button" class="b" value="Создать пустую смену" onclick="if (iCanManage) Ajax_add_empty_shed(' . $type . ', ' . $_GET['filial'] . ')"> (открывает пустой шаблон)';
                    }
                }
                if (!empty($shedTemplate)) {
                    echo '
								<div style="margin-bottom: 18px;">
									Применить этот график на месяц ';
                    echo '
									<select name="SelectMonthShedOptions" id="SelectMonthShedOptions">';

                    foreach ($monthsName as $mNumber => $mName) {
                        $selected = '';
                        if ((int)$mNumber == (int)date('m') + 1) {
                            $selected = 'selected';
                        }
                        echo '
										<option value="' . $mNumber . '" ' . $selected . '>' . $mName . '</option>';
                    }
                    echo '
									</select>
									год 
									<select name="SelectYearShedOptions" id="SelectYearShedOptions">
										<option value="' . date('Y') . '" selected>' . date('Y') . '</option>
										<option value="' . (date('Y') + 1) . '">' . (date('Y') + 1) . '</option>
									</select>
								</div>
								<div style="margin-bottom: 18px;">
									Начиная с <input id="SelectDayShedOptions" type="number" value="1" min="1" max="31" size="2" style="width: 40px;"> числа
								</div>
								<div s  tyle="margin-bottom: 18px;">
                                    <span style="color: rgba(82, 81, 81, 0.93);">Выберите филиалы, которые хотите изменить</span><br><br>';

                    echo '<input type="checkbox" id="fullAll" name="fullAll" class="fullType" value="1"> Все<br>';

                    if (!empty($filials_j)) {
                        foreach ($filials_j as $filials_j_data) {
                            echo '<input type="checkbox" id="filial_' . $filials_j_data['id'] . '" name="filials_chckd[]" filial_id="' . $filials_j_data['id'] . '" class="fullType filialItem" value="' . $filials_j_data['id'] . '"> ' . $filials_j_data['name'] . '<br>';
                        }
                    }


                    echo '
				                </div>
								<div style="margin-top: 10px; margin-bottom: 18px;">
									Игнорировать существующий график <input type="checkbox" name="ignoreshed" id="ignoreshed" value="1">
								</div>
								<input type="button" class="b" value="Применить" onclick="if (iCanManage) Ajax_change_shed(' . $type . ')">
							</div>';
                }
                echo '
						</li>';
			}
			echo '
						<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
						<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
							<a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
							<a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
							<a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
						</li>	
						<li>
							<div style="display: inline-block; margin-right: 20px;">
								<div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
									Филиалы
								</div>
								<div>
									<select name="SelectFilial" id="SelectFilial">
										<option value="0">Все</option>';
			if (!empty($filials_j)){
                foreach ($filials_j as $filials_j_data) {
					$selected = '';
					if (isset($_GET['filial'])){
						if ($filials_j_data['id'] == $_GET['filial']){
							$selected = 'selected';
						}
					}
					echo "<option value='".$filials_j_data['id']."' $selected>".$filials_j_data['name']."</option>";
				}
			}
			echo '
									</select>
								</div>
							</div>
							<div style="display: inline-block; margin-right: 20px;">
								<div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">День недели</div>
								<div>
									<select name="SelectDayW" id="SelectDayW">
										<option value="0">Все</option>';
				for ($i=1; $i<=count($dayWeek_arr); $i++){
					$selected = '';
					if (isset($_GET['dayw'])){
						if ($i == $_GET['dayw']){
							$selected = 'selected';
							
							if ($_GET['dayw'] != 0){
								//Какой день отображать
								$weekDays = '
									<div class="cellTime" style="padding: 4px 0; text-align: center; background-color:#FEFEFE; width: 150px; min-width: 125px; max-width: 150px;"><b>'.$dayWeek_arr[$i].'</b></div>';
							}
						}
					}
					echo "<option value='$i' $selected>".$dayWeek_arr[$i]."</option>";
			}
			echo '
									</select>
								</div>
							</div>
							<div style="display: inline-block; margin-right: 20px;">
								<a href="?'.$who.'" class="dotyel" style="font-size: 70%;">Сбросить</a>
							</div>
						</li>
					</ul>';
			echo '
				<div style="margin-bottom: 20px;">
					<div class="cellsBlock">
						<div class="cellName" style="font:size: 110%; text-align: center; background-color:#CCC; width: 120px; min-width: 120px; max-width: 120px;"></div>';
			echo $weekDays;
			echo '
					</div>';
			
			if (!empty($shedTemplate)){
			    //var_dump($shedTemplate);
				//Пробегаемся по филиалам
                    foreach ($shedTemplate as $filial_id => $shed_data) {
                        //                    var_dump($filial_id);
                        //                    var_dump($shed_data);

                        if (isset($filials_j2[$filial_id])) {

                            //смотрим, какие кабинеты есть
                            $kabsInFilial_arr = SelDataFromDB('spr_kabs', $filial_id, 'office_kabs');

                            if ($kabsInFilial_arr != 0) {

                                $kabsInFilial_json = $kabsInFilial_arr[0][$kabsForDoctor];
                                //var_dump($kabsInFilial_json);

                                if ($kabsInFilial_json != NULL) {
                                    $kabsInFilialExist = TRUE;
                                    $kabsInFilial = json_decode($kabsInFilial_json, true);
                                    //var_dump($kabsInFilial);

                                } else {
                                    $kabsInFilialExist = FALSE;
                                }

                            }

                            //var_dump($kabsInFilialExist);
                            //Если кабинеты все таки есть
                            if ($kabsInFilialExist) {
                                //var_dump($kabsInFilial);
                                echo '
                                <div class="cellsBlock cellsBlockHover">
                                    <div class="cellName" style="font:size: 110%; text-align: left; background-color: #FEFEFE; width: 120px; min-width: 120px; max-width: 120px;">
                                        ' . $filials_j2[$filial_id]['name'] . '
                                    </div>
                            ';

                                //Дни недели
                                $dayWcount = 7;
                                if (isset($_GET['dayw'])) {
                                    if ($_GET['dayw'] != 0) {
                                        $dayWcount = 1;
                                        $dayWvalue = $_GET['dayw'];
                                    }
                                }

                                for ($dayW = 1; $dayW <= $dayWcount; $dayW++) {
                                    if ($dayWcount > 1) $dayWvalue = $dayW;
                                    echo '
                                    <div class="cellTime" style="padding: 0; text-align: center; background-color: #FEFEFE; width: 150px; min-width: 125px; max-width: 150px;">';
                                    //номера смен 1 - день 2- вечер 3 - ночь 4 - утро
                                    for ($smenaN = 1; $smenaN <= $maxSmens; $smenaN++) {
                                        echo '
                                        <div style="outline: 1px solid #666; display: table; margin-bottom: 3px;">
                                            <div style="vertical-align: middle; width: 5px; box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2); display: table-cell !important;">
                                                ' . $smenaN . '
                                            </div>';

                                        //отсутствие врачей в клинике
                                        $now_ahtung = TRUE;
                                        $ahtung = TRUE;
                                        //переменная для вывода
                                        $resEcho2 = '';

                                        //Кабинеты
                                        for ($kabN = 1; $kabN <= count($kabsInFilial); $kabN++) {
                                            $resEcho = '';
                                            //если врач есть
                                            if (isset($shedTemplate[$filial_id][$dayWvalue][$smenaN][$kabN])) {
                                                $resEcho = WriteSearchUser('spr_workers', $shedTemplate[$filial_id][$dayWvalue][$smenaN][$kabN], 'user', false) . ' <a href="scheduler_own.php?id=' . $shedTemplate[$filial_id][$dayWvalue][$smenaN][$kabN] . '" class="info"><i class="fa fa-info-circle" title="График врача"></i></a>';
                                                $ahtung = FALSE;
                                                $fontSize = 'font-size: 100%;';
                                            } else {
                                                $resEcho = '<span style="color: red;">никого</span>';
                                                $now_ahtung = TRUE;
                                                $fontSize = '';
                                            }
                                            $resEcho2 .= '
                                                <div style="box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);" onclick="if (iCanManage) ShowSettingsScheduler(' . $filial_id . ', \'' . $filials_j2[$filial_id]['name'] . '\', ' . $dayWvalue . ', ' . $smenaN . ', ' . $kabN . ')">
                                                    <div style="text-align: right; color: #555;">
                                                        <b>каб. ' . $kabN . '</b>
                                                    </div>
                                                    <div style="text-align: left; ' . $fontSize . ' padding: 4px;">';
                                            $resEcho2 .= $resEcho;
                                            $resEcho2 .= '
                                                    </div>
                                                </div>';
                                        }

                                        if (!$ahtung OR !$now_ahtung) {
                                            $BgColor = ' background-color: rgba(81, 249, 89, 0.47);';
                                        } else {
                                            $BgColor = ' background-color: rgba(252, 153, 153, 0.7);';
                                        }
                                        if ($smenaN > 2) {
                                            $BgColor = ' background-color: rgba(220, 220, 220, 0.5);';
                                        }

                                        echo '
                                            <div style="text-align: middle; display: table-cell !important; width: 100%;' . $BgColor . '">';
                                        echo $resEcho2;
                                        echo '		
                                            </div>
                                            
                                            
                                        </div>';
                                    }

                                    echo '
                                    </div>';
                                }
                                echo '
                                </div>';
                            } else {
                                echo '
                                <div class="cellsBlock cellsBlockHover" style="height: auto;">
                                    <div class="cellName" style="text-align: left; background-color: #FEFEFE; width: auto;">
                                        ' . $filials_j2[$filial_id]['name'] . ' нет кабинетов ' . $whose . '
                                    </div>
                                </div>
                            ';
                            }
                        }
                    }
			}else{
                echo '
							<div class="cellsBlock cellsBlockHover" style="height: auto;">
								<div class="cellName" style="text-align: left; background-color: #FEFEFE; width: auto;">
									 нет кабинетов '.$whose.'
								</div>
							</div>
						';
            }
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '
					<div id="ShowSettingsScheduler" style="position: absolute; z-index: 105; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; padding:10px;">
						<a class="close" href="#" onclick="HideSettingsScheduler()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
							Close
						</a>
						
						<div id="SettingsScheduler">
								<label id="smena_error" class="error"></label><br />
								<label id="worker_error" class="error"></label>
								<div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
									<div class="cellLeft">День недели</div>
									<div class="cellRight" id="dayW">
									</div>
									<div style="display: none;" id="dayW_value"></div>
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
								</div>';
				echo '
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

				echo '
						<input type="button" class="b" value="OK" onclick="if (iCanManage) ChangeWorkerSheduler()" id="changeworkersheduletbutton">
						<input type="button" class="b" value="Отмена" onclick="HideSettingsScheduler()">
					</div>';	
			}		
					
			echo '
	        <div id="doc_title">Текущий график план - Асмедика</div>
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

			
			echo '
			
				<script>
				
					$(function() {
						$("#SelectFilial").change(function(){
						    
						    blockWhileWaiting (true);
						    
							var dayW = $("#SelectDayW").val();
							document.location.href = "?filial="+$(this).val()+"&dayw="+dayW+"'.$who.'";
						});
						$("#SelectDayW").change(function(){
						    
						    blockWhileWaiting (true);
						
							var filial = $("#SelectFilial").val();
							document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"'.$who.'";
						});
					});';
					
					
			if (($scheduler['edit'] == 1) || $god_mode){
				echo '	
					function ShowSettingsScheduler(filial, filial_name, dayW, smenaN, kabN){
						$("#ShowSettingsScheduler").show();
						$("#overlay").show();
						//console.log (dict.config.dw4[dayW]);
						//!!!! убрать скролл
						window.scrollTo(0,0)
						
						document.getElementById("dayW").innerHTML = dict.config.dw4[dayW];
						document.getElementById("dayW_value").innerHTML = dayW;
						document.getElementById("filial_value").innerHTML = filial;
						document.getElementById("filial_name").innerHTML = filial_name;
						document.getElementById("kabN").innerHTML = kabN;
						document.getElementById("smenaN").innerHTML = smenaN;
						
						//Те, кто уже есть
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "scheduler_workers_here.php",
							// какие данные будут переданы
							data: {
								filial:filial,
								dayW:dayW,
								smenaN:smenaN,
								kabN:kabN,
								type: '.$type.'
							},
							// действие, при ответе с сервера
							success: function(workers_here){
								document.getElementById("workersTodayDelete").innerHTML=workers_here;
							}
						});	
						
						//Те, кто свободен
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "scheduler_workers_free.php",
							// какие данные будут переданы
							data: {
								dayW:dayW,
								smenaN:smenaN,
								type:'.$type.',
							},
							// действие, при ответе с сервера
							success: function(workers){
								document.getElementById("ShowWorkersHere").innerHTML=workers;
							}
						});	
						
					}
					
					//Закрываем диалоговое окно
					function HideSettingsScheduler(){
						$(\'#ShowSettingsScheduler\').hide();
						$(\'#overlay\').hide();
						//!!!!! проверить всё ли тут нужно
						var input = document.getElementsByName(\'DateForMove\');
						for (var i=0; i<input.length; i++)  {
							if(input[i].value=="0") input[i].checked="checked";
						}
						
						$("#ShowWorkersHere").html(\'<div class="cellsBlock2" style="width:320px; font-size:80%;"><div class="cellRight">Не выбрана смена</div></div>\');
						
						$(".error").hide();
						
						$("#errror").html("");
					}
					
					
					//!!!
					function ShowWorkersSmena(){
						var smena = 0;
						if ( $("#smena1").prop("checked")){
							if ( $("#smena2").prop("checked")){
								smena = 9;
							}else{
								smena = 1;
							}
						}else if ( $("#smena2").prop("checked")){
							smena = 2;
						}
						
						$.ajax({
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "show_workers_free.php",
							// какие данные будут переданы
							data: {
								day:$(\'#day\').val(),
								month:$(\'#month\').val(),
								year:$(\'#year\').val(),
								smena:smena,
								datatable:"'.$datatable.'"
							},
							// действие, при ответе с сервера
							success: function(workers){
								document.getElementById("ShowWorkersHere").innerHTML=workers;
							}
						});	
					}
					
					//Удаляем врача из смены
					function DeleteWorkersSmena(worker, filial, day, smena, kab, type){
						var rys = confirm("Удалить сотрудника из смены Графика?");
						if (rys){
							$.ajax({
								// метод отправки 
								type: "POST",
								// путь до скрипта-обработчика
								url: "scheduler_worker_delete.php",
								// какие данные будут переданы
								data: {
									worker:worker,
									filial:filial,
									day:day,
									smena:smena,
									kab:kab,
									type:type
								},
								// действие, при ответе с сервера
								success: function(request){
									document.getElementById("workersTodayDelete").innerHTML=request;
									setTimeout(function () {
										location.reload()
									}, 100);
								}
							});	
						}
					}';
			}	
			echo '	
				  
                    $(".fullType").click(function() {
                        
					    var checked_status = $(this).is(":checked");
					    var thisId = $(this).attr("id");
					    var pin_status = false;
					    var allCheckStatus = false;
					    
                        if (thisId == "fullAll"){
                            if (checked_status){
                                pin_status = true;
                            }else{
                                pin_status = false;
                            }
                            $(".fullType").each(function() {
                                $(this).prop("checked", pin_status);
                            });
                        }else{
                            if (!checked_status){
                                $("#fullAll").prop("checked", false);
                            }else{
                                allCheckStatus = true; 
                                $(".fullType").each(function() {
                                    if ($(this).attr("id") != "fullAll"){
                                        if (!$(this).is(":checked")){
                                            allCheckStatus = false; 
                                        }
                                    }
                                });
                                if (allCheckStatus){
                                    $("#fullAll").prop("checked", true);
                                }
                            }
                        }
					});
				  
				  
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
			if (($scheduler['edit'] == 1) || $god_mode){					
				echo '
					function ChangeWorkerSheduler() {

						$(".error").hide();
						document.getElementById("errrror").innerHTML = "";
					
						// получение данных из полей
						var day = document.getElementById("dayW_value").innerHTML;
						var filial = document.getElementById("filial_value").innerHTML;
						var kab = document.getElementById("kabN").innerHTML;
						var smena = document.getElementById("smenaN").innerHTML;
						var type = '.$type.';

						var worker = $("input[name=worker]:checked").val();
						if(typeof worker == "undefined") worker = 0;

						$.ajax({
							dataType: "json",
							//statbox:SettingsScheduler,
							// метод отправки 
							type: "POST",
							// путь до скрипта-обработчика
							url: "scheduler_worker_edit_f.php",
							// какие данные будут переданы
							data: {
								day:day,
								filial:filial,
								kab:kab,
								smena:smena,
								type:type,
								worker:worker,
							},
							// действие, при ответе с сервера
							success: function(data){

								if (data.req == "ok"){
									// прячем текст ошибок
									$(".error").hide();
									document.getElementById("errrror").innerHTML = "";
									setTimeout(function () {
										location.reload()
									}, 100);
								}
							}
						});						
					};';
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