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
	function isSameFullName($datatable, $name){
		$rezult = array();
		$rezult = SelDataFromDB($datatable, $name, 'full_name');
		//var_dump ($rezult);
		
		if ($rezult != 0){
			return TRUE;
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
	function WriteSearchUser($datatable, $sw, $type){
		if ($type == 'user_full'){
			$search = 'user';
		}else{
			$search = $type;
		}
		$user = SelDataFromDB($datatable, $sw, $search);
		if ($user != 0){
			if ($type == 'user_full'){
				return $user[0]['full_name'];
			}else{
				return $user[0]['name'];
			}
		}else{
			return 'unknown';
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
	
	function FilialWorker($datatable, $y, $m, $d, $office){
		require 'config.php';
		$sheduler_workers = array();
		
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}'";
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
	
	function FilialKabSmenaZapisToday($table, $y, $m, $d, $office, $kab){
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
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' ORDER BY `start_time` ASC";
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
	
	
	function FilialKabSmenaZapisToday2($table, $y, $m, $d, $office, $kab, $wt){
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
		$query = "SELECT * FROM `$datatable` WHERE `year` = '{$y}' AND `month` = '{$m}'  AND `day` = '{$d}' AND `office` = '{$office}' AND `kab` = '{$kab}' AND `start_time` >= '{$wt}' AND `start_time` < '{$wt2}' AND `enter` <> 9 AND `enter` <> 8 ORDER BY `start_time` ASC";
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
	

?>