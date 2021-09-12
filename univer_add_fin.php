<?php

//univer_add_fin.php
//Проверить и Добавить задание в Univer

    require_once 'header.php';

    if ($enter_ok){
        require_once 'header_tags.php';

        if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode) {
//            echo '
//            <script type="text/javascript" src="js/webtricks_demo_js_jquery.form.js"></script>
//            ';

            include_once 'DBWork.php';
            include_once 'functions.php';

            //$offices = SelDataFromDB('spr_filials', '', '');
            $filials_j = getAllFilials(false, false, true);
//            var_dump($filials_j);

            //Получили список прав
            $permissions = SelDataFromDB('spr_permissions', '', '');
//            var_dump($permissions);

            //!!! для теста
            //unset($_SESSION['univer']);

            //Если есть права, сразу создадим в сессии переменную для задания, если её нет
            //или берём оттуда данные, если она есть
            if (isset($_SESSION['univer'])) {
                $task_id = $_SESSION['univer']['id'];
                $file_data = $_SESSION['univer']['file_data'];
                $task_theme = $_SESSION['univer']['theme'];
                $task_descr = $_SESSION['univer']['descr'];
                $task_workers = $_SESSION['univer']['workers'];
                $task_workers_type = $_SESSION['univer']['workers_type'];
                $task_filial = $_SESSION['univer']['filial'];
                $task_status = $_SESSION['univer']['status'];

//                var_dump($_SESSION['univer']);
//                var_dump($task_id);
//                var_dump($file_data);

                echo '
                    <div id="status">
                        <header>
                            <h2>Проверка задания перед добавлением</h2>
                        </header>';
                echo '
                        <div id="data">';

                //Видео или pdf
                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">';
                if (!empty($file_data)) {
                    echo '
                                    <div>К заданию приложен файл <b>' . $file_data['name'] . '</b></div>';
                    if ($file_data['ext'] == 'mp4') {
                        echo '
                                    <div id="video_block"  class="" style="">
                                        <video tabindex="-1" class="" controls controlslist="nodownload" id="my-video" width="320" height="240" poster="" preload="metadata">
                                           <!--<source src="video/nubex.ogv" type=\'video/ogg; codecs="theora, vorbis"\'>-->
                                           <source src="univerFiles/' . $file_data['path_name'] . '" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>
                                           <!--<source src="video/Сергей Мельников Кураторы лечения в стоматологии-master.m3u8" type="application/x-mpegURL">-->
                                           <!--<source src="video/nubex.webm" type=\'video/webm; codecs="vp8, vorbis"\'>-->
                                           Ваш браузер не поддерживает тег video.
                                        </video>
                                    </div>';
                    } else {
                        echo '
                                    <div style="margin-top: 20px;">
                                        <embed src="univerFiles/' . $file_data['path_name'] . '" width="600" height="500" alt="pdf">
                                    </div>
                    ';
                    }
                }else{
                    echo '<b>Без файла</b>';
                }
                    echo '
                                </div>
                            </div>';

                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Заголовок: <b>'.$task_theme.'</b>
                                </div>
                            </div>';

                echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Вопрос/задание: <i>'.$task_descr.'</i>
                                </div>
                            </div>';

//                var_dump(!empty($task_workers));
//                var_dump($task_workers);

                //Если есть конретные сотрудники, они в приоритете над остальным (20210912 отключено)
//                if (!empty($task_workers)) {
                    echo '
                            <div class="cellsBlock3">
                                <div class="cellLeft">
                                    Для кого <br><span style="font-size: 80%; color: #555;">конкретные сотрудники</span><br>
                                </div>
                                <div class="cellRight">';
                    if (!empty($task_workers)) {
                        foreach ($task_workers as $w_id) {
                            echo '<span style="display: block; font-style: italic; font-size: 90%;">' . WriteSearchUser('spr_workers', $w_id, 'user_full', true) . '</span>';
                        }
                    }else{
                        echo '<b>Никто не указан</b>';
                    }
                    echo '
                                </div>
                            </div>';


                //Если нет конретных сотрудников (20210912 отключено)
                //}else{
//                    var_dump($permissions);
//                    var_dump($task_workers_type);

                    //Сотрудники по должностям
                    echo '
                                    
                                <div id="selPermisDiv" class="cellsBlock3">
                                    <div class="cellLeft">
                                        Для кого из сотрудников (по должностям)
                                    </div>
                                    <div class="cellRight">';
                if (!empty($task_workers_type)) {
                    foreach ($task_workers_type as $p_id) {
                        echo '<span style="display: block; font-style: italic; font-size: 90%;">' . $permissions[$p_id]['name'] . '</span>';
                    }
                }else{
                    echo '<b>Никто не указан</b>';
                }
                    echo '
                                    </div>
                                </div>';


//                    var_dump($filials_j);
//                    var_dump($task_filial);

                    //По филиалам
                    echo '		
                                <div id="selFilialDiv" class="cellsBlock3">
                                    <div class="cellLeft">
                                        Для какого филиала
                                    </div>
                                    <div class="cellRight">';
                    if (!empty($task_workers_type)) {
                        foreach ($task_filial as $f_id) {
                            echo '<span style="display: block; font-style: italic; font-size: 90%;">' . $filials_j[$f_id]['name'] . '</span>';
                        }
                    }else{
                            echo '<b>Ничего не указано</b>';
                    }
                    echo '	
                                    </div>
                                </div>';
                //}

                echo '
                            <div id="errror"></div>
                            <!--<input type="button" class="b" value="Добавить" onclick=Ajax_add_test(\'add\')>-->
                            <input type="button" class="b" style="background: #a4f90acf;" value="Добавить и активировать" onclick="Ajax_univer_task_add(\'add\', 1)">
                            <!--<input type="button" class="b" value="Только добавить" onclick="Ajax_univer_task_add(\'add\', 0)">--><br><br>
                            <a href="univer_add.php" class="b">Вернуться к заполнению</a>';


                echo '
                        </div>
                    </div>';

                echo '	
                <!-- Подложка только одна / Вариант подложки для затемнения области вокруг элемента блока со своим стилем в CSS -->
                <div id="layer"></div>';

                echo '
                 <div id="doc_title">UNIVER - проверить и актоивировать задание</div>';

            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }

    }else{
        header("location: enter.php");
    }

require_once 'footer.php';

?>