<?php

//blocks_dom.php

    $block_fast_filter = '
                                <p style="margin: 1px 0; padding: 1px; text-align: center;">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                    <input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="">
                                </p>';


    $block_fast_search_client = '
                                <div class="cellRight" style="box-shadow: -1px 1px 8px #333;">
                                    <span style="font-size: 70%;">Быстрый поиск пациента</span><br />
                                    <input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите для поиска" value="" class="who_fc"  autocomplete="off">
                                    <!--<ul id="search_result_fc" class="search_result_fc"></ul><br />-->
                                    <div id="search_result_fc2"></div>
                                </div>';


    $block_fast_search_certificate = '
                                <div class="cellRight" style="box-shadow: -1px 1px 8px #333;">
                                    <span style="font-size: 70%;">Быстрый поиск сертификата</span><br />
                                    <input type="text" size="50" name="searchdata_fcert" id="search_cert" placeholder="Введите для поиска" value="" class="who_fcert"  autocomplete="off">
                                    <!--<ul id="search_result_fc" class="search_result_fc"></ul><br />-->
                                    <div id="search_result_fcert2"></div>
                                </div>';


    $block_show_settings_add_temp_zapis = '
                                <div id="ShowSettingsAddTempZapis" style="position: absolute; left: 10px; top: 0; background: rgb(186, 195, 192) none repeat scroll 0% 0%; display:none; z-index:105; padding:10px;">
                                    <a class="close" href="#" onclick="HideSettingsAddTempZapis()" style="display:block; position:absolute; top:-10px; right:-10px; width:24px; height:24px; text-indent:-9999px; outline:none;background:url(img/close.png) no-repeat;">
                                        Close
                                    </a>
                                    
                                    <div id="SettingsAddTempZapis">
            
                                        <div style="display:inline-block;">
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Число</div>
                                                <div class="cellRight">
											        <input type="text" id="month_date" name="month_date" class="dateс" style="text-align: inherit; font-size: 12px; border:none; color: rgb(30, 30, 30); font-weight: bold;" value="" onfocus="this.select();_Calendar.lcs(this)" 
												    onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
											        <!--<span style="font-size: 100%; cursor: pointer" onclick="PriemTimeCalcChangeDate();"><i class="fa fa-check-square" style=" color: green;"></i> Изменить</span>-->
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Смена</div>
                                                <div class="cellRight" id="month_date_smena">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Филиал</div>
                                                <div class="cellRight" id="filial_name">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Кабинет №</div>
                                                <div class="cellRight" id="kab">
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:400px;">
                                                <div class="cellLeft">Врач</div>
                                                <div class="cellRight" id="worker_name">
                                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО врача" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                                    <ul id="search_result2" class="search_result2"></ul><br />
                                                </div>
                                            </div>
            
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Пациент</div>
                                                <div class="cellRight">
                                                    <div>
                                                        <input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off" style="width: 90%;">
                                                        <ul id="search_result" class="search_result"></ul><br>
                                                    </div>
                                                    <div id="add_client_fio" style="cursor: pointer; border: 1px dotted #555;">
                                                        <i class="fa fa-plus-square" style="color: green; font-size: 120%;"></i> Добавить нового
                                                    </div>
									            </div>
								            </div>
                                            <!--<div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Телефон</div>
                                                <div class="cellRight" style="">
                                                    <input type="text" size="30" name="contacts" id="contacts" placeholder="Введите телефон" value="" autocomplete="off">
                                                </div>
                                            </div>-->
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Описание</div>
                                                <div class="cellRight" style="">
                                                    <textarea name="description" id="description" style="width:90%; overflow:auto; height: 100px;"></textarea>
                                                </div>
                                            </div>		
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Первичный</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="pervich" id="pervich" value="1"> да
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Страховой</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="insured" id="insured" value="1"> да
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-size:80%; width:400px;">
                                                <div class="cellLeft" style="font-weight: bold;">Ночной</div>
                                                <div class="cellRight">
                                                    <input type="checkbox" name="noch" id="noch" value="1"> да
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div style="display:inline-block; vertical-align: top; width: 360px; border: 1px solid #C1C1C1;">
                                            <div id="ShowTimeSettingsHere">
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Время начала</div>
                                                <div class="cellRight">
                                                    <!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
                                                    
                                                    <input type="number" size="2" name="work_time_h" id="work_time_h" min="0" max="23" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
                                                    <input type="number" size="2" name="work_time_m" id="work_time_m" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Длительность</div>
                                                <div class="cellRight">
                                                    <!--<div id="work_time_h" style="display:inline-block;"></div>:<div id="work_time_m" style="display:inline-block;"></div>-->
            
                                                    <input type="number" size="2" name="change_hours" id="change_hours" min="0" max="11" value="0" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> часов
                                                    <input type="number" size="2" name="change_minutes" id="change_minutes" min="0" max="59" step="5" value="30" class="mod" onchange="PriemTimeCalc();" onkeypress = "PriemTimeCalc();"> минут
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellLeft">Время окончания</div>
                                                <div class="cellRight">
                                                    <div id="work_time_h_end" style="display:inline-block;"></div>:<div id="work_time_m_end" style="display:inline-block;"></div>
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellRight">
                                                    <div id="exist_zapis" style="display:inline-block;"></div>
                                                </div>
                                            </div>
                                            <div class="cellsBlock2" style="font-weight: bold; font-size:80%; width:350px;">
                                                <div class="cellRight">
                                                    <div id="errror"></div>
                                                </div>
                                            </div>
                                        </div>
						            </div>';

    $block_modal_ticket_done = ' 
                                    <div id="modal_ticket_done" class="modal_div">
                                        <span class="modal_close" style="font-size: 80%;">отмена <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;"></i></span>
                                        <div>
                                            <div style="text-align: center; margin: 10px 0 8px;">
                                                Вы собираетесь завершить задачу
                                            </div>
                                            <div style="text-align: center;">
                                                <div style="padding: 10px 20px 10px;">
                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px;">Если необходимо, можете оставить комментарий</div>
                                                    <div>
                                                        <textarea name="ticket_last_comment" id="ticket_last_comment" cols="50" rows="5" style="vertical-align:top; text-align:left;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="padding: 5px 20px 0px;">
                                                    <div id="workers_exist_warn" style="font-size:80%;  color: red; margin-bottom: 7px;"></div>
                                                </div>
                                            </div>
                                            <div style="text-align: right; margin-right: 10px;">
                                                <input type="button" class="b" value="Завершить" onclick="Ajax_ticket_done($(\'#ticket_id\').val(), $(\'#workers_exist\').val());">
                                                <!--<input type="button" class="b" value="Отмена" onclick="">-->
                                            </div>
                                        </div>
                                    </div>';

    $block_modal_ticket_delete = ' 
                                    <div id="modal_ticket_delete" class="modal_div">
                                        <span class="modal_close" style="font-size: 80%;">отмена <i class="fa fa-times" aria-hidden="true" style="color: red; font-size: 110%;"></i></span>
                                        <div>
                                            <div style="text-align: center; margin: 10px 0 8px;">
                                                Вы собираетесь удалить задачу
                                            </div>
                                            <div style="text-align: center;">
                                                <div style="padding: 10px 20px 10px;">
                                                    <div style="font-size:80%;  color: #555; margin-bottom: 10px;">Если необходимо, можете оставить комментарий</div>
                                                    <div>
                                                        <textarea name="ticket_last_comment" id="ticket_last_comment" cols="50" rows="5" style="vertical-align:top; text-align:left;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="padding: 5px 20px 0px;">
                                                    <div id="workers_exist_warn" style="font-size:80%;  color: red; margin-bottom: 7px;"></div>
                                                </div>
                                            </div>
                                            <div style="text-align: right; margin-right: 10px;">
                                                <input type="button" class="b" value="Удалить" onclick="Ajax_delete_ticket($(\'#ticket_id\').val());">
                                                <!--<input type="button" class="b" value="Отмена" onclick="">-->
                                            </div>
                                        </div>
                                    </div>';



?>