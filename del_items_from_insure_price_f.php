<?php 

//del_items_from_insure_price.php
//Изменение

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
					if (isset($_POST['insure_id']) && isset($_POST['items'])){
					    $arr4del = $_POST['items'];

                        if (!empty($arr4del)){
                            //var_dump ($arr4fill);

                            $query_str = '';

                            require 'config.php';
                            mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                            mysql_select_db($dbName) or die(mysql_error());
                            mysql_query("SET NAMES 'utf8'");
                            $time = time();

                            for($i=0; $i < count($arr4del); $i++) {
                                $query_str .= "`item` = '{$arr4del[$i]}'";
                                if ($i != count($arr4del)-1) {
                                    $query_str .= ' OR ';
                                }
                            }
                           // var_dump ($query_str);


                            $query = "DELETE FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['insure_id']}' AND ($query_str)";
                            mysql_query($query) or die(mysql_error().' -> '.$query);

                            $query = "DELETE FROM `spr_priceprices_insure` WHERE`insure`='{$_POST['insure_id']}' AND ($query_str)";
                            mysql_query($query) or die(mysql_error().' -> '.$query);

                            echo '
                                <div class="query_ok">
                                    Позиции удалены
                                </div>';
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