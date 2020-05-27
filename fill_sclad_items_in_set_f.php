<?php 

//fill_sclad_items_in_set_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['type'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так</div>'));
			}else{
				include_once 'DBWork.php';
                include_once 'functions.php';

                require 'variables.php';

                $rezult = '';
                $number = 0;

                $msql_cnnct = ConnectToDB();

				if (isset($_SESSION['sclad'])){
                    if (!empty($_SESSION['sclad']['items_data'])){

                        $rez = array();

                        $itemsArr = implode(",", $_SESSION['sclad']['items_data']);

                        $query = "SELECT * FROM `spr_sclad_items` WHERE `id` IN ($itemsArr)";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);

                        if ($number != 0){

                            $num = 1;

                            $rezult .= 	'
                                <table style="/*border: 1px solid #CCC;*/ width: 100%;">';

                            //$border_top = 'border-top: 1px solid #CCC;';

                            $rezult .= 	'
                                    <tr class="sclad_item_tr" style="font-size: 80%; font-weight: bold;">
                                        <!--<td style="border-left: 1px solid #CCC; width: 20px; max-width: 20px; min-width: 20px; border-top: 1px solid #CCC; text-align: center;"></td>-->
                                        <td style="border-left: 1px solid #CCC; width: 10px; max-width: 10px; min-width: 10px; border-top: 1px solid #CCC; text-align: center;">№</td>
                                        <td style="border-top: 1px solid #CCC; text-align: center; ">Наименование</td>
                                        <!--<td style="border-top: 1px solid #CCC;">Срок годности</td>
                                        <td style="border-top: 1px solid #CCC;">Гарантия</td>
                                        <td style="width: 60px; border-top: 1px solid #CCC; text-align: center;">Кол-во</td>
                                        <td style="width: 43px; max-width: 43px; min-width: 43px; border-top: 1px solid #CCC; text-align: center; ">Ед.изм.</td>-->
                                    </tr>';

                            while ($arr = mysqli_fetch_assoc($res)){

                                $checked = '';

                                //!!! Тут есть градиент текста
                                $rezult .= 	'
                                    <tr class="<!--draggable--> sclad_item_tr">
                                        <!--<td style="border-left: 1px solid #CCC; text-align: center;">
                                            <a href="sclad_item.php?id='.$arr['id'].'" class="ahref"  target="_blank" rel="nofollow noopener"><i class="fa fa-folder-open-o" aria-hidden="true" style="color: #000000; text-shadow: 1px 1px 1px #ffc90f;"></i></a>
                                        </td>--> 
                                        <td style="border-left: 1px solid #CCC; text-align: center; font-size: 80%;">'.$num.'</td>
                                        <td style="position: relative; max-width: 400px; width: 400px; background: linear-gradient(to right, rgb(0, 0, 0) 88%, rgba(0, 186, 187, 0) 100%); -webkit-background-clip: text; color: transparent;">
                                             '.$arr['name'].'
                                                <div style="position:absolute; top: 1px; right: 1px;">
                                                    <!--<input type="checkbox" id="selected_item_'.$arr['id'].'" name="selected_item_'.$arr['id'].'" class="select_item" value="1" '.$checked.'>-->
                                                    <i class="fa fa-times" aria-hidden="true" style="cursor: pointer; color: red;"  title="Удалить" onclick="deleteScladItemsFromSet('.$arr['id'].');"></i>
                                                </div>
                                        </td>
                                        <!--<td style="">Срок годности</td>
                                        <td style="">Гарантия</td>
                                        <td style="width: 60px; text-align: right;">5</td>
                                        <td style="text-align: left;" ';

                                if (isset($units[$arr['unit']])) {
                                    $rezult .= 'item_unit_'.$arr['id'].'="'.$arr['unit'].'">'.$units[$arr['unit']];
                                }else{
                                    $rezult .= 'item_unit_'.$arr['id'].'="0"><i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                                }

                                $rezult .= 	'
                                        </td>-->
                                    </tr>';

                                $num++;

                            }
                            $rezult .= 	'
                                </table>';
                        }

                        //echo json_encode(array('result' => 'success', 'data' => $rezult, 'count' => $number));
                    }/*else{
                        echo json_encode(array('result' => 'error', 'data' => $rezult, 'count' => $number));
                    }*/


                    echo json_encode(array('result' => 'success', 'data' => $rezult, 'count' => $number));
				}
			}
		}
	}
?>