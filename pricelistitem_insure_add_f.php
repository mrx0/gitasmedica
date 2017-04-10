<?php 

//pricelistitem_insure_add_f.php
//Функция для добавления единичной позции в прайс страховой из основного

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['insure']) && isset($_POST['id'])){

                require 'config.php';
                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                mysql_select_db($dbName) or die(mysql_error());
                mysql_query("SET NAMES 'utf8'");
                $time = time();

                $arr3 = array();

                //Сегодня 09:00:00
                $fromdate = strtotime(date('d.m.Y', $time)." 09:00:00");

                //Берём цену из основного прайса
                $price = 0;
                $price2 = 0;
                $price3 = 0;

                $query = "SELECT `price`, `price2`, `price3` FROM `spr_priceprices` WHERE `item`='".$_POST['id']."' ORDER BY `date_from` DESC, `create_time` DESC LIMIT 1";

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
                //нулевые цены 2 и 3
                $price2 = 0;
                $price3 = 0;

                //Добавляем в базу позицию прайса для страховой
                $query = "INSERT INTO `spr_pricelists_insure` (`item`, `insure`, `create_time`, `create_person`) 
                VALUES (
                '{$_POST['id']}', '{$_POST['insure']}', '{$time}', '{$_SESSION['id']}')";
                mysql_query($query) or die(mysql_error().' -> '.$query);

                //ID новой позиции
                $mysql_insert_id = mysql_insert_id();

                //Добавляем в базу цену позиции прайса для страховой
                $query = "INSERT INTO `spr_priceprices_insure` (
                    `insure`, `item`, `price`, `price2`, `price3`, `date_from`, `create_time`, `create_person`) 
                    VALUES (
                '{$_POST['insure']}', '{$_POST['id']}', '{$price}', '{$price2}', '{$price3}', '{$fromdate}', '{$time}', '{$_SESSION['id']}')";
                mysql_query($query) or die(mysql_error().' -> '.$query);


                echo '
                    <div class="query_ok">
                        Позиция добавлена
                    </div>';

			}
		}
	}

?>