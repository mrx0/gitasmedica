<?php 

//
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		$task_face = '';
		$task_graf = '';
		
		if ($_POST){
			if (($_POST['face'] == '')||($_POST['client'] == '')||($_POST['client'] == 0)){
				echo 'Ошибка попробуйте еще.<br /><br />
					<a href="add_kd.php?client='.$_POST['client'].'" class="b">Вернуться</a>
					<a href="client.php?id='.$_POST['client'].'" class="b">Вернуться в карточку</a>';
			}else{
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "INSERT INTO `spr_kd_img` (
					`client`, `face_graf`, `uptime`) 
				VALUES (
					'{$_POST['client']}', '1', '{$time}'
				)";
				
				mysql_query($query) or die(mysql_error());
				
				$task_face = mysql_insert_id();
								
				$extension = pathinfo('uploads/'.$_POST['face'], PATHINFO_EXTENSION);
				
				rename('uploads/'.$_POST['face'], 'kd/'.$task_face.'.'.$extension);
				
				if ($_POST['graf'] != ''){
					$query = "INSERT INTO `spr_kd_img` (
						`client`, `face_graf`, `uptime`) 
					VALUES (
						'{$_POST['client']}', '2', '{$time}'
					)";
					mysql_query($query) or die(mysql_error());
					
					$task_graf = mysql_insert_id();
					
					$extension = pathinfo('uploads/'.$_POST['graf'], PATHINFO_EXTENSION);
					
					rename('uploads/'.$_POST['graf'], 'kd/'.$task_graf.'.'.$extension);
				}
				
				/*echo $task_face.'<br />';
				if ($task_graf != '') 
					echo $task_face;*/
				
				
				mysql_close();
				
					echo '
						Изображения добавлены<br /><br />
						<a href="client.php?id='.$_POST['client'].'" class="b">Вернуться в карточку</a>
						<a href="kd.php?client='.$_POST['client'].'" class="b">Посмотреть КД</a>
						<a href="add_kd.php?client='.$_POST['client'].'" class="b">Добавить этому пациенту КД</a>';
					
			}
		}
	}
?>