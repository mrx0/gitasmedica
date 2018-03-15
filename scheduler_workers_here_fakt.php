<?php 

//scheduler_workers_here_fakt.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			//var_dump($_POST);

            include_once 'DBWork.php';
			include_once 'functions.php';
			
			//получаем работников из базы
			$query = "SELECT `worker` FROM `scheduler` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}' AND `smena`='{$_POST['smena']}' AND `kab`='{$_POST['kab']}' AND `type`='{$_POST['type']}'";
			
			$shedWorkers = array();

            $msql_cnnct = ConnectToDB ();
			
			$arr = array();
			$rez = array();

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);
			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($shedWorkers, $arr);
				}
			}
			
			$rez = '';
			
			//var_dump ($shedWorkers);
						
			if (!empty($shedWorkers)){
				//var_dump ($shedWorkers);
				
				foreach ($shedWorkers as $value){
					//var_dump ($value);
					
					$rez .= WriteSearchUser('spr_workers', $value['worker'], 'user', false).' <a href="#" class="b" onclick="DeleteWorkersSmenaFakt('.$value['worker'].', '.$_POST['filial'].', '.$_POST['day'].', '.$_POST['month'].', '.$_POST['year'].', '.$_POST['smena'].', '.$_POST['kab'].', '.$_POST['type'].')">Удалить</a><br>';

				}
				echo $rez;
			}else{
				echo '<span style="color: red;">никого</span>';
			}
		}
	}
?>