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

                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($rezult_arr, $arr);

                        $rezult .= 	'
                            <tr id="item_'.$arr['id'].'" class="draggable sclad_item_tr">
                                <td style="border-left: 1px solid #CCC; width: 60px;  text-align: left; '.$border_top.'">#'.$arr['id'].'</td>
								<td id="item_name_'.$arr['id'].'" style="'.$border_top.'">'.$arr['name'].'</td>
								<!--<td style="'.$border_top.'">Срок годности</td>
								<td style="'.$border_top.'">Гарантия</td>
								<td style="'.$border_top.'">Описание</td>-->
								<td style="width: 60px; text-align: right; '.$border_top.'">5</td>
                            </tr>';

                        $border_top = '';

                    }
                    $rezult .= 	'</table>';
                }

                echo json_encode(array('result' => 'success', 'data' => $rezult, 'count' => $number));

            }
        }
    }


?>
	