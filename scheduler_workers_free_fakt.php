<?php 

//scheduler_workers_free.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			//var_dump($_POST);
			
			include_once 'functions.php';
			
			//получаем тех из базы, кто не в графике в эту смену, в этот день
			//!!!Выбираем врачей (не уволенные)
			//$query = "SELECT `worker` FROM `sheduler_template` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `kab`='{$_POST['kabN']}' AND `type`='{$_POST['type']}'";
			$query = "SELECT * FROM `spr_workers` WHERE `permissions` = '{$_POST['type']}' AND `fired` <> '1' AND `id`
			NOT IN (SELECT `worker` FROM `scheduler` WHERE `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}' AND `smena`='{$_POST['smena']}' AND `type`='{$_POST['type']}')
			ORDER BY `full_name` ASC";
			
			$workers = array();

            $msql_cnnct = ConnectToDB ();

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($workers, $arr);
				}
			}else{
				$workers = 0;
			}

			
			//получаем тех из базы, кто в графике сегодня в эту смену
			//$query = "SELECT `worker` FROM `sheduler_template` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `kab`='{$_POST['kabN']}' AND `type`='{$_POST['type']}'";
			$query = "SELECT * FROM `scheduler` WHERE `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}' AND `smena`='{$_POST['smena']}' AND `type`='{$_POST['type']}'";
			
			$works_today = array();

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($works_today, $arr);
				}
			}else{
				$works_today = 0;
			}

            CloseDB ($msql_cnnct);
				
			//var_dump($workers);

			if ($workers !=0){
				foreach($workers as $value){
					echo '
						<div class="cellsBlock2" style="width:320px; font-size:80%;">
							<div class="cellRight">
								<input type="radio" name="worker" value="'.$value['id'].'"> '.$value['name'].'
							</div>
						</div>';
				}
			}
			if (!empty($works_today) && $works_today != 0){
				//var_dump($works_today);
				echo '
					<div style="font-size:80%; background-color: #FF49E9; padding: 2px;">
						<a href="#open1" onclick="show(\'hidden_1\',200,5)" class="ahref">
							Уже в графике развернуть [+] 
						</a>
					</div>';	
				echo '
					<div id="hidden_1" style="display:none; border: 1px solid red;">';	
				foreach($works_today as $value){
					$filial = SelDataFromDB('spr_filials', $value['filial'], 'offices');
					//var_dump($filial);
					echo '
						<div class="cellsBlock2" style="width:320px; font-size:80%;">
							<div class="cellRight" style="background-color: rgba(255,83,75,.5);">
								<input type="radio" name="worker" value="'.$value['worker'].'"> '.WriteSearchUser('spr_workers', $value['worker'], 'user', false).'<br />
								<span style="font-size:80%;">Филиал '.$filial[0]['name'].'; кабинет '.$value['kab'].'; смена'.$value['smena'].'</span>
							</div>
						</div>';
				}
				echo '
					</div>';
			}
		}
	}
	
?>