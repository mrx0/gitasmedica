<?php 

//add_comment_task_soft_f.php
//Функция для добавления комментария к  задаче IT в журнал
	
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['t_s_comment'] == ''){
				echo 'Ничего не написали.<br /><br />
				<a href="task_soft.php?id='.$_POST['id'].'" class="b">К задаче</a>';
			}else{
				include_once 'DBWork.php';
				
				WriteToDB_EditComments ('soft', $_POST['t_s_comment'], time(), $_SESSION['id'], time(), $_SESSION['id'], $_POST['id']);
			
				echo '
					Комментарий добавлен
					<br /><br />
					<a href="task_soft.php?id='.$_POST['id'].'" class="b">К задаче</a>
					';
			}
		}
	}
	
?>