<?php 

//cosmet_journal_f.php
//Функция для 

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){
			$ttime = explode('_', $_POST['tab_index']);
			//var_dump ($ttime);
			
			$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
			
				echo '
					<p style="margin: 5px 0; padding: 1px; font-size:80%;">
						<!--Быстрый поиск по врачу: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
						-->
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">
								<div class="cellName" style="text-align: center">Дата</div>
								<div class="cellName" style="text-align: center">Пациент</div>';
				if (($cosm['see_all'] == 1) || $god_mode){
					echo '<div class="cellName" style="text-align: center">Врач</div>';
				}

				//отсортируем по nomer

				foreach($actions_cosmet as $key=>$arr_temp){
					$data_nomer[$key] = $arr_temp['nomer'];
				}
				array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
				//return $rez;
				//var_dump ($actions_cosmet);
				
				for ($i = 0; $i < count($actions_cosmet)-2; $i++) { 
					if ($actions_cosmet[$i]['active'] != 0){
						echo '<div class="cellCosmAct tooltip " style="text-align: center" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
					}
				}
				echo '
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
	
				for ($i = 0; $i < count($journal); $i++) {
					
					if (($journal[$i]['create_time'] >= $datestart)  && ($journal[$i]['create_time'] <= $datefinish)){
						//Надо найти имя клиента
						$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
						}else{
							$client = 'unknown';
						}
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_cosmet.php?id='.$journal[$i]['id'].'" class="cellName ahref">'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref">'.$client.'</a>';
						if (($cosm['see_all'] == 1) || $god_mode){
							echo '<a href="user.php?id='.$journal[$i]['worker'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user').'</a>';
						}		
						
						$decription = array();
						$decription = json_decode($journal[$i]['description'], true);

						//array_multisort($data_nomer, SORT_NUMERIC, $decription);
						
						//var_dump ($decription);		
						//var_dump ($actions_cosmet);		
						
						//for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						foreach ($actions_cosmet as $key => $value) { 
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
						}
						
						echo '
									<div class="cellText">'.$journal[$i]['comment'].'</div>
							</li>';
					}
				}
				echo '
						</ul>
					</div>';
			
			
			
		}

	}
	
?>