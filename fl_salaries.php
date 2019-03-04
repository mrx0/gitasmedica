<?php

//fl_salaries.php
//Оклады

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
        require_once 'header_tags.php';
        //var_dump($stom);

        if (($finances['see_all'] == 1) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';
            //$offices = SelDataFromDB('spr_filials', '', '');
            //var_dump ($offices);

            require 'variables.php';

            $who = '&who=5';
            $whose = 'Стоматологов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_stom';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])){
                if ($_GET['who'] == 5){
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                }elseif($_GET['who'] == 6){
                    $who = '&who=6';
                    $whose = 'Косметологи ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_cosm';
                    $kabsForDoctor = 'cosm';
                    $type = 6;

                    $stom_color = '';
                    $cosm_color = 'background-color: #fff261;';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                }elseif($_GET['who'] == 10){
                    $who = '&who=10';
                    $whose = 'Специалисты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    $datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';
                    $type = 10;


                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = 'background-color: #fff261;';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                }elseif($_GET['who'] == 4){
                    $who = '&who=4';
                    $whose = 'Администраторы ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                }elseif($_GET['who'] == 7){
                    $who = '&who=7';
                    $whose = 'Ассистенты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 7;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = 'background-color: #fff261;';
                    $other_color = '';
                }elseif($_GET['who'] == 11){
                    $who = '&who=11';
                    $whose = 'Ассистенты ';
                    $selected_stom = ' ';
                    $selected_cosm = ' selected';
                    /*$datatable = 'scheduler_somat';
                    $kabsForDoctor = 'somat';*/
                    $type = 11;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = 'background-color: #fff261;';
                }else{
                    $who = '&who=5';
                    $whose = 'Стоматологи ';
                    $selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';
                    $type = 5;

                    $stom_color = 'background-color: #fff261;';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = '';
                }
            }else{
                $who = '&who=5';
                $whose = 'Стоматологи ';
                $selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_stom';
                $kabsForDoctor = 'stom';
                $type = 5;

                $stom_color = 'background-color: #fff261;';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = '';
                $assist_color = '';
                $other_color = '';
            }

            include_once 'ffun.php';

            $msql_cnnct = ConnectToDB2 ();

            $workers_j = array();
            //$spr_salaries_j = array();

            //Сотрудники этого типа
            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '{$type}' AND `status` <> '8'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rez, $arr);
                }
                $workers_j = $rez;
            }

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
                                <a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                <a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                <a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                                <a href="?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                <a href="?who=11" class="b" style="'.$other_color.'">Прочие</a>
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
                    echo '
                                <li class="cellsBlock2 cellsBlockHover" style="font-weight: normal; font-size: 11px; margin-bottom: -1px;">
                                    <a href="user.php?id='.$worker['id'].'" class="cellFullName ahref 4filter" id="4filter" style="text-align: left;">
                                        '.$worker['full_name'].'
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