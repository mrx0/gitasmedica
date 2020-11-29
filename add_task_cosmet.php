<?php

//add_task_cosmet.php 
//Добавить задачу косметологов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($cosm['add_own'] == 1) || ($cosm['add_new'] == 1) || $god_mode) {
            //include_once 'DBWork.php';

            include_once('DBWorkPDO.php');

            include_once 'functions.php';

            require 'variables.php';

            include_once 'showZapisRezult.php';

            $db = new DB();

            $offices = SelDataFromDB('spr_filials', '', '');

            $post_data = '';
            $js_data = '';

            if (isset($_GET['zapis_id'])) {

                $zapis_id = $_GET['zapis_id'];

                $args = [
                    'zapis_id' => $zapis_id
                ];

                //Получаем данные по записи
                $query = "SELECT * FROM `zapis` WHERE `id`=:zapis_id LIMIT 1";

                //Выбрать все
                $sheduler_zapis = $db::getRows($query, $args);
                //var_dump($sheduler_zapis);

                if (!empty($sheduler_zapis)) {

                    $client_id = $sheduler_zapis[0]['patient'];
                    $filial_id = $sheduler_zapis[0]['office'];

                    //Если у нас по GET передали клиента
                    $get_client = '';
                    if (isset($_GET['client']) && ($_GET['client'] != '')) {
                        $client_j = SelDataFromDB('spr_clients', $_GET['client'], 'user');
                        if ($client_j != 0) {
                            $get_client = $client_j[0]['full_name'];
                        }

                    }

                    if (isset($_GET['filial'])) {
                        $selected_fil = $_GET['filial'];
                    } else {
                        //Автоматизация выбора филиала
                        /*if (isset($_SESSION['filial']) && !empty($_SESSION['filial'])){
                            $selected_fil = $_SESSION['filial'];
                        }else{
                            $selected_fil = 0;
                        }*/

                        $selected_fil = 0;
                    }

                    if (isset($_GET['pervich']) && (($_GET['pervich'] == 1) || ($_GET['pervich'] == 2))) {
                        $pervich_check = ' checked';
                    } else {
                        $pervich_check = '';
                    }

                    /*if (isset($_GET['insured']) && ($_GET['insured'] == 1)){
                        $insured_check = ' checked';
                    }else{
                        $insured_check = '';
                    }

                    if (isset($_GET['noch']) && ($_GET['noch'] == 1)){
                        $noch_check = ' checked';
                    }else{
                        $noch_check = '';
                    }*/

                    if (isset($_GET['date'])) {
                        $zapis_date = date('d.m.y H:i', $_GET['date']);
                        $zapis_date_hidden = $_GET['date'];
                    } else {
                        $zapis_date = date('d.m.y H:i', time());
                        $zapis_date_hidden = time();
                    }

//                    if (isset($_GET['zapis_id'])) {
//                        $zapis_id = $_GET['zapis_id'];
//                    } else {
//                        $zapis_id = 0;
//                    }
//var_dump($zapis_id);

                    //if (($client_j != 0) && ($selected_fil != 0)){
                    if (
                        ($client_j[0]['card'] == NULL) ||
                        ($client_j[0]['birthday2'] == '0000-00-00') ||
                        ($client_j[0]['sex'] == 0) ||
                        ($client_j[0]['address'] == NULL)
                    ) {
                        echo '<div class="query_neok">В <a href="client.php?id=' . $_GET['client'] . '">карточке пациента</a> не заполнены все необходимые графы.</div>';
                    } else {


                        echo '
                                <div id="status">
                                    <header>
                                        <h2>Добавить процедуры пациенту: <a href="client.php?id=' . $client_id . '" class="ahref">' . $get_client . '</a></h2>
                                    </header>';

                        //Показываем карточку записи
                        echo showZapisRezult($sheduler_zapis, false, false, false, false, false, false, 0, false, false);

                        echo '
                                    <div id="data">';

                        echo '
                                        <form action="add_task_cosmet_f.php">';
//                            echo '
//                                            <div class="cellsBlock3">
//                                                <div class="cellLeft">
//                                                    Время посещения
//                                                    <span style="font-size:70%;">
//                                                        Согласно записи
//                                                    </span>
//                                                </div>
//                                                <div class="cellRight">
//                                                    ' . $zapis_date . '
//                                                </div>
//                                            </div>';
//
//                            if (($cosm['add_new'] == 1) || $god_mode) {
//                                if (isset($_GET['worker'])) {
//                                    $workerEcho = WriteSearchUser('spr_workers', $_GET['worker'], 'user_full', false);
//                                } else {
//                                    $workerEcho = '';
//                                }
//                                echo '
//                                            <div style="margin-bottom: 10px; color: #777; font-size: 90%;">Необходимо выбрать исполнителя</div>
//                                            <div class="cellsBlock3" style="margin-bottom: 20px;">
//                                                <div class="cellLeft">Исполнитель</div>
//                                                <div class="cellRight">
//                                                    <input type="text" size="50" name="searchdata2" id="search_worker" placeholder="Введите первые три буквы для поиска" value="' . $workerEcho . '" class="who2"  autocomplete="off">
//                                                    <ul id="search_result2" class="search_result2"></ul><br />
//                                                    <label id="worker_error" class="error"></label>
//                                                </div>
//                                            </div>';
//                            }

                        /*echo '
                                        <div class="cellsBlock3">
                                            <div class="cellLeft">Филиал</div>
                                            <div class="cellRight">
                                                <select name="filial" id="filial">
                                                    <option value="0" selected>Выберите филиал</option>';
                                                if ($offices != 0){
                                                    for ($i=0;$i<count($offices);$i++){
                                                        echo "<option value='".$offices[$i]['id']."' ", $selected_fil == $offices[$i]['id'] ? "selected" : "" ,">".$offices[$i]['name']."</option>";
                                                    }
                                                }
                                                echo '
                                                </select>
                                                <label id="filial_error" class="error">
                                            </div>
                                        </div>';*/

//                            echo '
//                                            <div class="cellsBlock3">
//                                                <div class="cellLeft">Филиал</div>
//                                                <div class="cellRight">';
//                            $offices_j = SelDataFromDB('spr_filials', $selected_fil, 'offices');
//
//                            echo $offices_j[0]['name'] . '<input type="hidden" id="filial" name="filial" value="' . $selected_fil . '">';
//
//                            echo '
//                                                </div>
//                                            </div>';

                        echo '
                                            <!--<div class="cellsBlock3">
                                                <div class="cellLeft">Пациент</div>
                                                <div class="cellRight">
                                                    <input type="text" size="50" name="searchdata" id="search_client" placeholder="Введите первые три буквы для поиска" value="' . $get_client . '" class="who"  autocomplete="off">
                                                    <ul id="search_result" class="search_result"></ul><br />
                                                    <label id="client_error" class="error">
                                                </div>
                                            </div>-->
                
                
                    <!--<script type="text/javascript">
                        function showMe (box){
                            let vis = (box.checked) ? "block" : "none";
                            document.getElementById(\'div1\').style.display = vis;
                        }
                    </script>-->';

                        $actions_cosmet = SelDataFromDB('actions_cosmet', '', '');
                        //var_dump ($actions_cosmet);
                        if ($actions_cosmet != 0) {


                            //отсортируем по nomer

                            foreach ($actions_cosmet as $key => $arr_temp) {
                                $data_nomer[$key] = $arr_temp['nomer'];
                            }
                            //var_dump ($data_nomer);

                            array_multisort($data_nomer, SORT_NUMERIC, $actions_cosmet);
                            //var_dump ($actions_cosmet);

                            for ($i = 0; $i < count($actions_cosmet) - 2; $i++) {
                                /*$js_data .= '
                                    if ($("#action'.$actions_cosmet[$i]['id'].'").prop("checked")){
                                        action_value'.$actions_cosmet[$i]['id'].' = 1;
                                    }else{
                                        action_value'.$actions_cosmet[$i]['id'].' = 0;
                                    }

                                ';*/
                                $js_data .= '
                                        let action_value' . $actions_cosmet[$i]['id'] . ' = $("input[name=action' . $actions_cosmet[$i]['id'] . ']:checked").val();
                                    ';
                                $post_data .= '
                                                    action' . $actions_cosmet[$i]['id'] . ':action_value' . $actions_cosmet[$i]['id'] . ',';

                                //отметка для первички (костыль от старого определения)
                                if ($actions_cosmet[$i]['id'] == 13) {
                                    if (isset($_GET['pervich']) && ($_GET['pervich'] == 1)) {
                                        $pervich_cons_check = ' checked';

                                    } else {
                                        $pervich_cons_check = '';
                                    }
                                    $pervich_cons_check .= ' onclick="CheckPervich()"';
                                } else {
                                    $pervich_cons_check = '';
                                }

                                echo '
                                        <div class="cellsBlock3" style="font-size:80%;">
                                            <div class="cellLeft">' . $actions_cosmet[$i]['full_name'] . '</div>
                                            <div class="cellRight">
                                                <input type="checkbox" name="action' . $actions_cosmet[$i]['id'] . '" id="action' . $actions_cosmet[$i]['id'] . '" value="1" ' . $pervich_cons_check . '>
                                            </div>
                                        </div>';
                            }

                            //Новая отметка для первички
//                            echo '
//                                        <div class="cellsBlock3">
//                                            <div class="cellLeft">
//                                                Первичный<br>
//                                                <span style="font-size: 70%;">Определяется из записи пациента</span>
//                                            </div>
//                                            <div class="cellRight">
//                                                <input type="checkbox" name="pervich" id="pervich" value="1" ' . $pervich_check . ' disabled> да
//                                            </div>
//                                        </div>';

                            echo '
                                        <div class="cellsBlock3">
                                            <div class="cellLeft">Комментарий</div>
                                            <div class="cellRight">
                                                <textarea name="comment" id="comment" cols="35" rows="10"></textarea>
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
                            foreach ($for_notes[6] as $for_notes_id =>  $for_notes_descr){
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

                        }
                        echo '
                                            <input type="hidden" id="author" name="author" value="' . $_SESSION['id'] . '">
                                            <input type="hidden" id="zapis_date" name="zapis_date" value="' . $zapis_date_hidden . '">
                                            <input type="hidden" id="zapis" name="zapis" value="' . $zapis_id . '">
                                            <div id="errror"></div>
                                            <input type="button" class="b" value="Добавить" onclick="Ajax_add_task_cosmet()">
                                        </form>';

                        echo '
                                    </div>
                                    <div id="doc_title">Добавить процедуры [Косметология] </div>
                                </div>';
                    }
                } else {
                    echo '<h1>Ошибка при получении данных.</h1><a href="index.php">На главную</a>';
                }

            } else {
                echo '<h1>Ошибка при получении данных.</h1><a href="index.php">На главную</a>';
            }

            //Фунция JS для проверки не нажаты ли чекбоксы + AJAX

            echo '
                        <script>  
        
                            function Ajax_add_task_cosmet() {
                                
                                let link = "add_task_cosmet_f.php";
                                
                                ' . $js_data . '
                                                
                                if ($("#pervich").prop("checked")){
                                    pervich = 1;
                                }else{
                                    pervich = 0;
                                }
                                
                                let reqData = {';

            echo $post_data;
            echo '                
                                    zapis_id: $("#zapis").val(),
                        
//                                    complaints: $("#complaints").val(),
//                                    objectively: $("#objectively").val(),
//                                    diagnosis: $("#diagnosis").val(),
//                                    therapy: $("#therapy").val(),
//                                    recommended: $("#recommended").val(),
                        
                                    comment: $("#comment").val(),
                        
                                    notes: $("#add_notes_show").val(),
                                    //remove: $("#add_remove_show").val(),
                        
                                    //removeAct: JSON.stringify(arrayRemoveAct),
                                    //removeWork: JSON.stringify(arrayRemoveWorker),
                        
                                    add_notes_type: $("#add_notes_type").val(),
                                    add_notes_months: $("#add_notes_months").val(),
                                    add_notes_days: $("#add_notes_days").val(),
                                    
                                    pervich:pervich,
                                };
                                
                                //console.log(reqData);
                                
                                $.ajax({
                                    url: link,
                                    global: false,
                                    type: "POST",
                                    //dataType: "JSON",
                                    data: reqData,
                                    cache: false,
                                    beforeSend: function () {
                        
                                    },
                                    success: function (res) {
                                        //console.log (res);
                        
                                        $("#status").html(res);
                                    }
                                })


                            }
                              
                            function CheckPervich(){
                                //alert(document.getElementById("pervich").checked);
        
                                if(document.getElementById("action13").checked == true){
                                    document.getElementById("pervich").checked = true;
                                }else{
                                    document.getElementById("pervich").checked = false;
                                }
                            }
                            
                            
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
                              
                              
                              
                              
                        </script> 
                    ';



                    /*echo '
                        <script type="text/javascript">
                            $("input").change(function() {
                                let $input = $(this);';
                    echo $js_data;
                    echo '
                            });
                        </script>
                    ';*/
                //}
//            }else{
//                echo '<h1>Ошибка при получении данных.</h1><a href="index.php">На главную</a>';
//            }
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>