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
            $permissions_j = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions_j);

            //!!!Массив тех, кому видно заявку по умолчанию, потому надо будет вывести это в базу или в другой файл
            $permissionsWhoCanSee_arr = array(2, 3, 8, 9);

            //!!! для теста
            //unset($_SESSION['univer']);

            //Данные для подгрузки, если данные в сессии уже есть и не надо их снова добавлять
            $univer_exist_data = array();
            $univer_exist_data['file_data'] = '';

            //Если есть права, сразу создадим в сессии переменную для задания, если её нет
            //или берём оттуда данные, если она есть
            if (!isset($_SESSION['univer'])) {
                $_SESSION['univer'] = array();

                $task = array();
                $task['id'] = $task_id = 0;
                $task['file_data'] = $file_data = array();
                $task['theme'] = $task_theme = '';
                $task['descr'] = $task_descr = '';
                $task['workers'] = $task_workers = array();
                $task['workers_type'] = $task_workers_type = array();
                $task['filial'] = $task_filial = array();
                $task['status'] = $task_status = 0;

                $_SESSION['univer'] = $task;
            }else{
                $task_id = $_SESSION['univer']['id'];
                $file_data = $_SESSION['univer']['file_data'];
                $task_theme = $_SESSION['univer']['theme'];
                $task_descr = $_SESSION['univer']['descr'];
                $task_workers = $_SESSION['univer']['workers'];
                $task_workers_type = $_SESSION['univer']['workers_type'];
                $task_filial = $_SESSION['univer']['filial'];
                $task_status = $_SESSION['univer']['status'];

                if (!empty($univer_exist_data['file_data'])){
                    $univer_exist_data['file_data'] = '<div style="width: 60%; margin: 0 10px 10px; font-size: 85%; color: #333; border: 1px dashed rgb(255 0 0); padding: 7px;">Уже подгружен файл <b>'.$file_data['name'].'</b>. Загрузите новый файл и он будем заменён.</div>';
                }
            }

//            if (!isset($_SESSION['univer']['data'])) {
//                $_SESSION['univer']['items_data'] = array();
//            }
//            var_dump($_SESSION['univer']);
//            var_dump($task_id);

            echo '
				<div id="status">
					<header>
						<h2>Добавить задание</h2>
						Заполните поля и нажмите <b>Далее</b>
					</header>';
            echo '
					<div id="data">';

            //Форма загрузки видео и pdf
            echo '
                        <div id="upload_video_block"  class="visible_div" style="">
                            <div id="exist_file">'.$univer_exist_data['file_data'].'</div>
                            <form action="upload_video4Univer.php" id="form_video_upload" name="frmupload" method="post" enctype="multipart/form-data">
                                <div style="margin: 0 7px 7px; font-size: 80%; color: #999;">
                                    Если необходимо, выберите файл и нажмите <b>Загрузить</b> <br>
                                    * Допускаются только файлы форматов: mp4 и pdf
                                </div>
                                <div class="input__wrapper" style="margin: 10px;">
                                    <input type="file" id="upload_file" name="upload_file" class="input input__file" style="background-color: #deffde">
                                    <label for="upload_file" class="input__file-button">
                                        <span class="input__file-icon-wrapper">
                                            <img class="input__file-icon" src="img/add_file.svg" alt="Выбрать файл" width="25">
                                        </span>
                                        <span class="input__file-button-text">Выберите файл</span>
                                    </label>
                                </div>
                                <label id="upload_file_error" class="error"></label>
                                <div id="upload_file_button" style="display: none; margin: 5px 10px 0; ">
                                    <input type="submit" name="upload_file" class="b3" value="Загрузить" onclick="upload_video();">
                                    <div style="margin: 0 7px 7px; font-size: 80%; color: #666;">
                                        * Если не нажать загрузить, файл не добавится
                                    </div>
                                </div>
                            </form>
                        
                            <div class="progress_video_upload" id="progress_div">
                                <div class="bar_video_upload" id="bar_video_upload"></div>
                                <div class="percent_video_upload" id="percent_video_upload">0%</div>
                            </div>
                            <div id="output_video_result"></div>
                        </div>';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">
							    Заголовок<br>
							    <span style="font-size: 70%;">Не обязательно. Максимум 20 знаков</span>
							</div>
							<div class="cellRight">
								<input type="text" size="30" name="theme" id="theme" value="'.$task_theme.'" placeholder="" style="padding: 5px;">
							</div>
						</div>';

            echo '
						<div class="cellsBlock3">
							<div class="cellLeft">Вопрос/задание</div>
							<div class="cellRight">
								<textarea name="descr" id="descr" cols="60" rows="10">'.$task_descr.'</textarea>
								<label id="descr_error" class="error"></label>
							</div>
						</div>';

            echo '
                        <div class="cellsBlock3">
                            <div class="cellLeft">
                                Для кого <br><span style="font-size: 80%; color: #555;">если необходимо указать конкретных сотрудников</span><br>
                            </div>
                            <div class="cellRight">
                                <div class="side-by-side clearfix">
                                    <div>
                        
                                        <select name="postCategory[]" id="postCategory" data-placeholder="Введите для поиска" class="chosen-select" multiple="multiple" tabindex="4">
                                            <option value="" style="min-width: 300px;"></option>';
            if (!empty($task_workers)){
                foreach ($task_workers as $w_id){
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

            echo '
							
                        <div id="selPermisDiv" class="cellsBlock3">
                            <div class="cellLeft">
                                Для кого из сотрудников (по должностям)<br><span style="font-size:80%;  color: #555;">по умолчанию видно всем</span>
                            <!--<span style="font-size: 70%;">Если не выбрано, то для всех</span>-->
                            </div>
                            <div class="cellRight">
                                <span style="font-size:80%;  color: red;">Кому не видно</span>
                                <select multiple="multiple" name="workers_type[]" id="workers_type">';

            if (!empty($permissions_j)) {
                foreach ($permissions_j as $p_id => $permissions_j_data) {
                    $permissionSelected = '';
                    if (in_array($p_id, $task_workers_type)) {
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


//            if ($permissions != 0){
//                for ($i=0; $i<count($permissions); $i++){
//                    echo "<option value='".$permissions[$i]['id']."' selected>".$permissions[$i]['name']."</option>";
//                }
//            }
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


            //Выбор филиалов, где будет видно заявку
            //Если в сессии указан филиал, выберем его как одного по умолчанию
            if (isset($_SESSION['filial'])) {
                $haveFilialinSession = true;
                $FilialinSession = $_SESSION['filial'];
            } else {
                $haveFilialinSession = false;
                $FilialinSession = 0;
            }

            if (!empty($filials_j)) {
                foreach ($filials_j as $f_id => $filials_j_data) {
                    $filialSelected = '';
                    if (in_array($filials_j_data['id'], $task_filial)) {
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

//            if (!empty($filials_j)){
//                foreach ($filials_j as $f_id => $filial_data){
//                    echo "<option value='".$f_id."' selected>".$filial_data['name']."</option>";
//                }
//            }

            echo '
                                </select>
                                <label id="filial_error" class="error">
                            </div>
                        </div>';

            echo '
                        <div id="errror"></div>
                        <input type="button" class="b" value="Далее" onclick=Ajax_univer_add_fin(\'add\')>
                        <!--<a href="univer_add_fin.php" class="b">Далее</a>-->
                        ';



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
            echo '
				<script>  
//                    let elem1 = document.querySelector("#selFilialDiv");
//                    let elem2 = document . querySelector("#selPermisDiv");
//                    
//                    //При изменении поля с конкретными сотрудниками блокируем или деблокируем блоки с выбором должностей или филиалов
//				    $("#postCategory").bind("change keyup input click", function() {
//                        if ($("#postCategory").val() != null){
////                            console.log("1");
////                            console.log($("#postCategory").val());
//                            
//                            //selFilialDiv
////                            $("#workers_type").prop("disabled", true)
//                            
//                            elem1.classList.add("disableDiv")
//                            elem2.classList.add("disableDiv")
//                            
//                        }else{
////                            console.log("2");
////                            console.log($("#postCategory").val());
//
//                            elem1.classList.remove("disableDiv")
//                            elem2.classList.remove("disableDiv")
//                        }
//				    })
				
				    
				    let config = {
                      ".chosen-select"           : {},
                      ".chosen-select-deselect"  : { allow_single_deselect: true },
                      ".chosen-select-no-single" : { disable_search_threshold: 10 },
                      ".chosen-select-no-results": { no_results_text: "Oops, nothing found!" },
                      ".chosen-select-rtl"       : { rtl: true },
                      ".chosen-select-width"     : { width: "95%" }
                    };
				    
                    for (let selector in config) {
                        $(selector).chosen(config[selector]);
                    }
                    
                    //Реализуем выпадающий список через ajax
                    $(\'.chosen-select\').chosen();
                                        
                    $(\'.chosen-search-input\').autocomplete({
                        source: function() {
                            let search_param = $(\'.chosen-search-input\').val();
                            let req = {
                                search_param: search_param
                            };
                            if(search_param.length > 2) { //отправлять поисковой запрос к базе, если введено более трёх символов
                    
                                $.post("FastSearchW4Select.php", req, function onAjaxSuccess(res) {
                                    //console.log(res);
                                    
                                    if((res.length != "0")) {
                                        $("ul.chosen-results").find("li").each(function () {
                                            $(this).remove();//очищаем выпадающий список перед новым поиском
                                        });
                                        $("select").find("option").each(function () {
                                            //$(this).remove(); //очищаем поля перед новым поисков
                                        });
                                    }
                                    
                                    $("#postCategory").append(res);
                                    $("#postCategory").trigger("chosen:updated");
                                    $(".chosen-search-input").val(search_param);
                                });
                            }
                        }
                    
                    });
        
                    
//				    //Реализуем выпадающий список через ajax
//                    $(".chosen-select").chosen();
//                    
//                    //Предыдущий id поиска
//                    let prev_search_id = "";
//                                        
//                    $(".chosen-search-input").autocomplete({
//                        source: function() {
//                            let search_param = $(".chosen-search-input").val();
//                            
//                            let req = {
//                                search_param: search_param
//                            };
//                            if(search_param.length > 2) { //отправлять поисковой запрос к базе, если введено более двух символов
//                    
//                                $.post("FastSearchW4Select.php", req, function onAjaxSuccess(res) {
////                                    console.log(JSON.parse(res).data2);
////                                    console.log(res.length);
//
//                                    //Результат строка
//                                    let res_str = ""; 
//
//                                    let data = JSON.parse(res).data;
////                                    console.log(data);
////                                    console.log(typeof (data));
//
//                                    if (Object.keys(data).length > 0){
//                                        
//                                        //Пройти по всему объекту
//                                        for (key in data) {
//                                            console.log(data[key]);
//                                            
//                                            //Маркер, чтоб по условию двигаться дальше                                        
//                                            let move_next = true;
//                                        
//                                            //Уже есть в выбранных
//                                            if ($("#postCategory").val() != null){
////                                                console.log($("#postCategory").val().indexOf(data.id));
//                                                
//                                                if ($("#postCategory").val().indexOf(data[key].id) != -1){
//                                                    move_next = false;
//                                                }
//                                            }
//                                            
//                                            //Если не равно предыдущему поиску
//                                            if (prev_search_id == data[key].id){
//                                                move_next = false;
//                                            }
////                                            console.log(move_next);
//                                            
//                                            if (move_next){
//    
//                                                let type = "";
//                                                
//                                                //Должность
//                                                if (data[key].type != null){
//                                                    type = "[" + data[key].type + "]";
//                                                }
//                                                
//                                                res_str += "<option value=\'" + data[key].id + "\'>" + data[key].name + " " + type + "</option>";
//                                                console.log(res_str);
//
//                                                prev_search_id = data[key].id;
//                                            }   
//                                        }
//                                        
//                                           
//                                        //$("#postCategory").append(res_str);
//                                        $("#postCategory").html(res_str);
////                                        prev_search_id = data.id;
//                                        
//                                        $("#postCategory").trigger(\'chosen:updated\');
//                                        
//                                        $(".chosen-search-input").val(search_param);
//                                        
//                                        
//                                        $("ul.chosen-results").find("li").each(function () {
//                                            $(this).remove();//очищаем выпадающий список перед новым поиском
//                                        });
//                                        $("select").find("option").each(function () {
//                                            //$(this).remove(); //очищаем поля перед новым поисков
//                                        });
//                                            
//                                        
//                                    }
//                                        
//                                });
//                            }
//                        }
//                    
//                    });

                    //$("#postCategory").append("<option value=\'1\'>Name</option>");
				    //$("#postCategory").trigger(\'chosen:updated\');

				</script>';

            echo "				
				<!--КАК СТИЛИЗОВАТЬ ПОЛЕ ДЛЯ ОТПРАВКИ ФАЙЛА-->
                <script>
                    let inputs = document.querySelectorAll('.input__file');
                    
                    Array.prototype.forEach.call(inputs, function (input) {
                        let label = input.nextElementSibling,
                        labelVal = label.querySelector('.input__file-button-text').innerText;
                  
                        input.addEventListener('change', function (e) {
                            //console.log(e.target.files[0].name);
                            
                            //Прячем все ошибки
                            hideAllErrors ();
                            //Убираем прогресс-бар
                            $('#progress_div').hide();
                            //Убираем результат загрузки
                            $('#output_video_result').hide();

                            let fileName = $(this).val().split('/').pop().split('\\\').pop();
                            //console.log(fileName);
                            
                            //Тут сделано для multi выбора файлов. Мы это проигнорируем и будем работать как с одним
                            //не меняя кода
                            let countFiles = '';
                            if (this.files && this.files.length >= 1)
                                countFiles = this.files.length;
                  
                            //Если выбран файл
                            if (countFiles){
                                //label.querySelector('.input__file-button-text').innerText = 'Выбрано файлов: ' + countFiles;
                                label.querySelector('.input__file-button-text').innerText = fileName;
                                
                                //Данные о файле
                                //console.log(document.getElementById('upload_file').files[0]);
                                //Только расширение
                                //console.log(document.getElementById('upload_file').files[0].name.split('.')[1]);
                                
                                //Расширение
                                let ext = document.getElementById('upload_file').files[0].name.split('.')[1];
                                
                                if ((ext == 'mp4') || (ext == 'pdf')){
                                    //Показываем кнопку
                                    //toggleSomething('#upload_file_button');
                                    $('#upload_file_button').show();
                                }else{
                                    //Ошибка формата
                                    $('#upload_file_error').html('Ошибка формата.');
                                    $('#upload_file_error').show();
                                }
                                
                                //!!! тест, убрать потом
                                //toggleSomething('#upload_file_button');
                            }else{
                                label.querySelector('.input__file-button-text').innerText = labelVal;
                                
                                //Скрываем кнопку
                                toggleSomething('#upload_file_button');
                            }                                
                        });
                    });
                </script>
				
				";



        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }

    }else{
        header("location: enter.php");
    }

require_once 'footer.php';

?>