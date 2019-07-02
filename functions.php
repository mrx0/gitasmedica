<?php 

//functions.php
//Различные функции

	include_once 'DBWork.php';
	
	//Создаём Полное ФИО
	function CreateFullName($f, $i, $o){
		$full_name =$f.' '.$i.' '.$o;
		
		return $full_name;
	}	
	
	//Создаём Краткое ФИО
	function CreateName($f, $i, $o){
		$name = $f.' '.mb_substr($i, 0, 1, "UTF-8").'.'.mb_substr($o, 0, 1, "UTF-8").'.';
		
		return $name;
	}	
	
	//Создаём логин
	function CreateLogin($f, $i, $o){
		$replace = array(
			"А"=>"a","а"=>"a",
			"Б"=>"b","б"=>"b",
			"В"=>"v","в"=>"v",
			"Г"=>"g","г"=>"g",
			"Д"=>"d","д"=>"d",
			"Е"=>"e","е"=>"e",
			"Ё"=>"e","ё"=>"e",
			"Ж"=>"z","ж"=>"z",
			"З"=>"z","з"=>"z",
			"И"=>"i","и"=>"i",
			"Й"=>"i","й"=>"i",
			"К"=>"k","к"=>"k",
			"Л"=>"l","л"=>"l",
			"М"=>"m","м"=>"m",
			"Н"=>"n","н"=>"n",
			"О"=>"o","о"=>"o",
			"П"=>"p","п"=>"p",
			"Р"=>"r","р"=>"r",
			"С"=>"s","с"=>"s",
			"Т"=>"t","т"=>"t",
			"У"=>"u","у"=>"u",
			"Ф"=>"f","ф"=>"f",
			"Х"=>"h","х"=>"h",
			"Ц"=>"c","ц"=>"c",
			"Ч"=>"ch","ч"=>"ch",
			"Ш"=>"sh","ш"=>"sh",
			"Щ"=>"sh","щ"=>"sh",
			"Ы"=>"y","ы"=>"y",
			"Э"=>"e","э"=>"e",
			"Ю"=>"u","ю"=>"u",
			"Я"=>"y","я"=>"y"
		);
		$login = iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($i, 0, 1, "UTF-8"),$replace)).iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($o, 0, 1, "UTF-8"),$replace)).iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($f, 0, 1, "UTF-8"),$replace));
		
		return $login;
	}

	//Проверка на существование пользователя такими фио
	function isSameFullName($datatable, $name, $id){
		$rezult = array();
		$rezult = SelDataFromDB($datatable, $name, 'full_name');
		//var_dump ($rezult);
		
		if ($rezult != 0){
			if ($id != $rezult[0]['id']){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}	
	
	//Проверка на существование логина
	function isSameLogin($login){
		$rezult = array();
		$isSame = TRUE;
		$rez_login = $login;
		$dop = 2;
		while ($isSame){
			$rezult = SelDataFromDB('spr_workers', $rez_login, 'login');
			if ($rezult != 0){
				$rez_login = $login.$dop;
				$dop++;
			}else{
				$isSame = FALSE;
			}
		}
		
		return $rez_login;
	}	
	
	//PassGen
	function PassGen(){
		// Символы, которые будут использоваться в пароле.
		$chars = "1234567890";
		// Количество символов в пароле.
		$max = 4;
		// Определяем количество символов в $chars
		$size = StrLen($chars)-1;
		// Определяем пустую переменную, в которую и будем записывать символы.
		$password = null;
		// Создаём пароль.
		while($max--){
			$password .= $chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	//Поиск в много(двух???)мерном ассоциативном(!?) массиве по значению
	function SearchInArray($array, $data, $search){
		$rez = 0;
		foreach ($array as $key => $value){
			if (array_search ($data, $value)){
				$rez = $value[$search];
			}				
		}
		return $rez;
	}
	
	//Специализации работника (не должность)
	function workerSpecialization($worker_id){

        $msql_cnnct = ConnectToDB ();

        $specializations_str_rez = '';

        //$specializations = SelDataFromDB('journal_work_spec', $worker_id, 'worker_id');

        $arr = array();
        $specializations_j = array();

        if ($worker_id != 0) {
            $query = "SELECT ss.name, ss.id
        	FROM `journal_work_spec` jws 
        	INNER JOIN `spr_specialization` ss ON ss.id = jws.specialization_id 
        	WHERE `worker_id` = '$worker_id'";
        }else{
        	$query = "SELECT `name`, `id` FROM `spr_specialization`";
		}
		
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        //var_dump($res);

        if ($number != 0){
            //var_dump(mysqli_fetch_assoc($res));
            while ($arr = mysqli_fetch_assoc($res)){
                if ($worker_id != 0) {
                    array_push($specializations_j, $arr);
                }else{
                    $specializations_j[$arr['id']] = $arr['name'];
				}
            }
        }

        return $specializations_j;
	}

	//Пишем ФИО человека
	function WriteSearchUser($datatable, $sw, $type, $link){
		if ($type == 'user_full'){
			$search = 'user';
		}else{
			$search = $type;
		}
		
		if ($datatable == 'spr_clients'){
			$uri = 'client.php';
		}
		if ($datatable == 'spr_workers'){
			$uri = 'user.php';
		}
		
		if ($sw != ''){
			$user = SelDataFromDB($datatable, $sw, $search);
			//var_dump ($user);
			//var_dump ($search);

			if ($user != 0){
				if ($type == 'user_full'){
					if ($link){
						return '<a href="'.$uri.'?id='.$sw.'" class="ahref">'.$user[0]['full_name'].'</a>';
					}else{
						return $user[0]['full_name'];
					}
				}else{
					if ($link){
						return '<a href="'.$uri.'?id='.$sw.'" class="ahref">'.$user[0]['name'].'</a>';
					}else{
						return $user[0]['name'];
					}
				}
			}else{
				return 'не указан';
			}
		}else{
			return 'не указан';
		}
	}

	//Собираем все филиалы
	function getAllFilials($sort, $short_name, $closed){
		$filials_j = array();

        $msql_cnnct = ConnectToDB ();

        $query = "SELECT * FROM `spr_filials`";

        if (!$closed){
            $query .= " WHERE `status` = '0'";
		}

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                $filials_j[$arr['id']] = $arr;
            }
        }

        if ($sort){
            if (!empty($filials_j)) {
                $filials_j_names = array();

                //Определяющий массив из названий для сортировки
                foreach ($filials_j as $key => $arr) {
                    if ($short_name){
                        array_push($filials_j_names, $arr['name2']);
                    }else {
                        array_push($filials_j_names, $arr['name']);
                    }
                }

                array_multisort($filials_j_names, SORT_LOCALE_STRING, $filials_j);
            }
        }

        return $filials_j;
	}

	//Собираем все специальности
	function getAllPermissions($sort, $only_name){
		$permissions_j = array();

        $msql_cnnct = ConnectToDB ();

        if ($only_name){
            $query = "SELECT `id`,`name` FROM `spr_permissions`";
        }else {
            $query = "SELECT * FROM `spr_permissions`";
        }

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                $permissions_j[$arr['id']] = $arr;
            }
        }

        if ($sort) {
            if (!empty($permissions_j)) {
                $permissions_j_names = array();

                //Определяющий массив из названий для сортировки
                foreach ($permissions_j as $key => $arr) {
                    array_push($permissions_j_names, $arr['name']);
                }

                array_multisort($permissions_j_names, SORT_LOCALE_STRING, $permissions_j);
            }
        }

        return $permissions_j;
	}

	//Сложение двух массивов
	function ArraySum($array1, $array2){
		if (count($array1) > count($array2)){
			$temp_arr1 = $array1;
			$temp_arr2 = $array2;
		}else{
			$temp_arr1 = $array2;
			$temp_arr2 = $array1;
		}
		foreach ($temp_arr2 as $key => $value) {
			if (!isset($temp_arr1[$key])){
				$temp_arr1[$key] = 0;
			}
		}
		foreach ($temp_arr1 as $key => $value) {
			if (isset($temp_arr2[$key])){
				$temp_arr1[$key] = $temp_arr1[$key] + $temp_arr2[$key];
			}
		}
		return $temp_arr1;
	}
	
	function isFired($id){
		$user = SelDataFromDB('spr_workers', $id, 'user');
		if ($user != 0){
			if ($user[0]['fired'] == 1){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return TRUE;
		}
	}
	
	function FilialWorker($type, $y, $m, $d, $office){
		//require 'config.php';
		$sheduler_workers = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `scheduler` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `filial` = '{$office}' AND `type` = '{$type}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;

		//mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaWorker($datatable, $y, $m, $d, $office, $kab){
		//require 'config.php';
		$sheduler_workers = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;

		//mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaWorker2($datatable, $y, $m, $d, $office, $kab, $smena){
		//require 'config.php';
		$sheduler_workers = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;

		//mysql_close();
		
		return $sheduler_workers;
	}
	
	//поиск сотрудников в расписании по дате и смене
	function FilialSmenaWorkerFree($datatable, $y, $m, $d, $smena, $worker){
		require 'config.php';
		$work_arr = array();
		if (($smena == 1) || ($smena == 2)){
			$q_smena = " AND (`smena` = '{$smena}' OR `smena` = '9' )";
		}elseif($smena == 9){
			$q_smena = " AND (`smena` = '1' OR `smena` = '2' OR `smena` = '9')";
		}

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' {$q_smena} AND `worker` = '{$worker}'";
		//var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($work_arr, $arr);
			}
		}else
			$work_arr = 0;

		//mysql_close();
		
		return $work_arr;
	}
	
	function FilialSmenaWorker($datatable, $y, $m, $d, $worker){
		//require 'config.php';
		$sheduler_workers = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `worker` = '{$worker}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;

		//mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaZapis($table, $y, $m, $d, $office, $kab, $worker, $wt){
		//require 'config.php';
		if ($table == 'scheduler_stom'){
			$datatable = 'zapis_stom';
		}elseif ($table == 'scheduler_cosm'){
			$datatable = 'zapis_cosm';
		}else{
			$datatable = 'zapis_stom';
		}
		$sheduler_zapis = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' 
		AND `worker` = '{$worker}' 
		AND `start_time` >= '{$wt}' AND `start_time` < '".($wt + 30)."'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;

		//mysql_close();
		
		return $sheduler_zapis;
	}

	//Получение записи в кабинете
	function FilialKabSmenaZapisToday($table, $y, $m, $d, $filial_id, $kab, $type, $enter)
    {
        //$table - Таблица (сейчас не используется), год, месяц, день, филиал, кабинет, тип (стом, косм, спец...)

        //require 'config.php';
//		if ($table == 'scheduler_stom'){
//			$datatable = 'zapis_stom';
//		}elseif ($table == 'scheduler_cosm'){
//			$datatable = 'zapis_cosm';
//		}else{
//			$datatable = 'zapis_stom';
//		}
        $sheduler_zapis = array();

        $msql_cnnct = ConnectToDB();

        //Все без ограничений
        //без записи
        if ($enter == 6){
            $query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$filial_id}' AND `enter` = 6 ORDER BY `start_time` ASC";
		}else {
    	    $query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$filial_id}' AND `kab` = '{$kab}' AND `type` = '{$type}' ORDER BY `start_time` ASC";
	    }
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;

		//mysql_close();
		
		return $sheduler_zapis;
	}
	
	//Получение записи в кабинете за дату
	function FilialKabSmenaZapisToday2($table, $y, $m, $d, $filial_id, $kab, $wt, $type){
        //$table - Таблица (сейчас не используется), год, месяц, день, филиал, кабинет, время записи, тип (стом, косм, спец...)

		//require 'config.php';
//		if ($table == 'scheduler_stom'){
//			$datatable = 'zapis_stom';
//		}elseif ($table == 'scheduler_cosm'){
//			$datatable = 'zapis_cosm';
//		}else{
//			$datatable = 'zapis_stom';
//		}
		$sheduler_zapis = array();

        $msql_cnnct = ConnectToDB ();

		$wt2 = $wt+30;

		//Кроме тех, которые удалены или не пришли
		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$filial_id}' AND `kab` = '{$kab}' AND `type` = '{$type}' AND `start_time` >= '{$wt}' AND `start_time` < '{$wt2}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";

		//echo $query;
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;

		//mysql_close();
		
		return $sheduler_zapis;
	}

	//Получение записи на филиале, результат раскидывается по кабинетам и времени записи
	function FilialKabSmenaZapisToday3($table, $y, $m, $d, $filial_id, $type, $enter){
        //$table - Таблица (сейчас не используется), год, месяц, день, филиал, тип (стом, косм, спец...)

		//require 'config.php';
//		if ($table == 'scheduler_stom'){
//			$datatable = 'zapis_stom';
//		}elseif ($table == 'scheduler_cosm'){
//			$datatable = 'zapis_cosm';
//		}else{
//			$datatable = 'zapis_stom';
//		}
		$sheduler_zapis = array();

        $msql_cnnct = ConnectToDB ();

        //Кроме тех, которые удалены или не пришли
		//без записи
//        if ($enter == 6){
//            $query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$filial_id}' AND `enter` = 6 ORDER BY `start_time` ASC";
//		}else {
	        $query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$filial_id}' AND `type` = '{$type}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `kab`, `start_time` ASC";
//        }
		//echo $query;
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			//Раскидываем в массив по кабинетам и времени записи
			while ($arr = mysqli_fetch_assoc($res)){
				//array_push($sheduler_zapis, $arr);
				if (!isset($sheduler_zapis[$arr['kab']])){
                    $sheduler_zapis[$arr['kab']] = array();
				}
                if (!isset($sheduler_zapis[$arr['kab']][$arr['start_time']])){
                    $sheduler_zapis[$arr['kab']][$arr['start_time']] = array();
                }
                $sheduler_zapis[$arr['kab']][$arr['start_time']] = $arr;
			}
		}else {
            //$sheduler_zapis = 0;
        }

		//mysql_close();

		return $sheduler_zapis;
	}

	function FilialWorkerSmenaZapisToday($table, $y, $m, $d, $worker){
		//require 'config.php';

		$sheduler_zapis = array();

        $msql_cnnct = ConnectToDB ();

		//$wt2 = $wt+30;
		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `worker` = '{$worker}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;

		//mysql_close();
		
		return $sheduler_zapis;
	}
	
	//Полных лет / Возраст
	function getyeardiff($bday, $c_date){
	    if ($c_date == 0){
		    $today = time();
	    }else{
            $today = $c_date;
        }
		$arr1 = getdate($bday);
		$arr2 = getdate($today);
		if((int)date('md', $today) >= (int)date('md', $bday) ) { 
			$t = 1;
		} else {
			$t = 0;
		}
		return ($arr2['year'] - $arr1['year'] - 1) + $t;
	}	
	
	function clear_dir($path) {
		//var_dump($path);
		if (file_exists(''.$path.'/')){
			foreach (glob(''.$path.'/*') as $file){
				//var_dump($file);
				unlink($file);
			}
		}
	}
	
	
	
	//Санация
	function Sanation ($data){
		//var_dump ($data);
		foreach ($data as $key => $value){
			$id = $value['id'];
			unset ($value['id']);
			unset ($value['office']);
			unset ($value['client']);
			unset ($value['create_time']);
			unset ($value['create_person']);
			unset ($value['last_edit_time']);
			unset ($value['last_edit_person']);
			unset ($value['worker']);
			unset ($value['comment']);
			//var_dump ($value);
			foreach ($value as $tooth => $status){
				//var_dump ($status);
				$status_arr = explode(',', $status);
				//var_dump($status_arr);
				if ($status_arr[0] == '1'){
					echo 'Отсутствует<br />';
				}
				if ($status_arr[0] == '2'){
					echo 'Удален<br />';
				}
				if (($status_arr[0] == '3') && ($status_arr[1] == '1')){
					echo $id.'Имплантант<br />';
				}
				if ($status_arr[0] == '20'){
					echo 'Ретенция<br />';
				}
				if ($status_arr[0] == '22'){
					echo 'ЗО<br />';
				}
				
				echo $id.'<br />';
				
				if (($status_arr[3] == 64) || ($status_arr[4] == 64) || ($status_arr[5] == 64) || ($status_arr[6] == 64) || 
					($status_arr[7] == 64) || ($status_arr[8] == 64) || ($status_arr[9] == 64) || ($status_arr[10] == 64) || 
					($status_arr[11] == 64) || ($status_arr[12] == 64)){
					echo 'Пломба кариес<br />';
				}
				
				echo $id.'<br />';
				
				if (($status_arr[3] == 71) || ($status_arr[4] == 71) || ($status_arr[5] == 71) || ($status_arr[6] == 71) || 
					($status_arr[7] == 71) || ($status_arr[8] == 71) || ($status_arr[9] == 71) || ($status_arr[10] == 71) || 
					($status_arr[11] == 71) || ($status_arr[12] == 71)){
					echo 'Кариес<br />';
				}
				
				echo $id.'<br />';
				
				if (($status_arr[3] == 74) || ($status_arr[4] == 74) || ($status_arr[5] == 74) || ($status_arr[6] == 74) || 
					($status_arr[7] == 74) || ($status_arr[8] == 74) || ($status_arr[9] == 74) || ($status_arr[10] == 74) || 
					($status_arr[11] == 74) || ($status_arr[12] == 74)){
					echo 'Пульпит<br />';
				}
				
				echo $id.'<br />';
				
				if (($status_arr[3] == 75) || ($status_arr[4] == 75) || ($status_arr[5] == 75) || ($status_arr[6] == 75) || 
					($status_arr[7] == 75) || ($status_arr[8] == 75) || ($status_arr[9] == 75) || ($status_arr[10] == 75) || 
					($status_arr[11] == 75) || ($status_arr[12] == 75)){
					echo 'Периодонтит<br />';
				}
				
			}
		}
	}
	
	
	function Sanation2 ($t_id, $data, $cl_age){
		//var_dump ($data);
		/*unset ($data['id']);
		unset ($data['office']);
		unset ($data['client']);
		unset ($data['create_time']);
		unset ($data['create_person']);
		unset ($data['last_edit_time']);
		unset ($data['last_edit_person']);
		unset ($data['worker']);
		unset ($data['comment']);*/
		
		$sanat = true;
		
		//foreach ($data as $key => $val){
			//var_dump ($val);
			foreach ($data as $tooth => $status){
			    if ($tooth != 'status') {
                    //var_dump ($status);
                    //var_dump ($status);
                    //$status_arr = explode(',', $status);
                    //var_dump($status_arr);
                    if ($status['status'] == '1') {
                        //echo 'Отсутствует<br />';
                        if ((($tooth != 18) && ($tooth != 28) && ($tooth != 38) && ($tooth != 48)) && ($cl_age > 14)) {
                            $sanat = false;
                        }
                    }
                    if ($status['status'] == '2') {
                        //echo 'Удален<br />';
                        if (($tooth != 18) && ($tooth != 28) && ($tooth != 38) && ($tooth != 48)) {
                            $sanat = false;
                        }
                    }
                    if (($status['status'] == '3') && ($status['status'] == '1')) {
                        //echo $t_id.'Имплантант<br />';
                        $sanat = false;
                    }
                    if ($status['status'] == '20') {
                        //echo 'Ретенция<br />';
                        $sanat = false;
                    }
                    if ($status['status'] == '22') {
                        //echo 'ЗО<br />';
                        $sanat = false;
                    }

                    if (($status['surface1'] == 63) || ($status['surface2'] == 63) || ($status['surface3'] == 63) || ($status['surface4'] == 63) ||
                        ($status['top1'] == 63) || ($status['top2'] == 63) || ($status['top12'] == 63) || ($status['root1'] == 63) ||
                        ($status['root2'] == 63) || ($status['root3'] == 63)
                    ) {
                        //echo 'Временная пломба<br />';
                        $sanat = false;
                    }

                    //echo $t_id.'<br />';

                    if (($status['surface1'] == 64) || ($status['surface2'] == 64) || ($status['surface3'] == 64) || ($status['surface4'] == 64) ||
                        ($status['top1'] == 64) || ($status['top2'] == 64) || ($status['top12'] == 64) || ($status['root1'] == 64) ||
                        ($status['root2'] == 64) || ($status['root3'] == 64)
                    ) {
                        //echo 'Пломба кариес<br />';
                        $sanat = false;
                    }

                    //echo $t_id.'<br />';
                    //$sanat = false;

                    if (($status['surface1'] == 71) || ($status['surface2'] == 71) || ($status['surface3'] == 71) || ($status['surface4'] == 71) ||
                        ($status['top1'] == 71) || ($status['top2'] == 71) || ($status['top12'] == 71) || ($status['root1'] == 71) ||
                        ($status['root2'] == 71) || ($status['root3'] == 64)
                    ) {
                        //echo 'Кариес<br />';
                        $sanat = false;
                    }

                    //echo $t_id.'<br />';

                    if (($status['surface1'] == 74) || ($status['surface2'] == 74) || ($status['surface3'] == 74) || ($status['surface4'] == 74) ||
                        ($status['top1'] == 74) || ($status['top2'] == 74) || ($status['top12'] == 74) || ($status['root1'] == 74) ||
                        ($status['root2'] == 74) || ($status['root3'] == 64)
                    ) {
                        //echo 'Пульпит<br />';
                        $sanat = false;
                    }

                    //echo $t_id.'<br />';

                    if (($status['surface1'] == 75) || ($status['surface2'] == 75) || ($status['surface3'] == 75) || ($status['surface4'] == 75) ||
                        ($status['top1'] == 75) || ($status['top2'] == 75) || ($status['top12'] == 75) || ($status['root1'] == 75) ||
                        ($status['root2'] == 75) || ($status['root3'] == 64)
                    ) {
                        //echo 'Периодонтит<br />';
                        $sanat = false;
                    }
                }
				
			}
		//}
		return $sanat;
	}
	
	function missingTeeth  ($t_id, $data, $cl_age){
		//var_dump ($data);
		/*unset ($data['id']);
		unset ($data['office']);
		unset ($data['client']);
		unset ($data['create_time']);
		unset ($data['create_person']);
		unset ($data['last_edit_time']);
		unset ($data['last_edit_person']);
		unset ($data['worker']);
		unset ($data['comment']);*/
		
		$sanat = true;
		
		//foreach ($data as $key => $val){
			//var_dump ($val);
			foreach ($data as $tooth => $status){
				//var_dump ($status);
				//$status_arr = explode(',', $status);
				//var_dump($status_arr);
				if ($status['status'] == '1'){
					//echo 'Отсутствует<br />';
					if ((($tooth != 18) && ($tooth != 28) && ($tooth != 38) && ($tooth != 48)) && ($cl_age > 14)){
						$sanat = false;
					}
				}
				if ($status['status'] == '2'){
					//echo 'Удален<br />';
					if (($tooth != 18) && ($tooth != 28) && ($tooth != 38) && ($tooth != 48)){
						$sanat = false;
					}
				}
				if (($status['status'] == '3') && ($status['status'] == '1')){
					//echo $t_id.'Имплантант<br />';
					$sanat = false;
				}
				/*if ($status['status'] == '20'){
					//echo 'Ретенция<br />';
					$sanat = false;
				}*/
				/*if ($status['status'] == '22'){
					//echo 'ЗО<br />';
					$sanat = false;
				}*/
				
				//echo $t_id.'<br />';
				
				/*if (($status['surface1'] == 64) || ($status['surface2'] == 64) || ($status['surface3'] == 64) || ($status['surface4'] == 64) || 
					($status['top1'] == 64) || ($status['top2'] == 64) || ($status['top12'] == 64) || ($status['root1'] == 64) || 
					($status['root2'] == 64) || ( $status['root3'] == 64)){
					//echo 'Пломба кариес<br />';
					$sanat = false;
				}*/
				
				//echo $t_id.'<br />';
				//$sanat = false;
				
				/*
				if (($status['surface1'] == 71) || ($status['surface2'] == 71) || ($status['surface3'] == 71) || ($status['surface4'] == 71) || 
					($status['top1'] == 71) || ($status['top2'] == 71) || ($status['top12'] == 71) || ($status['root1'] == 71) || 
					($status['root2'] == 71) || ($status['root3'] == 64)){
					//echo 'Кариес<br />';
					$sanat = false;
				}*/
				
				//echo $t_id.'<br />';
				/*
				if (($status['surface1'] == 74) || ($status['surface2'] == 74) || ($status['surface3'] == 74) || ($status['surface4'] == 74) || 
					($status['top1'] == 74) || ($status['top2'] == 74) || ($status['top12'] == 74) || ($status['root1'] == 74) || 
					($status['root2'] == 74) || ($status['root3'] == 64)){
					//echo 'Пульпит<br />';
					$sanat = false;
				}*/
				
				//echo $t_id.'<br />';
				/*
				if (($status['surface1'] == 75) || ($status['surface2'] == 75) || ($status['surface3'] == 75) || ($status['surface4'] == 75) || 
					($status['top1'] == 75) || ($status['top2'] == 75) || ($status['top12'] == 75) || ($status['root1'] == 75) || 
					($status['root2'] == 75) || ($status['root3'] == 64)){
					//echo 'Периодонтит<br />';
					$sanat = false;
				}*/
				
			}
		//}
		return $sanat;
	}
	
	function selectDate ($selD, $selM, $selY){
		//var_dump($selD);
		//var_dump($selM);
		//var_dump($selY);

		$result = '';
		
		$month = array(
				"Январь",
				"Февраль",
				"Март",
				"Апрель",
				"Май",
				"Июнь",
				"Июль",
				"Август",
				"Сентябрь",
				"Октябрь",
				"Ноябрь",
				"Декабрь"
		);
		
		$i = 1;
		$j = 1920;
		
		//День
		$result .= '<select name="sel_date" id="sel_date">';
		$result .= '<option value="00">00</option>';
		while ($i <= 31) {
			if ($selD == $i) $selected = ' selected'; else $selected = '';
			
			$result .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			$i++;
		}
		$result .= '</select>';
		
		// Месяц
		$result .= '<select name="sel_month" id="sel_month">';
		$result .= '<option value="00">---</option>';
		foreach ($month as $m => $n) {
			if ($selM == $m+1) $selected = ' selected'; else $selected = '';
			
			$result .= '<option value="'.($m+1).'"'.$selected.'>'.$n.'</option>';
		}
		$result .= '</select>';
		
		// Год
		$result .= '<select name="sel_year" id="sel_year">';
		$result .= '<option value="0000">0000</option>';
		while ($j <= 2020) {
			if ($selY == $j) $selected = ' selected'; else $selected = '';
			
			$result .= '<option value="'.$j.'"'.$selected.'>'.$j.'</option>';
			$j++;
		}
		$result .= '</select>';
		
		return $result;
		
	}
	
	//Первая буква заглавная
	function firspUpperCase ($string){
		mb_internal_encoding("UTF-8");
		$first = mb_substr($string, 0, 1);//первая буква
		$last = mb_substr($string, 1);//все кроме первой буквы
		$first = mb_strtoupper($first);
		$last = mb_strtolower($last);
		return $first.$last;
	}

	//Долги/Авансы
	function DebtsPrepayments ($id){
		//require 'config.php';
		$result = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `journal_debts_prepayments` WHERE `client` = '{$id}' AND (`type`='4' OR `type`='3')";

		$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($result, $arr);
			}
		}else
			$result = 0;

		//mysql_close();
		
		return $result;
	}
	
	//Погашения
	function Repayments ($id){
		//require 'config.php';
		$result = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM `journal_debts_prepayments` WHERE `parent` = '{$id}' AND `type`='8'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($result, $arr);
			}
		}else
			$result = 0;

		//mysql_close();
		
		return $result;
	}

	//Дерево
	function showTree($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id){
		//require 'config.php';

        $msql_cnnct = ConnectToDB ();
						
		$arr = array();
		$rez = array();
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$deleted_str = '';
		
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		
		$q_dop = '';
		$dbprices = 'spr_priceprices';
		$link = 'pricelistitem.php?';
		
		//Для страховых
		if ($insure_id != 0){
			$q_dop = " AND `insure`='{$insure_id}'";
			$dbprices = 'spr_priceprices_insure';
			$link = 'pricelistitem_insure.php?insure='.$insure_id;
		}else{
			//
		}
		
		//Выбираем всё из этого уровня
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ".$deleted_str." ORDER BY `name`";
		
		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && ($type == 'list')){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				if ($type == 'select'){
					//echo $space.$value['name'].'<br>';
					$selected = '';
					if ($value['id'] == $sel_id){
						$selected = ' selected';
					}
					echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
				}
				
				if ($type == 'list'){
					//echo $space.$value['name'].'<br>';
					
					if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
						/*if ($this_level == 1){
							$style_name .= 'background-color: rgba(103, 251, 66, 0.5)';
						}elseif ($this_level == 2){
							$style_name .= 'background-color: rgba(97, 227, 255, 0.5);';
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}*/
					}
					
					//Если не страховая
					//if ($insure_id == 0){
						echo '
						<li class="cellsBlock" style="width: auto;">
							<div class="cellPriority" style=""></div>
							<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
								<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
							</div>
							<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
								<div class="managePriceList" style="font-style: normal; font-size: 13px;">
									<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
									<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
									<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
									<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
								</div>
							</div>
						</li>';
					//}
					
					
					$query = "SELECT * FROM `{$dbtable}` WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$deleted_str." ".$q_dop." ORDER BY `name`";			
					
					if ($insure_id != 0){
						//$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `{$dbtable}` WHERE `item` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$q_dop.") ".$deleted_str." ORDER BY `name`";			
						$query = "SELECT * FROM  `spr_pricelist_template` x INNER JOIN (SELECT `item` FROM `{$dbtable}` WHERE `item` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}')".$q_dop.") y ON x.id = y.item ".$deleted_str." ORDER BY `name`";			
					}
					
					//var_dump($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr2 = mysqli_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
						$items_j = $rez2;
					}else{
						$items_j = 0;
					}
					
					//var_dump($items_j);
					
					if ($items_j != 0){
						
						$anything_here = true;
						
						//Если страховая
						/*if ($insure_id != 0){
							echo '
							<li class="cellsBlock" style="width: auto;">
								<div class="cellPriority" style=""></div>
								<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
									<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
								</div>
								<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
									<div class="managePriceList" style="font-style: normal; font-size: 13px;">
										<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
										<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
										<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
										<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
									</div>
								</div>
							</li>';
						}*/
						
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
							
							if ($insure_id != 0){
								//$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".$items_j[$i]['id']."' AND `insure`='".$insure_id."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
								$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".$items_j[$i]['id']."' AND `insure`='".$insure_id."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";
							}
							//var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							$number = mysqli_num_rows($res);
							if ($number != 0){
								$arr3 = mysqli_fetch_assoc($res);
								$price = $arr3['price'];
							}else{
								$price = 0;
							}

							echo '
										<li class="cellsBlock" style="width: auto;">
											<div class="cellPriority" style=""></div>
											<a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref cellOffice" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
											<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.'</div>
										</li>';
						}
					}else{
						//
					}
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ".$deleted_str." ORDER BY `name`";
				//var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);
				if ($number != 0){
					//echo '_'.$value['name'].'<br>';
					$space2 = $space. '&nbsp;&nbsp;&nbsp;';
					$last_level2 = $last_level+1;
					showTree($value['id'], $space2, $type, $sel_id, $first, $last_level2, $deleted, $dbtable, $insure_id);
				}else{
					//var_dump ($color_index);
					//var_dump ($last_level);
					/*if ($color_index > $last_level){
						$color_index--;
					}*/
					//$space = substr($space, 0, -1);
					//echo '_'.$value['name'].'<br>';
				}
				//$space = substr($space, 0, -1);
			}
			//$color_index = $last_level;
		}
	}
	
	//Удаление дерева
	function DeleteTree($level, $space, $type, $sel_id, $first, $last_level, $deleted, $deleteallin){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		
		//var_dump ($deleteallin);
		
		$arr = array();
		$rez = array();
		
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$deleted_str = '';
		
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		//Выбираем всё из этого уровня
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ".$deleted_str." ORDER BY `name`";
		
		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && (($type == 'list') || ($type == 'clear'))){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){
				//Обновили статус родителю
				
				$query = "UPDATE `spr_storagegroup` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9', `level`='0' WHERE `id`='{$value['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						
				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				//!!! clear
				if ($type == 'clear'){
					//собираем все позиции в этой группе и удаляем их из группы и их самих
					$query = "SELECT * FROM `spr_itemsingroup` WHERE `group` = '{$value['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);
					if ($number != 0){
						while ($arr2 = mysqli_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
					}else{
						$rez2 = 0;
					}
					//var_dump($rez);
					
					if ($rez2 != 0){
						//...удаляем их из группы
						$query = "DELETE FROM `spr_itemsingroup` WHERE `group` = '{$value['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
						
						if ($deleteallin == 1){
							foreach ($rez2 as $ids){
								//var_dump($ids);
								//...и их самих
								$query = "UPDATE `spr_pricelist_template` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9' WHERE `id`='{$ids['item']}'";
								//var_dump($query);
                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
							}
						}
					}
				}

				//получаем группы, которые в этом родителе
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level` = '{$value['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);
				if ($number != 0){
					DeleteTree($value['id'], '', $type, $sel_id, $first, 0, $deleted, $deleteallin);
				}else{
				}
			}
		}
	}
	
	//Дерево с return
	function returnTree($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
				
		static $rezult_arr = array();
				
		$arr = array();
		$rez = array();
		/*$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$deleted_str = '';
		*/
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		
		$q_dop = '';
		$dbprices = 'spr_priceprices';
		
		//Для страховых
		/*if ($insure_id != 0){
			$q_dop = " AND `insure`='{$insure_id}'";
			$dbprices = 'spr_priceprices_insure';
		}else{
			//
		}*/
		
		//Выбираем всё из этого уровня
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ".$deleted_str." ORDER BY `name`";
		//var_dump ($query);
		
		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && ($type == 'return')){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);
		
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				/*if ($type == 'select'){
					//echo $space.$value['name'].'<br>';
					$selected = '';
					if ($value['id'] == $sel_id){
						$selected = ' selected';
					}
					echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
				}*/
				
				if ($type == 'return'){
					//echo $space.$value['name'].'<br>';
					
					/*if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
						/*if ($this_level == 1){
							$style_name .= 'background-color: rgba(103, 251, 66, 0.5)';
						}elseif ($this_level == 2){
							$style_name .= 'background-color: rgba(97, 227, 255, 0.5);';
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}*/
					//}
					
					//Если не страховая
					/*if ($insure_id == 0){
						echo '
						<li class="cellsBlock" style="width: auto;">
							<div class="cellPriority" style=""></div>
							<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
								<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
							</div>
							<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
								<div class="managePriceList" style="font-style: normal; font-size: 13px;">
									<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
									<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
									<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
									<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
								</div>
							</div>
						</li>';
					}*/
					
					$query = "SELECT * FROM `{$dbtable}` WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$deleted_str." ".$q_dop." ORDER BY `name`";			
					//var_dump($query);
					
					$res = mysql_query($query) or die(mysql_error().' -> '.$query);	
					$number = mysql_num_rows($res);	
					if ($number != 0){
						while ($arr2 = mysql_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
						$items_j = $rez2;
					}else{
						$items_j = 0;
					}
					
					//var_dump($items_j);
					
					if ($items_j != 0){
						
						$anything_here = true;
						
						//array_push($rezult_arr, $items_j[$i]['id']);
						
						//Если страховая
						/*if ($insure_id != 0){
							echo '
							<li class="cellsBlock" style="width: auto;">
								<div class="cellPriority" style=""></div>
								<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
									<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
								</div>
								<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
									<div class="managePriceList" style="font-style: normal; font-size: 13px;">
										<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
										<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
										<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
										<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
									</div>
								</div>
							</li>';
						}*/
						
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							$price2 = 0;
							$price3 = 0;

							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price`,`price2`,`price3` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
							//var_dump($query);
							
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);

							$number = mysql_num_rows($res);
							if ($number != 0){
								$arr3 = mysql_fetch_assoc($res);
								$price = $arr3['price'];
								$price2 = $arr3['price2'];
								$price3 = $arr3['price3'];
							}else{
								$price = 0;
								$price2 = 0;
								$price3 = 0;
							}
					
							//array_push($rezult_arr, $items_j[$i]['id']);
							$rezult_arr[$items_j[$i]['id']]['price'] = $price;
							$rezult_arr[$items_j[$i]['id']]['price2'] = $price2;
							$rezult_arr[$items_j[$i]['id']]['price3'] = $price3;

							/*echo '
										<li class="cellsBlock" style="width: auto;">
											<div class="cellPriority" style=""></div>
											<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
											<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.'</div>
										</li>';*/
						}
					}else{
						//
					}
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ".$deleted_str." ORDER BY `name`";
				//var_dump($query);
				
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				//var_dump($number);

				if ($number != 0){
					//var_dump('next');
					
					//echo '_'.$value['name'].'<br>';
					$space2 = $space. '&nbsp;&nbsp;&nbsp;';
					$last_level2 = $last_level+1;
					returnTree($value['id'], $space2, $type, $sel_id, $first, $last_level2, $deleted, $dbtable, $insure_id);
				}else{
					//var_dump ($color_index);
					//var_dump ($last_level);
					/*if ($color_index > $last_level){
						$color_index--;
					}*/
					//$space = substr($space, 0, -1);
					//echo '_'.$value['name'].'<br>';
				}
				//$space = substr($space, 0, -1);
			}
			//$color_index = $last_level;
		}
		return $rezult_arr;
	}
	
	//Ещё одно дерево
	function showTree2($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){

        $msql_cnnct = ConnectToDB ();
						
		$arr = array();
		$rez = array();
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$deleted_str = '';
		
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		
		$q_dop = '';
		$dbprices = 'spr_priceprices';
		$link = 'pricelistitem.php?';
		
		//Для страховых
		if ($insure_id != 0){
			$q_dop = " AND `insure`='{$insure_id}'";
			$dbprices = 'spr_priceprices_insure';
			$link = 'pricelistitem_insure.php?insure='.$insure_id;
		}else{
			//
		}
		
		//Выбираем всё из этого уровня
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ".$deleted_str." ORDER BY `name`";
		
		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && ($type == 'list')){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				/*if ($type == 'select'){
					//echo $space.$value['name'].'<br>';
					$selected = '';
					if ($value['id'] == $sel_id){
						$selected = ' selected';
					}
					echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
				}*/
				
				if ($type == 'list'){
					//echo $space.$value['name'].'<br>';
					
					if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
					}

					echo '
						<li style="cursor: e-resize;">
							<div class="drop" style="background-position: 0px 0px;"></div>
							<p class="drop"><b>'.$value['name'].'</b></p>';
					
					/*echo '
						<li class="cellsBlock" style="width: auto;">
							<div class="cellPriority" style=""></div>
							<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
								<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
							</div>
							<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
								<div class="managePriceList" style="font-style: normal; font-size: 13px;">
									<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
									<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
									<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
									<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
								</div>
							</div>
						</li>';
					*/
					
					echo '
							<ul style="display: none;">';
					
					$query = "SELECT * FROM `{$dbtable}` WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$deleted_str." ".$q_dop." ORDER BY `name`";			
					
					if ($insure_id != 0){
						$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `{$dbtable}` WHERE `item` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$q_dop.") ".$deleted_str." ORDER BY `name`";			
					}
					
					//var_dump($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
					$number = mysqli_num_rows($res);
					if ($number != 0){
						while ($arr2 = mysqli_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
						$items_j = $rez2;
					}else{
						$items_j = 0;
					}
					
					//var_dump($items_j);
					
					if ($items_j != 0){

						$anything_here = true;
						
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
							
							if ($insure_id != 0){
								$query = "SELECT `price` FROM `spr_priceprices_insure` WHERE `item`='".$items_j[$i]['id']."' AND `insure`='".$insure_id."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
							}
							//var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							$number = mysqli_num_rows($res);
							if ($number != 0){
								$arr3 = mysqli_fetch_assoc($res);
								$price = $arr3['price'];
							}else{
								$price = 0;
							}
						
							echo '
										<li style="cursor: pointer;">
											<p onclick="checkPriceItem('.$items_j[$i]['id'].', '.$dtype.')"><span class="4filter"><span style="font-size: 75%; font-weight: bold;">[#'.$items_j[$i]['id'].']</span> <i>'.$items_j[$i]['code'].'</i> '.$items_j[$i]['name'].'</span></p>
										</li>';
						}
					}else{
						//
					}
					
					/*echo '
							</ul>';
					
					echo '
						</li>';*/
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ".$deleted_str." ORDER BY `name`";
				//var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
				$number = mysqli_num_rows($res);
				if ($number != 0){
					//echo '_'.$value['name'].'<br>';
					$space2 = $space. '&nbsp;&nbsp;&nbsp;';
					$last_level2 = $last_level+1;
					showTree2($value['id'], $space2, $type, $sel_id, $first, $last_level2, $deleted, $dbtable, $insure_id, $dtype);
				}else{
					//---
					
					

					
				}
				
					echo '
							</ul>';
					
					echo '
						</li>';
				
			}
		}
	}


	function showTree4($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){

	    $msql_cnnct = ConnectToDB();
						
		$arr = array();
		$rez = array();
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$deleted_str = '';
		
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		
		$q_dop = '';
		$dbprices = 'spr_priceprices';
		$link = 'pricelistitem.php?';
		
		//Для страховых
		if ($insure_id != 0){
			$q_dop = " AND `insure`='{$insure_id}'";
			$dbprices = 'spr_priceprices_insure';
			$link = 'pricelistitem_insure.php?insure='.$insure_id;
		}else{
			//
		}
		
		//Выбираем всё из этого уровня
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ".$deleted_str." ORDER BY `name`";
		
		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && ($type == 'list')){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}else{
		}
		//var_dump ($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				/*if ($type == 'select'){
					//echo $space.$value['name'].'<br>';
					$selected = '';
					if ($value['id'] == $sel_id){
						$selected = ' selected';
					}
					echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
				}*/
				
				if ($type == 'list'){
					//echo $space.$value['name'].'<br>';
					
					if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
					}

                    //$style_name .= 'position: relative;';
					
					echo '
						<li style="border: none; position: relative;">
							<div class="drop" style="background-position: 0px 0px;"></div>
							<p class="drop" style="'.$style_name.'">
								<b>
								    '.$space.$value['name'].'
								</b>
							</p>';

                    if ($insure_id == 0) {
                        echo '
							<div style="position: absolute; top: 0; right: 3px;">
							   <a href="pricelistgroup.php?id=' . $value['id'] . '" class="ahref" style="font-weight: bold;" title="Открыть карточку группы">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>								    
							   </a>
								<div style="font-style: normal; font-size: 13px; display: inline-block;">
								    <div class="managePriceList">
                                        <a href="pricelistgroup_edit.php?id=' . $value['id'] . '" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать карточку группы"></i></a>
                                        <a href="add_pricelist_item.php?addinid=' . $value['id'] . '" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
                                        <!--<a href="pricelistgroup_del.php?id=' . $value['id'] . '" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
                                        <a href="pricelistgroup_del.php?id=' . $value['id'] . '" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить эту группу"></i></a>
									</div>
								</div>
							</div>';
                    }
					/*echo '
						<li class="cellsBlock" style="width: auto;">
							<div class="cellPriority" style=""></div>
							<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
								<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
							</div>
							<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
								<div class="managePriceList" style="font-style: normal; font-size: 13px;">
									<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
									<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
									<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
									<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
								</div>
							</div>
						</li>';
					*/

					echo '
							<ul style="display: none;">';
					
					$query = "SELECT * FROM `{$dbtable}` WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$deleted_str." ".$q_dop." ORDER BY `name`";			
					
					if ($insure_id != 0){
						$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `{$dbtable}` WHERE `item` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$q_dop.") ".$deleted_str." ORDER BY `name`";			
					}
					
					//var_dump($query);

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

					$number = mysqli_num_rows($res);
					if ($number != 0){
						while ($arr2 = mysqli_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
						$items_j = $rez2;
					}else{
						$items_j = 0;
					}
					
					//var_dump($items_j);
					
					if ($items_j != 0){

						$anything_here = true;
						
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							$price2 = 0;
							$price3 = 0;

							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price`, `price2`, `price3` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

							if ($insure_id != 0){
								$query = "SELECT `price`, `price2`, `price3` FROM `spr_priceprices_insure` WHERE `item`='".$items_j[$i]['id']."' AND `insure`='".$insure_id."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";
							}
							//var_dump($query);

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

							$number = mysqli_num_rows($res);

							if ($number != 0){
								$arr3 = mysqli_fetch_assoc($res);
								$price = $arr3['price'];
								$price2 = $arr3['price2'];
								$price3 = $arr3['price3'];
							}else{
								$price = 0;
                                $price2 = 0;
								$price3 = 0;
							}

                            if ($price2 == 0){
                                $price2 = $price * 1.1;
                            }
                            if ($price3 == 0){
                                $price3 = $price * 1.2;
                            }


						    //позиции с ценами
							echo '
										<li>
											<div class="priceitem">';
                            if ($insure_id != 0) {
                                echo '
                            			        <div class="cellManage" style="display: none;">
											      <span style="font-size: 80%; color: #777;">
											        <input type="checkbox" name="propDel[]" value="' . $items_j[$i]['id'] . '"> пометить на удаление
											      </span>
                                                </div>';
                            }
                            echo '
												<div class="priceitemDivname">
													<a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref" id="4filter">
														<span style="font-size: 75%; font-weight: bold;">[#'.$items_j[$i]['id'].']</span> 
														<i>'.$items_j[$i]['code'].'</i> 
														'.$items_j[$i]['name'].' ';

                            //Категория процентов
                            echo '['.$items_j[$i]['category'].']';

                            echo '							
													</a>
												</div>
												<div class="priceitemDiv">
													<div class="priceitemDivcost"><b>'.$price.'</b> руб.</div>';
                            if ($insure_id == 0) {
                                echo '
                                                    <div class="priceitemDivcost" ><b > '.$price2.'</b > руб.</div >
													<div class="priceitemDivcost" ><b > '.$price3.'</b > руб.</div >';
                            }
                            echo '

												</div>
											</div>
										</li>';
						}
					}else{
						//
					}
					
					/*echo '
							</ul>';
					
					echo '
						</li>';*/
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ".$deleted_str." ORDER BY `name`";
				//var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					//echo '_'.$value['name'].'<br>';
					$space2 = $space. '&nbsp;&nbsp;&nbsp;';
					$last_level2 = $last_level + 1;
					showTree4($value['id'], $space2, $type, $sel_id, $first, $last_level2, $deleted, $dbtable, $insure_id, $dtype);
				}else{
					//---
				}
				
					echo '
							</ul>';
					
					echo '
						</li>';

			}
		}
	}

	//Для дерева категорий номенлатуры
	function showTree5($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){
		//var_dump($level);
        //var_dump($space);
		//var_dump(func_get_args());

	    $msql_cnnct = ConnectToDB();

		$arr = array();
        $rezult = array();
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;

		$deleted_str = '';

		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}

		$q_dop = '';
		$dbprices = 'spr_priceprices';
		$link = 'pricelistitem.php?';

		//Выбираем всё из нулевого уровня
        $query = "SELECT * FROM `spr_equipment_groups` WHERE `parent_id` IS NULL ".$deleted_str." ORDER BY `name`";

		//Если не из корня смотрим, то выбираем всё, что в этой группе
		if ($first && ($level != 0) && ($type == 'list')){
			$query = "SELECT * FROM `spr_equipment_groups` WHERE `parent_id` ='{$level}' ".$deleted_str." ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rezult, $arr);
			}
		}
		//var_dump($rezult);

		//Выводим группу
		if (!empty($rezult)){

			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();

				if ($type == 'list'){
                    //var_dump($dbtable);
					//echo $space.$value['name'].'<br>';

					if ($value['parent_id'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
					}

                    //$style_name .= 'position: relative;';

					echo '
						<li style="border: none; position: relative;">
							<div class="" style="background-position: 0px 0px;"></div>
							<p class="" style="'.$style_name.'  padding-left: 10px; margin-bottom: 2px; border: 1px solid #CCC;">
								<b>
								    '.$value['name'].'
								</b>
							</p>';

                    if ($insure_id == 0) {
                        echo '
							<div style="position: absolute; top: 0; right: 3px;">
							   <a href="equipment_group.php?id=' . $value['id'] . '" class="ahref" style="font-weight: bold;" title="Открыть карточку группы">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>								    
							   </a>
								<div style="font-style: normal; font-size: 13px; display: inline-block;">
								    <div class="managePriceList">
                                        <a href="equipmentgroup_edit.php?id=' . $value['id'] . '" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать карточку группы"></i></a>
                                        <a href="add_equipment_item.php?addinid=' . $value['id'] . '" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
                                        <!--<a href="pricelistgroup_del.php?id=' . $value['id'] . '" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
                                        <a href="equipmentgroup_del.php?id=' . $value['id'] . '" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить эту группу"></i></a>
									</div>
								</div>
							</div>';
                    }

                    //var_dump($space);
					echo '
							<ul style="margin-left: '.$space.'px;">';


                    //Смотрим элементы в этой группе
//					$query = "SELECT * FROM `{$dbtable}` WHERE `parent_id`='{$value['id']}' ".$deleted_str." ".$q_dop." ORDER BY `name`";

					/*if ($insure_id != 0){
						$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `{$dbtable}` WHERE `item` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ".$q_dop.") ".$deleted_str." ORDER BY `name`";
					}*/

					//var_dump($query);

//                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
//
//					$number = mysqli_num_rows($res);
//					if ($number != 0){
//						while ($arr2 = mysqli_fetch_assoc($res)){
//							array_push($rez2, $arr2);
//						}
//						$items_j = $rez2;
//					}
//					//var_dump($items_j);
//
//					if (!empty($items_j)){
//
//						$anything_here = true;
//
//						for ($i = 0; $i < count($items_j); $i++) {
//
//						    //позиции
//							echo '
//										<li>
//											<table width="808px" style="margin: -2px -4px -3px 2.5em; ">
//												<tr>
//
//													<td width="408px" style="padding: 0 0 0 4px; border: 1px solid #CCC;">
//														<a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref" id="4filter">'.$items_j[$i]['name'].'</a>
//													</td>
//													<td width="200px" style="text-align: right; border: 1px solid #CCC;">
//														<i>'.$items_j[$i]['id'].'</i>
//													</td>
//                                                    <td width="200px" style="text-align: right; border: 1px solid #CCC;">
//                                                    	'.$items_j[$i]['serial_n'].'
//                                                   	</td>
//
//												</tr>
//											</table>
//										</li>';
//
//                            //позиции
//                           /* echo '
//										<li>
//											<div class="priceitem" style="border: none; margin: -2px -4px -3px 2.5em;">
//												<div class="cellsBlock2" style="width: 100%; margin: 0;">
//													<div class="cellText2" style="padding: 0 0 0 4px;">
//														<a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref" id="4filter">'.$items_j[$i]['name'].'</a>
//													</div>
//													<div class="cellName" style="text-align: right; border-left: 0; padding: 0 2px 0 4px;">
//														<i>'.$items_j[$i]['id'].'</i>
//													</div>
//                                                    <div class="cellName" style="text-align: right; border-left: 0; padding: 0 2px 0 4px;">
//                                                    	'.$items_j[$i]['serial_n'].'
//                                                   	</div >
//												</div>
//											</div>
//										</li>';*/
//						}
//					}else{
//						//
//					}

				}


				//Смотрим, есть ли в этой группе другие подгруппы
				$query = "SELECT * FROM `spr_equipment_groups` WHERE `parent_id`='{$value['id']}' ".$deleted_str."";
				//var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

				$number = mysqli_num_rows($res);

				if ($number != 0){
					//echo '_'.$value['name'].'<br>';
					//$space2 = $space;
					//var_dump($space2);
					$last_level2 = $last_level+1;
					showTree5($value['id'], $space, $type, $sel_id, TRUE, $last_level2, $deleted, $dbtable, $insure_id, $dtype);
					/*var_dump($value['id']);
					var_dump($space2);
					var_dump($type);
					var_dump($sel_id);
					var_dump($first);
					var_dump($last_level2);
                    var_dump($deleted);
					var_dump($dbtable);
					var_dump($insure_id);
					var_dump($dtype);*/

				}else{
					//---
				}

					echo '
							</ul>';
					echo '
						</li>';

			}
		}
	}








	
	
	//!!! не делал Обратное дерево
	function showReverseTree($level, $space, $type, $sel_id, $first, $last_level){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
						
		$arr = array();
		$rez = array();
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$level}' ORDER BY `name`";
		
		if ($first && ($level != 0) && ($type == 'list')){
			$query = "SELECT * FROM `spr_storagegroup` WHERE `id`='{$level}' ORDER BY `name`";
			$first = FALSE;
		}
		//var_dump ($query);
		
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$rezult = $rez;
		}else{
			$rezult = 0;
		}
		//var_dump($rezult);
		
		if ($rezult != 0){
			
			foreach ($rezult as $key => $value){

				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				if ($type == 'select'){
					//echo $space.$value['name'].'<br>';
					$selected = '';
					if ($value['id'] == $sel_id){
						$selected = ' selected';
					}
					echo '<option value="'.$value['id'].'" '.$selected.'>'.$space.$value['name'].'</option>';
				}
				
				if ($type == 'list'){
					//echo $space.$value['name'].'<br>';
					
					if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
						/*if ($this_level == 1){
							$style_name .= 'background-color: rgba(103, 251, 66, 0.5)';
						}elseif ($this_level == 2){
							$style_name .= 'background-color: rgba(97, 227, 255, 0.5);';
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}*/
					}
					
					echo '
						<li class="cellsBlock" style="width: auto;">
							<div class="cellPriority" style=""></div>
							<div class="cellOffice" style=" text-align: left; width: 350px; min-width: 350px; max-width: 350px; '.$style_name.'">
								<a href="pricelistgroup.php?id='.$value['id'].'" class="ahref" style="font-weight: bold;" id="4filter">'.$space.$value['name'].'</a>
							</div>
							<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px; '.$style_name.'">
								<div class="managePriceList" style="font-style: normal; font-size: 13px;">
									<a href="pricelistgroup_edit.php?id='.$value['id'].'" class="ahref"><i id="PriceListGroupEdit" class="fa fa-pencil-square-o pricemenu" aria-hidden="true" style="color: #777;" title="Редактировать"></i></a>
									<a href="add_pricelist_item.php?addinid='.$value['id'].'" class="ahref"><i id="PriceListGroupAdd" class="fa fa-plus pricemenu" aria-hidden="true" style="color: #36EA5E;" title="Добавить в эту группу"></i></a>
									<!--<a href="pricelistgroup_del.php?id='.$value['id'].'" class="ahref"><i id="" class="fa fa-bars pricemenu" aria-hidden="true" style="" title="Изменить порядок"></i></a>-->
									<a href="add_pricelist_item.php" class="ahref"><i id="PriceListGroupDelete" class="fa fa-trash pricemenu" aria-hidden="true" style="color: #FF3636" title="Удалить"></i></a>
								</div>
							</div>
						</li>';
						
					$query = "SELECT * FROM `spr_pricelist_template` WHERE `id` IN (SELECT `item` FROM `spr_itemsingroup` WHERE `group`='{$value['id']}') ORDER BY `name`";			
					//var_dump($query);
					
					$res = mysql_query($query) or die(mysql_error().' -> '.$query);	
					$number = mysql_num_rows($res);	
					if ($number != 0){
						while ($arr2 = mysql_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
						$items_j = $rez2;
					}else{
						$items_j = 0;
					}
					
					//var_dump($items_j);
					
					if ($items_j != 0){
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from`, `create_time` DESC LIMIT 1";
							//var_dump($query);
							
							$res = mysql_query($query) or die(mysql_error().' -> '.$query);

							$number = mysql_num_rows($res);
							if ($number != 0){
								$arr3 = mysql_fetch_assoc($res);
								$price = $arr3['price'];
							}else{
								$price = 0;
							}
					
							echo '
										<li class="cellsBlock" style="width: auto;">
											<div class="cellPriority" style=""></div>
											<a href="pricelistitem.php?id='.$items_j[$i]['id'].'" class="ahref cellOffice" style="text-align: left; width: 350px; min-width: 350px; max-width: 350px;" id="4filter">'.$items_j[$i]['name'].'</a>
											<div class="cellText" style="text-align: center; width: 150px; min-width: 150px; max-width: 150px;">'.$price.'</div>
										</li>';
						}
					}
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ORDER BY `name`";
				//var_dump($query);
				
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					//echo '_'.$value['name'].'<br>';
					$space2 = $space. '&nbsp;&nbsp;&nbsp;';
					$last_level2 = $last_level+1;
					//showTree($value['id'], $space2, $type, $sel_id, $first, $last_level2);
				}else{
					//var_dump ($color_index);
					//var_dump ($last_level);
					/*if ($color_index > $last_level){
						$color_index--;
					}*/
					//$space = substr($space, 0, -1);
					//echo '_'.$value['name'].'<br>';
				}
				//$space = substr($space, 0, -1);
			}
			//$color_index = $last_level;
		}
	}
	
	//для МКБ
	function showTree3 ($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id, $dtype){
		
		$arr = array();
		$mkb_rez = array();
		$rez = array();

		$mkb_avail_arr = array(
            "K00-K93",
            "K00-K14", "K00", "K01", "K02", "K03", "K04", "K05", "K06", "K07", "K08", "K09", "K10", "K11", "K12", "K13", "K14",
            "S00-T98",
            "S00-S09",
            "S02",
            "S03",
        );

		$parent_str = '';
		//global $rez_str;
		$rez_str = '';
		
		$style_name = '';
		$color_array = array(
			'background-color: rgba(255, 236, 24, 0.5);',
			'background-color: rgba(103, 251, 66, 0.5);',
			'background-color: rgba(97, 227, 255, 0.5);',
		);
		$color_index = $last_level;
		
		/*$deleted_str = '';
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}*/
		
		//Если первый проход
		if ($first){
			require 'config.php';
		
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
		}
		
		//определяем уровень для запроса
		if ($level == NULL){
			$parent_str = '`parent_id` IS NULL';
		}else{
			$parent_str = '`parent_id` = '.$level;
		}
		
		//берем верхний уровень
		$query = "SELECT * FROM `$dbtable` WHERE ".$parent_str;
		
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($mkb_rez, $arr);
			}
		}else{
			$mkb_rez = 0;
		}
		//var_dump($mkb_rez[0]);
		
		if ($first){
			$rez_str .= '	
				<div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
					<!--<span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>-->
				</div>';
			$rez_str .= '	
				<div style="/*width: 350px;*/ height: 492px; overflow: scroll; border: 1px solid #CCC;">
					<ul class="ul-tree ul-drop" id="lasttree">';
		}
		
		if ($mkb_rez != 0){
			foreach ($mkb_rez as $mkb_rez_value){
				if ((in_array($mkb_rez_value['code'], $mkb_avail_arr)) || ((in_array($mkb_rez_value['parent_code'], $mkb_avail_arr)) && ($mkb_rez_value['node_count'] == 0))) {
                    if ($mkb_rez_value['node_count'] > 0) {
                        $rez_str .= '	
						<li>
							<div class="drop" style="background-position: 0px 0px;"></div>
							<p onclick="checkMKBItem(' . $mkb_rez_value['id'] . ');"><b>' . $mkb_rez_value['code'] . '</b> ' . $mkb_rez_value['name'] . '</p>';

                        $rez_str .= '	
							<ul style="display: none;">';

                        $rez_str .= showTree3($mkb_rez_value['id'], '', 'list', 0, FALSE, 0, FALSE, 'spr_mkb', 0, 0);

                        $rez_str .= '	
							</ul>';
                        $rez_str .= '	
						</li>';

                    } else {
                        $rez_str .= '	
							<li>
								<p onclick="checkMKBItem(' . $mkb_rez_value['id'] . ');"><b>' . $mkb_rez_value['code'] . '</b> ' .  $mkb_rez_value['name'] . '</p>
							</li>';
                    }
                }
				//if ($type == 'list'){
					//echo $space.$value['name'].'<br>';
					
					//играемся с цветом	
					/*if ($value['level'] == 0) {
						$style_name = 'font-size: 130%;';
						$style_name .= $color_array[0];
						//$this_level = 0;
					}else{
						$style_name = 'font-size: 110%; font-style: oblique;';
						//$style_name .= 'background-color: rgba(97, 227, 255, 0.5)';
						if (isset($color_array[$color_index])){
							$style_name .= $color_array[$color_index];
						}else{
							$style_name .= 'background-color: rgba(225, 126, 255, 0.5);';
						}
					}*/
			}
		}
		
		if ($first){
			$rez_str .= '	
				</ul>
			</div>';
				mysql_close();
		}
		
		return $rez_str;
	}

	//Для контекстной менюшки для управления записью
    function contexMenuZapisMain ($zapisData, $filial, $office_j_arr, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, $main_zapis, $title_time, $title_client, $title_descr){
	    //$main_zapis - это определитель места, где отображаем это меню. true - в подробной записи, false - в основной
        //var_dump($zapisData);

        $start_time_h = floor($zapisData['start_time'] / 60);
        $start_time_m = $zapisData['start_time'] % 60;
        if ($start_time_m < 10) $start_time_m = '0' . $start_time_m;
        $end_time_h = floor(($zapisData['start_time'] + $zapisData['wt']) / 60);
        if ($end_time_h > 23) $end_time_h = $end_time_h - 24;
        $end_time_m = ($zapisData['start_time'] + $zapisData['wt']) % 60;
        if ($end_time_m < 10) $end_time_m = '0' . $end_time_m;


	    $rezult = '';

        $rezult .= '
               <ul id="zapis_options' . $zapisData['id'] . '" class="zapis_options" style="display: none;">';

        if ($filial != 0) {

            if ($filial == $zapisData['office']) {

                $smena = 0;

                if (($zapisData['start_time'] >= 540)  && ($zapisData['start_time'] < 900)){
                    $smena = 1;
                }
                if (($zapisData['start_time'] >= 900)  && ($zapisData['start_time'] < 1260)){
                    $smena = 2;
                }
                if (($zapisData['start_time'] >= 1260 )  && ($zapisData['start_time'] < 1440)){
                    $smena = 3;
                }

                if ($main_zapis){

                }else {
                    $rezult .=
                        '<li>
                            <div style="border: 1px dotted #F9FF00; background: rgba(0, 55, 255, 0.23); cursor: context-menu;">
                                '.$title_time.'<br><b>'.$title_client.'</b><br>'.$title_descr.'
                            </div>
                        </li>';
                }



                if ($zapisData['office'] != $zapisData['add_from']) {
                    if ($zapisData['enter'] != 8) {
                        $rezult .= '<li><div onclick="Ajax_TempZapis_edit_OK(' . $zapisData['id'] . ', ' . $zapisData['office'] . ')">Подтвердить</div></li>';
                    }
                }
                if ($zapisData['office'] == $zapisData['add_from']) {
                    if (($zapisData['enter'] != 8) && ($zapisData['enter'] != 9) && ($zapisData['enter'] != 6)) {
                        $rezult .=
                            '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 1)">Пришёл</div></li>';
                        $rezult .=
                            '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 9)">Не пришёл</div></li>';
                        $rezult .=
                            '<li><div onclick="ShowSettingsAddTempZapis(' . $zapisData['office'] . ', \'' . $office_j_arr[$zapisData['office']]['name'] . '\', ' . $zapisData['kab'] . ', ' . $year . ', '.$month.', '.$day.', '.$smena.', '.$zapisData['start_time'] . ', ' . $zapisData['wt'] . ', ' . $zapisData['worker'] . ', \'' . WriteSearchUser('spr_workers', $zapisData['worker'], 'user_full', false) . '\', \'' . WriteSearchUser('spr_clients', $zapisData['patient'], 'user_full', false) . '\', \'' . str_replace(array("\r", "\n"), " ", $zapisData['description']) . '\', ' . $zapisData['insured'] . ', ' . $zapisData['pervich'] . ', ' . $zapisData['noch'] . ', ' . $zapisData['id'] . ', ' . $zapisData['type'] . ', \'edit\')">Редактировать</div></li>';

                        //var_dump($zapisData['create_time']);
                        //var_dump($zapisData['description']);
                        //var_dump(time());

                        if (($zapisData['enter'] == 1) && ($finance_edit) && $main_zapis) {
                            $rezult .=
                                '<li>
                                                                    <div>
                                                                        <a href="invoice_add.php?client=' . $zapisData['patient'] . '&filial=' . $zapisData['office'] . '&date=' . strtotime($zapisData['day'] . '.' . $month . '.' . $zapisData['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $zapisData['id'] . '&worker=' . $zapisData['worker'] . '&type=' . $zapisData['type'] . '" class="ahref">
                                                                            Внести наряд
                                                                        </a>
                                                                    </div>
                                                                </li>';
                        }

                        $zapisDate = strtotime($zapisData['day'] . '.' . $zapisData['month'] . '.' . $zapisData['year']);
                        if (time() < $zapisDate + 60 * 60 * 24) {
                            $rezult .=
                                '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                        }
                    }
                    $rezult .= '
                                                            <li>
                                                                <div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 0)">
                                                                    Отменить все изменения
                                                                </div>
                                                            </li>';
                }
            } else {
                $rezult .=
                    '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 8)">Ошибка, удалить из записи</div></li>';
                $rezult .=
                    '<li><div onclick="Ajax_TempZapis_edit_Enter(' . $zapisData['id'] . ', 0)">Отменить все изменения</div></li>';
            }

            return $rezult;
        }

        //Дополнительное расширение прав на добавление посещений для специалистов, god_mode и управляющих
        if ($edit_options) {
            if ($zapisData['office'] == $zapisData['add_from']) {
                if ($zapisData['enter'] == 1) {
                    //var_dump($zapisData['type']);

                    if (($zapisData['type'] == 5) && $stom_edit && $main_zapis) {
                        $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="add_task_stomat.php?client=' . $zapisData['patient'] . '&filial=' . $zapisData['office'] . '&insured=' . $zapisData['insured'] . '&pervich=' . $zapisData['pervich'] . '&noch=' . $zapisData['noch'] . '&date=' . strtotime($zapisData['day'] . '.' . $month . '.' . $zapisData['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $zapisData['id'] . '&worker=' . $zapisData['worker'] . '" class="ahref">
                                                                    Внести Осмотр/Зубную формулу
                                                                </a>
                                                            </div>
                                                        </li>';
                    }
                    if (($zapisData['type'] == 6) && $cosm_edit && $main_zapis) {
                        $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="add_task_cosmet.php?client=' . $zapisData['patient'] . '&filial=' . $zapisData['office'] . '&insured=' . $zapisData['insured'] . '&pervich=' . $zapisData['pervich'] . '&noch=' . $zapisData['noch'] . '&date=' . strtotime($zapisData['day'] . '.' . $month . '.' . $zapisData['year'] . ' ' . $start_time_h . ':' . $start_time_m) . '&id=' . $zapisData['id'] . '&worker=' . $zapisData['worker'] . '" class="ahref">
                                                                    Внести посещение косм.
                                                                </a>
                                                            </div>
                                                        </li>';
                    }
                }
            } else {
                $rezult .= "&nbsp";
            }
            if ($upr_edit) {
                if (($zapisData['enter'] != 8) && ($zapisData['enter'] != 9) && ($zapisData['enter'] != 6) && $main_zapis){
                    $rezult .= '
                                                        <li>
                                                            <div>
                                                                <a href="edit_zapis_change_client.php?client_id=' . $zapisData['patient'] . '&zapis_id=' . $zapisData['id'] . '" class="ahref">
                                                                    Изменить пациента
                                                                </a>
                                                            </div>
                                                        </li>';
                }
            }
        }

        $rezult .= '</ul>';

        return $rezult;

    }

	function drawZapisDivVal ($cellZapisValue_TopSdvig, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, $contexMenuZapisMain){

        $rezult = '';

        $rezult .= '<div class="cellZapisVal" style="top: '.$cellZapisValue_TopSdvig.'px; height: '.$cellZapisValue_Height.'px; '.$back_color.'; text-align: left; padding: 2px;" 
        onclick="contextMenuShow('.$zapis_id.', 0, event, \'zapis_options\');">
            '.$title_time.'<br>
            
                <span style="font-weight:bold;">'.$title_client.'</span> : '.$title_descr.'';

        $rezult .= $contexMenuZapisMain;

        $rezult .= '</div>';

        return $rezult;
    }

    //Пагинатор
    function paginationCreate ($count_on_page, $page_number, $db, $file_name, $msql_cnnct, $dop){
        $paginator_str = '';
        $pages = 0;

        $rezult_str = '';
        $rezult = array();

        //Хочу получить общее количество
        $query = "SELECT COUNT(*) AS total_ids FROM `$db` $dop;";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $arr = mysqli_fetch_assoc($res);
            $total_ids = $arr['total_ids'];
        }else{
            $total_ids = 0;
        }

        if ($total_ids != 0) {

            $pages = (int)ceil($total_ids/$count_on_page);
            //var_dump($pages);

            if ($pages > 10){
                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';

                //next
                if ($page_number != 1) {
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number - 1) . '" class="paginator_btn" style=""><i class="fa fa-caret-left" aria-hidden="true"></i></a> ';
                }

                if (($page_number == 1) || ($page_number == 2) || ($page_number == $pages) || ($page_number == $pages-1)){
                    //1я
                    $paginator_str .= '<a href="'.$file_name.'?page=1" class="paginator_btn" style="';

                    if ($page_number == 1){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">1</a> ';

                    //2я
                    $paginator_str .= '<a href="'.$file_name.'?page=2" class="paginator_btn" style="';

                    if ($page_number == 2){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">2</a> ';

                    //3я
                    $paginator_str .= '<a href="'.$file_name.'?page=3" class="paginator_btn" style="';

                    if ($page_number == 3){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">3</a> ... ';

                    //Препредпоследняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages-2) . '" class="paginator_btn" style="';

                    if ($page_number == $pages-2){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages-2) . '</a>';
                    $paginator_str .= '</a> ';

                    //Предпоследняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages-1) . '" class="paginator_btn" style="';

                    if ($page_number == $pages-1){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages-1) . '</a>';
                    $paginator_str .= '</a> ';

                    //Последняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages) . '" class="paginator_btn" style="';

                    if ($page_number == $pages){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages) . '</a>';
                    $paginator_str .= '</a> ';
                }else {

                    //1я
                    $paginator_str .= '<a href="' . $file_name . '?page=1" class="paginator_btn" style="';
                    $paginator_str .= '">1</a> ';

                    if ($page_number - 1 != 2){
                        $paginator_str .= '... ';
                    }

                    //
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number - 1) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($page_number - 1) . '</a> ';

                    //
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($page_number) . '" class="paginator_btn" style="';
                    $paginator_str .= $pg_btn_bgcolor;
                    $paginator_str .= '">' . ($page_number) . '</a> ';

                    //
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number + 1) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($page_number + 1) . '</a> ';

                    if ($page_number+1 != $pages-1){
                        $paginator_str .= '... ';
                    }

                    //Последняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($pages) . '</a> ';

                }
                //next
                if ($page_number != $pages) {
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number + 1) . '" class="paginator_btn" style=""><i class="fa fa-caret-right" aria-hidden="true"></i></a> ';
                }

            }else {
                for ($i = 1; $i <= $pages; $i++) {
                    $pg_btn_bgcolor = '';
                    if (isset($_GET)) {
                        if (isset($page_number)) {
                            if ($page_number == $i) {
                                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                            }
                        } else {
                            if ($i == 1) {
                                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                            }
                        }
                    }
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($i) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($i) . '</a> ';
                }
            }
        }

        if ($pages > 1) {
            $rezult_str = '<div style="margin: 2px 6px 3px;">
						        <span style="font-size: 80%; color: rgb(0, 172, 237);">Перейти на страницу: </span>' . $paginator_str . '
						   </div>';
        }

        return $rezult_str;

    }

    //Вывод напоминаний
    function WriteNotes($notes, $worker_id, $option){
        require 'variables.php';

        $rez = '
            <div id="notes_change"></div>
            <div class="cellsBlock">';

        if (!empty($notes)){

            $rez .= '
                <ul class="live_filter" style="margin-left:6px;">
                    <li class="cellsBlock" style="font-weight:bold;">	
                        <div class="cellPriority" style="text-align: center"></div>
                        <div class="cellTime" style="text-align: center">Срок</div>
                        <div class="cellName" style="text-align: center">Пациент</div>
                        <div class="cellName" style="text-align: center">Посещение</div>
                        <div class="cellText" style="text-align: center">Описание</div>';
            if ($option) {
                $rez .= '
                        <div class="cellTime" style="text-align: center">Управление</div>';
            }
            $rez .= '
                        <div class="cellTime" style="text-align: center">Создано</div>
                        <div class="cellName" style="text-align: center">Автор</div>
                        <div class="cellTime" style="text-align: center">Закрыто</div>
                    </li>';
            for ($i = 0; $i < count($notes); $i++) {
                $dead_line_time = $notes[$i]['dead_line'] - time() ;
                if ($dead_line_time <= 0){
                    $priority_color = '#FF1F0F';
                }elseif (($dead_line_time > 0) && ($dead_line_time <= 2*24*60*60)){
                    $priority_color = '#FF9900';
                }elseif (($dead_line_time > 2*24*60*60) && ($dead_line_time <= 3*24*60*60)){
                    $priority_color = '#EFDF3F';
                }else{
                    $priority_color = '#FFF';
                }


                if ($notes[$i]['closed'] == 0){
                    $ended = 'Нет';
                    $background_style = '';
                    $background_style2 = '
                            background: rgba(231,55,71, 0.9);
                            color:#fff;
                            ';
                    if ($dead_line_time <= 0){
                        $background_style = '
                                background: rgba(239,23,63, 0.5);
                                background: -moz-linear-gradient(45deg, rgba(239,23,63, 1) 0%, rgba(231,55,39, 0.7) 33%, rgba(239,23,63, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
                                background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(239,23,63, 0.4)), color-stop(33%,rgba(231,55,39, 0.7)), color-stop(71%,rgba(239,23,63, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
                                background: -webkit-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                                background: -o-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                                background: -ms-linear-gradient(45deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                                background: linear-gradient(-135deg, rgba(239,23,63, 1) 0%,rgba(231,55,39, 0.7) 33%,rgba(239,23,63, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                    }
                }else{
                    $ended = 'Да';
                    $background_style = '
                            background: rgba(144,247,95, 0.5);
                            background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
                            background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
                            background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                    $background_style2 = 'background: rgba(144,247,95, 0.5);';
                }
                $rez .= '
                    <li class="cellsBlock cellsBlockHover">
                        <div class="cellPriority" style="background-color:'.$priority_color.'"></div>
                        <div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['dead_line']).'</div>
                        <div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients', $notes[$i]['client'], 'user', true).'</div>
                        <a href="task_stomat_inspection.php?id='.$notes[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$notes[$i]['task'].'</a>
                        <div class="cellText" style="'.$background_style.'">'.$for_notes[$notes[$i]['description']].'</div>';
                if ($option) {
                    $rez .= '
                        <div class="cellTime Change_notes_stomat" style="text-align: center;">';
                    if ($_SESSION['id'] == $notes[$i]['create_person']) {
                        if ($notes[$i]['closed'] != 1) {
                            if ($worker_id != 0) {
                                $rez .= '<a href="#" onclick="Change_notes_stomat(' . $notes[$i]['id'] . ', ' . $notes[$i]['description'] . ', ' . $worker_id . ' , $(this))">ред.</a>';
                            }
                            $rez .= '<a href="#" onclick="Close_notes_stomat(' . $notes[$i]['id'] . ', ' . $worker_id . ')">закр.</a>';
                        }
                    }
                    $rez .= '
                        </div>';
                }
                $rez .= ' 
                        <div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $notes[$i]['create_time']).'</div>
                        <div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$notes[$i]['create_person'], 'user', true).'</div>
                        <div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
                    </li>';
            }
            $rez .= '</ul>';
        }else{
        }
        $rez .= '</div>';

        return $rez;
    }

    //Вывод направлений
    function WriteRemoves($removes, $worker_id, $toMe, $option){
        include_once 'DBWork.php';

        $rez = '<div class="cellsBlock">';

        if (!empty($removes)){

            $rez .= '<br>';

            if ($toMe === 0) {
                $rez .= '';
            }else{
                if ($toMe) {
                    $rez .= 'Ко мне';
                } else {
                    $rez .= 'Мои';
                }
            }
            //$rez .= ' направления';

            $rez .= '
                                <ul class="live_filter" style="margin-left:6px;">
                                    <li class="cellsBlock" style="font-weight:bold;">	
                                        <div class="cellName" style="text-align: center">К кому</div>
                                        <div class="cellName" style="text-align: center">Пациент</div>
                                        <div class="cellName" style="text-align: center">Посещение</div>
                                        <div class="cellText" style="text-align: center">Описание</div>';
            if ($option) {
                $rez .= '
                                        <div class="cellTime" style="text-align: center">Управление</div>';
            }
            $rez .= '
                                        <div class="cellTime" style="text-align: center">Создано</div>
                                        <div class="cellName" style="text-align: center">Автор</div>
                                        <div class="cellTime" style="text-align: center">Закрыто</div>
                                    </li>';

            for ($i = 0; $i < count($removes); $i++) {
                if ($removes[$i]['closed'] == 0){
                    $ended = 'Нет';

                    $background_style = '
                            background: rgba(55,127,223, 0.5);
                            background: -moz-linear-gradient(45deg, rgba(55,127,223, 1) 0%, rgba(151,223,255, 0.7) 33%, rgba(55,127,223, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
                            background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(55,127,223, 0.4)), color-stop(33%,rgba(151,223,255, 0.7)), color-stop(71%,rgba(55,127,223, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
                            background: -webkit-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -o-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -ms-linear-gradient(45deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: linear-gradient(-135deg, rgba(55,127,223, 1) 0%,rgba(151,223,255, 0.7) 33%,rgba(55,127,223, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';

                    $background_style2 = '
                            background: rgba(231,55,71, 0.9);
                            color:#fff;';
                    if ($toMe === 0) {

                    }else{
                        if ($toMe) {
                        }else{
                            $background_style = '
                            background: rgba(255,255,71, 0.5);
                            background: -moz-linear-gradient(45deg, rgba(255,255,71, 1) 0%, rgba(255,255,157, 0.7) 33%, rgba(255,255,71, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
                            background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(255,255,71, 0.4)), color-stop(33%,rgba(255,255,157, 0.7)), color-stop(71%,rgba(255,255,71, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
                            background: -webkit-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -o-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -ms-linear-gradient(45deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: linear-gradient(-135deg, rgba(255,255,71, 1) 0%,rgba(255,255,157, 0.7) 33%,rgba(255,255,71, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                        }
                    }


                }else{
                    $ended = 'Да';
                    $background_style = '
                            background: rgba(144,247,95, 0.5);
                            background: -moz-linear-gradient(45deg, rgba(144,247,95, 1) 0%, rgba(55,215,119, 0.7) 33%, rgba(144,247,95, 0.4) 71%, rgba(255,255,255, 0.5) 91%);
                            background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(144,247,95, 0.4)), color-stop(33%,rgba(55,215,119, 0.7)), color-stop(71%,rgba(144,247,95, 0.6)), color-stop(91%,rgba(255,255,255, 0.5)));
                            background: -webkit-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -o-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: -ms-linear-gradient(45deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);
                            background: linear-gradient(-135deg, rgba(144,247,95, 1) 0%,rgba(55,215,119, 0.7) 33%,rgba(144,247,95, 0.4) 71%,rgba(255,255,255, 0.5) 91%);';
                    $background_style2 = '
                            background: rgba(144,247,95, 0.5);';
                }

                $rez .= '
                        <li class="cellsBlock cellsBlockHover">
                            <div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removes[$i]['whom'], 'user', true).'</div>
                            <div class="cellName" style="text-align: center">'.WriteSearchUser('spr_clients',$removes[$i]['client'], 'user', true).'</div>
                            <a href="task_stomat_inspection.php?id='.$removes[$i]['task'].'" class="ahref cellName" style="text-align: center">#'.$removes[$i]['task'].'</a>
                            <div class="cellText" style="'.$background_style.'">'.$removes[$i]['description'].'</div>';
                if ($option) {
                    if (($_SESSION['id'] == $removes[$i]['create_person']) || ($_SESSION['id'] == $removes[$i]['whom'])) {
                        $rez .= '
                            <div class="cellTime" style="text-align: center">
							    <a href="#" id="Close_removes_stomat" onclick="Close_removes_stomat(' . $removes[$i]['id'] . ', ' . $worker_id . ')">закр.</a>
							</div>';
                    }
                }
                $rez .= '
                            <div class="cellTime" style="text-align: center">'.date('d.m.y H:i', $removes[$i]['create_time']).'</div>
                            <div class="cellName" style="text-align: center">'.WriteSearchUser('spr_workers',$removes[$i]['create_person'], 'user', true).'</div>
                            <div class="cellTime" style="text-align: center; '.$background_style2.'">'.$ended.'</div>
                        </li>';
            }
            $rez .= '</ul>';
        }else{
            //echo '<h1>Нечего показывать.</h1>';
        }
        $rez .= '</div>';

        return $rez;
    }

    //Приводим месяц или день к виду 01 02 09 ...
    function dateTransformation ($data){
        //Взято отсюда http://www.cyberforum.ru/php-beginners/thread1460348.html
        //var_dump($data);

        if ((int)$data < 10) {
            if (strrpos($data, '0') === false) {
                $data = '0' . $data;
            }
        }
        if (strrpos($data, '0') !== false) {
            $data = join(array_unique(preg_split("//u", $data)));
        }
        //var_dump($month);

        return $data;
    }

    //функция формирует и показывает наряды визуализация
    function showInvoiceDivRezult($data, $minimal, $minimal_inline, $show_categories, $show_absent, $show_deleted, $only_debt){
        //$show_absent - сообщение если ничего нет
		//$only_debt - если полностью оплачены или оплата не требуется
    	//var_dump($data);

        $rezult = '';
        $rezult_deleted = '';

        $itemAll_str = '';
        $itemDelete_str = '';

        //Количество
        $rezult_count = 0;

        if (!empty($data)) {
        	//var_dump($data);

            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB ();

            if ($show_categories){
                //Категории процентов
            	$percent_cats_j = array();
            	//Для сортировки по названию
            	$percent_cats_j_names = array();
                //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
            	$query = "SELECT `id`, `name` FROM `fl_spr_percents`";
                //var_dump( $percent_cats_j);

				$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

				$number = mysqli_num_rows($res);
				if ($number != 0){
					while ($arr = mysqli_fetch_assoc($res)){
                        $percent_cats_j[$arr['id']] = $arr['name'];
                        //array_push($percent_cats_j_names, $arr['name']);
					}
				}

                //Определяющий массив из названий для сортировки
                /*foreach ($percent_cats_j as $key => $arr) {
                    array_push($percent_cats_j_names, $arr['name']);
                }*/

                //Сортируем по названию
                //array_multisort($percent_cats_j_names, SORT_LOCALE_STRING, $percent_cats_j);
                //var_dump( $percent_cats_j);

			}

            foreach ($data as $items) {
                //var_dump($items);

				if ($items['id'] != null) {
					//Отметка об объеме оплат
					$paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не оплачено"></i>';
					$status_mark = '<i class="fa fa-ban" aria-hidden="true" style="color: red; font-size: 110%;" title="Работа не закрыта"></i>';
					$calculate_mark = '<i class="fa fa-file" aria-hidden="true" style="color: red; font-size: 100%;" title="Нет расчётного листа"></i>';

					//Сумма рассчетных листов
					$calcSumm = 0;
					$refundSumm = 0;

					//Маркеры для статусов
					$paid_debt = false;
					$status_debt = false;
					$calculate_debt = false;
					$refund_exist = false;

					//Не оплачен
					if ($items['summ'] == $items['paid']) {
						//
					} else {
						$paid_debt = true;
					}

					//Работа закрыта
					if ($items['status'] == 5) {
						//
					} else {
						$status_debt = true;
					}

					//Расчетный лист
					$query = "SELECT SUM(`summ_inv`) AS `summCalcs` FROM `fl_journal_calculate` WHERE `invoice_id`='{$items['id']}'";
					//var_dump($query);

					$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

					$number = mysqli_num_rows($res);

					if ($number != 0) {
						$arr = mysqli_fetch_assoc($res);
						if ($arr['summCalcs'] != NULL) {
							$calcSumm = round($arr['summCalcs'], 2);
							//var_dump($arr);
						} else {
							$calculate_debt = true;
						}
					} else {
						//Отсутствуют РЛ
						$calculate_debt = true;
					}

					//Возвраты
					$query = "SELECT SUM(`summ`) AS `summRefund` FROM `fl_journal_refund` WHERE `invoice_id`='{$items['id']}'";
					//var_dump($query);

					$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

					$number = mysqli_num_rows($res);

					if ($number != 0) {
						$arr = mysqli_fetch_assoc($res);
						if ($arr['summRefund'] != NULL) {
							$refundSumm = round($arr['summRefund'], 2);
							$refund_exist = true;
							//var_dump($arr);
						}
					}

					//Если "нулевой наряд", то будем считать, что РЛ ему не нужен и статус закрыт у него автоматически должен быть
					if (($items['summ'] == $items['paid']) && ($items['summ'] == 0) && ($items['paid'] == 0) && ($items['summins'] == 0)) {
						//var_dump($items['summ']);
						//if ($only_debt) {
						//var_dump($items['summ']);
						$status_debt = false;
						$calculate_debt = false;
						//}
					}

					//Отметки
					if (!$paid_debt) {
						$paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Оплачено"></i>';
					}
					if (!$status_debt) {
						$status_mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Работа закрыта"></i>';
					}
					if (!$calculate_debt) {
						if ($calcSumm >= $items['summ']) {
							$calculate_mark = '<i class="fa fa-file" aria-hidden="true" style="color: darkgreen; font-size: 100%;" title="РЛ сделан"></i>';
						}
						if ($calcSumm < $items['summ']) {
							$calculate_mark = '<i class="fa fa-file" aria-hidden="true" style="color: rgba(255, 152, 0, 1); font-size: 110%;" title="Не вся сумма распределена по РЛ"></i>';
						}
					}

					$itemPercentCats_str = '';

					if (($only_debt && ($paid_debt || $status_debt || $calculate_debt || ($calcSumm < $items['summ']))) || (!$only_debt)) {

						//Покажем категории работ
						if ($show_categories) {
							$invoice_ex_j = array();
							$invoice_ex_j_temp = array();

							$query = "SELECT `percent_cats` FROM `journal_invoice_ex` WHERE `invoice_id`='{$items['id']}'";

							$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

							$number = mysqli_num_rows($res);

							if ($number != 0) {
								while ($arr = mysqli_fetch_assoc($res)) {
									array_push($invoice_ex_j, $arr);
								}
							}
							//var_dump($invoice_ex_j);

							if (!empty($invoice_ex_j)) {
								//var_dump($invoice_ex_j);

								foreach ($invoice_ex_j as $invoice_ex_item) {
									//var_dump($invoice_ex_item['percent_cats']);

									if ($invoice_ex_item['percent_cats'] == 0) {
										//--
									} else {
										if (!in_array($invoice_ex_item['percent_cats'], $invoice_ex_j_temp)) {
											$itemPercentCats_str .= '<i style="color: #041E35; font-size: 100%;">' . $percent_cats_j[$invoice_ex_item['percent_cats']] . '</i><br>';
											array_push($invoice_ex_j_temp, $invoice_ex_item['percent_cats']);
										}
									}
								}
							}
						}

						//Если отображение не минималичтичное
						if (!$minimal) {

							$rezult_count++;

							$itemTemp_str = '';

							$itemTemp_str .= '
												<li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36);">';
							$itemTemp_str .= '
													<a href="invoice.php?id=' . $items['id'] . '" class="cellOrder ahref" style="position: relative;">
														<div style="font-weight: bold;">Наряд #' . $items['id'] . '</div>
														<div style="margin: 3px;">';


							$itemTemp_str .= $itemPercentCats_str;


							$itemTemp_str .= '
														</div>
														<div style="font-size:80%; color: #555; border-top: 1px dashed rgb(179, 179, 179); margin-top: 5px;">';

							if (($items['create_time'] != 0) || ($items['create_person'] != 0)) {
								$itemTemp_str .= '
																Добавлен: ' . date('d.m.y H:i', strtotime($items['create_time'])) . '<br>
																<!--Автор: ' . WriteSearchUser('spr_workers', $items['create_person'], 'user', true) . '<br>-->';
							} else {
								$itemTemp_str .= 'Добавлен: не указано<br>';
							}
							if (($items['last_edit_time'] != 0) || ($items['last_edit_person'] != 0)) {
								$itemTemp_str .= '
																Редактировался: ' . date('d.m.y H:i', strtotime($items['last_edit_time'])) . '<br>
																<!--Кем: ' . WriteSearchUser('spr_workers', $items['last_edit_person'], 'user', true) . '-->';
							}

							$itemTemp_str .= '
														</div>';


							//Цвет если оплачено или нет
							$paycolor = "color: red;";
							if ($items['summ'] == $items['paid']) {
								$paycolor = 'color: #333333;';
							}

							$itemTemp_str .= '
														<span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . ' ' . $status_mark . ' ' . $calculate_mark . '</span>
													</a>
													<div class="cellName">
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Сумма:<br>
															<span class="calculateInvoice" style="font-size: 13px; ' . $paycolor . '">' . $items['summ'] . '</span> руб.
														</div>';
							if ($items['summins'] != 0) {
								$itemTemp_str .= '
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Страховка:<br>
															<span class="calculateInsInvoice" style="font-size: 13px">' . $items['summins'] . '</span> руб.
														</div>';
							}
							$itemTemp_str .= '
													</div>';

							$itemTemp_str .= '
													<div class="cellName">
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Оплачено:<br>
															<span class="calculateInvoice" style="font-weight: normal; font-size: 13px; color: #333;">' . $items['paid'] . '</span> руб.
														</div>';
							if ($items['summ'] != $items['paid']) {
								$itemTemp_str .= '
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Осталось <a href="payment_add.php?invoice_id=' . $items['id'] . '" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
															<span class="calculateInvoice" style="font-size: 13px">' . ($items['summ'] - $items['paid']) . '</span> руб.
														</div>';
							}

							if ($refund_exist) {
								$itemTemp_str .= '
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Возврат:<br>
															<span class="calculateInvoice" style="font-size: 13px">' . $refundSumm . '</span> руб.
														</div>';
							}

							$itemTemp_str .= '
													</div>
												</li>';

							if ($items['status'] != 9) {
								$itemAll_str .= $itemTemp_str;
							} else {
								$itemDelete_str .= $itemTemp_str;
							}
							//                        var_dump($itemTemp_str);
							//                        var_dump($itemDelete_str);
						}

						//Если минималистичное отображение
						if ($minimal) {

							if ($refund_exist) {
								$colorItem = 'background-color: rgba(255, 121, 121, 0.81);';
							} else {
								$colorItem = 'background-color: #FFF;';
							}

							$rezult_count++;

							$itemTemp_str = '';

							//Если минималистичное отображение и хотим отобразить всё в строку
							if ($minimal_inline) {
								$itemTemp_str .= '<div class="cellsBlockHover" style="background-color: rgb(255, 255, 255); display: inline-block; width: 140px; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36); margin-top: 1px; position: relative;">';
							} else {
								$itemTemp_str .= '<div class="cellsBlockHover" style="background-color: rgb(255, 255, 255); border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36); margin-top: 1px; position: relative;">';
							}

							$itemTemp_str .= '
															<a href="invoice.php?id=' . $items['id'] . '" class="ahref">
																<div>
																	<div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
																		<i class="fa fa-file-o" aria-hidden="true" style="' . $colorItem . ' text-shadow: none;"></i>
																	</div>
																	<div style="display: inline-block; vertical-align: middle;">
																		<i>#' . $items['id'] . '</i> <span style="font-size: 80%;"><!--от ' . date('d.m.y', strtotime($items['create_time'])) . '--></span>
																	</div>
																</div>
																<div style="margin: 3px;">';

							$itemTemp_str .= $itemPercentCats_str;

							$itemTemp_str .= '
																</div>
																<div>
																	<div style="border: 1px dotted #AAA; margin: 2px 2px; padding: 1px 3px; font-size: 10px">
																		<span class="calculateInvoice" style="font-size: 11px">' . $items['summ'] . '</span> руб.
																	</div>';
							if ($items['summins'] != 0) {
								$itemTemp_str .= '
																	<div style="border: 1px dotted #AAA; margin: 2px 2px; padding: 1px 3px; font-size: 10px">
																		Страховка:<br>
																		<span class="calculateInsInvoice" style="font-size: 11px">' . $items['summins'] . '</span> руб.
																	</div>';
							}


							if ($refund_exist) {
								$itemTemp_str .= '
														<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
															Возврат:<br>
															<span class="calculateInvoice" style="font-size: 13px">' . $refundSumm . '</span> руб.
														</div>';
							}
							$itemTemp_str .= '
																</div>
		
															</a>
															<span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . ' ' . $status_mark . ' ' . $calculate_mark . '</span>
														</div>';

							if ($items['status'] != 9) {
								$itemAll_str .= $itemTemp_str;
							} else {
								$itemDelete_str .= $itemTemp_str;
							}
							//var_dump($itemTemp_str);

						}
					}
				}
            }



            $rezult .= $itemAll_str;
            //var_dump($rezult);

            //Удалённые (если минималистичное, то не отображаем)
            if ($show_deleted && !$minimal){
                //if ((strlen($itemDelete_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                $rezult_deleted .= '<div id="invoices_deleted" style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                $rezult_deleted .= '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы наряды</li>';
                $rezult_deleted .= $itemDelete_str;
                $rezult_deleted .= '</div>';
                //}
                //$rezult .= $itemDelete_str;
            }


/*            $rezult .= $itemAll_str;
            if ($show_deleted && !$minimal){
                $rezult .= $itemDelete_str;
            }*/

            return array('data' => $rezult, 'data_deleted' => $rezult_deleted, 'count' => $rezult_count);

        }else{
        	if ($show_absent) {
                $rezult .= '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</i>';
            }

            return array('data' => $rezult, 'data_deleted' => $rezult_deleted, 'count' => 0);
        }



    }

    //функция формирует и показывает ордеры визуализация
    function showOrderDivRezult($data, $minimal, $show_absent, $show_deleted){

        $rezult = '';

        $itemAll_str = '';
        $itemClose_str = '';

        //Количество
        $rezult_count = 0;

        if (!empty($data)) {

            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB ();

            $offices_j = getAllFilials(false, false, true);
            //var_dump($offices_j);

            foreach ($data as $items) {
                //var_dump($items);

                $order_type_mark = '';

                if ($items['summ_type'] == 1){
                    $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал"></i>';
                }

                if ($items['summ_type'] == 2){
                    $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                }

                $itemTemp_str = '';

                $itemTemp_str .= '
                                            <li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(225, 255, 67, 0.69);">';
                $itemTemp_str .= '
                                                <a href="order.php?id='.$items['id'].'" class="cellOrder ahref" style="position: relative;">
                                                    <div style="font-weight: bold;">Ордер #'.$items['id'].'<span style="font-weight: normal;"> от '.date('d.m.y' ,strtotime($items['date_in'])).'</span></div>
                                                    <div style="margin: 3px;">';

                $itemTemp_str .= 'Филиал: '.$offices_j[$items['office_id']]['name'];

                $itemTemp_str .= '
                                                    </div>
                                                    <div style="font-size:80%;  color: #555;">';

                /*if (($items['create_time'] != 0) || ($items['create_person'] != 0)){
                    $itemTemp_str .= '
                                        Добавлен: '.date('d.m.y H:i' ,strtotime($items['create_time'])).'<br>
                                        <!--Автор: '.WriteSearchUser('spr_workers', $items['create_person'], 'user', true).'<br>-->';
                }else{
                    $itemTemp_str .= 'Добавлен: не указано<br>';
                }*/
                if (($items['last_edit_time'] != 0) || ($items['last_edit_person'] != 0)){
                    $itemTemp_str .= '
                                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($items['last_edit_time'])).'<br>
                                                            <!--Кем: '.WriteSearchUser('spr_workers', $items['last_edit_person'], 'user', true).'-->';
                }
                $itemTemp_str .= '
                                                    </div>
                                                    <span style="position: absolute; top: 2px; right: 3px;">'. $order_type_mark.'</span>
                                                </a>
                                                <div class="cellName">
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Сумма:<br>
                                                        <span class="calculateOrder" style="font-size: 13px">'.$items['summ'].'</span> руб.
                                                    </div>';
                /*if ($items['summins'] != 0){
                    echo '
                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                Страховка:<br>
                                <span class="calculateInsInvoice" style="font-size: 13px">'.$items['summins'].'</span> руб.
                            </div>';
                }*/
                $itemTemp_str .= '
                                                </div>';
                $itemTemp_str .= '
                                            </li>';

                if ($items['status'] != 9) {
                    $itemAll_str .= $itemTemp_str;
                } else {
                    $itemClose_str .= $itemTemp_str;
                }

            }

            /*if (strlen($orderAll_str) > 1){
                echo $orderAll_str;
            }else{
                echo '<li style="font-size: 75%; color: #7D7D7D; margin-bottom: 20px; color: red;">Нет ордеров</li>';
            }

            //Удалённые
            if ((strlen($orderClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                echo '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                echo '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                echo $orderClose_str;
                echo '</div>';
            }*/

            $rezult .= $itemAll_str;

            if ($show_deleted && !$minimal){
                //if ((strlen($itemClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
                    $rezult .= '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
                    $rezult .= '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
                    $rezult .= $itemClose_str;
                    $rezult .= '</div>';
                //}
                //$rezult .= $itemClose_str;
            }

            return array('data' => $rezult, 'count' => $rezult_count);

        }else{
            if ($show_absent) {
                $rezult .= '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет ордеров</i>';
            }

            return array('data' => $rezult, 'count' => 1);
        }
    }


    //функция формирует и показывает возвраты визуализация
    function showWithdrawDivRezult($data, $minimal, $show_absent, $show_deleted){

        $rezult = '';

        $itemAll_str = '';
        $itemClose_str = '';

        //Количество
        $rezult_count = 0;

        if (!empty($data)) {

            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB ();

            $offices_j = getAllFilials(false, false, true);
            //var_dump($offices_j);

            foreach ($data as $items) {
                //var_dump($items);

                $order_type_mark = '';

                if ($items['summ_type'] == 1){
                    $order_type_mark = '<i class="fa fa-money" aria-hidden="true" title="Нал"></i>';
                }

                if ($items['summ_type'] == 2){
                    $order_type_mark = '<i class="fa fa-credit-card" aria-hidden="true" title="Безнал"></i>';
                }

                $itemTemp_str = '';

                $itemTemp_str .= '
                                            <li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(255, 94, 67, 0.5);">';
                $itemTemp_str .= '
                                                <a href="withdraw.php?id='.$items['id'].'" class="cellOrder ahref" style="position: relative;">
                                                    <div style="font-weight: bold;">Выдача #'.$items['id'].'<span style="font-weight: normal;"> от '.date('d.m.y' ,strtotime($items['date_in'])).'</span></div>
                                                    <div style="margin: 3px;">';

                $itemTemp_str .= 'Филиал: '.$offices_j[$items['office_id']]['name'];

                $itemTemp_str .= '
                                                    </div>
                                                    <div style="font-size:80%;  color: #555;">';

                if (($items['last_edit_time'] != 0) || ($items['last_edit_person'] != 0)){
                    $itemTemp_str .= '
                                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($items['last_edit_time'])).'<br>
                                                            <!--Кем: '.WriteSearchUser('spr_workers', $items['last_edit_person'], 'user', true).'-->';
                }
                $itemTemp_str .= '
                                                    </div>
                                                    <span style="position: absolute; top: 2px; right: 3px;">'. $order_type_mark.'</span>
                                                </a>
                                                <div class="cellName">
                                                    <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                        Сумма:<br>
                                                        <span class="calculateInvoice" style="font-size: 13px">'.$items['summ'].'</span> руб.
                                                    </div>';

                $itemTemp_str .= '
                                                </div>';
                $itemTemp_str .= '
                                            </li>';

                if ($items['status'] != 9) {
                    $itemAll_str .= $itemTemp_str;
                } else {
                    $itemClose_str .= $itemTemp_str;
                }

            }

            $rezult .= $itemAll_str;

//            if ($show_deleted && !$minimal){
//                //if ((strlen($itemClose_str) > 1) && (($finances['see_all'] != 0) || $god_mode)) {
//                    $rezult .= '<div style="background-color: rgba(255, 214, 240, 0.5); padding: 5px; margin-top: 5px;">';
//                    $rezult .= '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px; height: 30px; ">Удалённые из программы ордеры</li>';
//                    $rezult .= $itemClose_str;
//                    $rezult .= '</div>';
//                //}
//                //$rezult .= $itemClose_str;
//            }

            return array('data' => $rezult, 'count' => $rezult_count);

        }else{
            if ($show_absent) {
                $rezult .= '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Не было выдач</i>';
            }

            return array('data' => $rezult, 'count' => 1);
        }
    }


/*    //функция формирует и показывает расчетные листы визуализация
    function showCalculateDivRezult($data){

        $rezult = '';

        $itemAll_str = '';
        $itemClose_str = '';

        //Количество
        $rezult_count = 0;

        if (!empty($data)) {

            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            $msql_cnnct = ConnectToDB ();

            if ($show_categories){
                //Категории процентов
                $percent_cats_j = array();
                //Для сортировки по названию
                $percent_cats_j_names = array();
                //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
                $query = "SELECT `id`, `name` FROM `fl_spr_percents`";
                //var_dump( $percent_cats_j);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        $percent_cats_j[$arr['id']] = $arr['name'];
                        //array_push($percent_cats_j_names, $arr['name']);
                    }
                }

                //Определяющий массив из названий для сортировки
                /*foreach ($percent_cats_j as $key => $arr) {
                    array_push($percent_cats_j_names, $arr['name']);
                }*/

                //Сортируем по названию
                //array_multisort($percent_cats_j_names, SORT_LOCALE_STRING, $percent_cats_j);
                //var_dump( $percent_cats_j);

            /*}

            foreach ($data as $items) {
                //var_dump($items);

                //Отметка об объеме оплат
                $paid_mark = '<i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;" title="Не оплачено"></i>';
                $status_mark = '<i class="fa fa-ban" aria-hidden="true" style="color: red; font-size: 110%;" title="Работа не закрыта"></i>';
                $calculate_mark = '<i class="fa fa-file" aria-hidden="true" style="color: red; font-size: 100%;" title="Нет расчётного листа"></i>';

                //Маркеры для статусов
                $paid_debt = false;
                $status_debt = false;
                $calculate_debt = false;

                //Не оплачен
                if ($items['summ'] == $items['paid']) {
                    //
                }else{
                    $paid_debt = true;
                }

                //Работа закрыта
                if ($items['status'] == 5) {
                    //
                }else{
                    $status_debt = true;
                }

                //Расчетный лист
                $query = "SELECT * FROM `fl_journal_calculate` WHERE `invoice_id`='{$items['id']}' LIMIT 1";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    //
                }else{
                    $calculate_debt = true;
                }

                //Если "нулевой наряд", то будем считать, что РЛ ему не нужен и статус закрыт у него автоматически должен быть
                if (($items['summ'] == $items['paid']) && ($items['summ'] == 0) && ($items['paid'] == 0) && ($items['summins'] == 0)){
                    if ($only_debt) {
                        $status_debt = false;
                        $calculate_debt = false;
                    }
                }


                if (!$paid_debt){
                    $paid_mark = '<i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Оплачено"></i>';
                }
                if (!$status_debt) {
                    $status_mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color: darkgreen; font-size: 110%;" title="Работа закрыта"></i>';
                }
                if (!$calculate_debt) {
                    $calculate_mark = '<i class="fa fa-file" aria-hidden="true" style="color: darkgreen; font-size: 100%;" title="РЛ сделан"></i>';
                }


                $itemPercentCats_str = '';

                if (($only_debt && ($paid_debt || $status_debt || $calculate_debt)) || (!$only_debt)) {

                    //Покажем категории работ
                    if ($show_categories) {
                        $invoice_ex_j = array();
                        $invoice_ex_j_temp = array();

                        $query = "SELECT `percent_cats` FROM `journal_invoice_ex` WHERE `invoice_id`='{$items['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($invoice_ex_j, $arr);
                            }
                        }
                        //var_dump($invoice_ex_j);

                        if (!empty($invoice_ex_j)) {
                            //var_dump($invoice_ex_j);

                            foreach ($invoice_ex_j as $invoice_ex_item) {
                                //var_dump($invoice_ex_item['percent_cats']);

                                if ($invoice_ex_item['percent_cats'] == 0) {
                                    //--
                                } else {
                                    if (!in_array($invoice_ex_item['percent_cats'], $invoice_ex_j_temp)) {
                                        $itemPercentCats_str .= '<i style="color: #041E35; font-size: 100%;">' . $percent_cats_j[$invoice_ex_item['percent_cats']] . '</i><br>';
                                        array_push($invoice_ex_j_temp, $invoice_ex_item['percent_cats']);
                                    }
                                }
                            }
                        }
                    }

                    if (!$minimal) {

                        $rezult_count++;

                        $itemTemp_str = '';

                        $itemTemp_str .= '
                                                    <li class="cellsBlock" style="width: auto; border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36);">';
                        $itemTemp_str .= '
                                                        <a href="invoice.php?id=' . $items['id'] . '" class="cellOrder ahref" style="position: relative;">
                                                            <div style="font-weight: bold;">Наряд #' . $items['id'] . '</div>
                                                            <div style="margin: 3px;">';


                        $itemTemp_str .= $itemPercentCats_str;


                        $itemTemp_str .= '
                                                            </div>
                                                            <div style="font-size:80%; color: #555; border-top: 1px dashed rgb(179, 179, 179); margin-top: 5px;">';

                        if (($items['create_time'] != 0) || ($items['create_person'] != 0)) {
                            $itemTemp_str .= '
                                                                    Добавлен: ' . date('d.m.y H:i', strtotime($items['create_time'])) . '<br>
                                                                    <!--Автор: ' . WriteSearchUser('spr_workers', $items['create_person'], 'user', true) . '<br>-->';
                        } else {
                            $itemTemp_str .= 'Добавлен: не указано<br>';
                        }
                        if (($items['last_edit_time'] != 0) || ($items['last_edit_person'] != 0)) {
                            $itemTemp_str .= '
                                                                    Редактировался: ' . date('d.m.y H:i', strtotime($items['last_edit_time'])) . '<br>
                                                                    <!--Кем: ' . WriteSearchUser('spr_workers', $items['last_edit_person'], 'user', true) . '-->';
                        }

                        $itemTemp_str .= '
                                                            </div>';


                        //Цвет если оплачено или нет
                        $paycolor = "color: red;";
                        if ($items['summ'] == $items['paid']) {
                            $paycolor = 'color: #333333;';
                        }

                        $itemTemp_str .= '
                                                            <span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . ' ' . $status_mark . ' ' . $calculate_mark . '</span>
                                                        </a>
                                                        <div class="cellName">
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Сумма:<br>
                                                                <span class="calculateInvoice" style="font-size: 13px; ' . $paycolor . '">' . $items['summ'] . '</span> руб.
                                                            </div>';
                        if ($items['summins'] != 0) {
                            $itemTemp_str .= '
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Страховка:<br>
                                                                <span class="calculateInsInvoice" style="font-size: 13px">' . $items['summins'] . '</span> руб.
                                                            </div>';
                        }
                        $itemTemp_str .= '
                                                        </div>';

                        $itemTemp_str .= '
                                                        <div class="cellName">
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Оплачено:<br>
                                                                <span class="calculateInvoice" style="font-weight: normal; font-size: 13px; color: #333;">' . $items['paid'] . '</span> руб.
                                                            </div>';
                        if ($items['summ'] != $items['paid']) {
                            $itemTemp_str .= '
                                                            <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">
                                                                Осталось <a href="payment_add.php?invoice_id=' . $items['id'] . '" class="ahref">внести <i class="fa fa-thumb-tack" aria-hidden="true"></i></a><br>
                                                                <span class="calculateInvoice" style="font-size: 13px">' . ($items['summ'] - $items['paid']) . '</span> руб.
                                                            </div>';
                        }

                        $itemTemp_str .= '
                                                        </div>
                                                    </li>';

                        if ($items['status'] != 9) {
                            $itemAll_str .= $itemTemp_str;
                        } else {
                            $itemClose_str .= $itemTemp_str;
                        }
                    }

                    if ($minimal) {

                        $rezult_count++;

                        $rezult .= '
                                                            <div class="cellsBlockHover" style=" border: 1px solid rgba(165, 158, 158, 0.92); box-shadow: -2px 2px 9px 1px rgba(67, 160, 255, 0.36); margin-top: 1px; position: relative;">
                                                                <a href="invoice.php?id=' . $items['id'] . '" class="ahref">
                                                                    <div>
                                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                                        </div>
                                                                        <div style="display: inline-block; vertical-align: middle;">
                                                                            <i>#' . $items['id'] . '</i> <span style="font-size: 80%;"><!--от ' . date('d.m.y', strtotime($items['create_time'])) . '--></span>
                                                                        </div>
                                                                    </div>
                                                                    <div style="margin: 3px;">';

                        $rezult .= $itemPercentCats_str;

                        $rezult .= '
                                                                    </div>
                                                                    <div>
                                                                        <div style="border: 1px dotted #AAA; margin: 2px 2px; padding: 1px 3px; font-size: 10px">
                                                                            <span class="calculateInvoice" style="font-size: 11px">' . $items['summ'] . '</span> руб.
                                                                        </div>';
                        if ($items['summins'] != 0) {
                            $rezult .= '
                                                                        <div style="border: 1px dotted #AAA; margin: 2px 2px; padding: 1px 3px; font-size: 10px">
                                                                            Страховка:<br>
                                                                            <span class="calculateInsInvoice" style="font-size: 11px">' . $items['summins'] . '</span> руб.
                                                                        </div>';
                        }
                        $rezult .= '
                                                                    </div>
            
                                                                </a>
                                                                <span style="position: absolute; top: 2px; right: 3px;">' . $paid_mark . ' ' . $status_mark . ' ' . $calculate_mark . '</span>
                                                            </div>';
                    }
                }
            }


            $rezult .= $itemAll_str;
            if ($show_deleted && !$minimal){
                $rezult .= $itemClose_str;
            }

            return array('data' => $rezult, 'count' => $rezult_count);

        }else{
            if ($show_absent) {
                $rezult .= '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</i>';
            }

            return array('data' => $rezult, 'count' => 1);
        }
    }*/

	function prepareDrawZapisDay($zapis, $start, $end, $worker_id, $filials_j, $filial_id, $kab, $year, $month, $day, $type, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit){
		//var_dump($zapis);

		$rezult = '';

        $NextTime = FALSE;
        $ThatTimeFree = TRUE;
        $PeredannNextTime = FALSE;
        $NextTime_val = 0;
        //сдвиг для блоков времени
        $cellZapisTime_TopSdvig = 0;
        $cellZapisValue_TopSdvig = 0;
        $PrevZapis = array();
        $NextFill = FALSE;

        $weHaveZapisHere = FALSE;
        $next_smena = FALSE;

		//Филиал в сессии
        if (isset($_SESSION['filial'])) {
            $contexMenuZapisMain_filial = $_SESSION['filial'];
        }else{
            $contexMenuZapisMain_filial = 0;
        }

        //Если назначен врач в смену в этот кабинет
        if ($worker_id != 0) {
            $bg_color = '';
        } else {
            //Если нет
            $bg_color = ' background-color: #f0f0f0;';
        }

        //массив для времен начала записей
        $time_start_arr = array();

        //Если в этом кабинете есть запись
        if (isset($zapis[$kab])){
            //var_dump($zapis[$kab]);
            //var_dump(array_keys($zapis[$kab]));

			$time_start_arr = array_keys($zapis[$kab]);
        }

		//Проходим про смене через каждые полчаса
		for ($wt=$start; $wt < $end; $wt=$wt+30) {

            $back_color = '';

            if (!empty($time_start_arr)) {
                //Проходим про всем временам существующих записей
                foreach ($time_start_arr as $time_start_item) {
                	//Если время начала работы совпадает с расчетным начало (1 раз в полчаса)
                    if ($time_start_item == $wt){
                        //var_dump($zapis[$kab][$time_start_item]);

						$weHaveZapisHere = TRUE;
                        $time_start_item_have_zapis = $time_start_item;
					}
					//Если время начала работы позже расчетного начала (1 раз в полчаса) но меньше следующей расчетной точки (через полчаса)
					if (($time_start_item > $wt) && ($time_start_item < $wt+30)){
                        //var_dump($zapis[$kab][$time_start_item]);

                        $weHaveZapisHere = TRUE;
                        $time_start_item_have_zapis = $time_start_item;
					}


				}
				//Если есть запись
				if ($weHaveZapisHere){
                    //вычисляем время начала приёма
                    $TempStartWorkTime_h = floor($zapis[$kab][$time_start_item_have_zapis]['start_time']/60);
                    $TempStartWorkTime_m = $zapis[$kab][$time_start_item_have_zapis]['start_time']%60;
                    if ($TempStartWorkTime_m < 10) $TempStartWorkTime_m = '0'.$TempStartWorkTime_m;

                    //вычисляем время окончания приёма
                    $TempEndWorkTime_h = floor(($zapis[$kab][$time_start_item_have_zapis]['start_time']+$zapis[$kab][$time_start_item_have_zapis]['wt'])/60);
                    if ($TempEndWorkTime_h > 23) $TempEndWorkTime_h = $TempEndWorkTime_h - 24;
                    $TempEndWorkTime_m = ($zapis[$kab][$time_start_item_have_zapis]['start_time']+$zapis[$kab][$time_start_item_have_zapis]['wt'])%60;
                    if ($TempEndWorkTime_m < 10) $TempEndWorkTime_m = '0'.$TempEndWorkTime_m;

                    //Сдвиг для блока
                    $cellZapisValue_TopSdvig = (floor(($zapis[$kab][$time_start_item_have_zapis]['start_time']-$end)/30)*60 + ($zapis[$kab][$time_start_item_have_zapis]['start_time']-$start)%30*2);
                    //Высота блока
                    $cellZapisValue_Height = $zapis[$kab][$time_start_item_have_zapis]['wt']*2;

                    //Если время выполнения работы больше чем осталось до конца смены
                    if ($zapis[$kab][$time_start_item_have_zapis]['start_time'] + $zapis[$kab][$time_start_item_have_zapis]['wt'] > $end){
                        //var_dump($zapis[$kab][$time_start_item_have_zapis]['start_time']);
                        //var_dump($zapis[$kab][$time_start_item_have_zapis]['wt']);
                        //var_dump($end);

                        $cellZapisValue_Height = ($end - $zapis[$kab][$time_start_item_have_zapis]['start_time'])*2;

                        $next_smena = TRUE;
					}


                    //Если пришёл
                    if ($zapis[$kab][$time_start_item_have_zapis]['enter'] == 1){
                        $back_color = 'background-color: rgba(119, 255, 135, 1);';
                    }else{
                        //Если оформлено не на этом филиале
                        if($zapis[$kab][$time_start_item_have_zapis]['office'] != $zapis[$kab][$time_start_item_have_zapis]['add_from']){
                            $back_color = 'background-color: rgb(119, 255, 250);';
                        }
                    }

                    $title_time = $TempStartWorkTime_h.':'.$TempStartWorkTime_m.' - '.$TempEndWorkTime_h.':'.$TempEndWorkTime_m;
                    $title_client = WriteSearchUser('spr_clients', $zapis[$kab][$time_start_item_have_zapis]['patient'], 'user', false);
                    $title_descr = $zapis[$kab][$time_start_item_have_zapis]['description'];
                    $zapis_id = $zapis[$kab][$time_start_item_have_zapis]['id'];

                    echo drawZapisDivVal ($cellZapisValue_TopSdvig+720, $cellZapisValue_Height, $back_color, $title_time, $title_client, $title_descr, $zapis_id, contexMenuZapisMain ($zapis[$kab][$time_start_item_have_zapis], $contexMenuZapisMain_filial, $filials_j, $year, $month, $day, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, false, $title_time, $title_client, $title_descr));
                }
            }

            $cellZapisTime_TopSdvig = $cellZapisTime_TopSdvig + 60;

            //Если нет записи тут вообще
            $wt_FreeSpace = 30;
            $wt_start_FreeSpace = $wt;
            $cellZapisFreeSpace_Height = $wt_FreeSpace*2;
            $cellZapisFreeSpace_TopSdvig = ($wt_start_FreeSpace-$start)*2;

            $rezult .= '
												<div class="cellZapisFreeSpace" style="top: '.$cellZapisFreeSpace_TopSdvig.'px; height: '.$cellZapisFreeSpace_Height.'px; '.$bg_color.'" onclick="ShowSettingsAddTempZapis('.$filial_id.', \''.$filial_id.'\', '.$kab.', '.$year.', '.$month.','.$day.', 1, '.$wt_start_FreeSpace.', '.$wt_FreeSpace.', '.$worker_id.', \''.WriteSearchUser('spr_workers', $worker_id, 'user_full', false).'\', \'\', \'\', 0, 0, 0, 0, '.$type.', \'add\')">';
            $rezult .= '
												</div>';
        }

		return $rezult;
	}

	//Тестовая функция для определения типа сотрудников по GET-запросу
	function returnGetWho ($who, $who_default, $need_arr){

		$result  = array(
			0 => array(
                'who' => '',
                'whose' => 'Все ',
                'selected_stom' => ' selected',
                'selected_cosm' => ' ',
                'datatable' => 'scheduler_stom',
                'kabsForDoctor' => 'stom',
                'type' => 0,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => 'background-color: #fff261;'
			),
            5 => array(
            	'who' => '&who=5',
                'whose' => 'Стоматологи ',
                'selected_stom' => ' selected',
                'selected_cosm' => ' ',
                'datatable' => 'scheduler_stom',
                'kabsForDoctor' => 'stom',
                'type' => 5,

                'stom_color' => 'background-color: #fff261;',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            6 => array(
                'who' => '&who=6',
                'whose' => 'Косметологи ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_cosm',
                'kabsForDoctor' => 'cosm',
                'type' => 6,

                'stom_color' => '',
                'cosm_color' => 'background-color: #fff261;',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            10 => array(
                'who' => '&who=10',
                'whose' => 'Специалистов ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 10,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => 'background-color: #fff261;',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            4 => array(
                'who' => '&who=4',
                'whose' => 'Администраторов ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 4,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => 'background-color: #fff261;',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            7 => array(
                'who' => '&who=7',
                'whose' => 'Ассистенты ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 7,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => 'background-color: #fff261;',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            13 => array(
                'who' => '&who=13',
                'whose' => 'Санитарки ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 13,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => 'background-color: #fff261;',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            14 => array(
                'who' => '&who=14',
                'whose' => 'Уборщицы ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 14,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => 'background-color: #fff261;',
                'dvornik_color' => '',
                'other_color' => '',
                'all_color' => ''
			),
            15 => array(
                'who' => '&who=15',
                'whose' => 'Дворники ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 15,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => 'background-color: #fff261;',
                'other_color' => '',
                'all_color' => ''
			),
            11 => array(
                'who' => '&who=11',
                'whose' => 'Прочее ',
                'selected_stom' => ' ',
                'selected_cosm' => ' selected',
                'datatable' => 'scheduler_somat',
                'kabsForDoctor' => 'somat',
                'type' => 11,

                'stom_color' => '',
                'cosm_color' => '',
                'somat_color' => '',
                'admin_color' => '',
                'assist_color' => '',
                'sanit_color' => '',
                'ubor_color' => '',
                'dvornik_color' => '',
                'other_color' => 'background-color: #fff261;',
                'all_color' => ''
			)
		);

		if (isset($result[$who])) {
//			var_dump($who);
//			var_dump($need_arr);
//			var_dump(in_array($who, $need_arr));

			if (in_array($who, $need_arr)) {
                return $result[$who];
            }else{
                return $result[$who_default];
			}
        }else{
            return $result[$who_default];
		}
	}

	//Функция возвращает, сколько денег с какого филиала надо будет снять при выплате ЗП
	function returnTabelFilialPaidouts ($tabel_id){
        $tabel_j = array();

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();

        $invoices_j = array();

        //Итоговый массив, куда соберем общие суммы по филиалам
        $itog_filials_summ = array();
        //Итоговый массив, куда соберем суммы по филиалам по тем работам,
        // которые мы хотим и должны оплатить другому человеку
        $itog_filials_summ_not4tou = array();
        //Массив с процентами по филиалам
        $itog_filials_percents = array();
        //Массив с процентами по филиалам TEMP
        $itog_filials_percents_temp = array();
        //Массив остатков денег после выдачи
        $itog_filials_summ_ostatok = array();
        //Наряды, которые на момент выдачи были не закрыты, но мы по ним делали РЛ и выдавали ЗП
        $opened_invoices = array();

        //Табель
        $query = "SELECT * FROM `fl_journal_tabels` WHERE `id` = '{$tabel_id}' LIMIT 1";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $arr = mysqli_fetch_assoc($res);

            $tabel_j = $arr;
        }
        var_dump($tabel_j);

        $worker_id = $tabel_j['worker_id'];

        //Наряды
        $query = "
            SELECT jcalc.invoice_id, ji.office_id AS filial_id, ji.status AS status
            FROM `fl_journal_calculate` jcalc
            LEFT JOIN `fl_journal_tabels_ex` jtabex ON jtabex.tabel_id = '{$tabel_id }'
            LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
            WHERE jtabex.calculate_id = jcalc.id
            GROUP BY jcalc.invoice_id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                $invoices_j[$arr['invoice_id']]['filial_id'] = $arr['filial_id'];
                $invoices_j[$arr['invoice_id']]['status'] = $arr['status'];
            }
        }

        echo '

    <div id="fact"></div>
    
    
    <span style="font-size: 85%;">Наряды (Филиал / статус)</span><br>';
        //var_dump($invoices_j);

        foreach ($invoices_j as $invoice_id => $invoice_item){
            //($invoice_item['status'] == 5)  - работа закрыта

            echo '<div style="width: 190px; margin-bottom: 5px; border: 1px solid rgba(191, 188, 181, 0.53);">';

            //Рисуем кнопку-ссылку на наряд
            echo '
            <div style="margin: 2px 0;">
                <a href="invoice.php?id='.$invoice_id.'" class="ahref button_tiny" style="margin: 0 3px; font-size: 90%;">
                    '.$invoice_id.' ('.$filials_j[$invoice_item['filial_id']]['name2'].' / '.$invoice_item['status'].')
                </a>';
            if ($invoice_item['status'] == 5){
                echo '<i class="fa fa-plus" style="color: green; font-size: 120%;"></i>';
            }else{
                echo '<i class="fa fa-minus" style="color: red; font-size: 120%;"></i>';
                //Добавим наряды, которые не закрыты
                array_push($opened_invoices, $invoice_item);
            }
            echo '
            </div>';

            //Позиции, которые прошли как подарки пациентам,
            //но нам же надо дать ЗП за них,
            //НЕ разбитые по филиалам, на которых был сделан наряд
            $gift_invoice_ex = array();
            //Сумма позиций с подарками, НЕ разбитые по филиалам, на которых был сделан наряд
            $gift_invoice_summ = 0;

            //Получаем позиции, которые прошли как подарки пациентам
            $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `gift` = '1'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //var_dump($arr);

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                        $itog_filials_summ[$invoice_item['filial_id']] = 0;
                    }
                    $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];


//                array_push($gift_invoice_ex, $arr);
//                if (!isset($gift_invoice_summ[$invoice_item['filial_id']])){
//                    $gift_invoice_summ[$filial_id] = 0;
//                }

                    $gift_invoice_summ += $arr['itog_price'];
                }
            }
//        var_dump($gift_invoice_ex);
//        var_dump($gift_invoice_summ);

            //Если были подарочные позиции по наряду
            if ($gift_invoice_summ > 0){
                //Нарисуем полученные оплаты
                //foreach ($payments_j as $filial_id => $payment_item) {
                echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(255, 235, 64, 0.42);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $gift_invoice_summ . '</b></span></i></div>';
                //}
            }


            //Оплаты
            $payments_j = array();

            //Получаем все оплаты по текущему наряду
            $query = "
            SELECT *
            FROM `journal_payment`
            WHERE `invoice_id` = '{$invoice_id}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    //array_push($payments_j, $arr);
                    //Раскидаем суммы оплат сразу по филиалам
                    if (!isset($payments_j[$arr['filial_id']])){
                        $payments_j[$arr['filial_id']]['summ'] = 0;
                    }
                    $payments_j[$arr['filial_id']]['summ'] += $arr['summ'];

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    if (!isset($itog_filials_summ[$arr['filial_id']])){
                        $itog_filials_summ[$arr['filial_id']] = 0;
                    }
                    $itog_filials_summ[$arr['filial_id']] += $arr['summ'];

                }
            }

            //echo '<span style="font-size: 85%;">Оплаты: Филиал -> Сумма</span><br>';
            //var_dump($payments_j);
            //Если оплаты оп наряду есть
            if (!empty($payments_j)) {
                //Нарисуем полученные оплаты
                foreach ($payments_j as $filial_id => $payment_item) {
                    echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 220, 14, 0.2);">' . $filials_j[$filial_id]['name2'] . ' ->  <b style="font-size: 105%;">' . $payment_item['summ'] . '</b></span></i></div>';
                }
            }else{

                //Посмотрим, а не страховой ли наряд (сделать мы это можем только пройдясь по всем позициям из наряда)
                //Если да, возьмём всю сумму и привяжем её к филиалу, где был сделан наряд

                $filial_insure_invoice_ex = array();

                $query = "
            SELECT *
            FROM `journal_invoice_ex`
            WHERE `invoice_id` = '{$invoice_id}'
            AND `insure` <> '0' AND `insure_approve` = '1'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                //Если что-то нашли страхового
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($filial_insure_invoice_ex[$invoice_item['filial_id']])){
                            $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] = 0;
                        }
                        $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] += $arr['itog_price'];

                        //Сразу добавляем в итоговый массив,
                        //предварительно добавив в массив элемент с ID филиала, если его не было
                        if (!isset($itog_filials_summ[$invoice_item['filial_id']])){
                            $itog_filials_summ[$invoice_item['filial_id']] = 0;
                        }
                        $itog_filials_summ[$invoice_item['filial_id']] += $arr['itog_price'];

                    }

                    echo '<div style="margin: 2px 0;"><i><span class="button_tiny" style="margin: 0 3px; font-size: 80%; background-color: rgba(0, 175, 220, 0.2);">' . $filials_j[$invoice_item['filial_id']]['name2'] . ' ->  <b style="font-size: 105%;">' . $filial_insure_invoice_ex[$invoice_item['filial_id']]['summ'] . '</b></span></i></div>';

                }else{

                    if ($gift_invoice_summ > 0){
                        //Уж даже и не страховой наряд и нет там подарков
                        echo '<i><span style="color: red; font-size: 80%;">нет оплат (наряд скорее всего "нулевой", РЛ к нему можно было бы и не создавать)</span></i>';
                    }/*else{
                    echo '<i><span style="color: red; font-size: 80%;">Ошибка #54. Требуется тщательная проверка.</span></i>';
                }*/
                }
            }

            //А теперь выберем РЛ, которые были не этому исполнителю.
            //Ибо может быть так, что наряд один, а исполнителей больше.
            //Выберем их и вычтем суммы из общих
            //... а может потом и не придется, заставим админов делать отдельные наряды
            $query = "SELECT * FROM `fl_journal_calculate` WHERE  `invoice_id` = '{$invoice_id}'  AND `worker_id` <> '{$worker_id}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            //Если нашли РЛ, которые не принадлежат указанному в табеле исполнителю
            //... и тут на самом деле жопа, потому что денег могли принести в одном, а работу сделают на другом
            //... и ппц
            if ($number != 0) {
                while ($arr = mysqli_fetch_assoc($res)) {

                    //Сразу добавляем в итоговый массив,
                    //предварительно добавив в массив элемент с ID филиала, если его не было
                    //... Тут у нас опять филиал берётся по наряду, что наверное не есть хорошо,
                    //... но пока так
                    if (!isset($itog_filials_summ_not4tou[$invoice_item['filial_id']])){
                        $itog_filials_summ_not4tou[$invoice_item['filial_id']] = 0;
                    }
                    $itog_filials_summ_not4tou[$invoice_item['filial_id']] += $arr['summ_inv'];
                }
            }

            echo '</div>';
        }

        //Сумма на всех филиалах
        $itog_all_filial_summ = 0;

        //Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников
        echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников</span>';
        //Сортируем, чтоб меньше денег было внизу
        arsort($itog_filials_summ);
        var_dump($itog_filials_summ);

        //Итоговый массив по филиалам по тем работам,
        //которые мы хотим и должны оплатить другому человеку
        echo '<span style="font-size: 85%;">Итоговый массив по филиалам по тем работам, которые мы хотим и должны оплатить ДРУГОМУ человеку</span>';
        var_dump($itog_filials_summ_not4tou);

        //Вычтем с филиалов суммы, которые уйдут в зп другому человеку
        foreach ($itog_filials_summ_not4tou as $filial_id => $summ){
            if (isset($itog_filials_summ[$filial_id])){
                $itog_filials_summ[$filial_id] -= $summ;
            }
        }
        //Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего
        echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего</span>';
        var_dump($itog_filials_summ);

        //Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
        echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
        $itog_all_filial_summ = array_sum($itog_filials_summ);
        var_dump($itog_all_filial_summ);

        //Нам нужен (?) последний ключ массива для
        //дальнейшей работы с ним
//    end($itog_filials_summ);
//    $last_key = key($itog_filials_summ);
//    var_dump($last_key);

        //Вычислим процентное соотношение
        foreach ($itog_filials_summ as $filial_id => $summ){

            $percent_value = 0;

            //предварительно добавляем в массив элемент с ID филиала, если его не было
            //!!! потом сделать это выше, когда суммы собираем
            if (!isset($itog_filials_percents[$filial_id])){
                $itog_filials_percents[$filial_id] = 0;
            }

            $percent_value = (100* $summ) / $itog_all_filial_summ;

            $itog_filials_percents[$filial_id] = $percent_value;
        }
        echo '<span style="font-size: 85%;">Процентное соотношение денег по филиалам в общей сумме</span>';
        var_dump($itog_filials_percents);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($itog_filials_percents));

        echo '<hr>';


        echo '<h3 style="font-size: 100%;">1. Мы хотим выдать сразу всю сумму зп. У нас все работы закрыты и оплачены:</h3>';

        //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP = array();

        //Сумма ЗП к выдаче
        //сейчас тут только сумма за РЛ и так по логике верно
        echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
        $iWantMyMoney1 = $tabel_j['summ_calc'];
        var_dump($iWantMyMoney1);


        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents as $filial_id => $percent){
            $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZP);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZP));


        //Вычислим остаток денег по филиалам после выдачи
        foreach ($itog_filials_summ as $filial_id => $summ){
            if (!isset($itog_filials_summ_ostatok[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = 0;
            }
            if (isset($summ4ZP[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZP[$filial_id];
            }
        }

        echo '<span style="font-size: 85%;">Остаток денег по филиалам после выдачи</span>';
        var_dump($itog_filials_summ_ostatok);

        echo '<hr>';

        echo '<h3 style="font-size: 100%;">2a. Мы хотим выдать только часть денег (аванс). У нас все работы закрыты и оплачены:</h3>';

        //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP = array();
        //Части по филиалам, которые хотим выдать, исходя из суммы аванса
        $summ4ZPNow = array();

        //Сумма ЗП к выдаче
        //сейчас тут только сумма за РЛ и так по логике верно
        echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
        $iWantMyMoney1 = $tabel_j['summ_calc'];
        var_dump($iWantMyMoney1);

        //Часть, которую хотим выдать
        echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
        $iWantMyMoney2 = 12000;
        var_dump($iWantMyMoney2);

        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents as $filial_id => $percent){
            $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZP);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZP));


        //Если выдаем часть !!! Потом можно будет расширить это понятие и на всю сумма. Сумма как часть самой себя
        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents as $filial_id => $percent){
            $summ4ZPNow[$filial_id] = intval($iWantMyMoney2 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZPNow);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZPNow));


        //Вычислим остаток денег еще останется выдать
        $summ4ZP_ostatok = array();

        foreach ($summ4ZP as $filial_id => $summ){
            if (!isset($summ4ZP_ostatok[$filial_id])){
                $summ4ZP_ostatok[$filial_id] = 0;
            }
            $summ4ZP_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
        }

        echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZP_ostatok);

        //Вычислим остаток денег по филиалам после выдачи
        foreach ($itog_filials_summ as $filial_id => $summ){
            if (!isset($itog_filials_summ_ostatok[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = 0;
            }
            if (isset($summ4ZPNow[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
            }

        }

        echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
        var_dump($itog_filials_summ_ostatok);


        echo '<hr>';

        echo '<h3 style="font-size: 100%;">2б. Мы уже выдали часть денег (аванс), а к концу месяца в табель еще накидали РЛ + были оплаты. У нас все работы закрыты и оплачены:</h3>';

        echo '<h2>Тут у нас все рассчеты до аванса, включая сам факт выдачи</h2>';

        //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP = array();
        //Части по филиалам, которые хотим выдать, исходя из суммы аванса
        $summ4ZPNow = array();

        //Сумма ЗП к выдаче
        //сейчас тут только сумма за РЛ и так по логике верно
        echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
        $iWantMyMoney1 = $tabel_j['summ_calc'];
        var_dump($iWantMyMoney1);

        //Часть, которую хотим выдать
        echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
        $iWantMyMoney2 = 12000;
        var_dump($iWantMyMoney2);

        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents as $filial_id => $percent){
            $summ4ZP[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZP);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZP));


        //Если выдаем часть !!! Потом можно будет расширить это понятие и на всю сумма. Сумма как часть самой себя
        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents as $filial_id => $percent){
            $summ4ZPNow[$filial_id] = intval($iWantMyMoney2 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZPNow);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZPNow));


        //Вычислим остаток денег еще останется выдать
        $summ4ZP_ostatok = array();

        foreach ($summ4ZP as $filial_id => $summ){
            if (!isset($summ4ZP_ostatok[$filial_id])){
                $summ4ZP_ostatok[$filial_id] = 0;
            }
            $summ4ZP_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
        }

        echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег</span>';
        var_dump($summ4ZP_ostatok);

        //Вычислим остаток денег по филиалам после выдачи
        foreach ($itog_filials_summ as $filial_id => $summ){
            if (!isset($itog_filials_summ_ostatok[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = 0;
            }
            if (isset($summ4ZPNow[$filial_id])){
                $itog_filials_summ_ostatok[$filial_id] = $summ - $summ4ZPNow[$filial_id];
            }

        }

        echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
        var_dump($itog_filials_summ_ostatok);


        echo '<br><h2>Тут изменения в деньгах после выдачи аванса (данные именно в данном исследовании вводились вручную...)</h2>';

        //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP_temp = array();
        //Части по филиалам, которые хотим выдать, исходя из суммы аванса
        $summ4ZPNow_temp = array();

        echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (только сумма за РЛ)</span>';
        $iWantMyMoney1 = 400381;
        var_dump($iWantMyMoney1);

        //Часть, которую хотим выдать
        echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас (в данном случае это финальная выплата, выдаем всё, что осталось после аванса).</span>';
        $iWantMyMoney2 = $iWantMyMoney1 - $iWantMyMoney2;
        var_dump($iWantMyMoney2);

        echo '<br>-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-><br>';

//Изменились исходные данные (ставим вручную)
//Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников
        $itog_filials_summ_temp =  array(19 => 135180, 15 => 29560, 13 => 500000);

        echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам до вычета сумм других сотрудников (тут мы тоже и далее задали вручную, задав + еще 1 филиал)</span>';
//Сортируем, чтоб меньше денег было внизу
        arsort($itog_filials_summ_temp);
        var_dump($itog_filials_summ_temp);

//Итоговый массив по филиалам по тем работам,
//которые мы хотим и должны оплатить другому человеку
        $itog_filials_summ_not4tou_temp =  $itog_filials_summ_not4tou;

        echo '<span style="font-size: 85%;">Итоговый массив по филиалам по тем работам, которые мы хотим и должны оплатить ДРУГОМУ человеку</span>';
        var_dump($itog_filials_summ_not4tou_temp);

//Вычтем с филиалов суммы, которые уйдут в зп другому человеку
        foreach ($itog_filials_summ_not4tou_temp as $filial_id => $summ){
            if (isset($itog_filials_summ_temp[$filial_id])){
                $itog_filials_summ_temp[$filial_id] -= $summ;
            }
        }
//Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего
        echo '<span style="font-size: 85%;">Итог сумм, с которых надо выдать ЗП, по филиалам с вычетом лишнего</span>';
        var_dump($itog_filials_summ_temp);

//Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)
        echo '<span style="font-size: 85%;">Общая сумма со всех филиалов, с которой надо выдать зп (с учетом всех нюансов)</span>';
        $itog_all_filial_summ_temp = array_sum($itog_filials_summ_temp);
        var_dump($itog_all_filial_summ_temp);

//Вычислим ИТОГОВОЕ процентное соотношение
        foreach ($itog_filials_summ_temp as $filial_id => $summ){

            $percent_value = 0;

            //предварительно добавляем в массив элемент с ID филиала, если его не было
            //!!! потом сделать это выше, когда суммы собираем
            if (!isset($itog_filials_percents_temp[$filial_id])){
                $itog_filials_percents_temp[$filial_id] = 0;
            }

            $percent_value = (100* $summ) / $itog_all_filial_summ_temp;

            $itog_filials_percents_temp[$filial_id] = $percent_value;
        }
        echo '<span style="font-size: 85%;">Процентное ИТОГОВОЕ соотношение денег по филиалам в общей сумме</span>';
        var_dump($itog_filials_percents_temp);
//Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($itog_filials_percents_temp));

        echo '<br><-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-<br>';

        //Массив где ключ - это ID филиала, а значение - это сколько всего надо выдать с этого филиала из общего объема денег
        $summ4ZP_temp = array();
        //Части по филиалам, которые хотим выдать, исходя из суммы аванса
        $summ4ZPNow_temp = array();

        /*//Сумма ЗП к выдаче
        //сейчас тут только сумма за РЛ и так по логике верно
        echo '<span style="font-size: 85%;">Сумма ЗП ВСЯ к выдаче (сейчас тут только сумма за РЛ)</span>';
        $iWantMyMoney1 = $tabel_j['summ_calc'];
        var_dump($iWantMyMoney1);*/

        /*//Часть, которую хотим выдать
        echo '<span style="font-size: 85%;">Сумма ЗП ЧАСТЬ к выдаче, которую, мы хотим выдать сейчас.</span>';
        $iWantMyMoney2 = 12000;
        var_dump($iWantMyMoney2);*/

        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам
        foreach ($itog_filials_percents_temp as $filial_id => $percent){
            $summ4ZP_temp[$filial_id] = intval($iWantMyMoney1 / 100 * $percent);
        }

        echo '<span style="font-size: 85%;">Сколько ВСЕГО надо БУДЕТ в итоге выдать с каждого филиала из общего объема денег НЕ учитывая то, что уже выдали</span>';
        var_dump($summ4ZP_temp);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZP_temp));


        //Если выдаем последнее, но уже выдавали аванс
        //Посчитаем по сколько надо выдать с каждого филиала
        //пропорционально полученным деньгам

        foreach ($summ4ZP_temp as $filial_id => $summ){
            $summ4ZPNow_temp[$filial_id] = $summ;

            if (isset($summ4ZPNow[$filial_id])) {
                $summ4ZPNow_temp[$filial_id] -= $summ4ZPNow[$filial_id];
            }
        }

        echo '<span style="font-size: 85%;">Сколько СЕЙЧАС надо выдать с каждого филиала из общего объема денег, УЧИТЫВАЯ уже выданное в аванс</span>';
        var_dump($summ4ZPNow_temp);
        //Просто для самоконтроля, что получается 100%, так как отказался от округлений при расчете %-в (так точнее)
        var_dump(array_sum($summ4ZPNow_temp));

        echo '<span style="font-size: 85%;">Столько будет выдано ВСЕГО</span>';
        var_dump(array_sum($summ4ZPNow_temp) + array_sum($summ4ZPNow));

        //Вычислим остаток денег еще останется выдать
        $summ4ZP_ostatok_temp = array();

        foreach ($summ4ZP_temp as $filial_id => $summ){
            if (!isset($summ4ZP_ostatok_temp[$filial_id])){
                $summ4ZP_ostatok_temp[$filial_id] = 0;
            }
            //!!!сюда потом тоже добавить if как и ниже для универсальности
            $summ4ZP_ostatok_temp[$filial_id] = $summ - $summ4ZPNow_temp[$filial_id];

            if (isset($summ4ZPNow[$filial_id])){
                $summ4ZP_ostatok_temp[$filial_id] -= $summ4ZPNow[$filial_id];
            }
        }

        echo '<span style="font-size: 85%;">Сколько останется выдать потом с каждого филиала из общего объема денег (если не будет доплат и увеличения ЗП)</span>';
        var_dump($summ4ZP_ostatok_temp);

        //Вычислим остаток денег по филиалам после выдачи
        foreach ($itog_filials_summ_temp as $filial_id => $summ){
            if (!isset($itog_filials_summ_ostatok_temp[$filial_id])){
                $itog_filials_summ_ostatok_temp[$filial_id] = 0;
            }
            if (isset($summ4ZPNow_temp[$filial_id])){
                $itog_filials_summ_ostatok_temp[$filial_id] = $summ - $summ4ZPNow_temp[$filial_id];
            }

        }

        echo '<span style="font-size: 85%;">Остаток денег по филиалам после ЭТОЙ ЧАСТИЧНОЙ выдачи</span>';
        var_dump($itog_filials_summ_ostatok_temp);
	}


?>
