<?php 

//fin_upload_etap.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);


		if ($_POST){
            include_once 'DBWork.php';

			if (($_POST['etap'] == '') || !isset($_POST['etap']) || !isset($_POST['imgs']) || ($_POST['imgs'] == '') || ($_POST['imgs'] == '[]')){
				echo 'Ошибка. Обновите страницу [Ctrl+F5]<br /><br />';
			}else{


                $path = '';
				
				$img_arr = explode(',', $_POST['imgs']);

                $msql_cnnct = ConnectToDB ();

				$time = time();
				
				foreach($img_arr as $value){
				
					$query = "INSERT INTO `journal_etaps_img` (
						`etap`, `uptime`) 
					VALUES (
						'{$_POST['etap']}', '{$time}'
					)";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    //ID новой позиции
                    $mysqli_insert_id = mysqli_insert_id($msql_cnnct);
					
/*$filename = 'uploads_etap/'.$value;

if (file_exists($filename)) {
    echo "Файл $filename существует";
} else {
    echo "Файл $filename не существует";
}
	*/				
					$extension = pathinfo('uploads_etap/'.$value, PATHINFO_EXTENSION);
					
					rename('uploads_etap/'.$value, $path.'etaps/'.$mysqli_insert_id.'.'.$extension);
				}

				//mysql_close();
				
					echo '
						Изображения добавлены<br /><br />
						<a href="etap.php?id='.$_POST['etap'].'" class="b">Вернуться в исследование</a>';
			
			}
		}
	}
?>