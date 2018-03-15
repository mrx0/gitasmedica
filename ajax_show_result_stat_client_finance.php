<?php 

//ajax_show_result_stat_client_finance.php
//Функция для Не погашенные авансы/долги (старое)

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
			
			$query .= "SELECT * FROM `journal_debts_prepayments` WHERE  `type`='4' OR `type`='3'";

            $msql_cnnct = ConnectToDB ();
			//$time = time();
			
			//Дата/время
			if ($_POST['all_time'] != 1){
				$queryDop .= " `create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
				$queryDopExist = true;
			}
				
			if ($queryDopExist){
					$query .= ' AND '.$queryDop;

			}
			
			$query = $query." ORDER BY `create_time` DESC";
			
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
				
				echo '
					<div id="data">';
				
						for ($i=0; $i<count($journal); $i++){
							$allpayed = false;
							$descr = '';
							$descrPayed = '';
							$url = 'finance_dp.php';
							
							if ($journal[$i]['type'] == 3){
								$descr = '<span style="color: green;">Аванс</span>';
								//$url = 'finance_prepayment.php';
							}
							if ($journal[$i]['type'] == 4){
								$descr = '<span style="color: red">Долг</span>';
								//$url = 'finance_debt.php';
							}
							
							$repayments = Repayments($journal[$i]['id']);
							if ($repayments != 0){
								$ostatok = 0;
								$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto;">
										Погашения
									</li>';
								foreach($repayments as $value){
									$ostatok += $value['summ'];
									$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto; background-color: rgba(255, 255, 0, 0.3);">	
										<a href="finance_dp_repayment.php?id='.$value['id'].'" class="cellTime ahref" style="text-align: center; border: 0;">'.date('d.m.y H:i', $value['create_time']).'</a>
										<div class="cellName" style="text-align: right; border: 0;">'.$value['summ'].' руб.</div>
										<div class="cellText" style="text-align: right; border: 0; max-width: 250px;">'.$value['comment'].'</div>
									</li>';
								}
								$descrPayed .= '
									<li class="cellsBlock" style="font-weight:bold; width: auto; margin-bottom: 10px;  margin-top: 2px; background-color: rgba(6, 255, 0, 0.3);">
										<div class="cellText" style="text-align: left; max-width: 250px;">
											Остаток: '.($journal[$i]['summ'] - $ostatok).' руб. ';
								if ($journal[$i]['summ'] - $ostatok == 0){
									$descrPayed .= '- <i>ЗАКРЫТО</i>';
									$allpayed = true;
								}
								$descrPayed .= '
										</div>
									</li>';
							}
							
							$bgColor = '';
							if ($journal[$i]['date_expires'] - time() <= 60*60*24*3){
								$bgColor = 'background-color: rgba(254, 63, 63, 0.69);';
							}
							
							if (!$allpayed){
								echo '
									<li class="cellsBlock" style="font-weight:bold; width: auto;">	
										<div class="cellFullName" style="text-align: center">'.WriteSearchUser('spr_clients', $journal[$i]['client'], 'user', true).'</div>
										<a href="'.$url.'?id='.$journal[$i]['id'].'" class="cellTime ahref" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
										<div class="cellTime" style="text-align: center">'.$descr.'</div>
										<div class="cellName" style="text-align: right;">'.$journal[$i]['summ'].' руб.</div>
										<div class="cellName" style="text-align: right; '.$bgColor.'">до '.date('d.m.y', $journal[$i]['date_expires']).'</div>
										<div class="cellText" style="text-align: right; max-width: 250px;">'.$journal[$i]['comment'].'</div>
									</li>';
									
								//echo $descrPayed;
							}
						}
						
						echo '
								</ul>
							</div>';
					}else{
						echo '<span style="color: red;">Ничего не найдено</span>';
					}				

				//mysql_close();
		}
	}
?>