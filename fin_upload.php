<?php 

//fin_upload.php
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
				
                $msql_cnnct = ConnectToDB ();

				$time = time();
				$query = "INSERT INTO `spr_kd_img` (
					`client`, `face_graf`, `uptime`) 
				VALUES (
					'{$_POST['client']}', '1', '{$time}'
				)";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                //ID новой позиции
				$task_face = mysqli_insert_id($msql_cnnct);
								
				$extension = pathinfo('uploads/'.$_POST['face'], PATHINFO_EXTENSION);
				
				rename('uploads/'.$_POST['face'], 'kd/'.$task_face.'.'.$extension);
				
				if ($_POST['graf'] != ''){
					$query = "INSERT INTO `spr_kd_img` (
						`client`, `face_graf`, `uptime`) 
					VALUES (
						'{$_POST['client']}', '2', '{$time}'
					)";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
					
					$task_graf = mysqli_insert_id($msql_cnnct);
					
					$extension = pathinfo('uploads/'.$_POST['graf'], PATHINFO_EXTENSION);
					
					rename('uploads/'.$_POST['graf'], 'kd/'.$task_graf.'.'.$extension);
				}
				
				/*echo $task_face.'<br />';
				if ($task_graf != '') 
					echo $task_face;*/


                CloseDB ($msql_cnnct);

                echo '
                    Изображения добавлены<br /><br />
                    <a href="client.php?id='.$_POST['client'].'" class="b">Вернуться в карточку</a>
                    <a href="kd.php?client='.$_POST['client'].'" class="b">Посмотреть КД</a>
                    <a href="add_kd.php?client='.$_POST['client'].'" class="b">Добавить этому пациенту КД</a>';
					
			}
		}
	}
?>