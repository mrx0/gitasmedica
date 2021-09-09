<?php

//univer_add.php
//Добавить задание в Univer

    require_once 'header.php';

    if ($enter_ok){
        require_once 'header_tags.php';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode) {
            echo '
            <script type="text/javascript" src="js/webtricks_demo_js_jquery.form.js"></script>
            ';

            include_once 'DBWork.php';
            include_once 'functions.php';

            //$offices = SelDataFromDB('spr_filials', '', '');
            $filials_j = getAllFilials(false, false, false);
            //Получили список прав
            $permissions = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions);

            echo '
				<div id="status">
					<header>
						<h2>Добавить задание</h2>
						Заполните поля и нажмите <b>Добавить</b>
					</header>';
            echo '
					<div id="data">';

            //ФОрма загрузки видео
            echo '
                        <div id="upload_video_block"  class="visible_div" style="">
                            <form action="upload_video4Univer.php" id="form_video_upload" name="frmupload" method="post" enctype="multipart/form-data">
                                <input type="file" id="upload_file" name="upload_file">
                                <input type="submit" name="submit_video" value="Submit Comment" onclick="upload_video();">
                            </form>
                        
                            <div class="progress_video_upload" id="progress_div">
                                <div class="bar_video_upload" id="bar_video_upload"></div>
                                <div class="percent_video_upload" id="percent_video_upload">0%</div>
                            </div>
                        </div>';

            echo '
                        <div id="output_video"></div>
            ';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">
							    Заголовок<br>
							    <span style="font-size: 70%;">Не обязательно. Максимум 20 знаков</span>
							</div>
							<div class="cellRight">
								<input type="text" size="30" name="theme" id="theme" value="" placeholder="" style="padding: 5px;">
							</div>
						</div>';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Вопрос/задание</div>
							<div class="cellRight">
								<textarea name="descr" id="descr" cols="60" rows="10"></textarea>
								<label id="descr_error" class="error"></label>
							</div>
						</div>';

            echo '
                        <div class="cellsBlock3">
                            <div class="cellLeft">
                                Для кого <br><span style="font-size: 80%; color: #555;">если необходимо указать определенных сотрудников</span><br>
                            </div>
                            <div class="cellRight">
                                <div class="side-by-side clearfix">
                                    <div>
                        
                                        <select name="postCategory[]" id="postCategory" data-placeholder="Введите для поиска" class="chosen-select" multiple="multiple" tabindex="4">
                                            <option value="" style="min-width: 300px;"></option>
                                        </select>
                                    </div>
                        
                                </div>
                              
                            </div>
                        </div>';

            echo '
							
                        <div id="selPermisDiv" class="cellsBlock3">
                            <div class="cellLeft">
                                Для кого из сотрудников (по должностям)<br><span style="font-size:80%;  color: #555;">по умолчанию видно всем</span>
                            <!--<span style="font-size: 70%;">Если не выбрано, то для всех</span>-->
                            </div>
                            <div class="cellRight">
                                <span style="font-size:80%;  color: red;">Кому не видно</span>
                                <select multiple="multiple" name="workers_type[]" id="workers_type">';
            if ($permissions != 0){
                for ($i=0; $i<count($permissions); $i++){
                    echo "<option value='".$permissions[$i]['id']."' selected>".$permissions[$i]['name']."</option>";
                }
            }
            echo '
                                </select>
                                <label id="workers_type" class="workers_type">
                            </div>
                        </div>';



            echo '		
                        <div id="selFilialDiv" class="cellsBlock3">
                            <div class="cellLeft">
                                Для какого филиала<br><span style="font-size:80%;  color: #555;">по умолчанию видно всем</span>
                                <!--<span style="font-size: 70%;">Если не выбрано, то для всех</span>-->
                            </div>
                            <div class="cellRight">
                                <span style="font-size:80%;  color: red;">Кому не видно</span>
                                <select multiple="multiple" name="filial[]" id="filial">';
            if (!empty($filials_j)){
                foreach ($filials_j as $f_id => $filial_data){
                    echo "<option value='".$f_id."' selected>".$filial_data['name']."</option>";
                }
            }
            echo '
                                </select>
                                <label id="filial_error" class="error">
                            </div>
                        </div>';

            echo '
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick=Ajax_add_test(\'add\')>';



            echo '
					</div>
				</div>';

            echo '	
			<!-- Подложка только одна / Вариант подложки для затемнения области вокруг элемента блока со своим стилем в CSS -->
			<div id="layer"></div>';

            echo '
             <div id="doc_title">UNIVER - добавить задание</div>';

            //Скрипт для красивого мульти выбора несколько элементов select
            echo '
				<script>  
				    $("#filial").multiSelect()
				    $("#workers_type").multiSelect()
				</script>';

            //Скрипт для красивого выбора нескольких сотрудников поиск по ФИО
            echo "
				<script>  
                    let elem1 = document.querySelector(\"#selFilialDiv\");
                    let elem2 = document . querySelector(\"#selPermisDiv\");
                    
                    //При изменении поля с конкретными сотрудниками блокируем или деблокируем блоки с выбором должностей или филиалов
				    $('#postCategory').bind('change keyup input click', function() {
                        if ($('#postCategory').val() != null){
//                            console.log('1');
//                            console.log($('#postCategory').val());
                            
                            //selFilialDiv
//                            $('#workers_type').prop('disabled', true)
                            
                            elem1.classList.add('disableDiv')
                            elem2.classList.add('disableDiv')
                            
                        }else{
//                            console.log('2');
//                            console.log($('#postCategory').val());

                            elem1.classList.remove('disableDiv')
                            elem2.classList.remove('disableDiv')
                        }
				    })
				
				    
				    let config = {
                      '.chosen-select'           : {},
                      '.chosen-select-deselect'  : { allow_single_deselect: true },
                      '.chosen-select-no-single' : { disable_search_threshold: 10 },
                      '.chosen-select-no-results': { no_results_text: 'Oops, nothing found!' },
                      '.chosen-select-rtl'       : { rtl: true },
                      '.chosen-select-width'     : { width: '95%' }
                    };
				    
                    for (let selector in config) {
                        $(selector).chosen(config[selector]);
                    }
        
                    
				    //Реализуем выпадающий список через ajax
                    $('.chosen-select').chosen();
                    
                    //Предыдущий id поиска
                    let prev_search_id = '';
                                        
                    $('.chosen-search-input').autocomplete({
                        source: function() {
                            let search_param = $('.chosen-search-input').val();
                            
                            let req = {
                                search_param: search_param
                            };
                            if(search_param.length > 2) { //отправлять поисковой запрос к базе, если введено более двух символов
                    
                                $.post('FastSearchW4Select.php', req, function onAjaxSuccess(res) {
//                                    console.log(JSON.parse(res));
//                                    console.log(res.length);

                                    //Результат строка
                                    let res_str = ''; 

                                    let data = JSON.parse(res).data;
//                                    console.log(data);
//                                    console.log(typeof (data));

                                    if (Object.keys(data).length > 0){
                                        //Маркер, чтоб по условию двигаться дальше                                        
                                        let move_next = true;
                                    
                                        //Уже выбранные
                                        if ($('#postCategory').val() != null){
//                                            console.log($('#postCategory').val().indexOf(data.id));
                                            
                                            if ($('#postCategory').val().indexOf(data.id) != -1){
                                                move_next = false;
                                            }
                                        }
                                        
                                        if (prev_search_id == data.id){
                                            move_next = false;
                                        }
                                        
                                        if (move_next){

                                            let type = '';
                                            
                                            if (data.type != null){
                                                type = '[' + data.type + ']';
                                            }
                                            
                                            res_str = '<option value=\"' + data.id + '\">' + data.name + ' ' + type + '</option>';
//                                            console.log(res_str);
                                            
//                                            if((res_str.length > 0)) {
//                                                $('ul.chosen-results').find('li').each(function () {
//                                                    $(this).remove();//очищаем выпадающий список перед новым поиском
//                                                });
//                                                $('select').find('option').each(function () {
//                                                    //$(this).remove(); //очищаем поля перед новым поисков
//                                                });
//                                            }
//                                            
                                            $('#postCategory').append(res_str);
                                            prev_search_id = data.id;
                                            
                                            $('#postCategory').trigger(\"chosen:updated\");
                                            
                                            $('.chosen-search-input').val(search_param);
                                            
                                            
                                            $('ul.chosen-results').find('li').each(function () {
                                                $(this).remove();//очищаем выпадающий список перед новым поиском
                                            });
                                            $('select').find('option').each(function () {
                                                //$(this).remove(); //очищаем поля перед новым поисков
                                            });
                                            
                                        }
                                    }
                                        
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