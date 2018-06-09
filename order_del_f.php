<?php 

//order_del_f.php
//Функция для Удаление(блокирование) 

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		include_once 'functions.php';

        require_once 'permissions.php';

		if ($_POST){

            $order_j = SelDataFromDB('journal_order', $_POST['id'], 'id');

            //Если заднее число
            if ((strtotime($order_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){
                echo '
                    <div class="query_neok" style="padding-bottom: 10px;">
                        <h3>Нельзя удалять задним числом.</h3>
                    </div>';
            }else {

                $msql_cnnct = ConnectToDB();

                $time = time();

                $query = "SELECT * FROM `journal_payment` WHERE `client_id`='{$order_j[0]['client_id']}' AND `date_in` > '{$order_j[0]['date_in']}' AND `status` <> '9' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    echo '
                    <div class="query_neok" style="padding-bottom: 10px;">
                        <h3>После внесения этого наряда, с баланса списывались средства. Удалить ордер нельзя.</h3>
                        <input type="button" class="b" value="Удалить принудительно" onclick="Ajax_del_order_anyway('.$_POST['id'].', '.$order_j[0]['client_id'].');">
                    </div>';
                } else {

                    $query = "UPDATE `journal_order` SET `status`='9' WHERE `id`='{$_POST['id']}'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    //mysql_close();

                    //!!! @@@ Пересчет баланса
                    include_once 'ffun.php';
                    calculateBalance($_POST['client_id']);

                    echo '
                    <div class="query_ok" style="padding-bottom: 10px;">
                        <h3>Ордер удален (заблокирован).</h3>
                    </div>';
                }
            }
		}
	}
	
?>