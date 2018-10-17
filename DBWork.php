<?php

//DBWork.php
//

///include_once 'writen.php';
///WriteToFile('1.TXT', $query);

	//Получить IP компа
	function GetRealIp(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$uip=$_SERVER['HTTP_CLIENT_IP'];
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$uip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$uip=$_SERVER['REMOTE_ADDR'];
		}
		return $uip;
	}


    //Подключение к БД MySQl
    function ConnectToDB () {
        require 'config.php';

        $msql_cnnct = mysqli_connect($hostname, $username, $db_pass, $dbName) or die("Не возможно создать соединение ");
        mysqli_query($msql_cnnct, "SET NAMES 'utf8'");

        return $msql_cnnct;
    }

    //Подключение к другой БД MySQl
    function ConnectToDB_2 ($config) {
        require $config.'.php';

        $msql_cnnct = mysqli_connect($hostname, $username, $db_pass, $dbName) or die("Не возможно создать соединение ");
        mysqli_query($msql_cnnct, "SET NAMES 'utf8'");

        return $msql_cnnct;
    }

    //Отключение от БД MySQl
    function CloseDB ($msql_cnnct) {

        mysqli_close($msql_cnnct);

    }

	//Логирование.
	function AddLog ($ip, $creator, $description_old, $description_new){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		$query = "INSERT INTO `logs` (
			`date`, `ip`, `mac`, `creator`, `description_old`, `description_new`) 
			VALUES (
			'{$time}', '{$ip}', '', '{$creator}', '{$description_old}', '{$description_new}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        mysqli_close($msql_cnnct);
	}

	//Вставка записей в журнал IT заявок из-под Web
	function WriteToDB_Edit ($office, $description, $create_time, $create_person, $last_edit_time, $last_edit_person, $worker, $end_time, $priority){

        $msql_cnnct = ConnectToDB ();

		//!сделать время отсюда?
		$time = time();
		$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `journal_it` (
			`office`, `description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `end_time`, `priority`) 
			VALUES (
			'{$office}', '{$description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '{$worker}', '{$end_time}', '{$priority}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлена заявка в IT. ['.date('d.m.y H:i', $create_time).']. Офис ['.$office.']. Описание: ['.$description.']');
	}
	
	//Вставка записей в журнал ПО заявок из-под Web
	function WriteToDB_EditSoft ($description, $full_description, $create_time, $create_person, $last_edit_time, $last_edit_person, $worker, $end_time, $priority){

        $msql_cnnct = ConnectToDB ();

		//!сделать время отсюда?
		$time = time();
		//$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `journal_soft` (
			`description`, `full_description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `end_time`, `priority`) 
			VALUES (
			'{$description}', '{$full_description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '1', '{$end_time}', '{$priority}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлена заявка в ПО. ['.date('d.m.y H:i', $create_time).']. Раздел ['.$priority.']. Описание: ['.$description.']:['.$full_description.']');
	}
	
	//Вставка комментариев из-под Web
	function WriteToDB_EditComments ($dtable, $description, $create_time, $create_person, $last_edit_time, $last_edit_person, $parent){

        $msql_cnnct = ConnectToDB ();

		//!сделать время отсюда?
		$time = time();

		//$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `comments` (
			`dtable`, `description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `parent`) 
			VALUES (
			'{$dtable}', '{$description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '{$parent}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');

	}
	
	//Вставка записей в расписание
	function WriteToDB_EditScheduler ($datatable, $year, $month, $day, $office, $kab, $smena, $smena_t, $worker, $create_person){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `$datatable` (
			`year`, `month`, `day`, `office`, `kab`, `smena`, `smena_t`, `worker`, `create_person`) 
			VALUES (
			'{$year}', '{$month}', '{$day}', '{$office}', '{$kab}', '{$smena}', '{$smena_t}', '{$worker}', '{$create_person}') ";
		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Вставка записей во временную запись
	function WriteToDB_EditZapis ($datatable, $year, $month, $day, $office, $add_from, $kab, $worker, $create_person, $patient, $contacts, $description, $start_time, $wt, $type, $pervich, $insured, $noch){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `zapis` (
			`year`, `month`, `day`, `office`, `add_from`, `kab`, `worker`, `create_time`, `create_person`, `patient`, `contacts`, `description`, `start_time`, `wt`, `type`, `pervich`, `insured`, `noch`) 
			VALUES (
			'{$year}', '{$month}', '{$day}', '{$office}', '{$add_from}', '{$kab}', '{$worker}', '$time', '{$create_person}', '{$patient}', '{$contacts}', '{$description}', '{$start_time}', '{$wt}', '{$type}', '{$pervich}', '{$insured}', '{$noch}') ";
		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}
	
	//Редактирование записей во временную запись
	function WriteToDB_UpdateZapis ($datatable, $worker, $edit_person, $patient, $contacts, $description, $start_time, $wt, $type, $pervich, $insured, $noch, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `zapis` SET 
		`last_edit_time`='{$time}', `last_edit_person`='{$edit_person}',
		`worker`='{$worker}', `patient`='{$patient}', `contacts`='{$contacts}', `description`='{$description}', 
		`start_time`='{$start_time}', `wt`='{$wt}', `type`='{$type}', `pervich`='{$pervich}', `insured`='{$insured}', `noch`='{$noch}'
		WHERE `id`='{$id}'";

		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}
	
	//Удаление записей в расписание
	function WriteToDB_DeleteScheduler ($datatable, $id){

	    $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "DELETE FROM $datatable WHERE `id`=$id";
		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		CloseDB ($msql_cnnct);
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Обновление записей в расписание
	function WriteToDB_UpdateScheduler ($datatable, $id, $smena){

	    $msql_cnnct = ConnectToDB ();

		$time = time();
		$query = "UPDATE $datatable SET `smena`=$smena WHERE `id`=$id";
		//echo $query;

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Добавление услуги.
	function WriteToDB_EditPriceName ($name, $pricecode, $category_id, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		$query = "INSERT INTO `spr_pricelist_template` (
			`name`, `category`, `code`, `create_time`, `create_person`) 
			VALUES (
			'{$name}', '{$category_id}', '{$pricecode}', '{$time}', '{$session_id}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');
		
		return ($mysql_insert_id);
	}
	
	//
	function WriteToDB_UpdatePriceItem ($name, $code, $category_id, $id, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		
		$query = "UPDATE `spr_pricelist_template` SET `last_edit_time`='{$time}', `last_edit_person`='{$session_id}', `name`='{$name}', `code`='{$code}', `category`='{$category_id}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');

	}
	
	function WriteToDB_UpdatePriceGroup ($name, $id, $level, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		
		$query = "UPDATE `spr_storagegroup` SET `last_edit_time`='{$time}', `last_edit_person`='{$session_id}', `name`='{$name}', `level`='{$level}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');

	}
	
	function WriteToDB_UpdatePriceItemInGroup ($item, $group, $session_id){

	    $msql_cnnct = ConnectToDB ();

		$time = time();
		
		$query = "SELECT * FROM `spr_itemsingroup` WHERE `item` = '{$item}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			
			$query = "DELETE FROM `spr_itemsingroup` WHERE `item` = '{$item}'";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
		}
		
		$query = "INSERT INTO `spr_itemsingroup` (
			`item`, `group`, `create_time`, `create_person`) 
			VALUES (
			'{$item}', '{$group}', '{$time}', '{$session_id}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
		
		//$mysql_insert_id = mysql_insert_id();

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');

	}
	
	//Добавление группы.
	function WriteToDB_EditPriceGroup ($name, $level, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		$query = "INSERT INTO `spr_storagegroup` (
			`name`, `level`, `create_time`, `create_person`) 
			VALUES (
			'{$name}', '{$level}', '{$time}', '{$session_id}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');
		
		return ($mysql_insert_id);
	}
	
	//О!!!!  ЧТо это??
	/*function WritePriceNameToDB_Update ($name, $session_id, $id){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Комментарий: ['.$arr['comment'].']. Карта: ['.$arr['card'].']. Дата рождения: ['.$arr['birthday'].']. Пол: ['.$arr['sex'].']. Телефон: ['.$arr['telephone'].']. Серия/номер паспорта ['.$arr['passport'].']. Серия/номер паспорта (иностр.) ['.$arr['alienpassportser'].'/'.$arr['passportvidandata'].']. Дата выдачи ['.$arr['passportvidandata'].']. Выдан кем ['.$arr['passportvidankem'].']. Адрес ['.$arr['address'].']. Полис ['.$arr['polis'].']. Дата ['.$arr['polisdata'].']. Страховая ['.$arr['insure'].']. Лечащий врач [стоматология]: ['.$arr['therapist'].']. Лечащий врач [косметология]: ['.$arr['therapist2'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_clients` SET `sex`='{$sex}', `birthday`='{$birthday}', `therapist`='{$therapist}', `therapist2`='{$therapist2}', `comment`='{$comment}', `card`='{$card}', `telephone`='{$telephone}', `passport`='{$passport}', `alienpassportser`='{$alienpassportser}', `alienpassportnom`='{$alienpassportnom}', `passportvidandata`='{$passportvidandata}', `passportvidankem`='{$passportvidankem}', `address`='{$address}', `polis`='{$polis}', `last_edit_time`='{$time}', `last_edit_person`='{$session_id}', `fo`='{$fo}', `io`='{$io}', `oo`='{$oo}', `htelephone`='{$htelephone}', `telephoneo`='{$telephoneo}', `htelephoneo`='{$htelephoneo}', `polisdata`='{$polisdata}', `insure`='{$insurecompany}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирован пациент ['.$id.']. ['.date('d.m.y H:i', $time).']. Комментарий: ['.$comment.']. Карта: ['.$card.']. Дата рождения: ['.$birthday.']. Пол: ['.$sex.']. Телефон: ['.$telephone.']. Серия/номер паспорта ['.$passport.']. Серия/номер паспорта (иностр.) ['.$alienpassportser.'/'.$passportvidandata.']. Дата выдачи ['.$passportvidandata.']. Выдан кем ['.$passportvidankem.']. Адрес ['.$address.']. Полис ['.$polis.']. Дата ['.$polisdata.']. Страховая ['.$insurecompany.']. Лечащий врач [стоматология]: ['.$therapist.']. Лечащий врач [косметология]: ['.$therapist2.']');
	}*/

	//Добавление цены услуги.
	function WriteToDB_EditPricePrice ($item, $price, $price2, $price3, $fromdate, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_priceprices` (
			`item`, `price`, `price2`, `price3`, `date_from`, `create_time`, `create_person`) 
			VALUES (
		'{$item}', '{$price}', '{$price2}', '{$price3}', '{$fromdate}', '{$time}', '{$session_id}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');
		
		return ($mysql_insert_id);
	}
	
	//Добавление цены услуги страховое
	function WriteToDB_EditPricePrice_insure ($item, $insure, $price,  $price2,  $price3, $fromdate, $session_id){

        $msql_cnnct = ConnectToDB ();

		$time = time();
		$query = "INSERT INTO `spr_priceprices_insure` (
			`item`, `insure`, `price`, `price2`, `price3`, `date_from`, `create_time`, `create_person`) 
			VALUES (
		'{$item}', '{$insure}', '{$price}', '{$price2}', '{$price3}', '{$fromdate}', '{$time}', '{$session_id}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        CloseDB ($msql_cnnct);
		
		//логирование
		//AddLog (GetRealIp(), $session_id, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');
		
		return ($mysql_insert_id);
	}

	//Вставка записей в журнал Cosmet из-под Web
	function WriteToDB_EditCosmet ($office, $client, $description, $create_time, $create_person, $worker, $comment, $pervich, $zapis_date, $zapis_id){
		$param = '';
		$values = '';
		$for_log = '';
		foreach($description as $key => $value){
			$param .= "`{$key}`, ";
			$values .= "'{$value}', ";
			
			$for_log .= $key.' => '.$value;
		}

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `journal_cosmet1` (
			`office`, `client`, $param `create_time`, `create_person`, `worker`, `comment`, `pervich`, `zapis_date`, `zapis_id`) 
			VALUES (
			'{$office}', '{$client}', $values '{$create_time}', '{$create_person}', '{$worker}', '{$comment}', '{$pervich}', '{$zapis_date}', '{$zapis_id}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлено посещение. ['.date('d.m.y H:i', $create_time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
		
		return ($mysql_insert_id);
	}
	
	//Обновление записей в журнале Cosmet из-под Web
	function WriteToDB_UpdateCosmet ($id, $office, $last_edit_time, $last_edit_person, $comment, $create_time, $rezult){
		//$param = '';
		$values = '';
		$for_log = '';
		$old = '';
		$arr = array();
		$rez = array();
		foreach($rezult as $key => $value){
			//$param .= "`{$key}`, ";
			$values .= "`{$key}` = '{$value}', ";
			
			$for_log .= '['.$key.' => '.$value.']';
		}

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `journal_cosmet1` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			$old = ' ОФис: ['.$rez[0]['office'].']. Комментарий: ['.$rez[0]['comment'].']';
			foreach ($rez[0] as $key => $value){
				//!!! Лайфхак
				if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && 
				($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
					$key = str_replace('c', '', $key);
					$old .= '['.$key.' => '.$value.']';
				}
			}
		}else{
			$old = 'Не нашли старую запись.';
		}

		$time = time();

		$query = "UPDATE `journal_cosmet1` SET $values `create_time`='{$create_time}', `last_edit_time`='{$time}', `last_edit_person`='{$last_edit_person}', `office`='{$office}', `comment`='{$comment}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Редактировано посещение косметолога ['.$id.']. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Описание: ['.$for_log.']. Комментарий: ['.$comment.']');
	}
	
	//Обновление записей в журнале IT заявок из-под Web (!Назначить исполнителя)
	function WriteToJournal_Update_Worker ($id, $worker, $last_edit_person, $db){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT `worker` FROM `$db` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = '['.$arr['worker'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}

		$time = time();

		$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$last_edit_person}', `worker`='{$worker}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Обновлён исполнитель в  ['.$db.']:['.$id.']. ['.date('d.m.y H:i', $time).']. Исполнитель: ['.$worker.']');
	}
	
	//Обновление записей в журнале IT заявок из-под Web (!Назначить исполнителя)
	function WriteToJournal_Update ($id, $office, $description, $last_edit_person, $priority, $db){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `$db` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'ОФис: ['.$arr['office'].']. Приоритет: ['.$arr['priority'].']. Описание: ['.$arr['description'].'].';
		}else{
			$old = 'Не нашли старую запись.';
		}	
		$time = time();
		if ($db == 'journal_soft'){
			$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$last_edit_person}', `full_description`='{$description}' WHERE `id`='{$id}'";
		}else{
			$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$last_edit_person}', `office`='{$office}', `description`='{$description}', `priority`='{$priority}' WHERE `id`='{$id}'";
		}

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Обновлёна заявка в  ['.$db.']:['.$id.']. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Приоритет: ['.$priority.']. Описание: ['.$description.'].');
	}
	
	//Обновление записей в журнале IT заявок из-под Web(!Закрытие)
	function WriteToJournal_Update_Close ($id, $user_id){
		//$old = '';

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `journal_it` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='{$time}', `worker`='{$user_id}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		//CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Закрыта заявка в IT ['.$id.']. ['.date('d.m.y H:i', $time).']');
	}
	
	//Обновление записей в журнале ПО заявок из-под Web(!Закрытие)
	function WriteToJournal_SoftUpdate_Close ($id, $user_id){
		//$old = '';

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `journal_soft` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='{$time}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Закрыта заявка в ПО ['.$id.']. ['.date('d.m.y H:i', $time).']');	
	}
	
	//Обновление записей в журнале заявок из-под Web (!Переоткрытие)
	function WriteToJournal_Update_ReOpen ($id, $user_id, $db){
		//$old = '';

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Переоткрыта заявка в ПО ['.$id.']. ['.date('d.m.y H:i', $time).']');
	}
	
	//Вставка и обновление списка пользователей из-под Web
	function WriteWorkerToDB_Edit ($session_id, $login, $name, $full_name, $password, $contacts, $permissions, $org){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_workers` (
			`login`, `name`, `full_name`, `password`, `contacts`, `permissions`, `org`)
			VALUES (
			'{$login}', '{$name}', '{$full_name}', '{$password}', '{$contacts}', '{$permissions}', '{$org}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен сотрудник. ['.date('d.m.y H:i', $time).']. Логин: ['.$login.']:['.$full_name.']. Контакты: ['.$contacts.']. Права: ['.$permissions.']. Организация: ['.$org.']');
	}
	
	//Вставка и обновление списка пациентов из-под Web
	function WriteClientToDB_Edit ($session_id, $name, $full_name, $f, $i, $o, $fo, $io, $oo, $comment, $card, $therapist, $therapist2, $birthday, $birthday2, $sex, $telephone, $htelephone, $telephoneo, $htelephoneo, $passport, $alienpassportser, $alienpassportnom, $passportvidandata, $passportvidankem, $address, $polis, $polisdata, $insurecompany){

	    $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_clients` (
			`name`, `full_name`, `f`, `i`, `o`, `fo`, `io`, `oo`, `comment`, `card`, `sex`, `birthday`, `birthday2`, `telephone`, `htelephone`, `telephoneo`, `htelephoneo`, `passport`, `alienpassportser`, `alienpassportnom`, `passportvidandata`, `passportvidankem`, `address`, `polis`, `polisdata`, `insure`, `therapist`, `therapist2`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`)
			VALUES (
			'{$name}', '{$full_name}', '{$f}', '{$i}', '{$o}', '{$fo}', '{$io}', '{$oo}', '{$comment}', '{$card}', '{$sex}', '{$birthday}', '{$birthday2}', '{$telephone}', '{$htelephone}', '{$telephoneo}', '{$htelephoneo}', '{$passport}', '{$alienpassportser}', '{$alienpassportnom}', '{$passportvidandata}', '{$passportvidankem}', '{$address}', '{$polis}', '{$polisdata}', '{$insurecompany}', '{$therapist}', '{$therapist2}', '{$time}', '{$session_id}', '0', '0') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен пациент. ['.date('d.m.y H:i', $time).']. ['.$full_name.']. Комментарий: ['.$comment.']. Карта: ['.$card.']. Пол: ['.$sex.']. Дата рождения: ['.$birthday.']. Телефон: ['.$telephone.'].  Телефон2: ['.$htelephone.']. Серия/номер паспорта ['.$passport.']. Серия/номер паспорта (иностр.) ['.$alienpassportser.'/'.$passportvidandata.']. Дата выдачи ['.$passportvidandata.']. Выдан кем ['.$passportvidankem.']. Адрес ['.$address.']. Полис ['.$polis.']. Дата полиса ['.$polisdata.']. Страховая компания ['.$insurecompany.']. Лечащий врач [стоматология]: ['.$therapist.']. Лечащий врач [косметология]: ['.$therapist2.']');
		
		return ($mysql_insert_id);
	}
	
	
	//Обновление карточки пациента из-под Web
	function WriteClientToDB_Update ($session_id, $id, $comment, $card, $therapist, $therapist2, $birthday, $birthday2, $sex, $telephone, $passport, $alienpassportser, $alienpassportnom, $passportvidandata, $passportvidankem, $address, $polis, $fo, $io, $oo, $htelephone, $telephoneo, $htelephoneo, $polisdata, $insurecompany){

	    $old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Комментарий: ['.$arr['comment'].']. Карта: ['.$arr['card'].']. Дата рождения: ['.$arr['birthday'].']. Пол: ['.$arr['sex'].']. Телефон: ['.$arr['telephone'].']. Серия/номер паспорта ['.$arr['passport'].']. Серия/номер паспорта (иностр.) ['.$arr['alienpassportser'].'/'.$arr['passportvidandata'].']. Дата выдачи ['.$arr['passportvidandata'].']. Выдан кем ['.$arr['passportvidankem'].']. Адрес ['.$arr['address'].']. Полис ['.$arr['polis'].']. Дата ['.$arr['polisdata'].']. Страховая ['.$arr['insure'].']. Лечащий врач [стоматология]: ['.$arr['therapist'].']. Лечащий врач [косметология]: ['.$arr['therapist2'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}

		$time = time();

		$query = "UPDATE `spr_clients` SET `sex`='{$sex}', `birthday`='{$birthday}', `birthday2`='{$birthday2}', `therapist`='{$therapist}', `therapist2`='{$therapist2}', `comment`='{$comment}', `card`='{$card}', `telephone`='{$telephone}', `passport`='{$passport}', `alienpassportser`='{$alienpassportser}', `alienpassportnom`='{$alienpassportnom}', `passportvidandata`='{$passportvidandata}', `passportvidankem`='{$passportvidankem}', `address`='{$address}', `polis`='{$polis}', `last_edit_time`='{$time}', `last_edit_person`='{$session_id}', `fo`='{$fo}', `io`='{$io}', `oo`='{$oo}', `htelephone`='{$htelephone}', `telephoneo`='{$telephoneo}', `htelephoneo`='{$htelephoneo}', `polisdata`='{$polisdata}', `insure`='{$insurecompany}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирован пациент ['.$id.']. ['.date('d.m.y H:i', $time).']. Комментарий: ['.$comment.']. Карта: ['.$card.']. Дата рождения: ['.$birthday.']. Пол: ['.$sex.']. Телефон: ['.$telephone.']. Серия/номер паспорта ['.$passport.']. Серия/номер паспорта (иностр.) ['.$alienpassportser.'/'.$passportvidandata.']. Дата выдачи ['.$passportvidandata.']. Выдан кем ['.$passportvidankem.']. Адрес ['.$address.']. Полис ['.$polis.']. Дата ['.$polisdata.']. Страховая ['.$insurecompany.']. Лечащий врач [стоматология]: ['.$therapist.']. Лечащий врач [косметология]: ['.$therapist2.']');
	}

	//Удаление(блокировка) карточки пациента из-под Web
	function WriteClientToDB_Delete ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_clients` SET `status`='9' WHERE `id`='{$id}'";

		$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Заблокирован пациент ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

	//Удаление(блокировка) 
	/*function WritePricelistGroupToDB_Delete ($session_id, $id){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");

		$time = time();
		$query = "UPDATE `spr_storagegroup` SET `status`='9' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Из прайса удалена группа ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}*/

	//Обновление карточки пациента из-под Web
	function WriteClientToDB_Reopen ($session_id, $id){

	    $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_clients` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирован пациент ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}
	
	function WriteToDB_ReopenPriceGroup ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_storagegroup` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирована группа прайса ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}
	
	function WriteToDB_ReopenPriceItem ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_pricelist_template` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирована позиция прайса ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

    //Разблокировать  страховую
	function WriteToDB_ReopenInsure ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_insure` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирована страховая ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

    //Разблокировать  лабораторию
	function WriteToDB_ReopenLabor ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `spr_labor` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирована лаборатория ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

    //Разблокировать  сертификат
	function WriteToDB_ReopenCert ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

		$query = "UPDATE `journal_cert` SET `status`='0', `last_edit_time`='{$time}', `last_edit_person`='{$session_id}' WHERE `id`='{$id}'";

		$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирован сертификат ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

    //Разблокировать наряд
	function WriteToDB_ReopenInvoice ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `journal_invoice` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирован наряд ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

    //Разблокировать ордер
	function WriteToDB_ReopenOrder ($session_id, $id){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "UPDATE `journal_order` SET `status`='0' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Разблокирован наряд ['.$id.']. ['.date('d.m.y H:i', $time).'].');
	}

	//Обновление ФИО пациента из-под Web
	function WriteFIOClientToDB_Update($session_id, $id, $name, $full_name, $f, $i, $o){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Фамилия: ['.$arr['f'].']. Имя: ['.$arr['i'].']. Отчество: ['.$arr['o'].'].';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_clients` SET `name`='{$name}', `full_name`='{$full_name}', `f`='{$f}', `i`='{$i}', `o`='{$o}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактированы ФИО пациента ['.$id.']. ['.date('d.m.y H:i', $time).']. Фамилия: ['.$f.']. Имя: ['.$i.']. Отчество: ['.$o.'].');
	}

	//Обновление лечащего врача пациента из-под Web
	function UpdateTherapist ($session_id, $client_id, $therapist, $sw){

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT `therapist` FROM `spr_clients` WHERE `id`=$client_id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Лечащий врач: ['.$arr['therapist'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_clients` SET `therapist{$sw}`='{$therapist}' WHERE `id`='{$client_id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Отредактирован лечащий врач у пациента ['.$client_id.']. ['.date('d.m.y H:i', $time).']. Лечащий врач: ['.$therapist.'].');
	}
	
	//Обновление карточки пользователя из-под Web
	function WriteWorkerToDB_Update($session_id, $worker_id, $org, $permissions, $specializations, $contacts, $fired){

        $msql_cnnct = ConnectToDB ();

	    $old = '';

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_workers` WHERE `id`=$worker_id";
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Контакты: ['.$arr['contacts'].']. Организация: ['.$arr['org'].']. Права: ['.$arr['permissions'].']. Уволен: ['.$arr['fired'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `spr_workers` SET `org`='{$org}', `permissions`='{$permissions}', `contacts`='{$contacts}', `fired`='{$fired}' WHERE `id`='{$worker_id}'";

		$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //Специализации
        $query = "DELETE FROM `journal_work_spec` WHERE `worker_id`='".$worker_id."'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        if (!empty($specializations)){
            foreach ($specializations as $data){
                $query = "INSERT INTO `journal_work_spec` (`worker_id`, `specialization_id`) VALUES ('$worker_id', '$data')";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
            }
        }

		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирован пользователь ['.$worker_id.']. ['.date('d.m.y H:i', $time).']. Контакты: ['.$contacts.']. Организация: ['.$org.']. Права: ['.$permissions.']. Уволен: ['.$fired.']');
	}
	
	//Обновление ФИО пользователя из-под Web
	function WriteFIOUserToDB_Update($session_id, $id, $name, $full_name){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Фамилия: ['.$arr['f'].']. Имя: ['.$arr['i'].']. Отчество: ['.$arr['o'].'].';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_workers` SET `name`='{$name}', `full_name`='{$full_name}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактированы ФИО пользователя ['.$id.']. ['.date('d.m.y H:i', $time).']. ['.$full_name.'].');
	}

	//Вставка и обновление списка филиалов из-под Web
	function WriteFilialToDB_Edit ($session_id, $name, $address, $contacts){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_filials` (
			`name`, `address`, `contacts`)
			VALUES (
			'{$name}', '{$address}', '{$contacts}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен филиал. Имя: ['.$name.']. Адрес: ['.$address.']. Контакты: ['.$contacts.']');
	}
	
	//Вставка и обновление списка Страховых из-под Web
	function WriteInsureToDB_Edit ($session_id, $name, $contract, $contacts){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_insure` (
			`name`, `contract`, `contacts`)
			VALUES (
			'{$name}', '{$contract}', '{$contacts}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлена страховая. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.']');
	}
	
	//Редактирование карточки страховой из-под Web
	function WriteInsureToDB_Update ($session_id, $id, $name, $contract, $contract2, $contacts){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_insure` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Название: ['.$arr['name'].']. Договор: ['.$arr['contract'].']/['.$arr['contract2'].']. Контакты: ['.$arr['contacts'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_insure` SET `name`='{$name}', `contract`='{$contract}', `contract2`='{$contract2}', `contacts`='{$contacts}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирована страховая ['.$id.']. ['.date('d.m.y H:i', $time).']. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.'].');
	}
	
	//Вставка и обновление списка Лабораторий из-под Web
	function WriteLaborToDB_Edit ($session_id, $name, $contract, $contacts){

        $msql_cnnct = ConnectToDB ();

		$time = time();

		$query = "INSERT INTO `spr_labor` (
			`name`, `contract`, `contacts`)
			VALUES (
			'{$name}', '{$contract}', '{$contacts}') ";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлена лаборатория. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.']');
	}

	//Редактирование карточки лаборатории из-под Web
	function WriteLaborToDB_Update ($session_id, $id, $name, $contract, $contacts){
		$old = '';

        $msql_cnnct = ConnectToDB ();

		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_labor` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Название: ['.$arr['name'].']. Договор: ['.$arr['contract'].']. Контакты: ['.$arr['contacts'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_labor` SET `name`='{$name}', `contract`='{$contract}', `contacts`='{$contacts}' WHERE `id`='{$id}'";

		$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирована лаборатория ['.$id.']. ['.date('d.m.y H:i', $time).']. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.'].');
	}

	//Вставка и обновление Сертификата из-под Web
	function WriteCertToDB_Edit ($session_id, $num, $nominal){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

		$query = "INSERT INTO `journal_cert` (
			`num`, `nominal`, `create_time`, `create_person`)
			VALUES (
			'{$num}', '{$nominal}', '{$time}', '{$session_id}') ";

		//mysqli_query($query) or die($query.' -> '.mysql_error());
		//mysqli_close();

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        //ID новой позиции
        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен сертификат. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

		return ($mysql_insert_id);
	}

	//Редактирование Сертификата из-под Web
	function WriteCertToDB_Update ($session_id, $id, $name, $contract, $contacts){
		$old = '';

        $msql_cnnct = ConnectToDB ();

        //Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_labor` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Название: ['.$arr['name'].']. Договор: ['.$arr['contract'].']. Контакты: ['.$arr['contacts'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_labor` SET `name`='{$name}', `contract`='{$contract}', `contacts`='{$contacts}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирована лаборатория ['.$id.']. ['.date('d.m.y H:i', $time).']. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.'].');
	}

	//Вставка и обновление специализации из-под Web
	function WriteToDB_EditSpecialization ($name, $session_id){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

		$query = "INSERT INTO `spr_specialization` (
			`name`, `create_time`, `create_person`)
			VALUES (
			'{$name}', '{$time}', '{$session_id}') ";

		//mysqli_query($query) or die($query.' -> '.mysql_error());
		//mysqli_close();

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        //ID новой позиции
        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлена специализация ['.$name.']');

		return ($mysql_insert_id);
	}

	//Редактирование специализации из-под Web
	function WriteSpecializationToDB_Update ($id, $name, $session_id){
		$old = '';

        $msql_cnnct = ConnectToDB ();

        //Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_labor` WHERE `id`=$id";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			$arr = mysqli_fetch_assoc($res);
			$old = 'Название: ['.$arr['name'].']. Договор: ['.$arr['contract'].']. Контакты: ['.$arr['contacts'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();

		$query = "UPDATE `spr_labor` SET `name`='{$name}', `contract`='{$contract}', `contacts`='{$contacts}' WHERE `id`='{$id}'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        //CloseDB ($msql_cnnct);

		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирована лаборатория ['.$id.']. ['.date('d.m.y H:i', $time).']. Название: ['.$name.']. Договор: ['.$contract.']. Контакты: ['.$contacts.'].');
	}

	
	//Выборка из БД записей из таблицы $datatable
	function SelDataFromDB ($datatable, $sw, $type){
        //var_dump($type);

		$arr = array();
		$rez = array();
		$q = '';
		if ($sw == ''){
			if (($datatable == 'spr_workers') || ($datatable == 'spr_clients')){
				$q = " ORDER BY `full_name` ASC";
			}elseif ((($datatable == 'journal_it') || ($datatable == 'journal_soft'))&&($type == '')){
				$q = " ORDER BY `create_time` DESC";
			}elseif (($datatable == 'logs')&&($type == '')){
				$q = " ORDER BY `date` DESC LIMIT 20";
			}elseif ($type == 'sort_filial'){
				$q = " ORDER BY `office` ASC";
			}elseif ($type == 'sort_added'){
				$q = " ORDER BY `create_time` DESC";
			}elseif (($datatable == 'journal_cosmet') || ($datatable == 'journal_cosmet1') || ($datatable == 'journal_insure_download')){
				$q =  "ORDER BY `create_time` DESC";
			}elseif ($datatable == 'journal_cert'){
				$q =  "ORDER BY `num` ASC LIMIT {$type[0]}, {$type[1]}";
			}else{
				$q = '';
			}
		}else{
			if ($datatable == 'notes'){
				if ($type == 'dead_line'){
					$q = ' WHERE (`dead_line` < '.time().' OR `dead_line` = '.time().') AND `closed` <> 1 ORDER BY `dead_line` DESC';
				}else{
					$q = ' WHERE `'.$type.'` = '.$sw.' ORDER BY `dead_line` ASC';
				}
			}elseif ($datatable == 'removes'){
					$q = ' WHERE `'.$type.'` = '.$sw.' ORDER BY `create_time` DESC';
			}elseif ($datatable == 'removes_open'){
					$q = ' WHERE `'.$type.'` = '.$sw.' AND `closed` = 0 ORDER BY `create_time` DESC';
					$datatable = 'removes';
			}elseif (($datatable == 'journal_soft') && ($type == 'see_own')){
				$q = ' WHERE `create_person` = '.$sw.' ORDER BY `create_time` DESC';
			}elseif (($datatable == 'spr_kd_img') && ($type == 'img')){
				$q = ' WHERE `client` = '.$sw.' ORDER BY `uptime` ASC';
			}elseif (($datatable == 'journal_etaps') || ($datatable == 'journal_laborder')){
				if ($type == 'client'){
					$q =  " WHERE `client_id`='$sw'";
				}else{
					$q =  " WHERE `id`='$sw'";
				}
			}elseif (($datatable == 'journal_etaps_img')){
				$q =  " WHERE `etap`='$sw' ORDER BY `uptime` ASC";
			}elseif (($datatable == 'fl_spr_percents')){
				if ($sw == 88){
					//$q =  " WHERE `task`='$sw' ORDER BY `uptime` ASC";
				}else{
                    $q = " WHERE `type` = '$sw'";

                    if ($type == 'id'){
                        $q = " WHERE `id` = '$sw'";
                    }
				}
			}else{
				if ($type == 'filter'){
					if ($datatable == 'spr_clients'){
						$q = ' WHERE '.$sw.' ORDER BY `full_name` ASC';
					}elseif($datatable == 'journal_it'){
						$q = ' WHERE '.$sw.' ORDER BY `office` ASC, `create_time` DESC';
					}else{
						$q = ' WHERE '.$sw.' ORDER BY `create_time` DESC';
					}
				}
				if ($type == 'alpha'){
					if ($datatable == 'spr_clients'){
						$q = " WHERE `full_name`  LIKE '$sw%' ORDER BY `full_name` ASC";
					}else{
						//$q = ' WHERE '.$sw.' ORDER BY `create_time` DESC';
					}
				}
				if ($type == 'parrent'){
					$sw_temp = explode(':', $sw);
					$q = " WHERE `parent` = '{$sw_temp[1]}' AND `dtable`= '{$sw_temp[0]}' ORDER BY `create_time` ASC";
				}
				
				if ($type == 'workers'){
					$q = " WHERE `name` = '$sw'";
				}
				if ($type == 'worker_id'){
					$q = " WHERE `id` = '$sw'";
                    if ($datatable == 'journal_work_spec'){
                        $q = " WHERE `worker_id` = '$sw'";
                    }
				}
				if ($type == 'worker_stom_id'){
					$q = " WHERE `worker` = '$sw' ORDER BY `create_time` DESC";
				}
				if ($type == 'worker_cosm_id'){
					$q = " WHERE `worker` = '$sw' ORDER BY `create_time` DESC";
				}
				if ($type == 'full_name'){
					$q = " WHERE `full_name` = '$sw'";
				}
				if ($type == 'name'){
					$q = " WHERE `name` = '$sw'";
				}
				if ($type == 'services'){
					$q = " WHERE `services` = '$sw'";
				}
				if ($type == 'offices'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'login'){
					$q = " WHERE `login` = '$sw'";
				}
				if ($type == 'user'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'task'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'task_cosmet'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'client_id'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'id'){
					$q = " WHERE `id` = '$sw'";
				}
				if ($type == 'office_kabs'){
					$q = " WHERE `office` = '$sw'";
				}
				if ($type == 'client_full_name'){
					$q = " WHERE `full_name` = '$sw'";
				}
				if ($type == 'worker_full_name'){
					$q = " WHERE `full_name` = '$sw'";
				}
				if (($type == 'client_cosm_id') || ($type == 'client_stom_id')){
					$q = " WHERE `client` = '$sw' ORDER BY `create_time` DESC";
				}
				if ($type == 'ended_tasks'){
					if ($sw == 1){
						$q = " WHERE `end_time` = '0'";
					}elseif ($sw == 2){
						$q = " WHERE `end_time` <> '0'";
					}else{
						$q = "";
					}
				}
				if ($type == 'filial_tasks'){
					$q = " WHERE `office` = '$sw'";
				}
				if ($type == 'priority_tasks'){
					$q = " WHERE `priority` = '$sw'";
				}
				if ($type == 'item'){
					$q = " WHERE `item` = '$sw'";
				}
				if ($type == 'level'){
					$q = " WHERE `level` = '$sw'";
				}
				if ($type == 'laborder_id'){
					$q = " WHERE `laborder_id` = '$sw'";
				}
				if ($type == 'type'){
					$q = " WHERE `type` = '$sw'";
				}
			}
		
		}
		
		/*
		$con = mysqli_connect("localhost","root","abcd123","payrolldb001") or die("Error " . mysqli_error($con));
		$sql="SELECT substationid,substationcode FROM wms_substation WHERE assemblylineid = '".$q."'";

		$result = mysqli_query($con,$sql);
		*/

        $msql_cnnct = ConnectToDB ();
		
		$query = "SELECT * FROM `$datatable`".$q;
		//echo $query;

		//$res = mysql_query($query) or die(mysql_error().' -> '.$query);
        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
		
		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;

        CloseDB ($msql_cnnct);
	}

	//Выборка для быстрого поиска по имени
	function SelForFastSearch ($datatable, $search_data){

        $msql_cnnct = ConnectToDB ();

        $arr = array();
        $rez = array();

		//!Использовать надо везде. Очищение данных от мусора
		$search_data = trim(strip_tags(stripcslashes(htmlspecialchars($search_data))));
		$datatable = trim(strip_tags(stripcslashes(htmlspecialchars($datatable))));

		//$query = "SELECT * FROM `$datatable` WHERE `full_name` LIKE '%$search_data%' LIMIT 5";
		$query = "SELECT * FROM `$datatable` WHERE `name` LIKE '%$search_data%' AND `status`<> 9 ORDER BY `name` ASC LIMIT 10";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
	}
	
	//Выборка для быстрого поиска сертификата
	function SelForFastSearchCert ($datatable, $search_data){

        $msql_cnnct = ConnectToDB ();

        $rez = array();

		//!Использовать надо везде. Очищение данных от мусора
		$search_data = trim(strip_tags(stripcslashes(htmlspecialchars($search_data))));
		$datatable = trim(strip_tags(stripcslashes(htmlspecialchars($datatable))));

		//$query = "SELECT * FROM `$datatable` WHERE `full_name` LIKE '%$search_data%' LIMIT 5";
		$query = "SELECT * FROM `$datatable` WHERE `num` LIKE '%$search_data%' AND `status`<> 9 ORDER BY `num` ASC LIMIT 3";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
		}

		return $rez;
	}

	function SelForFastSearchFullName ($datatable, $search_data){

        $msql_cnnct = ConnectToDB ();

		//$arr = array();
		$rez = array();

		//!Использовать надо везде. Очищение данных от мусора
		$search_data = trim(strip_tags(stripcslashes(htmlspecialchars($search_data))));
		$datatable = trim(strip_tags(stripcslashes(htmlspecialchars($datatable))));

		$query = "SELECT * FROM `$datatable` WHERE (
                  LOWER(`full_name`) RLIKE LOWER('^$search_data') 
                  OR
                  UPPER(`card`) LIKE UPPER('%$search_data%') 
                  OR
                  `telephone` LIKE '%$search_data%'
                  OR
                  `htelephone` LIKE '%$search_data%'
                  OR
                  `telephoneo` LIKE '%$search_data%'
                  OR
                  `htelephoneo` LIKE '%$search_data%'
                  OR
                  `passport` RLIKE '^$search_data'
                  OR
                  `id` RLIKE '^$search_data'
                  ) AND `status`<> 9 ORDER BY `full_name` ASC LIMIT 10";

	//	$query = "SELECT * FROM `$datatable` WHERE `full_name` LIKE '%$search_data%' ORDER BY `full_name` ASC LIMIT 10";
	//	$query = "SELECT * FROM `$datatable` WHERE `name` LIKE '%$search_data%' LIMIT 10";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);
		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;

		//CloseDB ($msql_cnnct);
		
	}
	
	
	
	//Выборка максимального значения из БД
	function SelMAXDataFromDB ($datatable, $col){
		$arr = array();
		$rez = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM $datatable WHERE  $col = (SELECT MAX($col) FROM $datatable);";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;

        CloseDB ($msql_cnnct);

	}

	//Выборка минимального значения из БД
	function SelMINDataFromDB ($datatable, $col){
		$arr = array();
		$rez = array();

        $msql_cnnct = ConnectToDB ();

		$query = "SELECT * FROM $datatable WHERE $col = (SELECT MIN($col) FROM $datatable);";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

		$number = mysqli_num_rows($res);

		if ($number != 0){
			while ($arr = mysqli_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;

        CloseDB ($msql_cnnct);

	}

?>