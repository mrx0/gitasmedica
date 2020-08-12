<?php

//fl_salaries2.php
//Оклады сотрудников вторая часть для конкретных сотрудников

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
        require_once 'header_tags.php';
        //var_dump($stom);

        //!!! Только для ВВ
        if (($_SESSION['id'] == 270) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';
            //$offices = SelDataFromDB('spr_filials', '', '');
            //var_dump ($offices);

            require 'variables.php';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 10, array(11,10));
            }else{
                $getWho = returnGetWho(10, 10, array(11,10));
            }
            //var_dump($getWho);

            $who = $getWho['who'];
            $whose = $getWho['whose'];
            $selected_stom = $getWho['selected_stom'];
            $selected_cosm = $getWho['selected_cosm'];
            $datatable = $getWho['datatable'];
            $kabsForDoctor = $getWho['kabsForDoctor'];
            $type = $getWho['type'];

            $stom_color = $getWho['stom_color'];
            $cosm_color = $getWho['cosm_color'];
            $somat_color = $getWho['somat_color'];
            $admin_color = $getWho['admin_color'];
            $assist_color = $getWho['assist_color'];
            $sanit_color = $getWho['sanit_color'];
            $ubor_color = $getWho['ubor_color'];
            $dvornik_color = $getWho['dvornik_color'];
            $other_color = $getWho['other_color'];
            $all_color = $getWho['all_color'];

            //Массив типов сотрудников, которые никуда не входят
            //$workers_target_arr = [1, 9, 12, 777];

            include_once 'ffun.php';

            $msql_cnnct = ConnectToDB2 ();

            $workers_target_str = implode(',', $workers_target_arr);

            //Сотрудники этого типа
            $arr = array();
            $workers_j = array();
            //$spr_salaries_j = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` IN ($workers_target_str) AND `status` <> '8' ORDER BY `full_name` ASC";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($workers_j, $arr);
                }
            }

            //Специализации
            //$specializations_j = workerSpecialization(0);
            //var_dump($specializations_j);

            /*//Оклады
            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `fl_salaries` WHERE `type` = '{$type}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $rez[$arr['id']] = $arr;
                }
                $spr_percents_j = $rez;
            }*/
            //var_dump($spr_percents_j);

            //переменная, чтоб вкл/откл редактирование
            echo '
                    <script>
                        var iCanManage = true;
                    </script>';

            echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <a href="fl_salaries_category.php" class="b">Оклады по должностям</a>
                            </div>
                            <h1>Оклады сотрудников</h1>
                        </header>';


            echo '
                        <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
    
                        </div>
                        <div id="data" style="margin: 8px 0 0;">';

            echo '		
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <!--<a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>-->
                                <!--<a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>-->
                                <a href="fl_salaries.php?who=10" class="b" style="">Специалисты</a>
                                <!--<a href="?who=4" class="b" style="'.$admin_color.'">Администраторы</a>-->
                                <!--<a href="?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>-->
                                <a href="fl_salaries.php?who=11" class="b" style="">Прочие</a>';
            //!!! Только для ВВ
            if (($_SESSION['id'] == 270) || ($god_mode)){
                echo '
                                <a href="fl_salaries2.php?who=999" class="b" style="background-color: #fff261;">Другие</a>';
            }
            echo '	
                            </li>';

            if (!empty($workers_j)){
                //var_dump($rezult2);
                echo '
                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
                                <li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF;">	
                                    <div class="cellFullName" style="text-align: center">
                                        Полное имя';
                echo $block_fast_filter;
                echo '
                                        
                                    </div>';

                    echo '
                                    <div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        Сумма
                                    </div>
                                    <!--<div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        С какого числа
                                    </div>-->
                                    ';


                echo '
                                </li>';

                foreach ($workers_j as $worker){
                    //var_dump($worker);

                    //Специализации
                    $specializations = workerSpecialization($worker['id']);

                    echo '
                                <li class="cellsBlock2 cellsBlockHover" style="font-weight: normal; font-size: 11px; margin-bottom: -1px;">
                                    <a href="user.php?id='.$worker['id'].'" class="cellFullName ahref 4filter" id="4filter" style="text-align: left;">
                                        '.$worker['full_name'];

                    //var_dump($specializations);

                    if (!empty($specializations)){
                        foreach ($specializations as $specialization_item){
                            echo ' <span class="tag" style="float: right; font-size: 90%;">'.$specialization_item['name'].'</span>';
                        }
                    }

                    echo '
                                    </a>';

                    //Оклад этого сотрудника, который действует сейчас
                    $arr = array();
                    $rez = array();

                    $salary = 0;

                    $query = "SELECT * FROM `fl_spr_salaries` WHERE `worker_id` = '{$worker['id']}' ORDER BY `date_from` DESC LIMIT 1";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            $salary = $arr['summ'];
                        }
                    }else{
                        $salary = '<span style="color: red;">Не указано</span>';
                    }

                    echo '
                                    <div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        <a href="fl_edit_salary.php?worker_id='.$worker['id'].'" class="ahref">'.$salary.'</a>
                                    </div>
                                    <!--<div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        С какого числа
                                    </div>-->
                                    ';


                    echo '
                                </li>';
                }

                echo '</ul>';
            }else{
                echo 'Ничего нет...';
            }


            echo '
                        </div>
                    </div>';

            echo '	
                <!-- Подложка только одна -->
                <div id="overlay"></div>';

            echo '
                 <div id="doc_title">Оклады сотрудников - Асмедика</div>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        header("location: enter.php");
    }

    require_once 'footer.php';

?>