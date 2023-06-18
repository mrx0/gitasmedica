<?php

//zapis_full2.php
//Вся запись на день отдельно для частных лиц

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($stom);
		
		if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			$offices = $offices_j = SelDataFromDB('spr_filials', '', '');
			//var_dump ($offices);

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

            if (!isset($_GET['filial'])){
                //Филиал
                if (isset($_SESSION['filial'])){
                    $_GET['filial'] = $_SESSION['filial'];
                }else{
                    $_GET['filial'] = 16;
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
					if ($key == 'filial')
						$dopFilial .= '&'.$key.'='.$value;
					if ($key == 'who')
						$dopWho .= '&'.$key.'='.$value;
				}

				$filial = SelDataFromDB('spr_filials', $_GET['filial'], 'offices');
				//var_dump($filial['name']);
				
				$kabsInFilial_arr = SelDataFromDB('spr_kabs', $_GET['filial'], 'office_kabs');
				if ($kabsInFilial_arr != 0){
					$kabsInFilial_json = $kabsInFilial_arr[0][$kabsForDoctor];
					//var_dump($kabsInFilial_json);
					
					if ($kabsInFilial_json` != NULL`){
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
									<a href="zapis.php?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'" class="b">Запись</a>
									<a href="scheduler.php?filial='.$_GET['filial'].''.$who.'" class="b">График</a>
									<a href="scheduler_own.php?id='.$_SESSION['id'].'" class="b">Мой график</a>
								</div>
							
								<h2>Запись '.$day.' ',$monthsName[$month],' ',$year,' <small>(подробное описание)</small></h2>
								<b>Филиал</b> '.$filial[0]['name'].'<br>
								<!--<b>Кабинет '.$kab.'</b><br>-->
								<span style="color: green; font-size: 120%; font-weight: bold;">Без записи</span><br>
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
	
					/*echo '
							<div id="data">';*/
							
					echo '		
							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=5&kab=1" class="b" style="'.$stom_color.'">Стоматологи</a>
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=6&kab=1" class="b" style="'.$cosm_color.'">Косметологи</a>
								<a href="zapis_full.php?'.$dopFilial.$dopDate.'&who=10&kab=1" class="b" style="'.$somat_color.'">Специалисты</a>
								<a href="zapis_solar.php" class="b" style="">Солярий</a>
								<a href="zapis_full2.php" class="b" style="background-color: #fff261;">Без записи</a>
							</li>
							<li class="cellsBlock" style="width: auto; margin-bottom: 20px;">
								<div style="display: inline-block; margin-right: 20px;">
									<div style="font-size: 110%; color: #7D7D7D; margin-bottom: 5px;">
										Выберите филиал
									</div>
									<div>
										<select name="SelectFilial" id="SelectFilial">
											';
				if ($offices_j != 0){
					for ($i=0;$i<count($offices_j);$i++){
						$selected = '';
						if (isset($_GET['filial'])){
							if ($offices_j[$i]['id'] == $_GET['filial']){
								$selected = 'selected';
							}
						}
						echo "<option value='".$offices_j[$i]['id']."' $selected>".$offices_j[$i]['name']."</option>";
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

							
					$ZapisHereQueryToday = FilialKabSmenaZapisToday($datatable, $year, $month, $day, $_GET['filial'], 0, 0, 6);
					//var_dump($ZapisHereQueryToday);
					
					
					//Календарик	
					echo '
	
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: left; margin-bottom: 10px;">
									<div style="font-size: 90%; color: rgb(125, 125, 125);">Сегодня: <a href="?'.$dopFilial.$dopWho.'&kab='.$kab.'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></div>
									<div>
										<span style="color: rgb(125, 125, 125);">
											Изменить дату:
											<input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off"> 
											<span class="button_tiny" style="font-size: 100%; cursor: pointer" onclick="iWantThisDate2(\'zapis_full2.php?&kab='.$kab.$dopFilial.$dopWho.'\')"><i class="fa fa-check-square" style=" color: green;"></i> Перейти</span>
										</span>
									</div>
								</li>';
					
					
					
					$kabsInFilial_arr = SelDataFromDB('spr_kabs', $_GET['filial'], 'office_kabs');
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
					if ($kabsInFilialExist){

                        $Work_Today_arr = array();

                        $Work_Today = FilialWorker	($type, $year, $month, $day, $_GET['filial']);
                        if ($Work_Today != 0){
                            //var_dump($Work_Today);

                            foreach($Work_Today as $Work_Today_value){
                                //var_dump($Work_Today_value);
                                //!!!Бля такой тут пиздец с этой 9ой сменой....
                                //а, сука, потому что сразу надо было головой думать
                                if ($Work_Today_value['smena'] == 9){
                                    $Work_Today_arr[$Work_Today_value['kab']][1] = $Work_Today_value;
                                    $Work_Today_arr[$Work_Today_value['kab']][2] = $Work_Today_value;
                                    //2018-10-17 захотел добавить сюда третью (ночную) смену, но передкмал тут же)
                                    //$Work_Today_arr[$Work_Today_value['kab']][3] = $Work_Today_value;
                                }else{
                                    $Work_Today_arr[$Work_Today_value['kab']][$Work_Today_value['smena']] = $Work_Today_value;
                                }
                            }
                        }else{
                            //никто не работает тут сегодня
                        }
                        //var_dump($Work_Today_arr);

//						echo '
//							<span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите кабинет</span><br>
//							<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">';

//						for ($k = 1; $k <= count($kabsInFilial); $k++){
//							$kab_color = '';
//							if ($k == $kab){
//								$kab_color = ' background-color: #fff261;';
//							}
//
//                            if (isset($Work_Today_arr[$k][1])){
//                                //var_dump($Kab_work_today_smena1);
//                                $worker = '<i>1 см: '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][1]['worker'], 'user', false).'</i><br>';
//                            }else{
//                                $worker = '1 см: <span style="font-weight: normal;">никого</span><br>';
//                            }
//                            if (isset($Work_Today_arr[$k][2])){
//                                //var_dump($Kab_work_today_smena1);
//                                $worker .= '<i>2 см: '.WriteSearchUser('spr_workers', $Work_Today_arr[$k][2]['worker'], 'user', false).'</i>';
//                            }else{
//                                $worker .= '2 см: <span style="font-weight: normal;">никого</span>';
//                            }
//
//							echo '
//								<a href="?filial='.$_GET['filial'].''.$who.'&d='.$day.'&m='.$month.'&y='.$year.'&kab='.$k.'" class="b" style="'.$kab_color.' text-align: center;"><span style="font-size: 120%">каб '.$k.'</span><br>'.$worker.'</a>';
//						}
//						echo '</li>';
					}
					
					if ($ZapisHereQueryToday != 0){

                        // !!! **** тест с записью
                        include_once 'showZapisRezult.php';

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

                        echo showZapisRezult($ZapisHereQueryToday, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $type, true, true);
					}else{
						echo 'Нет записи<br>';
					}

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
					<div id="doc_title">Без записи /'.$day.' ',$monthsName[$month],' ',$year,'/'.$filial[0]['name'].' - Асмедика</div>';
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
							    
								document.location.href = "?filial="+$(this).val()+  "&d="+day+"&m="+month+"&y="+year+"";
							});
						});
						
					</script>';

            //если есть права или бог или костыль (ассисенты ночью)
            if (($zapis['add_new'] == 1) || $god_mode || (($_SESSION['permissions'] == 7) && (date("H", time()-60*60) > 16))){
                echo '
				<script src="js/zapis.js"></script>';

            }


        }else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>