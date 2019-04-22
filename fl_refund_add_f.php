<?php 

//fl_refund_add_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);

		if ($_POST){

			if (!isset($_POST['invoice_id']) || !isset($_POST['zapis_id']) || !isset($_POST['client_id']) || !isset($_POST['worker_id']) ||
                !isset($_POST['refundSumm']) || !isset($_POST['payedSumm']) || !isset($_POST['comment']) || !isset($_POST['salaryDeductionCheck']) ||
                !isset($_POST['tabel_id']) || !isset($_POST['salaryDeductionSumm']) || !isset($_POST['checkedItems'])){
				//echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
			    //Если массив позиций не пустой
			    if (!empty($_POST['checkedItems'])){

                    include_once 'DBWork.php';
                    include_once 'functions.php';
                    include_once 'ffun.php';

                    $tabelError = FALSE;
                    $mysql_insert_id = 0;

                    $time = date('Y-m-d H:i:s', time());

                    $msql_cnnct = ConnectToDB();

                    //Если отметили вычет из ЗП и все данные в порядке...
                    if (($_POST['salaryDeductionCheck'] == 1) && ($_POST['tabel_id'] != 0) && ($_POST['salaryDeductionSumm'] > 0)) {

                        //Проверяем есть ли такой табель и не закрыт ли он уже
                        $query = "SELECT `status` FROM `fl_journal_tabels` WHERE `id`='{$_POST['tabel_id']}' AND `worker_id`='{$_POST['worker_id']}' LIMIT 1;";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            $arr = mysqli_fetch_assoc($res);
                            $status = $arr['status'];

                            if ($status == 7){
                                $tabelError = TRUE;
                            }else{
                                //Если всё ок с табелем, добавляем вычет
                                $query = "INSERT INTO `fl_journal_deductions` (`tabel_id`, `type`, `summ`, `descr`, `create_time`, `create_person`)
                                VALUES (
                                '{$_POST['tabel_id']}', '3', '{$_POST['salaryDeductionSumm']}', 'Вычет по наряду #{$_POST['invoice_id']}. Основание: {$_POST['comment']}.', '{$time}', '{$_SESSION['id']}');";

                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                //ID новой позиции
                                $mysql_insert_id = mysqli_insert_id($msql_cnnct);

                                //Обновим табель
                                updateTabelDeductionsSumm ($_POST['tabel_id']);
                            }

                        }else{
                            $tabelError = TRUE;
                        }
                    }

                    //Переходим к созданию непосредственно возврата средств
                    //Если ошибок табеля не было
                    if (!$tabelError){
                        //Создаем сам вычет
                        $query = "INSERT INTO `fl_journal_refund` (`zapis_id`, `invoice_id`, `client_id`, `worker_id`, `date_in`, `summ`, `deduction_id`, `deduction_summ`, `descr`, `create_time`, `create_person`)
                                VALUES (
                                '{$_POST['zapis_id']}', '{$_POST['invoice_id']}', '{$_POST['client_id']}', '{$_POST['worker_id']}', '{$time}', '{$_POST['refundSumm']}', '{$mysql_insert_id}', '{$_POST['salaryDeductionSumm']}', '{$_POST['comment']}', '{$time}', '{$_SESSION['id']}');";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        //ID новой позиции
                        $mysql_insert_id2 = mysqli_insert_id($msql_cnnct);

                        //Создаем по позициям
                        foreach($_POST['checkedItems'] as $item_id => $item_summ){

                            $query = "INSERT INTO `fl_journal_refund_ex` (`refund_id`, `inv_pos_id`, `summ`)
                                VALUES (
                                '{$mysql_insert_id2}', '{$item_id}', '{$item_summ}');";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        }

                        echo json_encode(array('result' => 'success', 'data' => 'Ok'));

                    }else{
                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #44. Указанного табеля нет или он закрыт</div>'));
                    }
                }else{
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Ошибка #43. Что-то пошло не так</div>'));
                }
			}
		}
	}

?>