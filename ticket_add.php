<?php

//ticket_add.php
//Добавить тикет

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($ticket['add_own'] == 1) || ($ticket['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			//$offices = SelDataFromDB('spr_filials', '', '');
            $filials_j = getAllFilials(true, false);
            //Получили список прав
            $permissions = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions);

            $day = date("d");
            $month = date("m");
            $year = date("Y");

			echo '
				<div id="status">
					<header>
						<div class="nav">
							<a href="tickets.php" class="b">Все тикеты</a>
						</div>
						
						<h2>Новый тикет</h2>
					</header>';

			echo '
					<div id="data">';

			echo '
						<form action="ticket_add_f.php">';

            echo '
						<div class="cellsBlock2">
							<div class="cellLeft">
							    <span style="font-size:80%;  color: #555;">Описание</span><br>
								<textarea name="descr" id="descr" cols="60" rows="8"></textarea>
							</div>
						</div>';

            //if (($ticket['add_new'] == 1) || $god_mode) {
                echo '
                        <div class="cellsBlock2">
							<div class="cellLeft">
                                <span style="font-size: 80%;">Выполнить до</span><br>
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="text-align: inherit; color: rgb(30, 30, 30); font-size: 12px;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)"  
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
                                        <option value="" style="min-width: 300px;"></option>
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

                if ($permissions != 0) {
                    for ($i = 0; $i < count($permissions); $i++) {
                        //Если в сессии указан филиал, выберем его как одного по умолчанию
                        $permissionSelected = '';
                        if (in_array((int)$permissions[$i]['id'], $permissionsWhoCanSee_arr)) {
                            $permissionSelected = 'selected';
                        }

                        echo "<option value='" . $permissions[$i]['id'] . "' ".$permissionSelected.">" . $permissions[$i]['name'] . "</option>";
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
            if (isset($_SESSION['filial'])){
                $haveFilialinSession = true;
                $FilialinSession = $_SESSION['filial'];
            }else{
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
                    foreach ($filials_j as $filials_j_data) {
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
							<input type="button" class="b" value="Сохранить" onclick=Ajax_add_ticket(\'add\')>
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
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>