<?php

//sclad_prihod_add.php
//Добавить приход

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//!!!Доделать права
		//if (($clients['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';
			
//			$orgs = SelDataFromDB('spr_org', '', '');
//			$permissions = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(true, true, true);

//			$goods_arr = array();
			
//			if (isset($_GET['g_id'])){
//                array_push($goods_arr, $_GET['g_id']);
//			}

            //!!!для тестов
//            unset($_SESSION['sclad']['items_prihod_data']);

			echo '
				<div id="status">
					<header>
                        <div class="nav">
							<a href="sclad.php" class="b">Склад</a>
                            <a href="sclad_prihods.php" class="b">Приходные накладные</a>
						</div>
						
					
					
						<h2>Добавить приход</h2>
					</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo '
					</div>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';

            echo '
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Филиал/Склад</div>
                                        <div class="cellRight">';
            echo '
                                            <select name="SelectFilial" id="SelectFilial">';

            $selected_filial = 0;

            if (isset($_SESSION['filial'])){
                $selected_filial = $_SESSION['filial'];
            }

            echo '
                                                <option value="0" ', $selected_filial == 0 ? 'selected': '' ,'>Главный склад [ПР21]</option>';



            foreach ($filials_j as $filial_item) {

                $selected = '';

                if ($selected_filial == $filial_item['id']) {
                    $selected = 'selected';
                }

                echo '
                                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name2'] . '</option>';
            }

            echo '
                                            </select>
                                        </div>
                                    </div>';

			echo '                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Дата прихода</div>
                            <div class="cellRight">';

			//echo selectDate (date('d', time()), date('m', time()), date('Y', time()), 2004, 1);

            echo '
                                <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgba(0,220,220,1);" value="'.date('d', time()).'.'.date('m', time()).'.'.date('Y', time()).'" onfocus="this.select();_Calendar.lcs(this)" 
                                    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)" autocomplete="off">';

            echo '	
                                <label id="sel_date_error" class="error"></label>
                                <label id="sel_month_error" class="error"></label>
                                <label id="sel_year_error" class="error"></label>
                            </div>
                        </div>
					
                        <div class="cellsBlock2">
                            <div class="cellLeft">Поставщик</div>
                            <div class="cellRight">
                                <input type="text" name="provider" id="provider" value="" style="width: 320px;">
                                <label id="provider_error" class="error"></label>
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">№ / дата документа поставщика</div>
                            <div class="cellRight">
                                <input type="text" name="prov_doc" id="prov_doc" value="">
                                <label id="prov_doc_error" class="error"></label>
                            </div>
                        </div>';


        //Массив с позициями из бд (названия, ед. измерения)
        $items_arr_j = array();
        //Массив с самим позициями для работы с ними
        $items_arr = array();
        //Всего позиций
        $itemInSetCount = 0;

        //Данные из сессии
        if (isset($_SESSION['sclad'])) {
            if (!empty($_SESSION['sclad']['items_data'])) {

                //!!! не забыть пересмотреть тут подход к решению... два массива в сессии.. обратная совместимость и тд
                //Сразу создадим новый массив в сессии для прихода, потому что может быть несколько одинаковых позиций
                if (!isset($_SESSION['sclad']['items_prihod_data'])) {
                    $_SESSION['sclad']['items_prihod_data'] = array();
                }

                //Обновляем только если он пустой? возможны проблемы
                //Надо это как-то обыграть иначе
                //!!!Не забудь еще сделать кнопку отмены, которая будет все в сессии чистить и перемещать пользюка в склад
                if (empty($_SESSION['sclad']['items_prihod_data'])) {
                    foreach ($_SESSION['sclad']['items_data'] as $item_id_in_set) {
//                        var_dump($item_data_in_set);

                        if (!isset($_SESSION['sclad']['items_prihod_data'][$item_id_in_set])) {
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set] = array();
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0] = array();
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0]['quantity'] = 0;
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0]['exp_garant_type'] = 0;
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0]['exp_garant_date'] = '';
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0]['price'] = 0;
                            $_SESSION['sclad']['items_prihod_data'][$item_id_in_set][0]['summ'] = 0;
                        }
                    }
                }

                $items_arr = $_SESSION['sclad']['items_prihod_data'];

//                var_dump($_SESSION['sclad']);
//                var_dump($_SESSION['sclad']['items_prihod_data']);
//                var_dump(array_keys ($_SESSION['sclad']['items_prihod_data']));

//                $item_id_in_set_arr = array();

                $msql_cnnct = ConnectToDB ();

                $itemsArr = implode(",", array_keys ($_SESSION['sclad']['items_prihod_data']));

                $query = "SELECT `id`, `name`,`unit` FROM `spr_sclad_items` WHERE `id` IN ($itemsArr) AND `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                $itemInSetCount = $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        //array_push($items_arr, $arr);

                        if (!isset($items_arr_j[$arr['id']])){
                            $items_arr_j[$arr['id']] = array();
                        }
                        $items_arr_j[$arr['id']]['name'] = $arr['name'];
                        $items_arr_j[$arr['id']]['unit'] = $arr['unit'];
                    }

                }
                //var_dump($items_arr_j);

                CloseDB ($msql_cnnct);
            }
        }


        echo '
                    <div style="white-space: nowrap;">';
        echo '
                        <div style="width: max-content; /*width: 700px;*/ border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

        echo '
                            <div class="invoceHeader" style="position: relative; padding: 5px 10px;">
                                <div style="display: inline-block;">
                                    <div style="">Всего позиций: <span id="itemInSetCount" style="">'.$itemInSetCount.'</span> шт.</div>
                                </div>
                                <div style="display: inline-block;">
                                    <div style="">На сумму: <span id="itemInSetSumm" style="">0</span> руб.</div>
                                </div>
<!--
                                <div style="position: absolute; top: 10px; right: 10px; font-size: 11px;">
                                     <div class="settings_text" onclick="deleteScladItemsFromSet();">Очистить всё</div>
                                </div>-->
';

        echo '
                            </div>';


        echo '
                            <div style="float: none">';

        if (!empty($items_arr)){

            echo '
                                <table style="/*border: 1px solid #CCC;*/ /*width: 100%;*/ table-layout: fixed;">';

            //$border_top = 'border-top: 1px solid #CCC;';


            echo '
                                    <tr class="sclad_item_tr" style="font-size: 80%; font-weight: normal;">
                                        <td colspan="8" style="border-left: 1px solid #CCC; text-align: left;">
                                            <span style="cursor:pointer;" onclick="showAddNewScladItemsSetINSession();">
                                                <i class="fa fa-plus-square" style="color: green; font-size: 120%;"></i> Добавить позицию
                                            </span>
                                        </td>
                                    </tr>';


            echo '
                                    <tr class="sclad_item_tr" style="font-size: 80%; font-weight: bold;">
                                        <td style="border-left: 1px solid #CCC; width: 8px; max-width: 8px; min-width: 8px; border-top: 1px solid #CCC; text-align: center;"></td>
                                        <td style="width: 10px; max-width: 10px; min-width: 10px; border-top: 1px solid #CCC; text-align: center;">№</td>
                                        <td style="border-top: 1px solid #CCC; text-align: center; ">Наименование</td>
                                        <td style="width: 80px; max-width: 80px; min-width: 80px; border-top: 1px solid #CCC; text-align: center; ">Цена</td>
                                        <td style="width: 60px; border-top: 1px solid #CCC; text-align: center;">Кол-во</td>
                                        <td style="width: 80px; max-width: 80px; min-width: 80px; border-top: 1px solid #CCC; text-align: center; ">Стоимость</td>
                                        <td style="border-top: 1px solid #CCC; text-align: center;">Срок годн./Гар.</td>
                                        <td style="border-top: 1px solid #CCC; text-align: center;">Дата</td>
                                    </tr>';

            //Номер по порядку
            $num = 1;

            foreach ($items_arr as $item_id => $items_arrs){
                foreach ($items_arrs as $ind => $sclad_item) {
//                    var_dump($sclad_item);

                    $checked = '';

                    echo '
                                    <tr class="<!--draggable--> sclad_item_tr">
                                        <td style="border-left: 1px solid #CCC; text-align: center;">
                                            <i class="fa fa-times" aria-hidden="true" style="cursor: pointer; color: red;"  title="Удалить" onclick="deleteScladItemsFromSet(' . $item_id . ', '.$ind.', true);"></i>
                                            <i class="fa fa-clone" aria-hidden="true" style="cursor: pointer; color: grey;"  title="Копировать" onclick="copyScladItemsFromSet(' . $item_id . ', '.$ind.');"></i>
                                        </td>
                                        <td style="text-align: center; font-size: 80%;">' . $num . '</td>
                                        <td style="position: relative; max-width: 300px; width: 300px; background: linear-gradient(to right, rgb(0, 0, 0) 88%, rgba(0, 186, 187, 0) 100%); -webkit-background-clip: text; color: transparent; white-space: normal;">
                                             ' . $items_arr_j[$item_id]['name'] . '
                                        </td>
                                        <td style="text-align: left; ">
                                            <input type="text" name="price_' . $item_id . '_'.$ind.'" id="price_' . $item_id . '_'.$ind.'" class="sclad_item_prihod_price" value="'.$sclad_item['price'].'" placeholder="0"  style="width: 50px; color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgb(118, 118, 118); border-radius: 2px;"><span style="font-size: 70%">руб.</span>
                                        </td>
                                        <td style="text-align: left;">
                                            <!--<input type="number" size="2" name="sclad_item_prihod_count" id="sclad_item_prihod_count_' . $item_id . '_'.$ind.'" class="sclad_item_prihod_count" min="0" max="10000" value="'.$sclad_item['quantity'].'" style="width: 50px;">-->
                                            <input type="text" name="sclad_item_prihod_count_' . $item_id . '_'.$ind.'" id="sclad_item_prihod_count_' . $item_id . '_'.$ind.'" class="sclad_item_prihod_count" value="'.$sclad_item['quantity'].'" placeholder="0"  style="width: 50px; color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgb(118, 118, 118); border-radius: 2px;">
                                        <!--</td>
                                        <td style="text-align: left;" -->';

                    if (isset($units[$items_arr_j[$item_id]['unit']])) {
                        //echo 'item_unit_' . $item_id . '_'.$ind.'="' . $items_arr_j[$item_id]['unit'] . '">' . $units[$items_arr_j[$item_id]['unit']];
                        echo '' . $units[$items_arr_j[$item_id]['unit']];
                    } else {
                        //echo 'item_unit_' . $item_id . '_'.$ind.'="0"><i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                        echo '<i class="fa fa-warning" aria-hidden="true" style="color: red;" title="Не указано"></i>';
                    }

                    echo '
                                        </td>
                                        <td style="text-align: center; ">
                                            <span id="summ_' . $item_id . '_'.$ind.'" class="sclad_item_prihod_summ">'.$sclad_item['quantity'] * $sclad_item['price'].'</span><span style="font-size: 70%">руб.</span>
                                        </td>';

                    //Тип гарантии / срок годности
                    $sel0 = '';
                    $sel1 = '';
                    $sel2 = '';

                    if ($sclad_item['exp_garant_type'] == 1){
                        $sel1 = 'selected';
                    }elseif($sclad_item['exp_garant_type'] == 2){
                        $sel2 = 'selected';
                    }else{
                        $sel0 = 'selected';
                    }

                    echo '
                                        <td style="border-top: 1px solid #CCC;">
                                            <select name="expirationDate" class="expirationDate" id="expirationDate_' . $item_id . '_'.$ind.'" class="expirationDate" onchange="changeExpGarantTypeScladItemPrihod('.$ind.', '.$item_id.', this);">
                                                <option value="0" '.$sel0.'>Не указано</option>
                                                <option value="1" '.$sel1.'>Гарантия до</option>
                                                <option value="2" '.$sel2.'>Годен до</option>
                                            </select>
                                        </td>';

                    if (mb_strlen($sclad_item['exp_garant_date']) == 10) {
                        $exp_garant_date = $sclad_item['exp_garant_date'];
                    }else{
                        $exp_garant_date = date('d', time()).'.'.date('m', time()).'.'.date('Y', time());
                    }

                    echo '
                                        <td style="border-top: 1px solid #CCC; text-align: center;">
                                            <input type="text" id="iWantThisDate2_' . $item_id . '_'.$ind.'" name="iWantThisDate2_' . $item_id . '_'.$ind.'" class="dateс eg_date" style="color: rgb(30, 30, 30); font-size: 12px; border: 1px solid rgb(118, 118, 118); border-radius: 2px;" value="'.$exp_garant_date.'" 
                                                
                                                /*
                                                onfocus="event.cancelBubble=true;
                                                this.select();
                                                _Calendar.lcs(this);"
                                                */
                                                 
                                                onclick="event.cancelBubble=true;
                                                this.select();
                                                _Calendar.lcs(this);"
                                                 
                                                autocomplete="off" 
                                                >
                                        </td>
                                        ';

                    echo '
                                    </tr>';

                    $num++;
                }
            }

            echo '
                                </table>';
        }




        echo '
                            </div>';
        echo '
                        </div>';
        echo '
                    </div>

                    ';


        echo '				
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick="Ajax_sclad_prihod_add()">
                        <input type="button" class="b" value="Отмена" onclick="Ajax_sclad_cancel(\'prihod_add\', \'sclad.php\');">
					</div>';
				
			echo '
					</div>
				</div>';


            echo '	
                <!-- Подложка только одна -->
                <div id="overlay"></div>';

            echo '
                <div id="doc_title">Склад - Асмедика</div>';

            echo '
				
				<script type="text/javascript">

                    //Создаем объект за пределами $(document).ready(function()) чтобы потом могли к нему обращаться из других скриптов
                    let eg_date_arr = {};
                    
                    $(document).ready(function() {
                        
                        //Соберем массив со значениями дат, чтобы потом следить за изменениями (а как иначе я хз)    
                        $(".eg_date").each(function() {
                    
                            let item_id = $(this).attr("id").split("_")[1];
                            let ind = $(this).attr("id").split("_")[2];
                            
                            //!!! пример проверка существования значения в объекте
                    //        console.log(item_id in eg_date_arr);
                            
                            if (!(item_id in eg_date_arr)){
                                eg_date_arr[item_id] = [];
                            }            
                            if (!(ind in  eg_date_arr[item_id])){
                                eg_date_arr[item_id][ind] = $(this).val();
                            }
                        });
                        
                        //console.log(eg_date_arr);
                        
                        
                        //Общая сумма
                        let summ = 0;

                        $(".sclad_item_prihod_summ").each(function() {
                            summ += parseFloat($(this).html());
                        });
                        $("#itemInSetSumm").html(summ);
                    });
                    
                    //Изменение цены
                    $(".sclad_item_prihod_price").bind("input", function() {
//                        console.log($(this).val());
//                        console.log($(this).attr("id"));

                        $(this).removeClass("input_error");

                        let item_id = $(this).attr("id").split("_")[1];
                        let ind = $(this).attr("id").split("_")[2];
                        
                        //if ($(this).val().length > 0){
                            
                            $(this).val($(this).val().replace(\',\', \'.\'));
                            
                            changePriceScladItemPrihod(ind, item_id, this);
                            
                        //}
                        
                        changeSumScladItemPrihod(ind, item_id);
                        
                    });

                    //Изменение кол-ва
                    $(".sclad_item_prihod_count").bind("input", function() {
//                        console.log($(this).val());
//                        console.log($(this).attr("id"));

                        $(this).removeClass("input_error");

                        let item_id = $(this).attr("id").split("_")[4];
                        let ind = $(this).attr("id").split("_")[5];
                        
//                        $(this).val($(this).val().replace(",", ""));
//                        console.log($(this).val().replace(",", ""));
//                        $(this).val(parseInt($(this).val()));
//                        console.log($(this).val().replace(".", "0"));
                        
                        //if ($(this).val().length > 0){

                            $(this).val($(this).val().replace(\',\', \'\'));
                            $(this).val($(this).val().replace(\'.\', \'\'));
                            
                            changeQuantityScladItemPrihod(ind, item_id, this);

                        //}
                        
                        changeSumScladItemPrihod(ind, item_id);
                        
                    });
                            
                            
                            
                            
                            
                            
                            


				</script>';
//		}else{
//			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
//		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>