<?php

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
	
	//Логирование.
	function AddLog ($ip, $creator, $description_old, $description_new){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `logs` (
			`date`, `ip`, `mac`, `creator`, `description_old`, `description_new`) 
			VALUES (
			'{$time}', '{$ip}', '', '{$creator}', '{$description_old}', '{$description_new}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
	}

	//Получаем подсеть из IP
	function GetSubFromIP($ip){
		$rez = array();
		$rez = explode('.', $ip);
		return $rez[2];
	}
	
	//Список всех таблиц подсетей
	function SubTables (){
		require 'config.php';
		$rez = '';
		$subs = array();
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		$res = mysql_query("SHOW TABLES") or die(mysql_error());
		while ($row = mysql_fetch_row($res)) {
			if (($row[0] != 'catalog') && ($row[0] != 'reserv') && ($row[0] != 'types') && ($row[0] != 'users') && ($row[0] != 'messages')){
				$rez .= $row[0].';';
			}
		}
		$subs = explode (';', $rez);
		natsort($subs);
		return array_values($subs);
		mysql_close();
	}
	
	//Очистка всех таблиц subs в БД - НЕ ИСПОЛЬЗУЕМ!!!
	/*function ClearDB_IPList (){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error());
		$subs = SubTables();
		for ($i=1;$i<count($subs);$i++){
			mysql_query("TRUNCATE TABLE `$datatable`") or die(mysql_error());
		}
		mysql_close();
	}*/

	//Очистка Reserv- НЕ ИСПОЛЬЗУЕМ!!!
	/*function ClearReserv_IPList (){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error());
		mysql_query("TRUNCATE TABLE `$r_datatable`") or die(mysql_error());
		mysql_close();
	}*/


	
	//Вставка записей в журнал IT заявок из-под Web
	function WriteToDB_Edit ($office, $description, $create_time, $create_person, $last_edit_time, $last_edit_person, $worker, $end_time, $priority){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//!сделать время отсюда?
		$time = time();
		$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `journal_it` (
			`office`, `description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `end_time`, `priority`) 
			VALUES (
			'{$office}', '{$description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '{$worker}', '{$end_time}', '{$priority}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлена заявка в IT. ['.date('d.m.y H:i', $create_time).']. Офис ['.$office.']. Описание: ['.$description.']');
	}
	
	//Вставка записей в журнал ПО заявок из-под Web
	function WriteToDB_EditSoft ($description, $full_description, $create_time, $create_person, $last_edit_time, $last_edit_person, $worker, $end_time, $priority){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//!сделать время отсюда?
		$time = time();
		//$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `journal_soft` (
			`description`, `full_description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `end_time`, `priority`) 
			VALUES (
			'{$description}', '{$full_description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '1', '{$end_time}', '{$priority}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлена заявка в ПО. ['.date('d.m.y H:i', $create_time).']. Раздел ['.$priority.']. Описание: ['.$description.']:['.$full_description.']');
	}
	
	//Вставка комментариев из-под Web
	function WriteToDB_EditComments ($dtable, $description, $create_time, $create_person, $last_edit_time, $last_edit_person, $parent){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//!сделать время отсюда?
		$time = time();
		//$description = trim($description, " \t\n\r\0\x0B");
		$query = "INSERT INTO `comments` (
			`dtable`, `description`, `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `parent`) 
			VALUES (
			'{$dtable}', '{$description}', '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '{$parent}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлен комментарий. ['.date('d.m.y H:i', $create_time).']. ['.$dtable.']:['.$parent.']. Описание: ['.$description.']');

	}
	
	//Вставка записей в расписание
	function WriteToDB_EditScheduler ($datatable, $year, $month, $day, $office, $kab, $smena, $smena_t, $worker, $create_person){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `$datatable` (
			`year`, `month`, `day`, `office`, `kab`, `smena`, `smena_t`, `worker`, `create_person`) 
			VALUES (
			'{$year}', '{$month}', '{$day}', '{$office}', '{$kab}', '{$smena}', '{$smena_t}', '{$worker}', '{$create_person}') ";
		//echo $query;
		
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Вставка записей во временную запись
	function WriteToDB_EditZapis ($datatable, $year, $month, $day, $office, $kab, $worker, $create_person, $patient, $contacts, $description, $start_time, $wt){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `$datatable` (
			`year`, `month`, `day`, `office`, `kab`, `worker`, `create_person`, `patient`, `contacts`, `description`, `start_time`, `wt`) 
			VALUES (
			'{$year}', '{$month}', '{$day}', '{$office}', '{$kab}', '{$worker}', '{$create_person}', '{$patient}', '{$contacts}', '{$description}', '{$start_time}', '{$wt}') ";
		//echo $query;
		
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Вставка записей в расписание
	function WriteToDB_DeleteScheduler ($datatable, $id){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "DELETE FROM $datatable WHERE `id`=$id";
		//echo $query;
		
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Вставка записей в расписание
	function WriteToDB_UpdateScheduler ($datatable, $id, $smena){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "UPDATE $datatable SET `smena`=$smena WHERE `id`=$id";
		//echo $query;
		
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		//!!!AddLog (GetRealIp(), $create_person, '', 'Изменение в расписании. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
	}

	//Вставка записей в журнал Cosmet из-под Web
	function WriteToDB_EditCosmet ($office, $client, $description, $create_time, $create_person, $last_edit_time, $last_edit_person, $worker, $comment){
		$param = '';
		$values = '';
		$for_log = '';
		foreach($description as $key => $value){
			$param .= "`{$key}`, ";
			$values .= "'{$value}', ";
			
			$for_log .= $key.' => '.$value;
		}
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `journal_cosmet1` (
			`office`, `client`, $param `create_time`, `create_person`, `last_edit_time`, `last_edit_person`, `worker`, `comment`) 
			VALUES (
			'{$office}', '{$client}', $values '{$create_time}', '{$create_person}', '{$last_edit_time}', '{$last_edit_person}', '{$worker}', '{$comment}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $create_person, '', 'Добавлено посещение. ['.date('d.m.y H:i', $create_time).']. ОФис: ['.$office.']. Пациент: ['.$client.']. Описание: ['.$for_log.']. Комментарий: '.$comment);
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
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `journal_cosmet1` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
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
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Редактировано посещение косметолога ['.$id.']. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Описание: ['.$for_log.']. Комментарий: ['.$comment.']');
	}
	
	//Обновление записей в журнале IT заявок из-под Web (!Назначить исполнителя)
	function WriteToJournal_Update_Worker ($id, $worker, $last_edit_person, $db){
		$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT `worker` FROM `$db` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
			$old = '['.$arr['worker'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$last_edit_person}', `worker`='{$worker}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Обновлён исполнитель в  ['.$db.']:['.$id.']. ['.date('d.m.y H:i', $time).']. Исполнитель: ['.$worker.']');
	}
	
	//Обновление записей в журнале IT заявок из-под Web (!Назначить исполнителя)
	function WriteToJournal_Update ($id, $office, $description, $last_edit_person, $priority, $db){
		$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `$db` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
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
		
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $last_edit_person, $old, 'Обновлёна заявка в  ['.$db.']:['.$id.']. ['.date('d.m.y H:i', $time).']. ОФис: ['.$office.']. Приоритет: ['.$priority.']. Описание: ['.$description.'].');
	}
	
	//Обновление записей в журнале IT заявок из-под Web(!Закрытие)
	function WriteToJournal_Update_Close ($id, $user_id){
		//$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "UPDATE `journal_it` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='{$time}', `worker`='{$user_id}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Закрыта заявка в IT ['.$id.']. ['.date('d.m.y H:i', $time).']');
	}
	
	//Обновление записей в журнале ПО заявок из-под Web(!Закрытие)
	function WriteToJournal_SoftUpdate_Close ($id, $user_id){
		//$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "UPDATE `journal_soft` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='{$time}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Закрыта заявка в ПО ['.$id.']. ['.date('d.m.y H:i', $time).']');	
	}
	
	//Обновление записей в журнале заявок из-под Web (!Переоткрытие)
	function WriteToJournal_Update_ReOpen ($id, $user_id, $db){
		//$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "UPDATE `{$db}` SET `last_edit_time`='{$time}', `last_edit_person`='{$user_id}', `end_time`='0' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $user_id, '', 'Переоткрыта заявка в ПО ['.$id.']. ['.date('d.m.y H:i', $time).']');
	}
	
	//Вставка и обновление списка пользователей из-под Web
	function WriteWorkerToDB_Edit ($session_id, $login, $name, $full_name, $password, $contacts, $permissions, $org){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `spr_workers` (
			`login`, `name`, `full_name`, `password`, `contacts`, `permissions`, `org`)
			VALUES (
			'{$login}', '{$name}', '{$full_name}', '{$password}', '{$contacts}', '{$permissions}', '{$org}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен сотрудник. ['.date('d.m.y H:i', $time).']. Логин: ['.$login.']:['.$full_name.']. Контакты: ['.$contacts.']. Права: ['.$permissions.']. Организация: ['.$org.']');
	}
	
	//Вставка и обновление списка пациентов из-под Web
	function WriteClientToDB_Edit ($session_id, $name, $full_name, $f, $i, $o, $contacts, $therapist, $therapist2, $birthday, $sex){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `spr_clients` (
			`name`, `full_name`, `f`, `i`, `o`, `contacts`, `sex`, `birthday`, `therapist`, `therapist2`)
			VALUES (
			'{$name}', '{$full_name}', '{$f}', '{$i}', '{$o}', '{$contacts}', '{$sex}', '{$birthday}', '{$therapist}', '{$therapist2}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен пациент. ['.date('d.m.y H:i', $time).']. ['.$full_name.']. Контакты: ['.$contacts.']. Пол: ['.$sex.']. Дата рождения: ['.$birthday.']. Лечащий врач [стоматология]: ['.$therapist.']. Лечащий врач [косметология]: ['.$therapist2.']');
	}
	
	
	//Обновление карточки пациента из-под Web
	function WriteClientToDB_Update($session_id, $id, $contacts, $therapist, $therapist2, $birthday, $sex){
		$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
			$old = 'Контакты: ['.$arr['contacts'].']. Дата рождения: ['.$arr['birthday'].']. Пол: ['.$arr['sex'].']. Лечащий врач [стоматология]: ['.$arr['therapist'].']. Лечащий врач [косметология]: ['.$arr['therapist2'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `spr_clients` SET `sex`='{$sex}', `birthday`='{$birthday}', `therapist`='{$therapist}', `therapist2`='{$therapist2}', `contacts`='{$contacts}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирован пациент ['.$id.']. ['.date('d.m.y H:i', $time).']. Контакты: ['.$contacts.']. Дата рождения: ['.$birthday.']. Пол: ['.$sex.']. Лечащий врач [стоматология]: ['.$therapist.']. Лечащий врач [косметология]: ['.$therapist2.']');
	}

	//Обновление ФИО пациента из-под Web
	function WriteFIOClientToDB_Update($session_id, $id, $name, $full_name, $f, $i, $o){
		$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_clients` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
			$old = 'Фамилия: ['.$arr['f'].']. Имя: ['.$arr['i'].']. Отчество: ['.$arr['o'].'].';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `spr_clients` SET `name`='{$name}', `full_name`='{$full_name}', `f`='{$f}', `i`='{$i}', `o`='{$o}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактированы ФИО пациента ['.$id.']. ['.date('d.m.y H:i', $time).']. Фамилия: ['.$f.']. Имя: ['.$i.']. Отчество: ['.$o.'].');
	}

	//Обновление лечащего врача пациента из-под Web
	function UpdateTherapist ($session_id, $client_id, $therapist, $sw){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT `therapist` FROM `spr_clients` WHERE `id`=$client_id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
			$old = 'Лечащий врач: ['.$arr['therapist'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `spr_clients` SET `therapist{$sw}`='{$therapist}' WHERE `id`='{$client_id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Отредактирован лечащий врач у пациента ['.$client_id.']. ['.date('d.m.y H:i', $time).']. Лечащий врач: ['.$therapist.'].');
	}
	
	//Обновление карточки пользователя из-под Web
	function WriteWorkerToDB_Update($session_id, $id, $org, $permissions, $contacts, $fired){
		$old = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		//Для лога соберем сначала то, что было в записи.
		$query = "SELECT * FROM `spr_workers` WHERE `id`=$id";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			$arr = mysql_fetch_assoc($res);
			$old = 'Контакты: ['.$arr['contacts'].']. Организация: ['.$arr['org'].']. Права: ['.$arr['permissions'].']. Уволен: ['.$arr['fired'].']';
		}else{
			$old = 'Не нашли старую запись.';
		}
		$time = time();
		$query = "UPDATE `spr_workers` SET `org`='{$org}', `permissions`='{$permissions}', `contacts`='{$contacts}', `fired`='{$fired}' WHERE `id`='{$id}'";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, $old, 'Отредактирован пользователь ['.$id.']. ['.date('d.m.y H:i', $time).']. Контакты: ['.$contacts.']. Организация: ['.$org.']. Права: ['.$permissions.']. Уволен: ['.$fired.']');
	}

	//Вставка и обновление списка филиалов из-под Web
	function WriteFilialToDB_Edit ($session_id, $name, $address, $contacts){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$time = time();
		$query = "INSERT INTO `spr_office` (
			`name`, `address`, `contacts`)
			VALUES (
			'{$name}', '{$address}', '{$contacts}') ";
		mysql_query($query) or die(mysql_error());
		mysql_close();
		
		//логирование
		AddLog (GetRealIp(), $session_id, '', 'Добавлен филиал. Имя: ['.$name.']. Адрес: ['.$address.']. Контакты: ['.$contacts).']';
	}
	
	//Очистка записи
	function WriteToDB_Clr ($ip){
		$q = '';
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
			$time = time();
			$userip = GetRealIp();
			$sub = GetSubFromIP($ip);
			$query = "UPDATE `sub{$sub}` SET `name`='', `mac`='', `place`='', `type`='0', `comment`='', `time`='{$time}', `userip`='{$userip}', `sw`='0', `port`='0', `port2`='0', `swtype`='0', `login`='0', `pass`='0'  WHERE `ip`='{$ip}'";
			mysql_query($query) or die(mysql_error());
		mysql_close();
	}	
	
	//Выборка из БД всех записей в journal
	//попробовать подбить в нижнюю ф-цию
	function SelDataFromJournal (){
		$arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `journal_it`";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
	}	
	
	
	//Выборка из БД записей из таблицы $datatable
	function SelDataFromDB ($datatable, $sw, $type){
		$arr = array();
		$rez = array();
		if ($sw == ''){
			if (($datatable == 'spr_workers') || ($datatable == 'spr_clients')){
				$q = " ORDER BY `full_name` ASC";
			}elseif ((($datatable == 'journal_it') || ($datatable == 'journal_soft'))&&($type == '')){
				$q = " ORDER BY `create_time` DESC";
			}elseif (($datatable == 'logs')&&($type == '')){
				$q = " ORDER BY `date` DESC";
			}elseif ($type == 'sort_filial'){
				$q = " ORDER BY `office` ASC";
			}elseif ($type == 'sort_added'){
				$q = " ORDER BY `create_time` DESC";
			}elseif (($datatable == 'journal_cosmet') || ($datatable == 'journal_cosmet1')){
				$q =  "ORDER BY `create_time` DESC";
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
			}
		
		}
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM `$datatable`".$q;
		//echo $query;
		$res = mysql_query($query) or die($q);
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
	}
	
	function SelForFastSearch ($datatable, $search_data){
		$arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Невозможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		
		//!Использовать надо везде. Очищение данных от мусора
		$search_data = trim(strip_tags(stripcslashes(htmlspecialchars($search_data))));
		$datatable = trim(strip_tags(stripcslashes(htmlspecialchars($datatable))));

	//	$query = "SELECT * FROM `$datatable` WHERE `full_name` LIKE '%$search_data%' LIMIT 5";
		$query = "SELECT * FROM `$datatable` WHERE `name` LIKE '%$search_data%' ORDER BY `name` ASC LIMIT 10";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
		
	}
	
	function SelForFastSearchFullName ($datatable, $search_data){
		$arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Невозможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		
		//!Использовать надо везде. Очищение данных от мусора
		$search_data = trim(strip_tags(stripcslashes(htmlspecialchars($search_data))));
		$datatable = trim(strip_tags(stripcslashes(htmlspecialchars($datatable))));

	//	$query = "SELECT * FROM `$datatable` WHERE `full_name` RLIKE '^$search_data' ORDER BY `full_name` ASC LIMIT 10";
		$query = "SELECT * FROM `$datatable` WHERE `full_name` LIKE '%$search_data%' ORDER BY `full_name` ASC LIMIT 10";
	//	$query = "SELECT * FROM `$datatable` WHERE `name` LIKE '%$search_data%' LIMIT 10";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				//echo "\n<li>".$row["name"]."</li>"; //$row["name"] - имя таблицы
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
		
	}
	
	
	
	//Выборка максимального значения из БД
	function SelMAXDataFromDB ($datatable, $col){
		$arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM $datatable WHERE  $col = (SELECT MAX($col) FROM $datatable);";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
	}

	//Выборка минимального значения из БД
	function SelMINDataFromDB ($datatable, $col){
		$arr = array();
		$rez = array();
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		mysql_query("SET NAMES 'utf8'");
		$query = "SELECT * FROM $datatable WHERE $col = (SELECT MIN($col) FROM $datatable);";
		$res = mysql_query($query) or die(mysql_error());
		$number = mysql_num_rows($res);
		if ($number != 0){
			while ($arr = mysql_fetch_assoc($res)){
				array_push($rez, $arr);
			}
			return $rez;
		}else
			return 0;
		mysql_close();
	}

	//Добавление новой ТАБЛИЦЫ подсети ($subnet[$i])
	function AddNewSubTable ($subnet){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error());
		$query = "
			CREATE TABLE `sub{$subnet}` (
				`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
				`ip` varchar(15) NOT NULL,
				`name` text NOT NULL,
				`mac` varchar(17) NOT NULL DEFAULT '00:00:00:00:00:00',
				`place` text NOT NULL,
				`type` int(1) unsigned NOT NULL,
				`comment` text NOT NULL,
				`time` int(10) unsigned NOT NULL DEFAULT '0',
				`userip` varchar(15) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `ip` (`ip`)
			)
			ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		mysql_query($query) or die(mysql_error());
		mysql_close();
	}
	
	//Удаление ТАБЛИЦЫ ($subnet[$i])
	function DeleteSubTable ($subnet){
		require 'config.php';
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
		mysql_select_db($dbName) or die(mysql_error());
		$query = "DROP TABLE `sub{$subnet}`;";
		mysql_query($query) or die(mysql_error());
		mysql_close();
	}
	
	//Проверка на существование ТАБЛИЦЫ подсети subnet[$i]
	function SubTableExist ($subnet){
		require 'config.php';
		$rez = FALSE;
		mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
		mysql_select_db($dbName) or die(mysql_error()); 
		$res = mysql_query("SHOW TABLES") or die(mysql_error());
		while ($row = mysql_fetch_row($res)) {
			if (mb_strpos($row[0], $subnet) != -1){
				if ($row[0] == $subnet){
					$rez = TRUE;
				}
			}
		}
		return $rez;
		mysql_close();
	}
	
?>