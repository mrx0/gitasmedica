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
				echo 'Ошибка. Обновите страницу [Ctrl+F5]<br /><br />';
			}else{
                include_once 'DBWork.php';
				
				$img_arr = explode(',', $_POST['imgs']);

                $time = time();

                $msql_cnnct = ConnectToDB ();

                foreach($img_arr as $value){
				
					$query = "INSERT INTO `journal_zub_img` (
						`task`, `client`, `uptime`) 
					VALUES (
						'{$_POST['task']}', '{$_POST['client']}', '{$time}'
					)";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $mysql_insert_id = mysqli_insert_id($msql_cnnct);
					
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

                CloseDB ($msql_cnnct);
				
				echo '
						Изображения добавлены<br /><br />
						<a href="task_stomat_inspection.php?id='.$_POST['task'].'" class="b">Перейти к формуле</a>
						<a href="client.php?id='.$_POST['client'].'" class="b">В карточку пациента</a>';
			
			}
		}
	}
?>