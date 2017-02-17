<?php 

//fin_upload_zub.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		
		if ($_POST){
			if (($_POST['task'] == '') || !isset($_POST['task']) || !isset($_POST['imgs']) || ($_POST['imgs'] == '') || ($_POST['imgs'] == '[]')){
				echo 'Ошибка. Обновите страницу [F5]<br /><br />';
			}else{
				
				$img_arr = explode(',', $_POST['imgs']);
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				
				foreach($img_arr as $value){
				
					$query = "INSERT INTO `journal_zub_img` (
						`task`, `client`, `uptime`) 
					VALUES (
						'{$_POST['task']}', '{$_POST['client']}', '{$time}'
					)";
					
					mysql_query($query) or die(mysql_error());
					
					$mysql_insert_id = mysql_insert_id();
					
/*$filename = 'uploads_etap/'.$value;

if (file_exists($filename)) {
    echo "Файл $filename существует";
} else {
    echo "Файл $filename не существует";
}
	*/				
					$extension = pathinfo('uploads_zub/'.$value, PATHINFO_EXTENSION);
					
					rename('uploads_zub/'.$value, 'zub_photo/'.$mysql_insert_id.'.'.$extension);										
				}

				mysql_close();
				
					echo '
						Изображения добавлены<br /><br />
						<a href="task_stomat_inspection.php?id='.$_POST['task'].'" class="b">Перейти к формуле</a>
						<a href="client.php?id='.$_POST['client'].'" class="b">В карточку пациента</a>';
			
			}
		}
	}
?>