<?php 

//ajax_show_result_stat_add_clients.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            include_once 'DBWork.php';

			$workerExist = false;
			$queryDopExist = false;
			$queryDopExExist = false;
			$queryDopClientExist = false;
			$query = '';
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';
			
			if ($_POST['worker'] != ''){

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
				$query .= "SELECT * FROM `spr_clients`";

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
					$queryDop .= "`create_person` = '".$worker."'";
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
				/*if ($_POST['sex'] != 0){
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
				
				*/
				
				if ($queryDopExist){
					$query .= ' WHERE '.$queryDop;
					
					/*if ($queryDopExExist){
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
					}*/
					
					$query = $query." ORDER BY `create_time` DESC";

                    $msql_cnnct = ConnectToDB ();
					
					$arr = array();
                    $journal = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							array_push($journal, $arr);
						}
					}
					//var_dump($journal);
					
					//Выводим результат
					if (!empty($journal)){
						include_once 'functions.php';
						
						//Общее кол-во посещений
						$journal_count_orig = 0;
						
						echo '
							<div id="data">
								<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
									<li class="cellsBlock" style="font-weight:bold;">
										<div class="cellTime" style="text-align: center">Добавлен</div>
										<div class="cellName" style="text-align: center">Кем</div>
										<div class="cellFullName" style="text-align: center">Полное имя</div>
										<div class="cellCosmAct" style="text-align: center">Пол</div>
										<div class="cellTime" style="text-align: center">Дата рождения</div>
										<div class="cellText" style="text-align: center">Комментарий</div>
									</li>';

						for ($i = 0; $i < count($journal); $i++) { 
						//var_dump($_SESSION['id']);
						//	if (isset($_GET['own_clients']) && ($_GET['own_clients'] == 'yes') && ($_SESSION['id'] == $journal[$i]['therapist'])){
						//		var_dump('мой');
						//	}
							echo '
									<li class="cellsBlock cellsBlockHover">
										<div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</div>
										<div class="cellName 4filter" style="text-align: center" id="4filter">'.WriteSearchUser('spr_workers', $journal[$i]['create_person'], 'user', true).'</div>
										<a href="client.php?id='.$journal[$i]['id'].'" class="cellFullName ahref">'.$journal[$i]['full_name'].'</a>';

							echo '
										<div class="cellCosmAct" style="text-align: center">';
							if ($journal[$i]['sex'] != 0){
								if ($journal[$i]['sex'] == 1){
									echo 'М';
								}
								if ($journal[$i]['sex'] == 2){
									echo 'Ж';
								}
							}else{
								echo '-';
							}
							
							echo '
										</div>';

							echo '
										<div class="cellTime" style="text-align: center">';
                if ($journal[$i]['birthday2'] == '0000-00-00'){
					echo 'не указана';
				}else{
					echo
                        date('d.m.Y', strtotime($journal[$i]['birthday2'])).'';

				}
				echo '
										</div>
										<div class="cellText">'.$journal[$i]['comment'].'</div>
									</li>';
						}
						
						echo '
							<li class="cellsBlock" style="margin-top: 20px; border: 1px dotted green; width: 300px; font-weight: bold; background-color: rgba(129, 246, 129, 0.5); padding: 5px;">
								Всего<br>
								Пациентов добавлено: '.count($journal).'<br>
								<!--Посещений: '.$journal_count_orig.'<br>
								Пациентов за период: <br>-->
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

                CloseDB ($msql_cnnct);

			}else{
				echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
			}
		}
	}
?>