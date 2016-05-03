<?php 

//edit_schedule_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if ($_POST['datatable'] == 'scheduler_stom'){
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$who = '&who=cosm';
				$datatable = 'zapis_cosm';
			}else{
				$who = '&who=stom';
				$datatable = 'zapis_stom';
			}
			if ($_POST['worker'] !=0){
				if ($_POST['patient'] != ''){
					if ($_POST['contacts'] != ''){
						if ($_POST['contacts'] != ''){
							//запись в базу
							WriteToDB_EditZapis ($datatable, $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab'], $_POST['worker'], $_POST['author'], $_POST['patient'], $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt']);
							
							echo '
								Изменения в расписание внесены<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
							//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
						}else{
							echo '
								Не указано описание<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
						}
					}else{
						echo '
							Не указали контакты<br /><br />
							<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
					}
				}else{
					echo '
						Не указали пациента<br /><br />
						<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
				}
			}else{
				echo '
					Не выбрали врача<br /><br />
					<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
			}
		}
	}
?>