<?php

//fl_tabels.php
//Важный отчёт


    //!!!Сортировка
    function cmp($a, $b)
    {
        return sort($massive, SORT_STRING);
    }


	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		//var_dump($_SESSION);

		if (($finances['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			include_once 'ffun.php';

			$workers_j = array();

			$offices_j = SelDataFromDB('spr_office', '', '');
            $permissions_j = SelDataFromDB('spr_permissions', '', '');

            $msql_cnnct = ConnectToDB ();

            if (!isset($_SESSION['fl_calcs_tabels'])){
                $_SESSION['fl_calcs_tabels'] = array();
            }

			if ($_POST){
			}else{

			    //Переменная для набора JS для tabs
                $tabs_rez_js = '';



				echo '
                    <div class="no_print"> 
					<header style="margin-bottom: 5px;">
						<h1>---</h1>
					</header>
					</div>';

				echo '
						<div id="data">';


                echo '
                    <div id="tabs_w">
                        <ul class="tabs">';

                //Для теста
                $permission['id'] = 5;
                $permission['name'] = $permissions_j[4]['name'];

                //вкладки по правам
                //foreach ($permissions_j as $permission){
                    if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){
                        echo '
                            <li>
                                <a href="#tabs-' . $permission['id'] . '">
                                    ' . $permission['name'] . '
                                </a>
                            </li>';
                    }
                //}
                echo '
                        </ul>';

                //проходим по каждой из должностей
                //foreach ($permissions_j as $permission){
                    //Обнуляем массив
                    $workers_j = array();


                    if (($permission['id'] != 1) && ($permission['id'] != 2) && ($permission['id'] != 3) && ($permission['id'] != 8) && ($permission['id'] != 9)){

                        //Выберем всех сотрудников с такой должностью
                        $query = "SELECT * FROM `spr_workers` WHERE `permissions`='{$permission['id']}' AND `fired` = '0'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0){
                            while ($arr = mysqli_fetch_assoc($res)){
                                $workers_j[$arr['name']] = $arr;
                            }
                        }

                        //Сортируем по имени
                        ksort($workers_j);

                        //содержимое по правам
                        echo '
                        <div id="tabs-'.$permission['id'].'" style="padding: 0;">';


                        //$tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'" ).tabs();';
                        $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );';
                        $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].' li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );';


                        echo '
                            <div id="tabs_w'.$permission['id'].'" style="font-size: 100%;">
                                <ul class="tabs" style="font-size: 125%; height: 500px; overflow-y: scroll;">';

                        //вкладки по сотрудникам
                        foreach ($workers_j as $worker){

                            echo '
                                    <li>
                                        <a href="#tabs-' . $permission['id'] . '_' . $worker['id'] . '" onclick="$(\'input:checked\').prop(\'checked\', false); $(\'input\').parent().parent().parent().css({\'background-color\': \'\'}); ">
                                            ' . $worker['name'] . '<div id="tabs_notes_' . $permission['id'] . '_' . $worker['id'].'" class="notes_count2" style="display: none; right: 0;"><i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i></div>
                                        </a>
                                    </li>';
                        }

                        echo '
                                </ul>';

                        //содержимое по сотрудникам
                        foreach ($workers_j as $worker){

                            echo '

                                <div id="tabs-' . $permission['id'] . '_' . $worker['id'] . '" style="width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); margin-left: 150px; font-size: 12px;">';

                            echo '<h2>' .$permission['name'].'</h2><h1>'.$worker['name'].'</h2>';

                            $tabs_rez_js .= '$( "#tabs_w'.$permission['id'].'_'.$worker['id'].'").tabs();';

                            echo '
                                    <div id="tabs_w'.$permission['id'].'_'.$worker['id'].'" style="font-size: 100%;">
                                        <ul class="tabs" style="font-size: 125%; float: left;">';

                            //закладки по офисам
                            foreach ($offices_j as $office){

                                if ($office['id'] != 11) {

                                    echo '
                                            <li class="tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" onclick="$(\'input:checked\').prop(\'checked\', false); $(\'input\').parent().parent().parent().css({\'background-color\': \'\'}); ">
                                                <a href="#tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '">
                                                    ' . $office['name'] . '<div id="tabs_notes_' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" class="notes_count2" style="display: none; right: 0px;"><i class="fa fa-exclamation-circle" aria-hidden="true" title=""></i></div>
                                                </a>
                                            </li>';
                                }
                            }

                            echo '
                                        </ul>';

                            //содержимое по офисам
                            foreach ($offices_j as $office){

                                if ($office['id'] != 11) {
                                    echo '
                                        <div id="tabs-' . $permission['id'] . '_' . $worker['id'] . '_' . $office['id'] . '" style="width: auto; float: none; border: 1px solid rgba(228, 228, 228, 0.72); font-size: 12px; margin-top: 65px;">';

                                    echo '
                                            <div class="tableTabels" id="'.$permission['id'] . '_' . $worker['id'] . '_' . $office['id'].'_tabels">
                                                <!--<div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>-->
                                            </div>';

                                    echo '
                                            <div class="tableDataNPaidCalcs" id="'.$permission['id'] . '_' . $worker['id'] . '_' . $office['id'].'">
                                                <div style="width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);"><img src="img/wait.gif" style="float:left;"><span style="float: right;  font-size: 90%;"> обработка...</span></div>
                                            </div>';


                                    echo '
                                        </div>';
                                }
                            }

                            echo '
                                    </div>';
                            echo '                
                                </div>';
                        }

                        echo '
                            </div>
                        </div>';

                    }
                //}

                echo '
                    </div>';










				/*echo '
                            <div class="no_print"> 
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9); display: inline-table;">
								
								<li style="margin-bottom: 10px;">
									Выберите условие
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Выберите период
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div style="margin-bottom: 10px;">
											C <input type="text" id="datastart" name="datastart" class="dateс" value="'.date("01.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
											 &bull;по <input type="text" id="dataend" name="dataend" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
										</div>
										<div style="vertical-align: middle; color: #333;">
											<input type="checkbox" name="all_time" value="1"> <span style="font-size:80%;">За всё время</span>
										</div>
									</div>
								</li>';*/

				/*echo '
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Филиал
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<div class="wrapper-demo">';
				//if (($finances['see_all'] == 1) || $god_mode){
                    echo '
											<select id="filial" class="wrapper-dropdown-2 b2" tabindex="2" name="filial">
												<ul class="dropdown">
													<li><option value="99" selected>Все</option></li>';
														if ($offices_j !=0){
															for ($i = 0; $i < count($offices_j); $i++){
																echo '<li><option value="'.$offices_j[$i]['id'].'" class="icon-twitter icon-large">'.$offices_j[$i]['name'].'</option></li>';
															}
														}
											
				    echo '
												</ul>
											</select>';
				/*}else{
                    $offices_j = SelDataFromDB('spr_office', $_SESSION['filial'], 'offices');
                    if ($offices_j != 0) {
                        echo $offices_j[0]['name'].'
                                <input type="hidden" id="filial" name="filial" value="'.$_SESSION['filial'].'">';
                    }
                }*/

                /*echo '
										</div>
									</div>
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник, к кому была запись<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">';
                if (($finances['see_all'] == 1) || $god_mode){
                    echo '
										<input type="text" size="30" name="searchdata4" id="search_client4" placeholder="Минимум три буквы для поиска" value="" class="who4" autocomplete="off">
										<ul id="search_result4" class="search_result4"></ul><br />';
                }else{
                    echo WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false).'
                                        <input type="hidden" id="search_client4" name="searchdata4" value="'.WriteSearchUser('spr_workers', $_SESSION['id'], 'user_full', false).'">';
                }
                echo '
									</div>
								</li>
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Сотрудник, который добавил запись<br>
										<span style="font-size:80%; color: #999; ">Если не выбрано, то для всех</span>
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="text" size="30" name="searchdata2" id="search_worker" placeholder="Минимум три буквы для поиска" value="" class="who2" autocomplete="off">
										<ul id="search_result2" class="search_result2"></ul><br />
									</div>
								</li>
								';


                echo '				
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Состояние
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="calculateAll" name="zapisAll" class="zapisType" value="1"> Все<br>
										<input type="checkbox" id="zapisArrive" name="zapisArrive" class="zapisType" value="1" checked> Пришли<br>
										<input type="checkbox" id="zapisNotArrive" name="zapisNotArrive" class="zapisType" value="1"> Не пришли<br>
										<input type="checkbox" id="zapisNull" name="zapisNull" class="zapisType" value="1"> Не закрытые<br>
										<input type="checkbox" id="zapisError" name="zapisError" class="zapisType" value="1"> Ошибочные<br>
									</div>
								</li>';

				echo '
							</ul>
							<ul style="border: 1px dotted #CCC; margin: 10px; padding: 10px 15px 20px; width: 420px; font-size: 95%; background-color: rgba(245, 245, 245, 0.9);display: inline-table;">
							    
								<li style="margin-bottom: 10px;">
									Дополнительные условия
								</li>
							    
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Тип
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input id="typeW" name="typeW" value="0" type="radio" checked>Все<br />
										<input id="typeW" name="typeW" value="5" type="radio">Стоматологи<br />
										<input id="typeW" name="typeW" value="6" type="radio">Косметологи<br />
										<input id="typeW" name="typeW" value="10" type="radio">Специалисты<br />
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Заполненность
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="fullAll" name="fullAll" class="fullType" value="1" checked> Все<br>
										<input type="checkbox" id="fullWOInvoice" name="fullWOInvoice" class="fullType" value="1" checked> Без нарядов<br>
										<input type="checkbox" id="fullWOTask" name="fullWOTask" class="fullType" value="1" checked> Без посещений<br>
										<input type="checkbox" id="fullOk" name="fullOk" class="fullType" value="1" checked> Заполненные полностью<br>
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Статус
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="statusAll" name="statusAll" class="statusType" value="1" checked> Все<br>
										<input type="checkbox" id="statusPervich" name="statusPervich" class="statusType" value="1" checked> Первичные<br>
										<input type="checkbox" id="statusInsure" name="statusInsure" class="statusType" value="1" checked> Страховые<br>
										<input type="checkbox" id="statusNight" name="statusNight" class="statusType" value="1" checked> Ночные<br>
										<input type="checkbox" id="statusAnother" name="statusAnother" class="statusType" value="1" checked> Все остальные<br>
									</div>
								</li>
								
								<li class="filterBlock">
									<div class="filtercellLeft" style="width: 120px; min-width: 120px;">
										Наряды
									</div>
									<div class="filtercellRight" style="width: 245px; min-width: 245px;">
										<input type="checkbox" id="invoiceAll" name="invoiceAll" class="invoiceType" value="1" checked> Все<br>
										<input type="checkbox" id="invoicePaid" name="invoicePaid" class="invoiceType" value="1" checked> Оплаченные<br>
										<input type="checkbox" id="invoiceNotPaid" name="invoiceNotPaid" class="invoiceType" value="1" checked> Не оплаченные<br>
										<input type="checkbox" id="invoiceInsure" name="invoiceInsure" class="invoiceType" value="1" checked> Страховые<br>
										<!--<input type="checkbox" id="statusAnother" name="statusAnother" class="invoiceType" value="1" checked> Все остальные<br>-->
									</div>
								</li>
								
							</ul>
							</div>
						</div>';
				
				echo '
                        <div class="no_print"> 
						<input type="button" class="b" value="Применить" onclick="Ajax_show_result_stat_calculate()">
						</div>';

                echo '
						<div id="status">
							<ul style="border: 1px dotted #CCC; margin: 10px; width: auto;" id="qresult">
								Результат отобразится здесь
							<ul>
						</div>';*/

				echo '

				<script type="text/javascript">
				
				
				$( "#tabs_w" ).tabs();
				//$( "#tabs_ww" ).tabs();
				//$( "#tabs_w2" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
				'.$tabs_rez_js.'
				
				
				$(document).ready(function() {
				    //console.log(123);
				    
				    //Рабочий пример клика на элементе после подгрузки его в DOM 
                    $("body").on("click", ".chkBoxCalcs", function(){
                        var checked_status = $(this).prop("checked");
                        //console.log(checked_status);
                        //console.log($(this).parent());
                            
                        if (checked_status){
                            $(this).parent().parent().parent().css({"background-color": "#83DB53"});
                        }else{
                            $(this).parent().parent().parent().css({"background-color": ""});
                        }
                    });   
				    
				    
				    
				    var ids = "0_0_0";
				    var ids_arr = {};
				    var permission = 0;
				    var worker = 0;
				    var office = 0;

				    
				    $(".tableDataNPaidCalcs").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "09"
                        };
                        
                        
                        //Необработанные расчеты
                        $.ajax({
                            url:"fl_get_calculates_f.php",
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                
                            data:certData,
                
                            cache: false,
                            beforeSend: function() {
                                //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
                            },
                            success:function(res){
                                //console.log(res);
                                //thisObj.html(res);
                                
                                if(res.result == \'success\'){
                                
                                    ids = thisObj.attr("id");
                                    ids_arr = ids.split("_");
                                    //console.log(ids_arr);
                                     
                                    permission = ids_arr[0];
                                    worker = ids_arr[1];
                                    office = ids_arr[2];
                                
                                    if (res.status == 1){
                                        thisObj.html(res.data);
                                        
                                        //Показываем оповещения на фио и филиале
                                        $("#tabs_notes_"+permission+"_"+worker).show();
                                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).show();
                                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);
                                        
                                        thisObj.parent().find(".summCalcsNPaid").html(res.summCalc);

                                    }
                                    
                                    if (res.status == 0){
                                        thisObj.html("Нет данных по необработанным расчетным листам");
                                    	
                                    	//Спрячем пустые вкладки, где нет данных
                                    	
                                    	console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));

                                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                                    }
                                }
                                
                                if(res.result == \'error\'){
                                    thisObj.html(res.data);
                                    
                                    
                                }
                            }
                        });
                    });
                    
                    
				    $(".tableTabels").each(function() {
                        //console.log($(this).attr("id"));
                        
                        var thisObj = $(this);

                        ids = $(this).attr("id");
                        ids_arr = ids.split("_");
                        //console.log(ids_arr);
                         
                        permission = ids_arr[0];
                        worker = ids_arr[1];
                        office = ids_arr[2];
                        
                        var certData = {
                            permission: permission,
                            worker: worker,
                            office: office,
                            month: "09"
                        };
                        
                         
                        //Табели
                        $.ajax({
                            url:"fl_get_tabels_f.php",
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                
                            data:certData,
                
                            cache: false,
                            beforeSend: function() {
                                //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
                            },
                            success:function(res){
                                //console.log(res);
                                //thisObj.html(res);
                                
                                if(res.result == \'success\'){
                                
                                    ids = thisObj.attr("id");
                                    ids_arr = ids.split("_");
                                    //console.log(ids_arr);
                                     
                                    permission = ids_arr[0];
                                    worker = ids_arr[1];
                                    office = ids_arr[2];
                                
                                    if (res.status == 1){
                                        thisObj.html(res.data);
                                        
                                        //Показываем оповещения на фио и филиале
                                        $("#tabs_notes_"+permission+"_"+worker).show();
                                        $("#tabs_notes_"+permission+"_"+worker+"_"+office).show();
                                        //console.log("#tabs_notes_"+permission+"_"+worker+"_"+office);
                                        
                                        //
                                        thisObj.parent().find(".summTabelNPaid").html(res.summCalc);

                                    }
                                    
                                    if (res.status == 0){
                                        thisObj.html("Нет данных по табелям");
                                    	
                                    	
                                    	//!!! доделать тут чтоб правильно прятались или нет вкладки
                                    	//Спрячем пустые вкладки, где нет данных
                                    	
                                    	console.log($(".tabs-"+permission+"_"+worker+"_"+office).css("display"));
                                    	
                                        //$(".tabs-"+permission+"_"+worker+"_"+office).hide();
                                    }
                                }
                                
                                if(res.result == "error"){
                                    thisObj.html(res.data);
                                    
                                    
                                }
                            }
                        });
                    });
				});
				
				
				
                //Проверка и установка checkbox
                /*$(".zapisType").click(function() {
                    
                    var checked_status = $(this).is(":checked");
                    var thisId = $(this).attr("id");
                    var pin_status = false;
                    var allCheckStatus = false;
                    
                    if (thisId == "zapisAll"){
                        if (checked_status){
                            pin_status = true;
                        }else{
                            pin_status = false;
                        }
                        $(".zapisType").each(function() {
                            $(this).prop("checked", pin_status);
                        });
                    }else{
                        if (!checked_status){
                            $("#zapisAll").prop("checked", false);
                        }else{
                            allCheckStatus = true; 
                            $(".zapisType").each(function() {
                                if ($(this).attr("id") != "zapisAll"){
                                    if (!$(this).is(":checked")){
                                        allCheckStatus = false; 
                                    }
                                }
                            });
                            if (allCheckStatus){
                                $("#zapisAll").prop("checked", true);
                            }
                        }
                    }
                });
                
                $(".fullType").click(function() {
                    
                    var checked_status = $(this).is(":checked");
                    var thisId = $(this).attr("id");
                    var pin_status = false;
                    var allCheckStatus = false;
                    
                    if (thisId == "fullAll"){
                        if (checked_status){
                            pin_status = true;
                        }else{
                            pin_status = false;
                        }
                        $(".fullType").each(function() {
                            $(this).prop("checked", pin_status);
                        });
                    }else{
                        if (!checked_status){
                            $("#fullAll").prop("checked", false);
                        }else{
                            allCheckStatus = true; 
                            $(".fullType").each(function() {
                                if ($(this).attr("id") != "fullAll"){
                                    if (!$(this).is(":checked")){
                                        allCheckStatus = false; 
                                    }
                                }
                            });
                            if (allCheckStatus){
                                $("#fullAll").prop("checked", true);
                            }
                        }
                    }
                });
                
                $(".statusType").click(function() {
                    
                    var checked_status = $(this).is(":checked");
                    var thisId = $(this).attr("id");
                    var pin_status = false;
                    var allCheckStatus = false;
                    
                    if (thisId == "statusAll"){
                        if (checked_status){
                            pin_status = true;
                        }else{
                            pin_status = false;
                        }
                        $(".statusType").each(function() {
                            $(this).prop("checked", pin_status);
                        });
                    }else{
                        if (!checked_status){
                            $("#statusAll").prop("checked", false);
                        }else{
                            allCheckStatus = true; 
                            $(".statusType").each(function() {
                                if ($(this).attr("id") != "statusAll"){
                                    if (!$(this).is(":checked")){
                                        allCheckStatus = false; 
                                    }
                                }
                            });
                            if (allCheckStatus){
                                $("#statusAll").prop("checked", true);
                            }
                        }
                    }
                });*/
                
                //Наряды
                /*$(".invoiceType").click(function() {
                    
                    var checked_status = $(this).is(":checked");
                    var thisId = $(this).attr("id");
                    var pin_status = false;
                    var allCheckStatus = false;
                    
                    if (thisId == "invoiceAll"){
                        if (checked_status){
                            pin_status = true;
                        }else{
                            pin_status = false;
                        }
                        $(".invoiceType").each(function() {
                            $(this).prop("checked", pin_status);
                        });
                    }else{
                        if (!checked_status){
                            $("#invoiceAll").prop("checked", false);
                        }else{
                            allCheckStatus = true; 
                            $(".invoiceType").each(function() {
                                if ($(this).attr("id") != "invoiceAll"){
                                    if (!$(this).is(":checked")){
                                        allCheckStatus = false; 
                                    }
                                }
                            });
                            if (allCheckStatus){
                                $("#invoiceAll").prop("checked", true);
                            }
                        }
                    }
                });
                    
                    
                var all_time = 0;
                
                $("input[name=all_time]").change(function() {
                    all_time = $("input[name=all_time]:checked").val();
                    
                    if (all_time === undefined){
                        all_time = 0;
                    }
                    
                    if (all_time == 1){
                        document.getElementById("datastart").disabled = true;
                        document.getElementById("dataend").disabled = true;
                    }
                    if (all_time == 0){
                        document.getElementById("datastart").disabled = false;
                        document.getElementById("dataend").disabled = false;
                    }
                });*/
				</script>';
			}
			//mysql_close();
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>