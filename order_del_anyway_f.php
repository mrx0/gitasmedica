<?php 

//order_del_anyway_f.php
//Функция для Удаление(блокирование) минуя ограничение оплаты после него

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

		    $data = '';

		    $status = 'error';

            $order_j = SelDataFromDB('journal_order', $_POST['id'], 'id');

            //Если заднее число
            if ((strtotime($order_j[0]['create_time']) + 12*60*60 < time()) && (($finances['see_all'] != 1) && !$god_mode)){

                $data = '
                    <div class="query_neok" style="padding-bottom: 10px;">
                        <h3>Нельзя удалять задним числом.</h3>
                    </div>';

            }else {

                $msql_cnnct = ConnectToDB();

                $time = time();

                $query = "SELECT `summ`, `debited` FROM `journal_balance` WHERE `client_id`='{$_POST['client_id']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {

                    $arr = mysqli_fetch_assoc($res);

                    if ($arr['summ'] - $arr['debited'] < $order_j[0]['summ']){
                        $data = '
                            <div class="query_neok" style="padding-bottom: 10px;">
                                <h3>Нельзя удалить ордер, если на счету не хватает средств для списания.</h3>
                            </div>';

                    }else {

                        $query = "UPDATE `journal_order` SET `status`='9' WHERE `id`='{$_POST['id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //mysql_close();

                        //!!! @@@ Пересчет баланса
                        include_once 'ffun.php';
                        calculateBalance($_POST['client_id']);

                        $data = '
                            <div class="query_ok" style="padding-bottom: 10px;">
                                <h3>Ордер удален (заблокирован).</h3>
                            </div>';

                        $status = 'success';

                    }

                }

                echo json_encode(array('result' => $status, 'data' => $data));

            }
		}
	}
	
?>