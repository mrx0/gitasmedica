<?php 

//edit_task_cosmet_f.php
//Функция для редактирования посещения косметолога

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['filial'] != 0){
				
				$create_time = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year'].' '.$_POST['sel_hours'].':'.$_POST['sel_minutes'].':'.$_POST['sel_seconds']);
				
						/*foreach ($_POST as $key => $value){
							if (($key != 'id') && ($key != 'filial') && ($key != 'comment') && ($key != 'sel_date') && ($key != 'sel_month') && ($key != 'sel_year') && ($key != 'sel_seconds') && ($key != 'sel_minutes') && ($key != 'sel_hours') && ($key != 'ajax')){
								//array_push ($arr, $value);
								$key = str_replace('action', '', $key);
								//echo $key.'<br />';
								$arr[$key] = $value;
							}				
						}*/
						
						foreach ($_POST as $key => $value){
							if (mb_strstr($key, 'action') != FALSE){
								//array_push ($arr, $value);
								$key = str_replace('action', 'c', $key);
								//echo $key.'<br />';
								$arr[$key] = $value;
							}				
						}	
						

						//var_dump ($arr);
						//$rezult = json_encode($arr);
						//echo $rezult.'<br />';
				
				//!!!Временно можно менять время добавления!!!
				WriteToDB_UpdateCosmet ($_POST['id'], $_POST['filial'], time(), $_SESSION['id'], $_POST['comment'], $create_time, $arr);
				
				echo '
					Посещение отредактировано.
					<br /><br />
					<a href="task_cosmet.php?id='.$_POST['id'].'" class="b">В посещение</a>
					';
			}else{
				echo '
					Вы не выбрали филиал<br /><br />
					<a href="task_cosmet.php?id='.$_POST['id'].'" class="b">В посещение</a>';
			}
		}

	}
	
?>