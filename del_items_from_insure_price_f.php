<?php 

//del_items_from_insure_price_f.php
//Удалить из прайса страховой позиции

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

                            $time = time();

                            $msql_cnnct = ConnectToDB ();

                            for($i=0; $i < count($arr4del); $i++) {
                                $query_str .= "`item` = '{$arr4del[$i]}'";
                                if ($i != count($arr4del)-1) {
                                    $query_str .= ' OR ';
                                }
                            }
                           // var_dump ($query_str);


                            $query = "DELETE FROM `spr_pricelists_insure` WHERE `insure`='{$_POST['insure_id']}' AND ($query_str)";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $query = "DELETE FROM `spr_priceprices_insure` WHERE`insure`='{$_POST['insure_id']}' AND ($query_str)";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            echo '
                                <div class="query_ok">
                                    Позиции удалены
                                </div>';

                            CloseDB ($msql_cnnct);
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