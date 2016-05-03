<?php

//cosmet.php
//Косметология

	require_once 'header.php';
	//var_dump ($enter_ok);
	//var_dump ($god_mode);
	
	if ($enter_ok){
		//var_dump($permissions);
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Косметология статистика</h1>';
					
			//$user = SelDataFromDB('spr_workers', $_SESSION['id'], 'user');
			//var_dump ($user);
			//echo 'Польз: '.$user[0]['name'].'<br />';
			
			if (($cosm['add_own'] == 1) || $god_mode){
				echo '
						<a href="add_task_cosmet.php" class="b">Добавить</a>';
			}
			if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
				echo '
						<a href="cosmet.php" class="b">В журнал</a>';
			}

			echo '
				</header>';
				
			$journal = 0;
			
			if (($cosm['see_own'] == 1) || $god_mode){
				$journal = SelDataFromDB('journal_cosmet1', $_SESSION['id'], 'worker_cosm_id');
			}
			if (($cosm['see_all'] == 1) || $god_mode){
				$journal = SelDataFromDB('journal_cosmet1', '', '');
			}	
			
		/*	echo '
				<a href="#" onclick="switchDisplay(\'filter\')" class="ahref_sort">Фильтр (пока не работает)</a>';*/

			
			if ($journal != 0){
				$actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
				//var_dump ($actions_cosmet);
				
				//Вот тут полный пиздец...  Заебался выделываться с этой хуйнёй
				//Выборка оригинальных пациентов с общим количеством процедур
				
				$client = array();
				$description = array();


				for ($i=0; $i<count($journal); $i++){
					$client[$i] = $journal[$i]['client'];
					$description[$i] = $journal[$i]['description'];
				}

				//var_dump($client);
				//var_dump($description);

				$temp_arr = array();

				for ($i=0; $i<count($client); $i++){
					if (!array_key_exists($client[$i], $temp_arr)){
						//echo '<br />id: '.$client[$i].'<br />';
						for ($j=$i+1; $j<count($client); $j++){
							//echo '<br />i='.$i.'; j='.$j.'<br />';
							if ($client[$i] == $client[$j]){
								//echo '<b>+OK</b>.<br />';
								//echo $description[$i].'<br />';
								//echo $description[$j].'<br />';
								//var_dump (json_decode($description[$i], true));
								$description[$i] = json_encode(ArraySum(json_decode($description[$i], true), json_decode($description[$j], true)));
								//array_splice($journal , $i, 1);
								//echo $description[$i].'<br />';
							}//else{
								//echo '<b>-NOT</b>.<br />';
							//}
						}
						//echo '<br />id: '.$client[$i].'<br />';
						if (!array_key_exists($client[$i], $temp_arr)){
							$temp_arr[$client[$i]] = $description[$i];
						}else{
							$temp_arr[$client[$i]] .= $description[$i];
						}
						//echo '<br /><br />temp_arr:<br />';
						//var_dump($temp_arr);
						//echo '<br />///********************////***********************///<br />';
					}
				}
				//var_dump($temp_arr);
				//Кончился пиздец
				
				echo '
					<p style="margin: 5px 0; padding: 2px;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">
								<!--<div class="cellName" style="text-align: center">Дата</div>-->
								<div class="cellName" style="text-align: center">Пациент</div>';
				for ($i = 0; $i < count($actions_cosmet)-2; $i++) { 
					if ($actions_cosmet[$i]['active'] != 0){
						echo '<div class="cellCosmAct" style="text-align: center" title="'.$actions_cosmet[$i]['full_name'].'">'.$actions_cosmet[$i]['name'].'</div>';
					}
				}
				echo '
								<!--<div class="cellText" style="text-align: center">Комментарий</div>-->
							</li>';
				foreach ($temp_arr as $key => $value) {
					//Надо найти имя клиента
					$clients = SelDataFromDB ('spr_clients', $key, 'client_id');
					if ($clients != 0){
						$client = $clients[0]["name"];
					}else{
						$client = 'unknown';
					}
					echo '
						<li class="cellsBlock cellsBlockHover">
								<a href="client.php?id='.$key.'" class="cellName ahref" id="4filter">'.$client.'</a>';
								
					$decription = array();
					$decription = json_decode($value, true);
					
					//var_dump ($decription);		
					
					for ($j = 1; $j <= count($actions_cosmet)-2; $j++) { 
						$cell_color = '#FFFFFF';
						$action = '';
						if (isset($decription[$j])){
							if ($decription[$j] != 0){
								$cell_color = $actions_cosmet[$j-1]['color'];
								$action = 'V'.$decription[$j];
							}
							echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'</div>';
						}else{
							echo '<div class="cellCosmAct" style="text-align: center"></div>';
						}
					}
					
					echo '
						</li>';
				}
				echo '
						</ul>
					</div>';
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>