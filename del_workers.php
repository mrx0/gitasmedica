<?php 

//ajax_tempzapis_edit_enter_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		
		$rez = '';
		
		//var_dump ($_POST);
		
		if ($_POST){
			if ($_POST['datatable'] == 'scheduler_stom'){
				$datatable = 'scheduler_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$datatable = 'scheduler_cosm';
			}else{
				$datatable = 'scheduler_stom';
			}
			$FilialKabSmenaWorker = FilialKabSmenaWorker($datatable, $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab']);
			
			if ($FilialKabSmenaWorker != 0){
				//var_dump ($FilialKabSmenaWorker);
				
				foreach ($FilialKabSmenaWorker as $key => $value){
					//var_dump ($value);
					
					if ($value['smena'] == 1){
						$rez .= 'Смена 1 в графике: '.WriteSearchUser('spr_workers', $value['worker'], 'user').' <a href="#" class="b" onclick=alert(\'Скоро\')>Удалить</a><br />';
					}elseif ($value['smena'] == 2){
						$rez .= 'Смена 2 в графике: '.WriteSearchUser('spr_workers', $value['worker'], 'user').' <a href="#" class="b" onclick=alert(\'Скоро\')>Удалить</a><br />';
					}elseif ($value['smena'] == 9){
						$rez .= 'Смены 1+2 в графике: '.WriteSearchUser('spr_workers', $value['worker'], 'user').' <a href="#" class="b" onclick=alert(\'Скоро\')>Удалить</a><br />';
					}
				}
				echo $rez;
			}else{
				echo 'В графике никого нет';
			}
			
		}
	}
?>