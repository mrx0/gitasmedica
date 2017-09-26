<?php

//exit.php
//Закрытие сессии на сайту

	//Запускаем сессию для работы с куками
	session_start();
	
	include_once 'DBWork.php';
	//логирование
	AddLog (GetRealIp(), $_SESSION['id'], '', 'Пользователь вышел из системы');
	//Так как пользователь хотел выйти,
	//удаляем ему логин и id из кукисов
	unset($_SESSION['login']);
	unset($_SESSION['id']);
	unset($_SESSION['journal_tooth_status_temp']);
	unset($_SESSION['permissions']);
	unset($_SESSION['filial']);
	unset($_SESSION['invoice_data']);
    unset($_SESSION['calculate_data']);
	 
	//Переадресовываем на главную
	header("location: index.php");

?>