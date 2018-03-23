<?php

//ajax_show_result_stat_client_finance2.php
//долги и авансы

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
            $creatorExist = false;
            $workerExist = false;
            $clientExist = false;
            $queryDopExist = false;
            $queryDopExExist = false;
            $queryDopEx2Exist = false;
            $queryDopClientExist = false;
            $query = '';
            $queryDop = '';
            $queryDopEx = '';
            $queryDopEx2 = '';
            $queryDopClient = '';

            include_once 'functions.php';
            include_once 'ffun.php';

            $clientInvoices = array();

            $filials_j = getAllFilials(false, true);
            //var_dump($filials_j);


            //Кто создал запись
            if ($_POST['creator'] != ''){
                include_once 'DBWork.php';
                $creatorSearch = SelDataFromDB ('spr_workers', $_POST['creator'], 'worker_full_name');

                if ($creatorSearch == 0){
                    $creatorExist = false;
                }else{
                    $creatorExist = true;
                    $creator = $creatorSearch[0]['id'];
                }
            }else{
                $creatorExist = true;
                $creator = 0;
            }

            //К кому запись
            if ($_POST['worker'] != ''){
                include_once 'DBWork.php';
                $workerSearch = SelDataFromDB ('spr_workers', $_POST['worker'], 'worker_full_name');

                if ($workerSearch == 0){
                    $workerExist = false;
                }else{
                    $workerExist = true;
                    $worker = $workerSearch[0]['id'];
                }
            }else{
                $workerExist = true;
                $worker = 0;
            }

            //Клиент
            if ($_POST['client'] != ''){
                include_once 'DBWork.php';
                $clientSearch = SelDataFromDB ('spr_clients', $_POST['client'], 'client_full_name');

                if ($clientSearch == 0){
                    $clientExist = false;
                }else{
                    $clientExist = true;
                    $client = $clientSearch[0]['id'];
                }
            }else{
                $clientExist = true;
                $client = 0;
            }

            if ($creatorExist && $workerExist) {
                if ($clientExist) {

                    //Кто создал запись
                    if ($creator != 0) {
                        if ($queryDopExist) {
                            $queryDop .= ' AND';
                        }
                        $queryDop .= " jinv.create_person = '" . $creator . "'";
                        $queryDopExist = true;
                    }

                    //К кому запись
                    if ($worker != 0) {
                        if ($queryDopExist) {
                            $queryDop .= ' AND';
                        }
                        $queryDop .= " jinv.worker_id = '" . $worker . "'";
                        $queryDopExist = true;
                    }

                    //Клиент
                    if ($client != 0) {
                        if ($queryDopExist) {
                            $queryDop .= ' AND';
                        }
                        $queryDop .= " jinv.client_id = '" . $client . "'";
                        $queryDopExist = true;
                    }

                    //Филиал
                    if ($_POST['filial'] != 99) {
                        if ($queryDopExist) {
                            $queryDop .= ' AND';
                        }
                        $queryDop .= " jinv.office_id = '" . $_POST['filial'] . "'";
                        $queryDopExist = true;
                    }


                    if ($queryDopExist) {
                        $query .= ' AND ' . $queryDop;

                        if ($queryDopExExist) {
                            $query .= ' AND (' . $queryDopEx . ')';
                        }

                        if ($queryDopEx2Exist) {
                            $query .= ' AND (' . $queryDopEx2 . ')';
                        }
                    }


                    $msql_cnnct = ConnectToDB ();

                    //Соберем все (неудаленные) наряды, где общая сумма не равна оплаченной
                    //$query = "SELECT * FROM `journal_invoice` WHERE `status` <> '9' AND `summ` <> `paid`";

                    $query = "SELECT jinv.*, scli.full_name FROM `journal_invoice` jinv
                                LEFT JOIN `spr_clients` scli
                                ON scli.id = jinv.client_id
                                WHERE jinv.status <> '9' AND jinv.summ <> jinv.paid AND jinv.create_time > '2017-12-01 08:30:00' ".$query;

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($clientInvoices, $arr);
                        }
                    }else{
                    }
                    //var_dump($clientInvoices);
                    //var_dump($query);

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
										    <div class="cellName" style="text-align: left; width: 160px; min-width: 160px;">
										        автор: '.WriteSearchUser('spr_workers', $data['create_person'], 'user', true).'<br>
										        исп-ль: '.WriteSearchUser('spr_workers', $data['worker_id'], 'user', true).'
										    </div>
										
									</li>';
                        }

                    }else{
                        echo '<span style="color: red;">Ничего не найдено</span>';
                    }

                    CloseDB($msql_cnnct);


                }else {
                    echo '<span style="color: red;">Не найден пациент.</span>';
                }
            }else{
                echo '<span style="color: red;">Не найден сотрудник. Проверьте, что полностью введены ФИО.</span>';
            }

		}
	}
?>