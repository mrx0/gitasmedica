<?php

//ticket_edit.php
//Редактировать тикет

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($ticket['edit'] == 1) || $god_mode) {
            if ($_GET){
                include_once 'DBWork.php';
                include_once 'functions.php';

                $ticket_j = array();

                $msql_cnnct2 = ConnectToDB_2 ('config_ticket');

                $query_dop = '';

                //Для начала получим этот самый тикет из базы

                //Если не "бог" надо выбрать те, которые относятся к специализации, указанной при добавлении
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_worker_type` WHERE `worker_type` = '{$_SESSION['permissions']}' AND `ticket_id` = j_ticket.id)";
                }

                //Надо выбрать те, которые относятся к филиалу, указанному при добавлении
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    if (isset($_SESSION['filial'])) {
                        $query_dop .= " AND j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_filial` WHERE `filial_id` = '{$_SESSION['filial']}' AND `ticket_id` = j_ticket.id)";
                    }
                }

                //Надо выбрать те, которые относятся к конкретному сотруднику
                if (($ticket['see_all'] != 1) && (!$god_mode)){
                    $query_dop .= " OR j_ticket.id IN (SELECT `ticket_id` FROM `journal_tickets_workers` WHERE `worker_id` = '{$_SESSION['id']}' AND `ticket_id` = j_ticket.id)";
                }

                $query = "SELECT j_ticket.*, jticket_rm.status as read_status, j_tickets_worker.worker_id,
                GROUP_CONCAT(DISTINCT j_tickets_worker2.worker_id ORDER BY j_tickets_worker2.worker_id ASC SEPARATOR \",\") AS worker_ids,
                GROUP_CONCAT(DISTINCT j_tickets_worker_t.worker_type ORDER BY j_tickets_worker_t.worker_type ASC SEPARATOR \",\") AS worker_types,
                GROUP_CONCAT(DISTINCT j_tickets_filial.filial_id ORDER BY j_tickets_filial.filial_id ASC SEPARATOR \",\") AS filials
                FROM `journal_tickets` j_ticket
                LEFT JOIN `journal_tickets_readmark` jticket_rm ON j_ticket.id = jticket_rm.ticket_id AND jticket_rm.create_person = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_workers` j_tickets_worker ON j_ticket.id = j_tickets_worker.ticket_id AND j_tickets_worker.worker_id = '{$_SESSION['id']}'
                LEFT JOIN `journal_tickets_workers` j_tickets_worker2 ON j_ticket.id = j_tickets_worker2.ticket_id 
                LEFT JOIN `journal_tickets_worker_type` j_tickets_worker_t ON j_tickets_worker_t.ticket_id = '{$_GET['id']}' 
                LEFT JOIN `journal_tickets_filial` j_tickets_filial ON j_tickets_filial.ticket_id = '{$_GET['id']}' 
                /*WHERE j_ticket.status <> '9'*/
                WHERE (TRUE 
                {$query_dop}
                OR j_ticket.create_person = '{$_SESSION['id']}')
                AND j_ticket.id = '{$_GET['id']}'";

                //echo $query;

                $res = mysqli_query($msql_cnnct2, $query) or die(mysqli_error($msql_cnnct2).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($ticket_j, $arr);
                    }
                }

                //var_dump($ticket_j);

                if (!empty($ticket_j)) {
                    if ($ticket_j[0]['id'] == $_GET['id']){

                        //$offices = SelDataFromDB('spr_filials', '', '');
                        $filials_j = getAllFilials(true, false);
                        //var_dump($filials_j);
                        //Получили список прав
                        $permissions_j = getAllPermissions(false, true);
                        //var_dump($permissions_j);

                        //Дата по плану
                        $plan_date_arr_temp = explode('-', $ticket_j[0]['plan_date']);

                        $day = $plan_date_arr_temp[2];
                        $month = $plan_date_arr_temp[1];
                        $year = $plan_date_arr_temp[0];

                        $workers_arr_temp = array();
                        $worker_types_arr_temp = array();
                        $filials_arr_temp = array();

                        //Исполнители
                        if ($ticket_j[0]['worker_ids'] != NULL) {
                            $workers_arr_temp = explode(',', $ticket_j[0]['worker_ids']);
                        }
                        //Категории
                        if ($ticket_j[0]['worker_types'] != NULL) {
                            $worker_types_arr_temp = explode(',', $ticket_j[0]['worker_types']);
                        }
                        //Филиалы
                        if ($ticket_j[0]['filials'] != NULL) {
                            $filials_arr_temp = explode(',', $ticket_j[0]['filials']);
                        }

                        echo '
                                <div id="status">
                                    <header>
                                        <div class="nav">
                                            <a href="tickets.php" class="b">Все тикеты</a>
                                        </div>
                                        
                                        <h2>Редактировать тикет <a href="ticket.php?id=' . $ticket_j[0]['id'] . '" class="ahref">#' . $ticket_j[0]['id'] . '</a></h2>
                                        <input type="hidden" id="ticket_id" value="'.$ticket_j[0]['id'].'">
                                    </header>';

                        echo '
                                    <div id="data">';

                        echo '
                                        <form action="ticket_add_f.php">';

                        echo '
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                <span style="font-size:80%;  color: #555;">Описание</span><br>
                                                <textarea name="descr" id="descr" cols="60" rows="8">'.$ticket_j[0]['descr'].'</textarea>
                                            </div>
                                        </div>';

                        //if (($ticket['add_new'] == 1) || $god_mode) {
                        echo '
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                <span style="font-size: 80%;">Выполнить до</span><br>
                                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="text-align: inherit; color: rgb(30, 30, 30); font-size: 12px;" value="' . date($day . '.' . $month . '.' . $year) . '" onfocus="this.select();_Calendar.lcs(this)"  
                                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                
                                            </div>
                                        </div>';
                        //}

                        //if (($ticket['add_new'] == 1) || $god_mode) {
                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">
                                            <span style="font-size: 80%;">Для кого (если необходимо указать исполнителя)</span><br>
                
                                            <div class="side-by-side clearfix">
                                                <div>
                                    
                                                    <select name="postCategory[]" id="postCategory" data-placeholder="Введите для поиска" class="chosen-select" multiple="multiple" tabindex="4">
                                                        <option value="" style="min-width: 300px;"></option>';
                        if (!empty($workers_arr_temp)){
                            foreach ($workers_arr_temp as $w_id){
                                echo '
                                                        <option value="'.$w_id.'" style="min-width: 300px;" selected    >'.WriteSearchUser('spr_workers', $w_id, 'user_full', false).'</option>';
                            }
                        }
                        echo '
                                                    </select>
                                                </div>
                                    
                                            </div>
                                          
                                        </div>
                                    </div>';
                        //}

                        //Выбор категории сотрудников, кому будет видно заявку

                        //!!!Массив тех, кому видно заявку по умолчанию, потому надо будет вывести это в базу или в другой файл
                        $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

                        if (($ticket['add_new'] == 1) || $god_mode) {
                            echo '
                                            
                                            <div class="cellsBlock2">
                                                <div class="cellLeft">
                
                                                    <span style="font-size:80%;  color: #555;">Для каких категорий сотрудников</span>
                                                    <select multiple="multiple" name="workers_type[]" id="workers_type">';

                            if (!empty($permissions_j)) {
                                foreach ($permissions_j as $p_id => $permissions_j_data) {
                                    $permissionSelected = '';
                                    if (in_array($p_id, $worker_types_arr_temp)) {
                                        $permissionSelected = 'selected';
                                    }

                                    echo "<option value='" . $p_id . "' " . $permissionSelected . ">" . $permissions_j_data['name'] . "</option>";
                                }
                            }else{
                                foreach ($permissions_j as $p_id => $permissions_j_data) {
                                    $permissionSelected = '';
                                    if (in_array((int)$p_id, $permissionsWhoCanSee_arr)) {
                                        $permissionSelected = 'selected';
                                    }

                                    echo "<option value='" . $p_id . "' " . $permissionSelected . ">" . $permissions_j_data['name'] . "</option>";
                                }
                            }
                            echo '
                                                    </select>
                                                    <label id="workers_type_error" class="workers_type_error">
                                                </div>
                                            </div>';
                        }

                        //Выбор филиалов, где будет видно заявку
                        //Если в сессии указан филиал, выберем его как одного по умолчанию
                        if (isset($_SESSION['filial'])) {
                            $haveFilialinSession = true;
                            $FilialinSession = $_SESSION['filial'];
                        } else {
                            $haveFilialinSession = false;
                            $FilialinSession = 0;
                        }

                        //if (($ticket['add_new'] == 1) || $god_mode) {
                        echo '		
                                            <div class="cellsBlock2">
                                                <div class="cellLeft">
                                                    <span style="font-size:80%;  color: #555;">Для каких филиалов</span>
                                                    <select multiple="multiple" name="filial[]" id="filial">';

                        if (!empty($filials_j)) {
                            foreach ($filials_j as $f_id => $filials_j_data) {
                                $filialSelected = '';
                                if (in_array($filials_j_data['id'], $filials_arr_temp)) {
                                    $filialSelected = 'selected';
                                }

                                echo "<option value='" . $filials_j_data['id'] . "' " . $filialSelected . ">" . $filials_j_data['name'] . "</option>";
                            }
                        }else{
                            foreach ($filials_j as $f_id => $filials_j_data) {
                                //Если в сессии указан филиал, выберем его как одного по умолчанию
                                $filialSelected = '';
                                if ($haveFilialinSession){
                                    if ($FilialinSession == $filials_j_data['id']){
                                        $filialSelected = 'selected';
                                    }
                                }

                                echo "<option value='" . $filials_j_data['id'] . "' ".$filialSelected.">" . $filials_j_data['name'] . "</option>";
                            }
                        }

                        echo '
                                                    </select>
                                                    <label id="filial_error" class="error">
                                                </div>
                                            </div>';
                        //}

                        echo '
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                <span style="font-size: 80%;">Фото (если необходимо)</span><br>
                
                
                                              
                                            </div>
                                        </div>';


                        echo '
                                            <div id="errror"></div>
                                            <input type="button" class="b" value="Сохранить" onclick=Ajax_add_ticket(\'edit\')>
                                        </form>';

                        echo '
                                    <!--<div id="req"></div>-->
                                    </div>
                                </div>';


                        echo "
                                <script>  
                                    $('#filial').multiSelect();
                                    $('#workers_type').multiSelect();
                                    
                                    var config = {
                                      '.chosen-select'           : {},
                                      '.chosen-select-deselect'  : { allow_single_deselect: true },
                                      '.chosen-select-no-single' : { disable_search_threshold: 10 },
                                      '.chosen-select-no-results': { no_results_text: 'Oops, nothing found!' },
                                      '.chosen-select-rtl'       : { rtl: true },
                                      '.chosen-select-width'     : { width: '95%' }
                                    };
                                    
                                    //$('.chosen-select').chosen({disable_search_threshold: 10});
                                    
                                    for (var selector in config) {
                                        $(selector).chosen(config[selector]);
                                    }
                        
                                    
                                    //Реализуем выпадающий список через ajax
                                    $('.chosen-select').chosen();
                                                        
                                    $('.chosen-search-input').autocomplete({
                                        source: function() {
                                            var search_param = $('.chosen-search-input').val();
                                            var data = {
                                                search_param: search_param
                                            };
                                            if(search_param.length > 2) { //отправлять поисковой запрос к базе, если введено более трёх символов
                                    
                                                $.post('FastSearchW4Select.php', data, function onAjaxSuccess(data) {
                                                    //console.log(data);
                                                    
                                                    if((data.length != '0')) {
                                                        $('ul.chosen-results').find('li').each(function () {
                                                            $(this).remove();//очищаем выпадающий список перед новым поиском
                                                        });
                                                        $('select').find('option').each(function () {
                                                            //$(this).remove(); //очищаем поля перед новым поисков
                                                        });
                                                    }
                                                    
                                                    $('#postCategory').append(data);
                                                    $('#postCategory').trigger(\"chosen:updated\");
                                                    $('.chosen-search-input').val(search_param);
                                                });
                                            }
                                        }
                                    
                                    });
                
                                    //$('#postCategory').append('<option value=\"1\">Name</option>');
                                    //$('#postCategory').trigger(\"chosen:updated\");
                
                                </script>";
                    }else{
                        echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                    }
                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>