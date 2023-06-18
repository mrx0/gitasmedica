<?php

//fl_to_director_all.php
//Показать всё, что АН

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
            include_once 'widget_calendar.php';

            //Опция доступа к филиалам конкретных сотрудников
            $optionsWF = getOptionsWorkerFilial($_SESSION['id']);
            //var_dump($optionsWF);

            if (!empty($optionsWF[$_SESSION['id']]) || ($god_mode)){

                $filials_j = getAllFilials(true, true, true);
                //var_dump($filials_j);
                //Получили список прав
                //$permissions = SelDataFromDB('spr_permissions', '', '');
                //var_dump($permissions);

                //Дата
                //операции со временем
                if (isset($_GET['m'])){
                    $month = $_GET['m'];
                }else {
                    $month = date('m');
                }
                if (isset($_GET['y'])) {
                    $year = $_GET['y'];
                }else{
                    $year = date('Y');
                }
                if (isset($_GET['d'])) {
                    $day = $_GET['d'];
                }else{
                    $day = date("d");
                }
    //            var_dump($day);
    //            var_dump($month);
    //            var_dump($year);

                //Филиал
                if (isset($_GET['filial'])) {
                    $filial_id = $_GET['filial'];
                }else{
                    $filial_id = 16;
                }

                if (!$god_mode) {
                    if (!in_array($filial_id, $optionsWF[$_SESSION['id']])) {
                        $filial_id = $optionsWF[$_SESSION['id']][0];
                    }
                }

                $dop = '';

                foreach ($_GET as $key => $value){
                    if (($key == 'd') || ($key == 'm') || ($key == 'y'))
                        $dop .= '&' . $key . '=' . $value;
                    if ($key == 'filial'){
                        if ($value != 0) {
                            $dop .= '&' . $key . '=' . $value;
                        }
                    }
//                    if ($key == 'who'){
//                        if ($value != 0) {
//                            $dop .= '&' . $key . '=' . $value;
//                        }
//                    }
                }
                //var_dump($dop);

                //Массив данных
                $rezult = array();

                $msql_cnnct = ConnectToDB ();

                //Поехали собирать данные
                $query = "SELECT * FROM `fl_journal_to_director` WHERE `filial_id`='$filial_id' AND `year`= '$year' AND `month`='$month'
                    ORDER BY `day` ASC";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($rezult, $arr);
                    }
                }
                //var_dump($rezult);

                echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                            </div>
                            <h2>Все, что было для АН</h2>
                            <a href="fl_to_director_add.php" class="b">Добавить новый</a>
                        </header>';

                echo '
                        <div id="data">';


                echo '
                            <ul style="margin-left: 6px; margin-bottom: 20px;"> 			
                                <div class="no_print"> 
                                    <li style="width: auto; margin-bottom: 20px;">
                                        <div style="display: inline-block; margin-right: 20px;">
                                            <div style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">
                                                Филиалы
                                            </div>
                                            <div>
                                                <select name="SelectFilial" id="SelectFilial">
											';
                foreach ($filials_j as $f_id => $filial_item){
                    $selected = '';
                    if (in_array($f_id, $optionsWF[$_SESSION['id']]) || $god_mode) {
                        if ($f_id == $filial_id) {
                            $selected = 'selected';
                        }
                        echo "<option value='".$f_id."' $selected>".$filial_item['name']."</option>";
                    }
                }
                echo '
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                    
                                </div>';

                echo '<div class="no_print">';
                echo widget_calendar ($month, $year, 'fl_to_director_all.php', $dop);
                echo '</div>';

                echo '
                            </ul>';


                if (!empty($rezult)){
                    //var_dump($rezult);

                    $result = '';
                    $deleted_orders = '';

                    echo '
                            <div class="" style="">
                                <ul style="margin-left: 6px; margin-bottom: 10px; font-size: 14px;">
                                    <li style="font-size: 110%; margin-bottom: 5px;">
                                        Найдено:
                                    </li>';
                    echo '
                                    <li class="cellsBlock" style="width: auto; background: rgb(253, 244, 250);">';
                    echo '
                                        <div class="cellOrder" style="text-align: center; border-right: none;">
                                            <b>№</b>
                                        </div>
                                        <div class="cellName" style="text-align: center; border-right: none;">
                                            <b>Сумма</b>
                                                     </div>
                                        <div class="cellName" style="text-align: center; border-right: none;">
                                            <b>Комментарий</b>
                                        </div>
                                        <div class="cellCosmAct" style="text-align: center;">
                                            <b>-</b>
                                        </div>';
                    echo '
                                    </li>';

                    foreach ($rezult as $item){

                        //Если удалён, то меняем цвет на серый
                        if ( $item['status'] != 9){
                            $bgColor = '';
                        }else{
                            $bgColor = 'background-color: rgba(199, 199, 199, 1);';
                        }

                        $result_temp = '
                                <li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">';
                        $result_temp .= '
                                    <div class="cellOrder" style="position: relative; border-right: none; border-top: none;">
                                        <b>В банк #' . $item['id'] . '</b><br>от ' . $item['day'].'.'.$item['month'].'.'.$item['year'].'<br>
                                        <span style="font-size: 90%;  color: #555;">';

                        if (($item['create_time'] != 0) || ($item['create_person'] != 0)) {
                            $result_temp .= '
                                            Добавлен: ' . date('d.m.y H:i', strtotime($item['create_time'])) . '<br>
                                            Автор: ' . WriteSearchUser('spr_workers', $item['create_person'], 'user', true) . '<br>';
                        } else {
                            $result_temp .= 'Добавлен: не указано<br>';
                        }
                        /*if (($order_item['last_edit_time'] != 0) || ($order_item['last_edit_person'] != 0)){
                            echo'
                                            Последний раз редактировался: '.date('d.m.y H:i',strtotime($order_item['last_edit_time'])).'<br>
                                            <!--Кем: '.WriteSearchUser('spr_workers', $order_item['last_edit_person'], 'user', true).'-->';
                        }*/
                        $result_temp .= '
                                        </span>
                                                        
                                    </div>';


                        $result_temp .= '                              
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="text-align: right;">
                                            <span class="calculateInvoice" style="font-size: 13px">' . $item['summ'] . '</span> руб.
                                        </div>
                                    </div>
                                    <div class="cellName" style="border-right: none; border-top: none;">
                                        <div style="margin: 1px 0; padding: 1px 3px;">
                                            <span class="" style="font-size: 13px">' . $item['comment'] . '</span>
                                        </div>
                                    </div>';

                        //Удалить или восстановить
                        if ( $item['status'] != 9) {
                            $result_temp .= ' 
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_deleteToDirector(' . $item['id'] . ');">
                                        <i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
                                    </div>';
                        }else {
                            $result_temp .= '
                                    <div class="cellCosmAct info" style="font-size: 100%; text-align: center; border-top: none;" onclick="fl_reopenToDirector('.$item['id'].');">
                                        <i class="fa fa-reply" aria-hidden="true" style="cursor: pointer;"  title="Восстановить"></i>
                                    </div>';
                        }

                        $result_temp .= '
                                </li>';

                        //Если не удалённый
                        if ( $item['status'] != 9){
                            $result .= $result_temp;
                        }else{
                            $deleted_orders .= $result_temp;
                        }

                    }

                    //Выводим
                    echo $result;

                    if (($finances['see_all'] == 1) || $god_mode) {
                        echo $deleted_orders;
                    }

                    echo '
                                </ul>
                            </div>';
                }else{
                    echo '<span style="color: red;">Ничего не найдено</span>';
                }


                echo '
                    </div>
                </div>
                <div id="doc_title">Расходные ордеры - Асмедика</div>';

                echo '	
			<!-- Подложка только одна -->
			<div id="overlay"></div>';



                echo '
					<script>
					
						$(function() {

                            $("#SelectFilial").change(function(){
							    
							    blockWhileWaiting (true);
							    
							    var get_data_str = "";

                                
                                var params = window
                                    .location
                                    .search
                                    .replace("?","")
                                    .split("&")
                                    .reduce(
                                        function(p,e){
                                            var a = e.split(\'=\');
                                            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                            return p;
                                        },
                                        {}
                                    );
                                //console.log(params);
                                                                
                                for (key in params) {
                                    if (key.indexOf("filial") == -1){
                                        get_data_str = get_data_str + "&" + key + "=" + params[key];
                                    }
                                }
                                //console.log(get_data_str);
							    
								document.location.href = "?filial="+$(this).val() + get_data_str;
								
							});
							
							$("#SelectDayW").change(function(){
							
							    blockWhileWaiting (true);
							    
								var filial = document.getElementById("SelectFilial").value;
								document.location.href = "?dayw="+$(this).val()+"&filial="+filial+"";
							});
						});
						
					</script>';


            }else{
                echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
            }
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>