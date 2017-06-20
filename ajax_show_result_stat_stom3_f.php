<?php 

//ajax_show_result_stat_stom3_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			$workerExist = false;
			$queryDopExist = false;
			$queryDopExExist = false;
			$queryDopClientExist = false;
			$query = '';
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';
			
			if ($_POST['worker'] != ''){
				include_once 'DBWork.php';
				$workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
				
				if ($workerSearch == 0){
					$workerExist = false;
				}else{
					$workerExist = true;
					$worker = $workerSearch[0]['id'];
				}
			}else{
				$workerExist = true;
				$worker = 0;
			}	
			
			if ($workerExist){
				$query .= "SELECT * FROM `journal_tooth_status`";
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				//$time = time();
				
				//Дата/время
				if ($_POST['all_time'] != 1){
					$queryDop .= "`create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
					$queryDopExist = true;
				}
				
				//!!! Тут возраст, пока не готово
				
				//Сотрудник
				if ($worker != 0){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`worker` = '".$worker."'";
					$queryDopExist = true;
				}
				
				//Филиал
				if ($_POST['filial'] != 99){
					if ($queryDopExist){
						$queryDop .= ' AND';
					}
					$queryDop .= "`office` = '".$_POST['filial']."'";
					$queryDopExist = true;
				}
				
				//Пол
				if ($_POST['sex'] != 0){
					if ($queryDopClientExist){
						$queryDopClient .= ' AND';
					}
					$queryDopClient .= "`sex` = '".$_POST['sex']."'";
					$queryDopClientExist = true;
					
					//Без пола
					if ($_POST['wo_sex'] == 1){
						if ($queryDopClientExist){
							$queryDopClient .= ' OR';
						}
						$queryDopClient .= "`sex` = '0'";
						$queryDopClient = "(".$queryDopClient.")";
						$queryDopClientExist = true;
					}
				}
				

				
				//Первичка
				if ($_POST['pervich'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['pervich'] == 1){
						$queryDopEx .= "`pervich` = '1'";
					}else{
						$queryDopEx .= "`pervich` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Страховые
				if ($_POST['insured'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['insured'] == 1){
						$queryDopEx .= "`insured` = '1'";
					}else{
						$queryDopEx .= "`insured` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				//Ночные
				if ($_POST['noch'] != 0){
					if ($queryDopExExist){
						$queryDopEx .= ' AND';
					}
					if ($_POST['noch'] == 1){
						$queryDopEx .= "`noch` = '1'";
					}else{
						$queryDopEx .= "`noch` <> '1'";
					}
					$queryDopExExist = true;
				}
				
				
				
				if ($queryDopExist){
					$query .= ' WHERE '.$queryDop;
					
					if ($queryDopExExist){
						$queryDopEx = "SELECT `id` FROM `journal_tooth_ex` WHERE ".$queryDopEx;
						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= "`id` IN (".$queryDopEx.")";
						$queryDopExist = true;
					}
					if ($queryDopClientExist){
						$queryDopClient = "SELECT `id` FROM `spr_clients` WHERE ".$queryDopClient;
						if ($queryDopExist){
							$query .= ' AND';
						}
						$query .= "`client` IN (".$queryDopClient.")";
					}
					
					$query = $query." ORDER BY `create_time` DESC";
					
					//var_dump($query);
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					$arr = array();
					$rez = array();
					
					$res = mysql_query($query) or die($query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($rez, $arr);
						}
						$journal = $rez;
					}else{
						$journal = 0;
					}
					//var_dump($journal);
					
					//Выводим результат
					if ($journal != 0){
						include_once 'functions.php';
						
						//Общее кол-во посещений
						$journal_count_orig = 0;
						//Массив с оригинальными пациентами
						$orig_clients = array();

						//
                        $count_journal_minus = 0;
                        $journal_count_orig_minus = 0;
                        $orig_clients_minus = 0;
						
						$actions_stomat = SelDataFromDB('actions_stomat', '', '');
						/*if (($stom['see_all'] == 1) || $god_mode){*/	
							$id4filter4worker = '';
						/*	$id4filter4upr = 'id="4filter"';
						}else{
							$id4filter4worker = 'id="4filter"';*/
							$id4filter4upr = '';
						/*}*/
						/*	echo '
								<p style="margin: 5px 0; padding: 1px; font-size:80%;">
									Быстрый поиск: 
									<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
									
								</p>';*/
						echo '
								<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
									<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>
										<div class="cellCosmAct" style="text-align: center">-</div>';
						//if (($stom['see_all'] == 1) || $god_mode){
							echo '<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';
						//}
						/*echo '
										<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Тип</div>';*/
						//отсортируем по nomer

						/*foreach($actions_stomat as $key=>$arr_temp){
							$data_nomer[$key] = $arr_temp['nomer'];
						}*/
						//array_multisort($data_nomer, SORT_NUMERIC, $actions_stomat);
						//return $rez;
						//var_dump ($actions_stomat);
						
						/*for ($i = 0; $i < count($actions_stomat)-2; $i++) { 
							if ($actions_stomat[$i]['active'] != 0){
								echo '<div class="cellCosmAct tooltip " style="text-align: center; background-color:#FEFEFE;" title="'.$actions_stomat[$i]['full_name'].'">'.$actions_stomat[$i]['name'].'</div>';
							}
						}*/
						echo '
										<div class="cellText" style="text-align: center">Комментарий</div>
									</li>';
						
						//!!!!!!тест санации Sanation ($journal);
						
						for ($i = 0; $i < count($journal); $i++) {
						    //var_dump($journal[$i]['create_time']);
							
							$orig_clients[$journal[$i]['client']] = true;
							
							//Если один приём, но не сколько осмотров.
							if (($i > 0) && ($journal[$i]['client'] == $journal[$i-1]['client'])){
								if ($journal[$i]['create_time'] - $journal[$i-1]['create_time'] < 60*60){
								}else{
									
								}
							}else {
                                $journal_count_orig++;

                                $rez_color = '';

                                $journal_ex_bool = FALSE;

                                /*if ((isset($filter_rez['pervich'])) && ($filter_rez['pervich'] == true)){
                                    $query = "SELECT * FROM `journal_tooth_ex` WHERE `pervich` = 1 AND `id` = '{$journal[$i]['id']}' ORDER BY `id` DESC";
                                    $res = mysql_query($query) or die($query);
                                    $number = mysql_num_rows($res);
                                    if ($number != 0){
                                        $journal_ex_bool = true;
                                    }else{
                                    }
                                }*/
                                //if ((isset($filter_rez['pervich']) && $journal_ex_bool) || (!isset($filter_rez['pervich']))){
                                //if (($journal[$i]['create_time'] >= $datestart)  && ($journal[$i]['create_time'] <= $datefinish)){
                                //Надо найти клиента
                                $clients = SelDataFromDB('spr_clients', $journal[$i]['client'], 'client_id');
                                //var_dump($clients);

                                if ($clients != 0) {
                                    $client = $clients[0]["name"];
                                    if (($clients[0]["birthday"] != -1577934000) || ($clients[0]["birthday"] == 0)) {
                                        $cl_age = getyeardiff($clients[0]["birthday"], $journal[$i]['create_time']);
                                    } else {
                                        $cl_age = 0;
                                    }
                                } else {
                                    $client = 'не указан';
                                    $cl_age = 0;
                                }
                                //var_dump($cl_age);
                                //var_dump($_POST['age']);

                                //Возраст
                                if (($_POST['age'] == 0) || (($_POST['age'] == 1) && (($cl_age <= 14) && (($cl_age > 0) || ($_POST['wo_age'] == 1)))) || (($_POST['age'] == 2) && (($cl_age > 14) || (($cl_age ==0) && ($_POST['wo_age'] == 1))))){

                                    //Дополнительно
                                    $dop = array();
                                    $dop_img = '';
                                    $query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$journal[$i]['id']}'";
                                    $res = mysql_query($query) or die($query);
                                    $number = mysql_num_rows($res);
                                    if ($number != 0) {
                                        while ($arr = mysql_fetch_assoc($res)) {
                                            array_push($dop, $arr);
                                        }

                                    }
                                    //var_dump ($dop);
                                    if (!empty($dop)) {
                                        if ($dop[0]['insured'] == 1) {
                                            $dop_img .= '<img src="img/insured.png" title="Страховое">';
                                        }
                                        if ($dop[0]['pervich'] == 1) {
                                            $dop_img .= '<img src="img/pervich.png" title="Первичное">';
                                        }
                                        if ($dop[0]['noch'] == 1) {
                                            $dop_img .= '<img src="img/night.png" title="Ночное">';
                                        }
                                    }


                                    echo '
                                                    <li class="cellsBlock cellsBlockHover">
                                                            <a href="task_stomat_inspection.php?id=' . $journal[$i]['id'] . '" class="cellName ahref" title="' . $journal[$i]['id'] . '">' . date('d.m.y H:i', $journal[$i]['create_time']) . ' ' . $dop_img . '</a>
                                                            <a href="client.php?id=' . $journal[$i]['client'] . '" class="cellName ahref" ' . $id4filter4worker . '>' . $client . '</a>';


                                    ///if (Sanation2($journal[$i]['id'], $journal[$i], $cl_age)){


                                    //ЗО и тд
                                    $dop = array();
                                    $query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$journal[$i]['id']}'";
                                    $res = mysql_query($query) or die($query);
                                    $number = mysql_num_rows($res);
                                    if ($number != 0) {
                                        while ($arr = mysql_fetch_assoc($res)) {
                                            array_push($dop, $arr);
                                        }

                                    }

                                    include_once 'tooth_status.php';
                                    include_once 't_surface_name.php';
                                    include_once 't_surface_status.php';


                                    $arr = array();
                                    $decription = $journal[$i];

                                    //var_dump($decription);

                                    unset($decription['id']);
                                    unset($decription['office']);
                                    unset($decription['client']);
                                    unset($decription['create_time']);
                                    unset($decription['create_person']);
                                    unset($decription['last_edit_time']);
                                    unset($decription['last_edit_person']);
                                    unset($decription['worker']);

                                    unset($decription['comment']);
                                    unset($decription['zapis_date']);
                                    unset($decription['zapis_id']);

                                    $t_f_data = array();

                                    //собрали массив с зубами и статусами по поверхностям
                                    foreach ($decription as $key => $value) {
                                        $surfaces_temp = explode(',', $value);
                                        //var_dump($surfaces_temp);
                                        foreach ($surfaces_temp as $key1 => $value1) {
                                            ///!!!Еба костыль
                                            if ($key1 < 13) {
                                                $t_f_data[$key][$surfaces[$key1]] = $value1;
                                                //var_dump($t_f_data[$key][$surfaces[$key1]]);
                                            }
                                        }
                                    }
                                    //var_dump ($t_f_data);
                                    if (!empty($dop[0])) {
                                        //var_dump($dop[0]);
                                        unset($dop[0]['id']);
                                        //var_dump($dop[0]);
                                        foreach ($dop[0] as $key => $value) {
                                            //var_dump($value);
                                            if ($value != '0') {
                                                //var_dump($value);
                                                $dop_arr = json_decode($value, true);
                                                //var_dump($dop_arr);
                                                foreach ($dop_arr as $n_key => $n_value) {
                                                    if ($n_key == 'zo') {
                                                        $t_f_data[$key]['zo'] = $n_value;
                                                        //$t_f_data_draw[$key]['zo'] = $n_value;
                                                    }
                                                    if ($n_key == 'shinir') {
                                                        $t_f_data[$key]['shinir'] = $n_value;
                                                        //$t_f_data_draw[$key]['shinir'] = $n_value;
                                                    }
                                                    if ($n_key == 'podvizh') {
                                                        $t_f_data[$key]['podvizh'] = $n_value;
                                                        //$t_f_data_draw[$key]['podvizh'] = $n_value;
                                                    }
                                                    if ($n_key == 'retein') {
                                                        $t_f_data[$key]['retein'] = $n_value;
                                                        //$t_f_data_draw[$key]['retein'] = $n_value;
                                                    }
                                                    if ($n_key == 'skomplect') {
                                                        $t_f_data[$key]['skomplect'] = $n_value;
                                                        //$t_f_data_draw[$key]['skomplect'] = $n_value;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    //var_dump ($t_f_data);


                                    if (Sanation2($journal[$i]['id'], $t_f_data, $cl_age)) {
                                        $rez_color = "style= 'background: rgba(87,223,63,0.7);'";
                                    } else {
                                        $rez_color = "style= 'background: rgba(255,39,119,0.7);'";
                                    }
                                    echo '
                                                            <div class="cellCosmAct" ' . $rez_color . '>
                                                                <a href="#" onclick="window.open(\'task_stomat_inspection_window.php?id=' . $journal[$i]['id'] . '\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;">
                                                                    <img src="img/tooth_state/1.png">
                                                                </a>	
                                                            </div>';

                                    //if (($stom['see_all'] == 1) || $god_mode){
                                    echo '<div class="cellName" ' . $id4filter4upr . '>' . WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user', true) . '</div>';
                                    //}

                                    /*echo '
                                            <div class="cellName">!!!ТИП</div>';*/

                                    $decription = array();
                                    $decription_temp_arr = array();
                                    $decription_temp = '';

                                    /*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
                                    /*foreach($journal[$i] as $key => $value){
                                        if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
                                            $decription_temp_arr[mb_substr($key, 1)] = $value;
                                        }
                                    }*/

                                    //var_dump ($decription_temp_arr);

                                    $decription = $decription_temp_arr;

                                    //array_multisort($data_nomer, SORT_NUMERIC, $decription);

                                    //var_dump ($decription);
                                    //var_dump ($actions_stomat);

                                    //for ($j = 1; $j <= count($actions_stomat)-2; $j++) {
                                    /*foreach ($actions_stomat as $key => $value) {
                                        $cell_color = '#FFFFFF';
                                        $action = '';
                                        if ($value['active'] != 0){
                                            if (isset($decription[$value['id']])){
                                                if ($decription[$value['id']] != 0){
                                                    $cell_color = $value['color'];
                                                    $action = 'V';
                                                }
                                                echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'</div>';
                                            }else{
                                                echo '<div class="cellCosmAct" style="text-align: center"></div>';
                                            }
                                        }
                                    }*/

                                    echo '
                                                            <div class="cellText">' . $journal[$i]['comment'] . '</div>
                                                    </li>';
                                    //}
                                }else{
                                    $count_journal_minus++;
                                    $journal_count_orig_minus++;
                                    $orig_clients_minus++;
                                }
							}
						}
						
						echo '
							<li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
								Всего<br>
								Осмотров отмечено: '.(count($journal)-$count_journal_minus).'<br>
								Посещений: '.($journal_count_orig-$journal_count_orig_minus).'<br>
								Пациентов за период: '.(count($orig_clients)-$orig_clients_minus).'<br>
							</li>';
						
						echo '
								</ul>
							</div>';
					}else{
						echo '<span style="color: red;">Ничего не найдено</span>';
					}					
					
				}else{
					echo '<span style="color: red;">Ожидается слишком большой результат выборки. Уточните запрос.</span>';
				}
				
				//var_dump($query);
				//var_dump($queryDopEx);
				//var_dump($queryDopClient);
				
				mysql_close();
			}else{
				echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
			}
		}
	}
?>