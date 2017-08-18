<?php

//fl_DBWork.php


    //Подключение к БД MySQl
    function ConnectToDB () {
        require 'fl_config.php';

        $msql_cnnct = mysqli_connect($hostname, $username, $db_pass, $dbName) or die("Не возможно создать соединение ");
		
        mysqli_query($msql_cnnct, "SET NAMES 'utf8'");

        return $msql_cnnct;
    }

    //Отключение от БД MySQl
    function CloseDB ($msql_cnnct) {

        mysqli_close($msql_cnnct);

    }

	//Вставка и обновление категории процентов из-под Web
	function WritePercentCatToDB_Edit ($session_id, $cat_name, $work_percent, $material_percent, $personal_id){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

        $query = "INSERT INTO `fl_percents` (
			`cat_name`, `work_percent`, `material_percent`, `personal_id`, `create_time`, `create_person`)
			VALUES (
			'{$cat_name}', '{$work_percent}', '{$material_percent}', '{$personal_id}', '{$time}', '{$session_id}') ";

        //mysqli_query($query) or die($query.' -> '.mysql_error());
        //mysqli_close();

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        //ID новой позиции
        $mysql_insert_id = mysqli_insert_id($msql_cnnct);

        //логирование
        AddLog (GetRealIp(), $session_id, '', 'Добавлен сертификат. Номер: ['.$num.']. Номинал: ['.$nominal.'] руб.');

        return ($mysql_insert_id);
    }
	
?>