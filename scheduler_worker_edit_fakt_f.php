<?php 

//scheduler_worker_edit_fakt_f.php
//Функция для редактирования расписания

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);

        $msql_cnnct = ConnectToDB();

		if ($_POST){
			if ($_POST['worker'] != 0){

                $workerBusy = FALSE;
                $request = '';

			    //Если НЕ указан тип графика, например 2 через 2
                //if ($_POST['twobytwo'] == 0) {

                    $workers = array();
                    $arr = array();

                    //надо посмотреть, а не работает ли этот сотрудник еще где-то в эту смену в этот день
                    $query = "SELECT `id`, `filial`, `day`, `month`, `year`, `smena`, `kab`, `worker` FROM `scheduler` WHERE `worker` = '{$_POST['worker']}' AND `type` = '{$_POST['type']}' AND `day` =  '{$_POST['day']}' AND `month` =  '{$_POST['month']}' AND `year` =  '{$_POST['year']}' AND `smena` =  '{$_POST['smena']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($workers, $arr);
                        }
                        $workerBusy = TRUE;
                    } else {
                        $workers = 0;
                    }
                    //var_dump ($workers);
                    //var_dump ($query);

                    //Если есть уже в графике, то удаляем оттуда
                    if ($workers != 0) {
                        foreach ($workers as $value) {
                            $query = "DELETE FROM `scheduler` WHERE `id`='{$value['id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        }
                        //логирование
                        AddLog('0', $_SESSION['id'], '', '[ПЕРЕНОС ИЗ ДРУГОЙ ФАКТИЧЕСКОЙ СМЕНЫ] [' . $_POST['worker'] . '] удален из смены  Графика [' . $_POST['smena'] . ']. Филиал [' . $_POST['filial'] . ']. Кабинет [' . $_POST['kab'] . ']. День [' . $_POST['day'] . ']. Месяц [' . $_POST['month'] . ']. Год [' . $_POST['year'] . ']. Тип [' . $_POST['type'] . ']');
                    }

                    //Надо посмотреть, есть ли кто уже именно тут, в этом каб, смене, дне, филиале и удалить его потом
                    $query = "SELECT `id` FROM `scheduler` WHERE `type` = '{$_POST['type']}' AND `day` =  '{$_POST['day']}' AND `month` =  '{$_POST['month']}' AND `year` =  '{$_POST['year']}' AND `smena` =  '{$_POST['smena']}' AND `filial` =  '{$_POST['filial']}' AND `kab` =  '{$_POST['kab']}'";
                    $workers = array();

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($workers, $arr);
                        }
                    } else {
                        $workers = 0;
                    }
                    //var_dump ($workers);

                    //Если есть уже в графике, то удаляем оттуда
                    if ($workers != 0) {
                        foreach ($workers as $value) {
                            $query = "DELETE FROM `scheduler` WHERE `id`='{$value['id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                        }

                        //логирование
                        AddLog('0', $_SESSION['id'], '', '[ЗАМЕНА НА ДРУГОГО В ФАКТИЧЕСКОМ ГРАФИКЕ] [' . $_POST['worker'] . '] удален из смены Графика [' . $_POST['smena'] . ']. Филиал [' . $_POST['filial'] . ']. Кабинет [' . $_POST['kab'] . ']. День [' . $_POST['day'] . ']. Месяц [' . $_POST['month'] . ']. Год [' . $_POST['year'] . ']. Тип [' . $_POST['type'] . ']');
                    }

                    //Добавляем новую запись
                    $query = "INSERT INTO `scheduler` (`filial`, `day`, `month`, `year`, `smena`, `kab`, `worker`, `type`) VALUES ('{$_POST['filial']}', '{$_POST['day']}', '{$_POST['month']}', '{$_POST['year']}', '{$_POST['smena']}', '{$_POST['kab']}', '{$_POST['worker']}', '{$_POST['type']}')";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //логирование
                    AddLog('0', $_SESSION['id'], '', 'Добавили сотрудника [' . $_POST['worker'] . '] в смену Фактического графика [' . $_POST['smena'] . ']. Филиал [' . $_POST['filial'] . ']. Кабинет [' . $_POST['kab'] . ']. День [' . $_POST['day'] . ']. Месяц [' . $_POST['month'] . ']. Год [' . $_POST['year'] . ']. Тип [' . $_POST['type'] . ']');

//                }else{
//                    //Если мы будем ставить сотрудника по типу графика, начиная с сегодняшней даты и до конца месяца
//                    if ($_POST['twobytwo'] == 1) {
//
//                        //Сначала удалим его везде в этом месяце, с указанной даты и до конца месяца
//                        $query = "DELETE FROM `scheduler` WHERE `worker`='{$_POST['worker']}' AND `year`='{$_POST['year']}' AND `month`='{$_POST['month']}' AND `day`>='{$_POST['day']}'";
//
//                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//                    }
//                }

				if ($workerBusy){
					$request = 'Переместили сотрудника в смену';
				}else{
					$request = 'Поставили сотрудника в смену';
				}
				
				echo '{"req": "ok", "text":"'.$request.'"}';
			}
		}
	}
?>