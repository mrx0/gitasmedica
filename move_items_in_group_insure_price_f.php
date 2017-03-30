<?php 

//move_items_in_group_insure_price_f.php
//переместить позиции в другую группу в прайсе

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
					if (isset($_POST['group']) && isset($_POST['items'])){
					    $arr4move = $_POST['items'];
                        if ($_POST['group'] > 0){
                            if (!empty($arr4move)){

                                //$query_str = '';

                                require 'config.php';
                                mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                                mysql_select_db($dbName) or die(mysql_error());
                                mysql_query("SET NAMES 'utf8'");

                                $time = time();

                                for($i=0; $i < count($arr4move); $i++) {
                                    //$query_str .= "`item` = '{$arr4move[$i]}'";
                                    /*if ($i != count($arr4move)-1) {
                                        $query_str .= ' OR ';
                                    }*/

                                    $query = "INSERT INTO `spr_itemsingroup` (
                                    `item`, `group`, `create_time`, `create_person`) 
                                    VALUES (
                                    '{$arr4move[$i]}', '{$_POST['group']}', '{$time}', '{$_SESSION['id']}')";

                                    mysql_query($query) or die(mysql_error().' -> '.$query);

                                }
                               // var_dump ($query_str);

                                echo '
                                    <div class="query_ok">
                                        Позиции перемещены
                                    </div>';
                            }
                        }
					}else{
						echo '
							<div class="query_neok">
								Что-то пошло не так.<br><br>
							</div>';
					}
		}
	}
?>