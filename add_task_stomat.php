<?php

//add_task_stomat.php
//Добавить посещение стоматолога

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($stom['add_own'] == 1) || ($stom['add_new'] == 1) || $god_mode){
			
			
			include_once 'DBWork.php';
			include_once 'functions.php';

            require 'variables.php';

            // !!! **** тест с записью
            include_once 'showZapisRezult.php';

			include_once 'tooth_status.php';

            $filials_j = getAllFilials(true, false, true);
            //var_dump($filials_j);

            $sheduler_zapis = array();
			
			//$post_data = '';
			//$js_data = '';
			$dop = array();
			$t_f_data_draw = array();
			$first_db = TRUE;

            $client_j = 0;
			
			$t_f_data_db_first = array(
				11 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				12 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				13 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				14 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				15 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				16 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				17 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				18 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				21 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				22 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				23 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				24 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				25 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				26 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				27 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				28 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				31 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				32 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				33 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				34 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				35 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				36 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				37 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				38 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				41 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				42 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				43 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				44 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				45 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				46 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				47 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
				48 => '0,0,0,0,0,0,0,0,0,0,0,0,0',
			);	
			
			$t_f_data_db_temp = array();
			$t_f_data_db_temp_dop = array();
			
			//var_dump($_GET);
			
			//Если у нас по GET передали ID записи
			if (isset($_GET['zapis_id'])){

                $msql_cnnct = ConnectToDB ();

                $zapis_id = $_GET['zapis_id'];

			    //Получаем данные по записи
                $query = "SELECT * FROM `zapis` WHERE `id`='".$zapis_id."' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($sheduler_zapis, $arr);
                    }
                }
                //var_dump($sheduler_zapis);

                //Если запись есть (а она должна быть)
                if (!empty($sheduler_zapis)) {

                    $client_id = $sheduler_zapis[0]['patient'];
                    $filial_id = $sheduler_zapis[0]['office'];

                    //Получаем все данные по клиенту
                    $client_j = SelDataFromDB('spr_clients', $client_id, 'user');

                    $get_client = $client_j[0]['full_name'];

                    //Получаем последнюю ЗФ этого пациента (если есть), чтобы прошлые состояния зубов показать
                    $query = "SELECT * FROM `journal_tooth_status` WHERE `client` = '{$client_id}' ORDER BY `create_time` DESC LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);

                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            array_push($t_f_data_db_temp, $arr);
                        }
                        $t_f_data_db = $t_f_data_db_temp[0];
                        $first_db = FALSE;
                    } else {
                        $t_f_data_db = $t_f_data_db_first;
                    }

                    //Если карточка пациента не заполнена
                    if (
                        ($client_j[0]['card'] == NULL) ||
                        ($client_j[0]['birthday2'] == '0000-00-00') ||
                        ($client_j[0]['sex'] == 0) ||
                        ($client_j[0]['address'] == NULL)
                    ){
                        echo '<div class="query_neok">В <a href="client.php?id='.$client_id.'">карточке пациента</a> не заполнены все необходимые графы.</div>';
                    }else {

                        echo '
                        <script src="js/init.js" type="text/javascript"></script>
                        <div id="status">
                            <header>
                                <h2>Добавить осмотр пациенту: <a href="client.php?id='.$client_id.'" class="ahref">'.$get_client.'</a></h2>';

                        //переменная для просроченных
                        $allPayed = true;

                        //Долги/авансы
                        //
                        //!!! @@@
                        //Баланс контрагента
                        include_once 'ffun.php';

                        //Долг контрагента
                        $client_debt = json_decode(calculateDebt ($client_id), true);

                        if ($client_debt['summ'] > 0){
                            $allPayed = false;
                        }

                        //Если есть долги
                        if (!$allPayed) {
                            echo '
                            <div style="color: red; font-size: 13px;">
							    <span style="font-size: 17px;"><i class="fa fa-exclamation-circle" aria-hidden="true" title="Есть долги"></i></span> У пациента есть долги.
                            </div>';
                        }

                        echo ' 
                            </header>';

                        //Показываем карточку записи
                        echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);

                        echo '
                            <div id="data">';

                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size:80%;  color: #555;">Зубная формула</span><br>
                                    </div>
                                </div>';
                        echo '						
                                <div class="cellsBlock3">
                                    <div class="cellRight" id="teeth_map">';

                        //Разбиваем запись с ',' на массив и записываем в новый массив
                        foreach ($t_f_data_db as $key => $value) {
                            $surfaces_temp = explode(',', $value);
                            foreach ($surfaces_temp as $key1 => $value1) {
                                ///!!!Еба костыль
                                if ($key1 < 13) {
                                    $t_f_data[$key][$surfaces[$key1]] = $value1;
                                }
                            }
                        }
                        //var_dump($t_f_data_db);

                        //ЗО и тд
                        if (!$first_db) {

                            $query = "SELECT * FROM `journal_tooth_status_temp` WHERE `id` = '{$t_f_data_db['id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                            $number = mysqli_num_rows($res);
                            if ($number != 0) {
                                while ($arr = mysqli_fetch_assoc($res)) {
                                    array_push($dop, $arr);
                                }

                            }
                        }
                        //var_dump($dop);
                        //var_dump($t_f_data);

                        unset($t_f_data['id']);
                        unset($t_f_data['office']);
                        unset($t_f_data['client']);
                        unset($t_f_data['create_time']);
                        unset($t_f_data['create_person']);
                        unset($t_f_data['last_edit_time']);
                        unset($t_f_data['last_edit_person']);
                        unset($t_f_data['worker']);
                        unset($t_f_data['comment']);
                        unset($t_f_data['zapis_date']);
                        unset($t_f_data['zapis_id']);

                        unset($t_f_data_db['id']);
                        unset($t_f_data_db['office']);
                        unset($t_f_data_db['client']);
                        unset($t_f_data_db['create_time']);
                        unset($t_f_data_db['create_person']);
                        unset($t_f_data_db['last_edit_time']);
                        unset($t_f_data_db['last_edit_person']);
                        unset($t_f_data_db['worker']);
                        unset($t_f_data_db['comment']);
                        unset($t_f_data_db['zapis_date']);
                        unset($t_f_data_db['zapis_id']);
                        //unset($dop[0]['id']);


                        foreach ($t_f_data_db as $key => $value) {
                            $surfaces_temp = explode(',', $value);
                            foreach ($surfaces_temp as $key1 => $value1) {
                                ///!!!Еба костыль
                                if ($key1 < 13) {
                                    $t_f_data_draw[$key][$surfaces[$key1]] = $value1;
                                }
                            }
                        }

                        //var_dump ($t_f_data);

                        if (!empty($dop[0])) {
                            //var_dump($dop[0]);
                            unset($dop[0]['id']);
                            //var_dump($dop[0]);
                            foreach ($dop[0] as $key => $value) {
                                //var_dump($value);
                                if ($value != '0') {
                                    //var_dump($value);
                                    $dop_arr = json_decode($value, true);
                                    //var_dump($dop_arr);
                                    foreach ($dop_arr as $n_key => $n_value) {
                                        if ($n_key == 'zo') {
                                            //$t_f_data[$key]['zo'] = $n_value;
                                            $t_f_data_draw[$key]['zo'] = $n_value;
                                        }
                                        if ($n_key == 'shinir') {
                                            //$t_f_data[$key]['shinir'] = $n_value;
                                            $t_f_data_draw[$key]['shinir'] = $n_value;
                                        }
                                        if ($n_key == 'podvizh') {
                                            //$t_f_data[$key]['podvizh'] = $n_value;
                                            $t_f_data_draw[$key]['podvizh'] = $n_value;
                                        }
                                        if ($n_key == 'retein') {
                                            //$t_f_data[$key]['retein'] = $n_value;
                                            $t_f_data_draw[$key]['retein'] = $n_value;
                                        }
                                        if ($n_key == 'skomplect') {
                                            //$t_f_data[$key]['skomplect'] = $n_value;
                                            $t_f_data_draw[$key]['skomplect'] = $n_value;
                                        }
                                    }
                                }
                            }
                        }
                        //var_dump ($t_f_data);

                        //Пробуем записать в сессию.
                        $_SESSION['journal_tooth_status_temp'][$client_id] = $t_f_data_draw;
                        //var_dump($_SESSION['journal_tooth_status_temp']);

                        //рисуем зубную формулу
                        include_once 'teeth_map_svg.php';
                        DrawTeethMap($t_f_data_draw, 1, $tooth_status, $tooth_alien_status, $surfaces, '');

                        echo '
                                    </div>
                                </div>';

                        //Жалобы
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 90%;">Жалобы</span><br>
                                        <textarea name="complaints" id="complaints" cols="80" rows="4"></textarea>
                                    </div>
                                </div>';

                        //Объективно
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 90%;">Объективно</span><br>
                                        <textarea name="objectively" id="objectively" cols="80" rows="6"></textarea>
                                    </div>
                                </div>';

                        //Диагноз
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 90%;">Диагноз</span><br>
                                        <textarea name="diagnosis" id="diagnosis" cols="80" rows="2"></textarea>
                                    </div>
                                </div>';

                        //Лечение
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 90%;">Лечение</span><br>
                                        <textarea name="therapy" id="therapy" cols="80" rows="6"></textarea>
                                    </div>
                                </div>';

                        //Рекомендовано
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
        							    <span style="font-size: 90%;">Рекомендовано</span><br>
                                        <textarea name="recommended" id="recommended" cols="80" rows="5"></textarea>
                                    </div>
                                </div>';

                        //Комментарий
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <span style="font-size: 80%;">Комментарий</span><br>
                                        <textarea name="comment" id="comment" cols="50" rows="4"></textarea>
                                    </div>
                                </div>';



                        //Напоминания
                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <!--<span class="ahref button_tiny" style="font-size:80%;  color: #555;">Создать напоминание</span> <input type="checkbox" name="add_notes_show" id="add_notes_show" value="1" onclick="Add_notes_stomat_show(this)"><br>-->
                                        <span class="ahref button_tiny" style="font-size:80%;  color: #555;" onclick="toggleSomething (\'#add_notes_here\'); Add_notes_stomat_show();">Создать напоминание</span><br><br>
                                        <input type="hidden" name="add_notes_show" id="add_notes_show" value="0">
                                        <table id="add_notes_here" style="display:none;">
                                            <tr>
                                                <td colspan="2">
                                                
                                                    <!--<form action="add_notes_stomat_f.php">-->
                                                        <select name="add_notes_type" id="add_notes_type">
                                                            <option value="0" selected>Выберите</option>';
                        foreach ($for_notes as $for_notes_id =>  $for_notes_descr){
                            echo '<option value="'.$for_notes_id.'">'.$for_notes_descr.'</option>';
                        }
                        /*echo '
                                                                <option value="1">Каласепт, Метапекс, Септомиксин (Эндосольф)</option>
                                                                <option value="2">Временная пломба</option>
                                                                <option value="3">Открытый зуб</option>
                                                                <option value="4">Депульпин</option>
                                                                <option value="5">Распломбирован под вкладку (вкладка)</option>
                                                                <option value="6">Имплантация (ФДМ ,  абатмент, временная коронка на импланте)</option>
                                                                <option value="7">Временная коронка</option>
                                                                <option value="10">Установлены брекеты</option>
                                                                <option value="8">Санированные пациенты ( поддерживающее лечение через 6 мес)</option>
                                                                <option value="9">Прочее</option>';*/
                        echo '
                                                        </select>
                                                    <!--</form>-->
                                                
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Месяцев</td>
                                                <td>Дней</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" size="2" name="add_notes_months" id="add_notes_months" min="0" max="12" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" size="2" name="add_notes_days" id="add_notes_days" min="0" max="31" value="0">
                                                </td>
                                            </tr>
                                            <!--<tr>
                                                <td>
                                                    <input type=\'button\' class="b" value=\'Добавить\' onclick=Ajax_add_notes_stomat()>
                                                </td>
                                            </tr>-->
                                        </table>
                                    </div>
                                </div>';


                        echo '
                                <div class="cellsBlock3">
                                    <div class="cellLeft">
                                        <!--<span style="font-size:80%;  color: #555;">Создать направление</span> <input type="checkbox" name="add_remove_show" id="add_remove_show" value="1" onclick="Add_remove_stomat_show(this)"><br>-->
                                        <span class="ahref button_tiny" style="font-size:80%;  color: #555;" onclick="toggleSomething (\'#add_remove_here\'); Add_remove_stomat_show();">Создать направление</span><br><br>
                                        <input type="hidden" name="add_remove_show" id="add_remove_show" value="0">
                                        <div id="add_remove_here" style="display:none;">
                                            <table id="table_container">
                                            </table>
                                            <a href="#modal1" class="open_modal b2" id="">Добавить направление</a>
                                            <!--<input type="button" class="b" value="Добавить поле" id="add" onclick="return add_new_image(' . $_SESSION['id'] . ');">-->
                                            <div id="mini"></div>
                                        </div>
                                    </div>
                                </div>';

                        echo '                                
                                <input type="hidden" id="zapis" name="zapis" value="' . $zapis_id . '">
                                <input type="hidden" id="client" name="client" value="' . $client_id . '">
                                <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick=Ajax_add_task_stomat()>';

                        echo '
                            </div>
                            <div id="doc_title">Добавить осмотр [Стоматология] </div>
                        </div>';




                        echo '

                        <!-- Подложка только одна -->
                        <div id="overlay"></div>

                        <!-- Модальные окна -->
                        
                        <!--Направления-->
                        <div id="modal1" class="modal_div">
                            <span class="modal_close">X</span>
                            <table>
                                <tr>
                                    <td>
                                        Причина направления
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="input_title" id="input_title" class="search_data"  autocomplete="off" style="width: 200px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        К кому направляем
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" size="50" name="searchdata3" id="search_client3" placeholder="Введите первые три буквы для поиска" value="" class="who3"  autocomplete="off" />
                                        <ul id="search_result3" class="search_result3"></ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#" class="b" id="close_mdd" onclick="AddRemoveData()" style="">Добавить</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!--Для ЗФ-->
                        <div id="modal2" class="modal_div">
                            <span class="modal_close">X</span>
                            
                            <h3>Выбор нескольких сегментов зубной формулы.</h3>
                            <b>Статус: </b>
                            <div id="t_summ_status"></div>
        
                            <table>
                                <tr>
                                    <td>
                                        <table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    18
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    17
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    16
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    15
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    14
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    13
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    12
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    11
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t18" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t17" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t16" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t15" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t14" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t13" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t12" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t11" value="1">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    21
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    22
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    23
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    24
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    25
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    26
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    27
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    28
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t21" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t22" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t23" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t24" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t25" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t26" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t27" value="1">
                                                </td>
                                                    <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t28" value="1">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    48
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    47
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    46
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    45
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    44
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    43
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    42
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    41
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t48" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t47" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t46" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t45" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t44" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t43" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t42" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t41" value="1">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table width="100%" style="border: 1px solid #BEBEBE; margin:5px;">
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    31
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    32
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    33
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    34
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    35
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    36
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    37
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    38
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t31" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t32" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t33" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t34" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t35" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t36" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t37" value="1">
                                                </td>
                                                <td style="border: 1px solid #BEBEBE;">
                                                    <input type="checkbox" name="t38" value="1">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="implant" value="1"> + имплант
                                    </td>
                                </tr>
                            </table>
                            <a href="#" class="b" onclick="refreshAllTeeth()">Применить</a>
                            
                        </div>
                        
                        ';
                        

                    }


                }else{
                    echo '<h1>Ошибка при получении данных.</h1><a href="index.php">На главную</a>';
                }

			}else{
                echo '<h1>Ошибка при получении данных.</h1><a href="index.php">На главную</a>';
			}



                    //Фунции JS
                    echo '
                    
                    <script type="text/javascript">
        
                        
                        function AddRemoveData(){
                                
                            var arrayRemoveAct = new Array();
                            var arrayRemoveWorker = new Array();
                            var maxIndex = 1;
                            
                            var input_title = $("#input_title").val();
                            var search_client3 = $("#search_client3").val();
                            //alert(input_title);
                            
                            $(".remove_add_search").each(function() {
                                if (($(this).attr("id")).indexOf("td_title") != -1){
                                        var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                                        arrayRemoveAct[IndexArr] = document.getElementById($(this).attr("id")).value;
                                    }
                                    if (($(this).attr("id")).indexOf("td_worker") != -1){
                                        var IndexArr = $(this).attr("id")[$(this).attr("id").length-1];
                                        arrayRemoveWorker [IndexArr] = document.getElementById($(this).attr("id")).value;
                                    }
                                maxIndex = Number(IndexArr)+1;
                            });
                            
                            arrayRemoveAct[maxIndex] = input_title;
                            arrayRemoveWorker[maxIndex] = search_client3;
                            
                             $(document.getElementById("table_container")).empty();
                            
                            arrayRemoveAct.forEach(function(item, i, arrayRemoveAct){
                                $(\'<tr>\')
                                .attr(\'id\',\'tr_image_\'+i)
                                .css({lineHeight:\'20px\'})
                                .append (
                                    $(\'<td>\')
                                    .css({paddingRight:\'5px\',width:\'190px\'})
                                    .append(
                                        $(\'<input type="text" />\')
                                        .css({width:\'200px\'})
                                        .attr(\'id\',\'td_title_\'+i)
                                        .attr(\'class\',\'remove_add_search\')
                                        .attr(\'name\',\'input_title_\'+i)
                                        .attr(\'value\',item)
                                    )		
                                    
                                )
                                
                                .append (
                                    $(\'<td>\')
                                    .css({paddingRight:\'5px\',width:\'200px\'})
                                    .append(
                                        $(\'<input type="text" size="50" name="" placeholder="" autocomplete="off" />\')
                                            .attr(\'id\',\'td_worker_\'+i)
                                            .attr(\'class\',\'remove_add_search\')
                                            .attr(\'value\',arrayRemoveWorker[i])
                                    )
                                )	
                                
                                .append (
                                    $(\'<td>\')
                                    .css({width:\'60px\'})
                                    .append (
                                        $(\'<span id="progress_\'+i+\'"><a  href="#" onclick="$(\\\'#tr_image_\'+i+\'\\\').remove();" class="ico_delete"><img src="./img/delete.png" alt="del" border="0"></a></span>\')
                                    )
                                )
                                .appendTo(\'#table_container\');
                                
                                
                                //$("#mini").append(this + "<br>");
                                
                            });
                                    
                            //скрываем модальные окна
                            $("#modal1, #modal2") // все модальные окна
                                .animate({opacity: 0, top: \'45%\'}, 50, // плавно прячем
                            function(){ // после этого
                                $(this).css(\'display\', \'none\');
                                $(\'#overlay\').fadeOut(50); // прячем подложку
                            }
                            );
                                    
                            //Очистим поля ввода
                            $("#input_title").val(""); 
                            $("#search_client3").val("");
            
                        };
                    </script>
                    
                    
                        <script>
        
        $(function(){
                
            //Живой поиск
            $(\'.who3\').bind("change keyup input click", function() {
                //alert(123);
                if(this.value.length > 2){
                    $.ajax({
                        url: "FastSearchNameW.php", //Путь к обработчику
                        //statbox:"status",
                        type:"POST",
                        data:
                        {
                            \'searchdata3\':this.value
                        },
                        response: \'text\',
                        success: function(data){
                            $(".search_result3").html(data).fadeIn(); //Выводим полученые данные в списке
                        }
                    })
                }else{
                    var elem1 = $("#search_result3"); 
                    elem1.hide(); 
                }
            })
                
            $(".search_result3").hover(function(){
                $(".who3").blur(); //Убираем фокус с input
            })
                
            //При выборе результата поиска, прячем список и заносим выбранный результат в input
            $(".search_result3").on("click", "li", function(){
                s_user = $(this).text();
                $(".who3").val(s_user);
                //$(".who").val(s_user).attr(\'disabled\', \'disabled\'); //деактивируем input, если нужно
                $(".search_result3").fadeOut();
            })
            //Если click за пределами результатов поиска - убираем эти результаты
            $(document).click(function(e){
                var elem = $("#search_result3"); 
                if(e.target!=elem[0]&&!elem.has(e.target).length){
                    elem.hide(); 
                } 
            })
        })
        
                        
//                        document.getElementById(\'add_notes_show\').checked=false;
//                        document.getElementById(\'add_remove_show\').checked=false;
//                        
//                        function Add_notes_stomat_show(box) {
//                            var vis = (box.checked) ? "block" : "none";
//                            document.getElementById(\'add_notes_here\').style.display = vis;
//                        }

                        function Add_notes_stomat_show() {
//                            console.log($("#add_notes_here").css("display"));
//                            console.log($("#add_notes_show").css("display"));
                            
                            //через полсекунды ставим значение 1 в маркер
                            setTimeout(function () {
                                if ($("#add_notes_here").css("display") == "none"){
                                    $("#add_notes_show").val(0);
                                }else{
                                    $("#add_notes_show").val(1);
                                }                                
                            }, 500);
                            

                        }

//                        function Add_remove_stomat_show(box) {
//                            var vis = (box.checked) ? "block" : "none";
//                            document.getElementById(\'add_remove_here\').style.display = vis;
//                        }

                        function Add_remove_stomat_show() {
//                            console.log($("#add_remove_here").css("display"));
//                            console.log($("#add_remove_show").css("display"));
                            
                            //через полсекунды ставим значение 1 в маркер
                            setTimeout(function () {
                                if ($("#add_remove_here").css("display") == "none"){
                                    $("#add_remove_show").val(0);
                                }else{
                                    $("#add_remove_show").val(1);
                                }                                
                            }, 500);
                            

                        }
        

       </script> 
                        
        
                        
                        
                        
                    ';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>