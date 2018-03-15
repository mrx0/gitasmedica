<?php

//ajax_show_result_stat_client_finance.php
//долги и авансы

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			$workerExist = false;
			$queryDopExist = false;
			$queryDopExExist = false;
			$queryDopClientExist = false;
			$query = '';
			$queryDop = '';
			$queryDopEx = '';
			$queryDopClient = '';

            include_once 'functions.php';
            include_once 'ffun.php';

            $clientInvoices = array();

            $filials_j = getAllFilials(false, true);
            //var_dump($filials_j);

            $msql_cnnct = ConnectToDB ();

            //Соберем все (неудаленные) наряды, где общая сумма не равна оплаченной
            //$query = "SELECT * FROM `journal_invoice` WHERE `status` <> '9' AND `summ` <> `paid`";

            $query = "SELECT jinv.*, scli.full_name FROM `journal_invoice` jinv
                                LEFT JOIN `spr_clients` scli
                                ON scli.id = jinv.client_id
                                WHERE jinv.status <> '9' AND jinv.summ <> jinv.paid";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($clientInvoices, $arr);
                }
            }else{
            }
			//var_dump($clientInvoices);

            //Дата/время
			/*if ($_POST['all_time'] != 1){
				$queryDop .= " `create_time` BETWEEN '".strtotime ($_POST['datastart'])."' AND '".strtotime ($_POST['dataend']." 23:59:59")."'";
				$queryDopExist = true;
			}
				
			if ($queryDopExist){
					$query .= ' AND '.$queryDop;

			}*/
			
			/*$query = $query." ORDER BY `create_time` DESC";
			
			$arr = array();
			$rez = array();*/
					
			/*$res = mysql_query($query) or die($query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($rez, $arr);
				}
				$journal = $rez;
			}else{
				$journal = 0;
			}*/
			//var_dump($journal);
					
			//Выводим результат
			if (!empty($clientInvoices)){
				include_once 'functions.php';
				
				echo '
					<div id="data">';
				
						foreach ($clientInvoices as $data) {
                            echo '
									<li class="cellsBlock" style="font-weight: bold; width: auto; background-color: rgba(255, 255, 0, 0.3); margin: 2px">	
										<a href="invoice.php?id=' . $data['id'] . '"    class="cellTime ahref" style="text-align: center; ">Наряд #'.$data['id'].' от '. $data['create_time'] . '</a>
										    <div class="cellName" style="text-align: right; ">Сумма наряда: ' . $data['summ'] . ' руб.</div>
										    <div class="cellName" style="text-align: right; ">Не оплачено: <span style="color:red"><BR>' . ($data['summ']-$data['paid']) . '</span> руб.</div>
										    <a href="invoice.php?id='.$data['client_id'].'" class="ahref cellText" style="max-width: 250px;">'.$data['full_name'].'<br><br>
										    '.$filials_j[$data['office_id']]['name2'].'
										    </a>
										
									</li>';
                        }

            }else{
                echo '<span style="color: red;">Ничего не найдено</span>';
            }

            CloseDB($msql_cnnct);
		}
	}
?>