<?php 

//scheduler_workers_here.php
//Выборка врачей, кто работает здесь и сейчас

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			//var_dump($_POST);
			
			include_once 'functions.php';
			
			//получаем шаблон графика из базы
			$query = "SELECT `worker` FROM `sheduler_template` WHERE `filial`='{$_POST['filial']}' AND `day`='{$_POST['dayW']}' AND `smena`='{$_POST['smenaN']}' AND `kab`='{$_POST['kabN']}' AND `type`='{$_POST['type']}'";
			
			$shedTemplate = array();

            $msql_cnnct = ConnectToDB ();
			
			$arr = array();
			$rez = array();

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);

			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($rez, $arr);
				}
				$shedTemplate = $rez;
			}
			
			$rez = '';
			
			//var_dump ($shedTemplate);
						
			if (!empty($shedTemplate)){
				//var_dump ($shedTemplate);
				
				foreach ($shedTemplate as $value){
					//var_dump ($value);
					
					$rez .= WriteSearchUser('spr_workers', $value['worker'], 'user', false).' <a href="#" class="b" onclick="DeleteWorkersSmena('.$value['worker'].', '.$_POST['filial'].', '.$_POST['dayW'].', '.$_POST['smenaN'].', '.$_POST['kabN'].', '.$_POST['type'].')">Удалить</a><br>';

				}
				echo $rez;
			}else{
				echo '<span style="color: red;">никого</span>';
			}
			
		}
	}
?>