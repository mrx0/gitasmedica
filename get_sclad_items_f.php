<?php

//get_sclad_items_f.php
//получаем элементы склада

    session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        //var_dump ($_POST);

        if ($_POST){
            if (isset($_POST['cat_id']) && isset($_POST['start']) && isset($_POST['limit']) && isset($_POST['free'])){

                $rezult = '';
                $rezult_arr = array();

                include_once 'DBWork.php';
                include_once 'functions.php';

                require 'variables.php';

                $msql_cnnct = ConnectToDB();

                $arr = array();
                $rez = array();

                $start = $_POST['start'];
                $count = $_POST['limit'];
                $cat_id = $_POST['cat_id'];

                $dop_cat = '';

                //Если хотим позиции определённой категории
                if ($_POST['free'] !== 'true'){
                    $dop_cat = " AND sit.parent_id = '$cat_id'";
                }

                //Выбираем
                //Если нет категории
//                $query = "SELECT * FROM `spr_sclad_items` sit
//                WHERE sit.parent_id = '$cat_id'
//                LIMIT $start , $count";

                $query = "SELECT * FROM `spr_sclad_items` sit
                WHERE TRUE $dop_cat
                LIMIT $start , $count";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    $rezult .= 	'<table style="/*border: 1px solid #CCC;*/ width: 100%;">';

                    $border_top = 'border-top: 1px solid #CCC;';

                    $rezult .= 	'
                            <tr class="sclad_item_tr" style="font-size: 80%; font-weight: bold;">
                                <td style="border-left: 1px solid #CCC; width: 20px; max-width: 20px; min-width: 20px; border-top: 1px solid #CCC; text-align: center;"></td>
                                <td style="width: 60px; max-width: 60px; min-width: 60px; border-top: 1px solid #CCC; text-align: center;">Номер</td>
								<td style="border-top: 1px solid #CCC; text-align: center; ">Наименование</td>
								<!--<td style="border-top: 1px solid #CCC;">Срок годности</td>
								<td style="border-top: 1px solid #CCC;">Гарантия</td>-->
								<td style="width: 60px; border-top: 1px solid #CCC; text-align: center;">Кол-во</td>
								<td style="width: 43px; max-width: 43px; min-width: 43px; border-top: 1px solid #CCC; text-align: center; ">Ед.изм.</td>
                            </tr>';

                    $border_top = '';

                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($rezult_arr, $arr);

                        //Переменная для выделения галочкой позиции
                        $checked = '';
                        if (false){
                            $checked = 'checked';
                        }

                        $rezult .= 	'
                            <tr id="item_'.$arr['id'].'" class="draggable sclad_item_tr">
                                <td style="border-left: 1px solid #CCC; text-align: center;">
                                    <a href="sclad_item.php?id='.$arr['id'].'" class="ahref"><i class="fa fa-folder-open-o" aria-hidden="true" style="color: #000000; text-shadow: 1px 1px 1px #ffc90f;"></i></a> 
                                </td>
                                <td style="text-align: left; font-size: 80%;">'.$arr['id'].'</td>
								<td id="item_name_'.$arr['id'].'" style="position: relative; max-width: 400px; width: 400px; background: linear-gradient(to right, rgb(0, 0, 0) 88%, rgba(0, 186, 187, 0) 100%); -webkit-background-clip: text; color: transparent;">
								     '.$arr['name'].'
                                    <div style="position:absolute; top: 1px; right: 1px;">
								        <input type="checkbox" id="selected_item_'.$arr['id'].'" name="selected_item_'.$arr['id'].'" class="selected_item" value="1" '.$checked.'>
                                    </div>
                                </td>
								<!--<td style="">Срок годности</td>
								<td style="">Гарантия</td>-->
								<td style="width: 60px; text-align: right;">5</td>
								<td style="text-align: left;" ';

                        if (isset($units[$arr['unit']])) {
                            $rezult .= 'item_unit_'.$arr['id'].'="'.$arr['unit'].'">'.$units[$arr['unit']];
                        }else{
                            $rezult .= 'item_unit_'.$arr['id'].'="0"><i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                        }

                        $rezult .= 	'
                                </td>
                            </tr>';



                    }
                    $rezult .= 	'</table>';
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult, 'count' => $number));

            }
        }
    }


?>
	