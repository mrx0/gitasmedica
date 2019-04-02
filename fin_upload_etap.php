<?php 

//
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		
		if ($_POST){
			if (($_POST['etap'] == '') || !isset($_POST['etap']) || !isset($_POST['imgs']) || ($_POST['imgs'] == '') || ($_POST['imgs'] == '[]')){
				echo 'Ошибка. Обновите страницу [Ctrl+F5]<br /><br />';
			}else{
				
				$img_arr = explode(',', $_POST['imgs']);
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				
				foreach($img_arr as $value){
				
					$query = "INSERT INTO `journal_etaps_img` (
						`etap`, `uptime`) 
					VALUES (
						'{$_POST['etap']}', '{$time}'
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
					$extension = pathinfo('uploads_etap/'.$value, PATHINFO_EXTENSION);
					
					rename('uploads_etap/'.$value, 'etaps/'.$mysql_insert_id.'.'.$extension);										
				}

				mysql_close();
				
					echo '
						Изображения добавлены<br /><br />
						<a href="etap.php?id='.$_POST['etap'].'" class="b">Вернуться в исследование</a>';
			
			}
		}
	}
?>