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
	
	//Поиск в многомерном массиве
	function SearchInArray($array, $data, $search){
		$rez = 0;
		foreach ($array as $key => $value){
			if (array_search ($data, $value)){
				$rez = $value[$search];
			}				
		}
		return $rez;
	}
	
	//
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
		require 'config.php';
		$sheduler_workers = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `scheduler` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `filial` = '{$office}' AND `type` = '{$type}'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;
		mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaWorker($datatable, $y, $m, $d, $office, $kab){
		require 'config.php';
		$sheduler_workers = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;
		mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaWorker2($datatable, $y, $m, $d, $office, $kab, $smena){
		require 'config.php';
		$sheduler_workers = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;
		mysql_close();
		
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
		
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' {$q_smena} AND `worker` = '{$worker}'";
		//var_dump($query);
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($work_arr, $arr);
			}
		}else
			$work_arr = 0;
		mysql_close();
		
		return $work_arr;
	}
	
	function FilialSmenaWorker($datatable, $y, $m, $d, $worker){
		require 'config.php';
		$sheduler_workers = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `worker` = '{$worker}'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_workers, $arr);
			}
		}else
			$sheduler_workers = 0;
		mysql_close();
		
		return $sheduler_workers;
	}
	
	function FilialKabSmenaZapis($table, $y, $m, $d, $office, $kab, $worker, $wt){
		require 'config.php';
		if ($table == 'scheduler_stom'){
			$datatable = 'zapis_stom';
		}elseif ($table == 'scheduler_cosm'){
			$datatable = 'zapis_cosm';
		}else{
			$datatable = 'zapis_stom';
		}
		$sheduler_zapis = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' 
		AND `worker` = '{$worker}' 
		AND `start_time` >= '{$wt}' AND `start_time` < '".($wt + 30)."'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;
		mysql_close();
		
		return $sheduler_zapis;
	}
	
	function FilialKabSmenaZapisToday($table, $y, $m, $d, $office, $kab, $type){
		require 'config.php';
		if ($table == 'scheduler_stom'){
			$datatable = 'zapis_stom';
		}elseif ($table == 'scheduler_cosm'){
			$datatable = 'zapis_cosm';
		}else{
			$datatable = 'zapis_stom';
		}
		$sheduler_zapis = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' AND `type` = '{$type}' ORDER BY `start_time` ASC";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;
		mysql_close();
		
		return $sheduler_zapis;
	}
	
	
	function FilialKabSmenaZapisToday2($table, $y, $m, $d, $office, $kab, $wt, $type){
		require 'config.php';
		if ($table == 'scheduler_stom'){
			$datatable = 'zapis_stom';
		}elseif ($table == 'scheduler_cosm'){
			$datatable = 'zapis_cosm';
		}else{
			$datatable = 'zapis_stom';
		}
		$sheduler_zapis = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$wt2 = $wt+30;
		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' AND `type` = '{$type}' AND `start_time` >= '{$wt}' AND `start_time` < '{$wt2}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
		//echo $query;
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;
		mysql_close();
		
		return $sheduler_zapis;
	}
	
	function FilialWorkerSmenaZapisToday($table, $y, $m, $d, $worker){
		require 'config.php';

		$sheduler_zapis = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//$wt2 = $wt+30;
		$query = "SELECT * FROM `zapis` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `worker` = '{$worker}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
		//echo $query;
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($sheduler_zapis, $arr);
			}
		}else
			$sheduler_zapis = 0;
		mysql_close();
		
		return $sheduler_zapis;
	}
	
	//Полных лет / Возраст
	function getyeardiff($bday){
		$today = time();
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
				if ($status['status'] == '20'){
					//echo 'Ретенция<br />';
					$sanat = false;
				}
				if ($status['status'] == '22'){
					//echo 'ЗО<br />';
					$sanat = false;
				}

				if (($status['surface1'] == 63) || ($status['surface2'] == 63) || ($status['surface3'] == 63) || ($status['surface4'] == 63) || 
					($status['top1'] == 63) || ($status['top2'] == 63) || ($status['top12'] == 63) || ($status['root1'] == 63) || 
					($status['root2'] == 63) || ( $status['root3'] == 63)){
					//echo 'Временная пломба<br />';
					$sanat = false;
				}
				
				//echo $t_id.'<br />';
				
				if (($status['surface1'] == 64) || ($status['surface2'] == 64) || ($status['surface3'] == 64) || ($status['surface4'] == 64) || 
					($status['top1'] == 64) || ($status['top2'] == 64) || ($status['top12'] == 64) || ($status['root1'] == 64) || 
					($status['root2'] == 64) || ( $status['root3'] == 64)){
					//echo 'Пломба кариес<br />';
					$sanat = false;
				}
				
				//echo $t_id.'<br />';
				//$sanat = false;
				
				if (($status['surface1'] == 71) || ($status['surface2'] == 71) || ($status['surface3'] == 71) || ($status['surface4'] == 71) || 
					($status['top1'] == 71) || ($status['top2'] == 71) || ($status['top12'] == 71) || ($status['root1'] == 71) || 
					($status['root2'] == 71) || ($status['root3'] == 64)){
					//echo 'Кариес<br />';
					$sanat = false;
				}
				
				//echo $t_id.'<br />';

				if (($status['surface1'] == 74) || ($status['surface2'] == 74) || ($status['surface3'] == 74) || ($status['surface4'] == 74) || 
					($status['top1'] == 74) || ($status['top2'] == 74) || ($status['top12'] == 74) || ($status['root1'] == 74) || 
					($status['root2'] == 74) || ($status['root3'] == 64)){
					//echo 'Пульпит<br />';
					$sanat = false;
				}
				
				//echo $t_id.'<br />';
				
				if (($status['surface1'] == 75) || ($status['surface2'] == 75) || ($status['surface3'] == 75) || ($status['surface4'] == 75) || 
					($status['top1'] == 75) || ($status['top2'] == 75) || ($status['top12'] == 75) || ($status['root1'] == 75) || 
					($status['root2'] == 75) || ($status['root3'] == 64)){
					//echo 'Периодонтит<br />';
					$sanat = false;
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
		require 'config.php';
		$result = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `journal_debts_prepayments` WHERE `client` = '{$id}' AND (`type`='4' OR `type`='3')";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($result, $arr);
			}
		}else
			$result = 0;
		mysql_close();
		
		return $result;
	}
	
	//Погашения
	function Repayments ($id){
		require 'config.php';
		$result = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `journal_debts_prepayments` WHERE `parent` = '{$id}' AND `type`='8'";
		$res = mysql_query($query) or die($query);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($result, $arr);
			}
		}else
			$result = 0;
		mysql_close();
		
		return $result;
	}

	//Дерево
	function showTree($level, $space, $type, $sel_id, $first, $last_level, $deleted, $dbtable, $insure_id){
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
		
		$deleted_str = '';
		
		if ($deleted){
			//$deleted_str = 'AND `status` = 9';
		}else{
			//выбираем не удалённые
			$deleted_str = 'AND `status` <> 9';
		}
		
		$q_dop = '';
		$dbprices = 'spr_priceprices';
		
		//Для страховых
		if ($insure_id != 0){
			$q_dop = " AND `insure`='{$insure_id}'";
			$dbprices = 'spr_priceprices_insure';
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
					
					//Если не страховая
					if ($insure_id == 0){
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
					}
					
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
						
						//Если страховая
						if ($insure_id != 0){
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
						}
						
						for ($i = 0; $i < count($items_j); $i++) {

							$price = 0;
							
							//$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `create_time` DESC LIMIT 1";
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from` DESC LIMIT 1";
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
					}else{
						//
					}
				}
				
				
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level`='{$value['id']}' ".$deleted_str." ORDER BY `name`";
				//var_dump($query);
				
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
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
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
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
				//Обновили статус родителю
				
				$query = "UPDATE `spr_storagegroup` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9', `level`='0' WHERE `id`='{$value['id']}'";
				mysql_query($query) or die(mysql_error().' -> '.$query);
						
				$arr2 = array();
				$rez2 = array();
				$arr3 = array();
				$rez3 = array();
				
				//!!! clear
				if ($type == 'clear'){
					//собираем все позиции в этой группе и удаляем их из группы и их самих
					$query = "SELECT * FROM `spr_itemsingroup` WHERE `group` = '{$value['id']}'";
					$res = mysql_query($query) or die(mysql_error().' -> '.$query);
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr2 = mysql_fetch_assoc($res)){
							array_push($rez2, $arr2);
						}
					}else{
						$rez2 = 0;
					}
					//var_dump($rez);
					
					if ($rez2 != 0){
						//...удаляем их из группы
						$query = "DELETE FROM `spr_itemsingroup` WHERE `group` = '{$value['id']}'";
						mysql_query($query) or die(mysql_error().' -> '.$query);
						
						if ($deleteallin == 1){
							foreach ($rez2 as $ids){
								//var_dump($ids);
								//...и их самих
								$query = "UPDATE `spr_pricelist_template` SET `last_edit_time`='{$time}', `last_edit_person`='{$_SESSION['id']}', `status`='9' WHERE `id`='{$ids['item']}'";
								//var_dump($query);
								mysql_query($query) or die(mysql_error().' -> '.$query);								
							}
						}
					}
				}

				//получаем группы, которые в этом родителе
				$query = "SELECT * FROM `spr_storagegroup` WHERE `level` = '{$value['id']}'";
				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					DeleteTree($value['id'], '', $type, $sel_id, $first, 0, $deleted, $deleteallin);
				}else{
				}
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
							$query = "SELECT `price` FROM `spr_priceprices` WHERE `item`='".$items_j[$i]['id']."' ORDER BY `date_from` DESC LIMIT 1";
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
	
?>