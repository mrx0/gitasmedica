<?php

//zapis_solar.php
//Для солярия

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($stom);
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

			//$offices = $offices_j = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            require 'variables.php';

            $edit_options = false;
            $upr_edit = false;
            $admin_edit = false;
            $stom_edit = false;
            $cosm_edit = false;
            $finance_edit = false;

			$post_data = '';
			$js_data = '';
			$kabsInFilialExist = FALSE;
			$kabsInFilial = array();
			$dopWho = '';
			$dopDate = '';
			$dopFilial = '';			
			
			$NextSmenaArr_Bool = FALSE;
			$NextSmenaArr_Zanimayu = 0;

			$who = '&who=stom';
			$whose = 'Стоматологов ';
			$selected_stom = ' selected';
			$selected_cosm = ' ';
			$datatable = 'scheduler_stom';

            //операции со временем
            $day = date('d');
            $month = date('m');
            $year = date('Y');

            if (!isset($_GET['filial_id'])){
                //Филиал
                if (isset($_SESSION['filial'])){
                    $_GET['filial_id'] = $_SESSION['filial'];
                }else{
                    $_GET['filial_id'] = 16;
                }
            }

			
			if ($_GET){
				//var_dump ($_GET);

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

                $stom_color = '';
                $cosm_color = $getWho['cosm_color'];
                $somat_color = $getWho['somat_color'];
                $admin_color = $getWho['admin_color'];
                $assist_color = $getWho['assist_color'];
                $sanit_color = $getWho['sanit_color'];
                $ubor_color = $getWho['ubor_color'];
                $dvornik_color = $getWho['dvornik_color'];
                $other_color = $getWho['other_color'];
                $all_color = $getWho['all_color'];
				
				if (isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
					//операции со временем						
					$day = $_GET['d'];
					$month = $_GET['m'];
					$year = $_GET['y'];
				}

				if (!isset($day) || $day < 1 || $day > 31)
					$day = date("d");				
				if (!isset($month) || $month < 1 || $month > 12)
					$month = date("m");
				if (!isset($year) || $year < 2010 || $year > 2037)
					$year = date("Y");

				//Приводим месяц к виду 01 02 09 ...
                $month = dateTransformation ($month);

				
				if (isset($_GET['kab'])){
					$kab = $_GET['kab'];
				}else{
					$kab = 1;
				}

				foreach ($_GET as $key => $value){
					if (($key == 'd') || ($key == 'm') || ($key == 'y'))
						$dopDate  .= '&'.$key.'='.$value;
					if ($key == 'filial_id')
						$dopFilial .= '&'.$key.'='.$value;
					if ($key == 'who')
						$dopWho .= '&'.$key.'='.$value;
				}

				//!!! переделать, потому что выше есть $filials_j
				$filial = SelDataFromDB('spr_filials', $_GET['filial_id'], 'offices');
				//var_dump($filial['name']);
				
				$kabsInFilial_arr = SelDataFromDB('spr_kabs', $_GET['filial_id'], 'office_kabs');
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
					</script>';
				
				if ($filial != 0){
					
					echo '
						<div id="status">
							<header>
								<div class="nav">
									<a href="zapis.php?filial='.$_GET['filial_id'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'" class="b">Запись</a>
									<a href="scheduler.php?filial='.$_GET['filial_id'].''.$who.'" class="b">График</a>
									<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
								</div>
							
								<h2>Запись '.$day.' ',$monthsName[$month],' ',$year,' <small>(подробное описание)</small></h2>
								<b>Филиал</b> '.$filial[0]['name'].'<br>
								<!--<b>Кабинет '.$kab.'</b><br>-->
								<span style="color: green; font-size: 120%; font-weight: bold;">Солярий</span><br>
								<br><br>';

					echo '			
							</header>';
					if (!isset($_SESSION['filial'])){
						echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';							
					}

                    echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 1;">';

                    echo $block_fast_search_client;

                    echo '
					    </div>';

					echo '		
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=5&kab=1" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=6&kab=1" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=10&kab=1" class="b" style="'.$somat_color.'">Специалисты</a>
								<a href="zapis_solar.php" class="b" style="background-color: #fff261;">Солярий</a>
								<a href="zapis_full2.php" class="b" style="">Без записи</a>
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
                        foreach ($filials_j as $f_id => $filial_item){
                            $selected = '';
                            if (isset($_GET['filial_id'])){
                                if ($f_id == $_GET['filial_id']){
                                    $selected = 'selected';
                                }
                            }
                            echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
                        }
                    }
                    echo '
										</select>
									</div>
								</div>
								<div style="display: inline-block; margin-right: 20px;">

									<div style="display: inline-block; margin-right: 20px;">
										<a href="?'.$who.'" class="dotyel" style="font-size: 80%;">Сбросить</a>
									</div>
								</div>
							</li>';

					//Календарик	
					echo '
	
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
                                <div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?'.$dopFilial.$dopWho.'&kab='.$kab.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
                                <div>
                                    <span style="color: rgb(125, 125, 125);">
                                        Изменить дату:
                                        <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
                                            onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
                                        <span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'zapis_solar.php?&kab='.$kab.$dopFilial.$dopWho.'\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
                                    </span>
                                </div>
                            </li>';
				}

                echo '<a href="solar_add.php?filial_id='.$_GET['filial_id'].'" class="ahref b">Добавить посещение</a>';
                //echo '<a href="solar_realiz_add.php" class="ahref b">Реализация</a>';

                $msql_cnnct = ConnectToDB ();

                $solar_j = array();

                //Получаем все по солярию за дату по филиалу
                $query = "SELECT js.*, jas.num FROM `journal_solar` js 
                LEFT JOIN `journal_abonement_solar` jas ON jas.id=js.abon_id
                WHERE js.filial_id = '{$_GET['filial_id']}' AND
                DAY(js.date_in) = '".dateTransformation ($day)."' AND MONTH(js.date_in) = '".dateTransformation ($month)."' AND YEAR(js.date_in) = '{$year}' ORDER BY js.date_in DESC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($solar_j, $arr);
                    }
                }
                //var_dump($query);
                //var_dump($solar_j);

                $realiz_j = array();

                //Получаем все по реализации средств для загара за дату по филиалу
                $query = "SELECT * FROM `journal_realiz` WHERE `filial_id` = '{$_GET['filial_id']}' AND 
                DAY(`date_in`) = '".dateTransformation ($day)."' AND MONTH(`date_in`) = '".dateTransformation ($month)."' AND YEAR(`date_in`) = '{$year}' ORDER BY `date_in` DESC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($realiz_j, $arr);
                    }
                }
                //var_dump($query);
                //var_dump($realiz_j);

                //Выводим посещения солярия
                if (!empty($solar_j)) {

                    $result = '';
                    $result_abon = '';
                    $deleted_orders = '';

                    echo '
                        <div class="" style="">
                            <ul style="margin-left: 6px; margin-bottom: 10px; font-size: 14px;">
                                <li style="font-size: 110%; margin-bottom: 5px;">
                                </li>';
                    echo '
                                <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250); ">';
                    echo '
                                    <div class="cellOrder" style="text-align: center; border-right: none;">
                                        <b>№</b>
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Тип оплаты</b>           
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Минуты</b>
                                     </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Сумма</b>
                                     </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Комментарий</b>
                                    </div>
                                    <div class="cellCosmAct" style="text-align: center;">
                                        <b>-</b>
                                    </div>';
                    echo '
                                </li>';

                    foreach ($solar_j as $item){
                        //var_dump($item);

                        //Если удалён, то меняем цвет на серый
                        if ( $item['status'] != 9){
                            $bgColor = '';
                            if ($item['summ_type'] == 3){
                                $bgColor = 'background-color: rgba(234, 232, 147, 0.19);';
                            }
                        }else{
                            $bgColor = 'background-color: rgba(199, 199, 199, 1);';
                        }

                        $result_temp = '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
                        $result_temp .= '
                                    <div class="cellOrder" style="position: relative; border-right: none; border-top: none;">
                                        <b>Посещение солярия #' . $item['id'] . '</b><br>от ' . date('d.m.y', strtotime($item['date_in'])) . '<br>
                                        <span style="font-size: 90%;  color: #555;">';

                        if (($item['create_time'] != 0) || ($item['create_person'] != 0)) {
                            $result_temp .= '
                                            Добавлен: ' . date('d.m.y H:i', strtotime($item['create_time'])) . '<br>
                                            Автор: ' . WriteSearchUser('spr_workers', $item['create_person'], 'user', true) . '<br>';
                        } else {
                            $result_temp .= 'Добавлен: не указано<br>';
                        }
                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            echo'
                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }*/
                        $result_temp .= '
                                        </span>
                                                        
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">';
                        if ($item['summ_type'] == 1) {
                            $result_temp .= 'Оплачено наличными';
                        }elseif ($item['summ_type'] == 2) {
                            $result_temp .= 'Оплачено безналом';
                        }elseif($item['summ_type'] == 3) {
                            $result_temp .= 'По абонементу<br><br>';

                            if ($item['abon_id'] > 0){
                                $result_temp .= '<a href="abonement.php?id='.$item['abon_id'].'" class="ahref button_tiny">Абонемент #'.$item['num'].'</a>';
                            }

                        }else{
                            $result_temp .= '<span style="color: red;">Ошибка #58!</span>';
                        }

                        $result_temp .= '                              
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateOrder" style="font-size: 13px">' . $item['min_count'] . '</span>
                                        </div>
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
                                        </div>
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="margin: 1px 0; padding: 1px 3px;">
                                            <span class="" style="font-size: 13px">' . $item['descr'] . '</span>
                                        </div>
                                    </div>';

                        //Удалить или восстановить
                        if ( $item['status'] != 9) {
                            $result_temp .= ' 
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_deleteSolar(' . $item['id'] . ');">
                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                    </div>';
                        }else {
                            $result_temp .= '
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenSolar('.$item['id'].');">
                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
                                    </div>';
                        }

                        $result_temp .= '
                                </li>';

                        //Если не удалённый
                        if ( $item['status'] != 9){
                            if($item['summ_type'] == 3){
                                $result_abon .= $result_temp;
                            } else {
                                $result .= $result_temp;
                            }
                        }else{
                            $deleted_orders .= $result_temp;
                        }

                    }
                    //Выводим
                    echo $result;
                    echo $result_abon;

                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo $deleted_orders;
                    }

                    echo '
                            </ul>
                        </div>';
                }else{
                    echo '<div style="color: red;">Не было посещения солярия</div>';
                }

                //Выводим реализацию
                if (!empty($realiz_j)) {

                    $result = '';
                    $deleted_orders = '';

                    echo '
                        <div class="" style="">
                            <ul style="margin-left: 6px; margin-bottom: 10px; font-size: 14px;">
                                <li style="font-size: 110%; margin-bottom: 5px;">
                                </li>';
                    echo '
                                <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250); ">';
                    echo '
                                    <div class="cellOrder" style="text-align: center; border-right: none;">
                                        <b>№</b>
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Тип оплаты</b>           
                                    </div>
                                    <div class="cellName" style="text-align: center; border-right: none;">
                                        <b>Сумма</b>
                                     </div>
                                    <div class="cellCosmAct" style="text-align: center;">
                                        <b>-</b>
                                    </div>';
                    echo '
                                </li>';

                    foreach ($realiz_j as $item){
                        //var_dump ($item);

                        //Если удалён, то меняем цвет на серый
                        if ( $item['status'] != 9){
                            $bgColor = '';
                            if ($item['summ_type'] == 3){
                                $bgColor = 'background-color: rgba(234, 232, 147, 0.19);';
                            }
                        }else{
                            $bgColor = 'background-color: rgba(199, 199, 199, 1);';
                        }

                        $result_temp = '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
                        $result_temp .= '
                                    <div class="cellOrder" style="position: relative; border-right: none; border-top: none;">
                                        <b>Реализация #' . $item['id'] . '</b><br>от ' . date('d.m.y', strtotime($item['date_in'])) . '<br>
                                        <span style="font-size: 90%;  color: #555;">';

                        if (($item['create_time'] != 0) || ($item['create_person'] != 0)) {
                            $result_temp .= '
                                            Добавлен: ' . date('d.m.y H:i', strtotime($item['create_time'])) . '<br>
                                            Автор: ' . WriteSearchUser('spr_workers', $item['create_person'], 'user', true) . '<br>';
                        } else {
                            $result_temp .= 'Добавлен: не указано<br>';
                        }
                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            echo'
                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }*/
                        $result_temp .= '
                                        </span>
                                                        
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">';
                        if ($item['summ_type'] == 1) {
                            $result_temp .= 'Оплачено наличными';
                        }elseif ($item['summ_type'] == 2) {
                            $result_temp .= 'Оплачено безналом';
                        }elseif($item['summ_type'] == 3) {
                            $result_temp .= 'По абонементу<br><br>';

//                            if ($item['abon_id'] > 0){
//                                $result_temp .= '<a href="abonement.php?id='.$item['abon_id'].'" class="ahref button_tiny">Абонемент #'.$item['abon_id'].'</a>';
//                            }

                        }else{
                            $result_temp .= '<span style="color: red;">Ошибка #77!</span>';
                        }

                        $result_temp .= '                              
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
                                        </div>
                                    </div>';

                        //Удалить или восстановить
                        if ( $item['status'] != 9) {
                            $result_temp .= ' 
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_deleteRealiz(' . $item['id'] . ');">
                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                    </div>';
                        }else {
                            $result_temp .= '
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenRealiz('.$item['id'].');">
                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
                                    </div>';
                        }

                        $result_temp .= '
                                </li>';

                        //Если не удалённый
                        if ( $item['status'] != 9){
                            $result .= $result_temp;
                        }else{
                            $deleted_orders .= $result_temp;
                        }

                    }
                    //Выводим
                    echo $result;

                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo $deleted_orders;
                    }

                    echo '
                            </ul>
                        </div>';
                }else{
                    echo '<div style="color: red;">Не было реализаций средств для загара</div>';
                }


            }else{
				echo '
					<div id="status">
						<header>';
				echo '			
				        </header>';
			}

			echo '
					</div>
				</div>';


					
			echo '	
						
					</div>
					<div id="doc_title">Солярий /'.$day.' ',$monthsName[$month],' ',$year,'/'.$filial[0]['name'].' - Асмедика</div>';
			echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';

            echo '
					<script>
					
						$(function() {
							$("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
							    var date_arr = $("#iWantThisDate2").val().split(".");
                         
                                var day = date_arr[0];
                                var month = date_arr[1];
                                var year = date_arr[2];
							    
								document.location.href = "?filial_id="+$(this).val()+  "&d="+day+"&m="+month+"&y="+year+"";
							});
						});
						
					</script>';

            //если есть права или бог или костыль (ассисенты ночью)
//            if (($zapis['add_new'] == 1) || $god_mode || (($_SESSION['permissions'] == 7) && (date("H", time()-60*60) > 16))){
//                echo '
//				<script src="js/zapis.js"></script>';
//
//            }


        }else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>