<?php 

//insure_price_copy_f.php
//Функция для копирования прайса из одной страховой в другую

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['id']) && isset($_POST['id2'])){
                if (($_POST['id'] != 0) && ($_POST['id2'] != 0)) {
                    //var_dump ($arr4fill);

                    require 'config.php';
                    mysql_connect($hostname, $username, $db_pass) OR DIE("Не возможно создать соединение ");
                    mysql_select_db($dbName) or die(mysql_error());
                    mysql_query("SET NAMES 'utf8'");
                    $time = time();

                    //Выбираем данные компании донора
                    $query = "SELECT * FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['id']}'";
                    $arr = array();
                    // $rez = array();
                    $arr4fill = array();

                    $res = mysql_query($query) or die($query);
                    $number = mysql_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysql_fetch_assoc($res)) {
                            $arr4fill[$arr['item']]['item'] = $arr['item'];
                            //array_push($rez);
                        }
                    } else {
                        $arr4fill = 0;
                    }
                    //var_dump($arr4fill);

                    if ($arr4fill != 0) {
                        $query = "SELECT `item`, `price`, `price2`, `price3`, `date_from` FROM `spr_priceprices_insure` WHERE `insure`='{$_POST['id']}' ORDER BY `date_from` DESC, `create_time` DESC";
                        $arr = array();
                        $price4fill = array();

                        $res = mysql_query($query) or die($query);
                        $number = mysql_num_rows($res);
                        if ($number != 0) {
                            while ($arr = mysql_fetch_assoc($res)) {
                                if (isset($price4fill[$arr['item']])){
                                    if ($price4fill[$arr['item']]['date_from'] < $arr['date_from']){
                                        $price4fill[$arr['item']]['price'] = $arr['price'];
                                        $price4fill[$arr['item']]['price2'] = $arr['price2'];
                                        $price4fill[$arr['item']]['price3'] = $arr['price3'];
                                        $price4fill[$arr['item']]['date_from'] = $arr['date_from'];
                                    }
                                }else {
                                    $price4fill[$arr['item']]['price'] = $arr['price'];
                                    $price4fill[$arr['item']]['price2'] = $arr['price2'];
                                    $price4fill[$arr['item']]['price3'] = $arr['price3'];
                                    $price4fill[$arr['item']]['date_from'] = $arr['date_from'];
                                    //var_dump($arr['item']);
                                    //var_dump($price4fill[936]);
                                    //echo '<hr>';
                                    //var_dump($arr['item']);
                                    //var_dump($price4fill[$arr['item']]['price']);
                                }
                            }
                        } else {
                            //$price4fill = 0;
                        }
                        //var_dump($price4fill);


                        //Удаляем старые данные для второй компании
                        $query = "DELETE FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['id2']}'";
                        mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                        $query = "DELETE FROM `spr_priceprices_insure` WHERE `insure`='{$_POST['id2']}'";
                        mysql_query($query) or die(mysql_error() . ' -> ' . $query);


                        foreach ($arr4fill as $item) {
                            //Добавляем в базу позицию прайса для страховой
                            $query = "INSERT INTO `spr_pricelists_insure` (`item`, `insure`, `create_time`, `create_person`) 
                            VALUES (
                            '{$item['item']}', '{$_POST['id2']}', '{$time}', '{$_SESSION['id']}')";
                            mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                            //ID новой позиции
                            $mysql_insert_id = mysql_insert_id();

                            $price = 0;
                            //Сегодня 09:00:00
                            $fromdate = strtotime(date('d.m.Y', $time) . " 09:00:00");

                            if (isset($price4fill[$item['item']])) {
                                $price = $price4fill[$item['item']]['price'];
                                $price2 = $price4fill[$item['item']]['price2'];
                                $price3 = $price4fill[$item['item']]['price3'];
                                $fromdate = $price4fill[$item['item']]['date_from'];
                            }


                            //Добавляем в базу цену позиции прайса для страховой
                            $query = "INSERT INTO `spr_priceprices_insure` (
                                `insure`, `item`, `price`, `price2`, `price3`, `date_from`, `create_time`, `create_person`) 
                                VALUES (
                            '{$_POST['id2']}', '{$item['item']}', '{$price}', '{$price2}', '{$price3}', '{$fromdate}', '{$time}', '{$_SESSION['id']}')";
                            mysql_query($query) or die(mysql_error() . ' -> ' . $query);

                        }
                        //var_dump($price4fill);
                        echo '
                            <div class="query_ok">
                                Прайс заполнен<br><br>
                            </div>';
                    }
                }else{
                    echo '
					<div class="query_neok">
						Не выбрана страховая<br><br>
					</div>';
                }
				
			}else{
				echo '
					<div class="query_neok">
						Не выбрана страховая<br><br>
					</div>';
			}
		}

	}
?>