<?php

//fl_taxes.php
//Налоги

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
        require_once 'header_tags.php';
        //var_dump($stom);

        if (($_SESSION['id'] == 270) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';
            //$offices = SelDataFromDB('spr_filials', '', '');
            //var_dump ($offices);

            require 'variables.php';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 5, array(0,4,7,13,14,15,11,5,6,10));
            }else{
                $getWho = returnGetWho(5, 5, array(0,4,7,13,14,15,11,5,6,10));
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

            include_once 'ffun.php';

            $msql_cnnct = ConnectToDB2 ();

            $workers_j = array();

            //Сотрудники этого типа
            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `spr_workers` WHERE `permissions` = '{$type}' AND `status` <> '8' ORDER BY `full_name`";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rez, $arr);
                }
                $workers_j = $rez;
            }



            //переменная, чтоб вкл/откл редактирование
//            echo '
//                    <script>
//                        var iCanManage = true;
//                    </script>';

            echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <!--<a href="fl_salaries_category.php" class="b">Оклады по должностям</a>-->
                            </div>
                            <h1>Налоги сотрудников</h1>
                        </header>';


            echo '
                        <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
    
                        </div>
                        <div id="data" style="margin: 8px 0 0;">';

            echo '		
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <!--<a href="contacts.php" class="b" style="'.$all_color.'">Все</a>-->
                                <a href="fl_taxes.php?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                <a href="fl_taxes.php?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                <a href="fl_taxes.php?who=10" class="b" style="'.$somat_color.'">Специалисты</a>
                                <a href="fl_taxes.php?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="fl_taxes.php?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                <a href="fl_taxes.php?who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                <a href="fl_taxes.php?&who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                <a href="fl_taxes.php?who=15" class="b" style="'.$dvornik_color.'">Дворники</a>
                                <a href="fl_taxes.php?who=11" class="b" style="'.$other_color.'">Прочие</a>';
            if (in_array($_SESSION['permissions'], $workers_target_arr) || ($_SESSION['id'] == 270) || $god_mode) {
                echo '
                                <a href="fl_taxes2.php" class="b" style="">Другие</a>';
            }
            echo '
                            </li>';

            if (!empty($workers_j)){
                //var_dump($rezult2);
                echo '
                            <ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
                                <li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF;">	
                                    <div class="cellFullName" style="text-align: center">
                                        ФИО';
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

//                    if (!empty($specializations)){
//                        foreach ($specializations as $specialization_item){
//                            echo ' <span class="tag" style="float: right; font-size: 90%;">'.$specialization_item['name'].'</span>';
//                        }
//                    }

                    echo '
                                    </a>';

                    //Оклад этого сотрудника, который действует сейчас
                    $arr = array();
                    $rez = array();

                    $tax = 0;

                    //$query = "SELECT * FROM `fl_journal_taxes` WHERE `worker_id` = '{$worker['id']}' ORDER BY `date_from` DESC LIMIT 1";
                    $query = "SELECT * FROM `fl_journal_taxes` WHERE `worker_id` = '{$worker['id']}' LIMIT 1";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            $tax = $arr['summ'];
                        }
                    }else{
                        $tax = 0;
                    }

                    echo '
                                    <!--<div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        <a href="fl_edit_salary.php?worker_id='.$worker['id'].'" class="ahref">'.$tax.'</a>
                                    </div>-->
                                    <!--<div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        С какого числа
                                    </div>-->
                                    
                                    <div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                        <div class="changeCurrentTax" worker_id="'.$worker['id'].'" style="display: inline; cursor: pointer;" >'.number_format($tax, 2, '.', '').'</div> <div id="textAfterTax" style="display: inline;">руб. </div>

                                    </div>';

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
                 <div id="doc_title">Налоги сотрудников - Асмедика</div>';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        header("location: enter.php");
    }

    require_once 'footer.php';

?>