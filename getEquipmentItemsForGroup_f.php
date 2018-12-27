<?php

//getEquipmentItemsForGroup_f.php
//Получаем оборудование в группе

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){

            if (!isset($_POST['equipment_group_id'])){
                //echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
            }else{
                include_once 'DBWork.php';
                include_once 'functions.php';

                $equipments = array();
                $data = '';

                $msql_cnnct = ConnectToDB ('config');

                $query = "SELECT * FROM `spr_equipment` WHERE `parent_id` = '{$_POST['equipment_group_id']}' ORDER BY `name` ASC";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($equipments, $arr);
                    }
                }

                CloseDB ($msql_cnnct);

                $data .= '
												<div class="cellsBlock2" style="    width: auto; margin: 0; font-weight: bold; font-size: 11px;">
													<div class="cellFullName" style="text-align: center; padding: 0 0 0 4px;">
														Наименование</a>
													</div>
													<div class="cellName" style="text-align: center; border-left: 0; padding: 4px;">
														Инв. номер
													</div>
                                                    <div class="cellName" style="width: 170px; min-width: 170px; text-align: center; border-left: 0; padding: 0 2px 0 4px;">
                                                    	Серийный номер
                                                   	</div >
												</div>';

                if (!empty($equipments)){
                    foreach ($equipments as $item){
                        $data .= '
												<div class="cellsBlock2" style="width: 100%; margin: 0;">
													<div class="cellFullName" style="padding: 0 0 0 4px;">
														<a href="equipment_item.php?id='.$item['id'].'" class="ahref" id="4filter">'.$item['name'].'</a>
													</div>
													<div class="cellName" style="text-align: right; border-left: 0; padding: 0 2px 0 4px;">
														<i>'.$item['id'].'</i>
													</div>
                                                    <div class="cellName" style="width: 170px; min-width: 170px; text-align: right; border-left: 0; padding: 0 2px 0 4px;">
                                                    	'.$item['serial_n'].'
                                                   	</div >
												</div>';
                    }
                }

                echo json_encode(array('result' => 'success', 'data' => $data));
            }
        }
    }

?>