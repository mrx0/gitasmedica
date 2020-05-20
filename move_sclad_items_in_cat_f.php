<?php 

//move_sclad_items_in_cat_f.php
//Функция перемещения позиции в категорию

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){

			if (!isset($_POST['item_id']) || !isset($_POST['cat_id']) || !isset($_POST['target_cat_id'])){
                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так1</div>'));
			}else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                if ((($_POST['item_id'] != 0) && ($_POST['cat_id'] != 0))) {
                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так2</div>'));
                }else{

                    //Обновляем
                    $msql_cnnct = ConnectToDB();

//                    $time = date('Y-m-d H:i:s', time());

//                    $cell_time = date_format(date_create($_POST['cell_date'].' '.date('H:i:s', time())), 'Y-m-d  H:i:s');
//
//                    $expires_time = date_create($cell_time);
//
//                    if ($_POST['expirationDate'] == 3) {
//                        date_modify($expires_time, '+3 month');
//                    }
//                    if ($_POST['expirationDate'] == 6) {
//                        date_modify($expires_time, '+6 month');
//                    }
//                    if ($_POST['expirationDate'] == 12) {
//                        date_modify($expires_time, '+12 month');
//                    }
//
//                    //date_modify($expires_time, '+3 month');
//                    $expires_time = date_format($expires_time, 'Y-m-d');

                    if ($_POST['item_id'] != 0) {
                        $query = "UPDATE `spr_sclad_items` SET `parent_id`='{$_POST['target_cat_id']}' WHERE `id`='{$_POST['item_id']}'";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                    }

                    if ($_POST['cat_id'] != 0) {

                        $parent_id_now = 0;
                        $node_count_now = 0;

                        $query = "";

                        //Текущая группа, где находится элемент
                        $query = "SELECT `id`, `node_count` FROM `spr_sclad_category` WHERE `id` iN (SELECT `parent_id` FROM `spr_sclad_category` WHERE `id`='{$_POST['cat_id']}')";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0) {
                            $arr = mysqli_fetch_assoc($res);

                            $parent_id_now = $arr['id'];
                            $node_count_now = $arr['node_count'];
                        }

                        //$query = "";

                        //Если родитель указан не тот же самый, который был
                        if ($parent_id_now != $_POST['target_cat_id']) {

                            //Если полмещаем не в дочерний элемент
                            if (!checkExistTreeParents ('spr_sclad_category', $_POST['cat_id'], $_POST['target_cat_id'])) {

                                //Если бывший родитель указан и это не корень (0)
                                if ($parent_id_now != 0) {

                                    //Обновляем кол-во подкатегорий (node_count)
                                    $query = "UPDATE `spr_sclad_category` SET `node_count`=`node_count`-1 WHERE `id` = '$parent_id_now'";
                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);
                                }

                                //Обновляем подкатегорию
                                $query = "UPDATE `spr_sclad_category` SET `parent_id`='{$_POST['target_cat_id']}' WHERE `id`='{$_POST['cat_id']}'";
                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                                //Обновляем кол-во подкатегорий (node_count)
                                $query = "UPDATE `spr_sclad_category` SET `node_count`=`node_count`+1 WHERE `id`='{$_POST['target_cat_id']}'";
                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            }
                        }
                    }

                    CloseDB ($msql_cnnct);

                    //логирование
//                    AddLog (GetRealIp(), $_SESSION['id'], '', '---------');

                    echo json_encode(array('result' => 'success', 'data' => $query));

                }
			}
		}
	}
?>