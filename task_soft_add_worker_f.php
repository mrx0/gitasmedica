<?php 

//task_soft_add_worker_f.php
//Функция для добавления исполнителя IT

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['worker'] == ''){
				echo '
					Не выбрали исполнителя. Давайте еще разок =)<br /><br />
					<a href="task__soft_add_worker.php?id='.$_POST['id'].'" class="b">Назначить/Изменить</a>
					<a href="soft.php" class="b">В журнал</a>';
			}else{
				//Ищем работника
				$workers = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');
				if ($workers != 0){
					$worker = $workers[0]["id"];
						
					WriteToJournal_Update_Worker ($_POST['id'], $worker, $_SESSION['id'], 'journal_soft');
					
					echo '
						Задаче назначен исполнитель.
						<br /><br />
						<a href="task_soft.php?id='.$_POST['id'].'" class="b">К задаче</a>
						<a href="soft.php" class="b">В журнал</a>
					';
				}else{
					echo '
						В нашей баз нет такого сотрудника :(<br /><br />
						<a href="task_soft_add_worker.php?id='.$_POST['id'].'" class="b">Назначить/Изменить</a>
						<a href="soft.php" class="b">В журнал</a>';
				}

			}
		}
	}
?>