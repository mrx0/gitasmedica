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

        $query = "SELECT ss.name, ss.id
        FROM `journal_work_spec` jws 
        INNER JOIN `spr_specialization` ss ON ss.id = jws.specialization_id 
        WHERE `worker_id` = '$worker_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        //var_dump($res);

        if ($number != 0){
            //var_dump(mysqli_fetch_assoc($res));
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($specializations_j, $arr);
            }
        }else
            $specializations_j = 0;

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
	function getAllFilials($sort, $short_name){
		$filials_j = array();

        $msql_cnnct = ConnectToDB ();

        $query = "SELECT * FROM `spr_filials`";

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
	
	function FilialKabSmenaZapisToday($table, $y, $m, $d, $office, $kab, $type){
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

		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' AND `type` = '{$type}' ORDER BY `start_time` ASC";

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
	
	
	function FilialKabSmenaZapisToday2($table, $y, $m, $d, $office, $kab, $wt, $type){
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

		$wt2 = $wt+30;

		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' AND `type` = '{$type}' AND `start_time` >= '{$wt}' AND `start_time` < '{$wt2}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";

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
													<a href="'.$link.'&id='.$items_j[$i]['id'].'" class="ahref" id="4filter"><span style="font-size: 75%; font-weight: bold;">[#'.$items_j[$i]['id'].']</span> <i>'.$items_j[$i]['code'].'</i> '.$items_j[$i]['name'].'</a>
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
					$last_level2 = $last_level+1;
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
                    if (($zapisData['enter'] != 8) && ($zapisData['enter'] != 9)) {
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
                if (($zapisData['enter'] != 8) && ($zapisData['enter'] != 9) && $main_zapis){
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
    function showInvoiceDivRezult($data, $minimal, $show_categories, $show_absent, $show_deleted, $only_debt){

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

			}

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


            //Удалённые
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


/*            $rezult .= $itemAll_str;
            if ($show_deleted && !$minimal){
                $rezult .= $itemClose_str;
            }*/

            return array('data' => $rezult, 'count' => $rezult_count);

        }else{
        	if ($show_absent) {
                $rezult .= '<i style="font-size: 80%; color: #7D7D7D; margin-bottom: 5px; color: red;">Нет нарядов</i>';
            }

            return array('data' => $rezult, 'count' => 1);
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

            $offices_j = getAllFilials(false, false);
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


/*    //функция формирует и показывает расчетные листы визуализация
    function showCalculateDivRezult($data, $minimal, $show_categories, $show_absent){

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



?>
