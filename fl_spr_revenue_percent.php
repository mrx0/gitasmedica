<?php

//fl_spr_revenue_percent
//Проценты от выручки

    require_once 'header.php';
    require_once 'blocks_dom.php';

    if ($enter_ok){
        require_once 'header_tags.php';
        //var_dump($stom);

        if (($finances['see_all'] == 1) || $god_mode){
            include_once 'DBWork.php';
            include_once 'functions.php';

            require 'variables.php';

            /*$who = '&who=5';
            $whose = 'Стоматологов ';
            $selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_stom';*/


            $who = '&who=4';
            $whose = 'Администраторы ';
            /*$selected_stom = ' selected';
            $selected_cosm = ' ';
            $datatable = 'scheduler_stom';
            $kabsForDoctor = 'stom';*/
            $type = 4;

            $stom_color = '';
            $cosm_color = '';
            $somat_color = '';
            $admin_color = 'background-color: #fff261;';
            $assist_color = '';
            $other_color = '';

            //тип (космет/стомат/...)
            if (isset($_GET['who'])){
                /*if ($_GET['who'] == 5){
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
                }else*/if($_GET['who'] == 4){
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
                /*}elseif($_GET['who'] == 11){
                        $who = '&who=11';
                        $whose = 'Ассистенты ';
                        $selected_stom = ' ';
                        $selected_cosm = ' selected';
                        /*$datatable = 'scheduler_somat';
                        $kabsForDoctor = 'somat';*/
                    /*$type = 11;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = '';
                    $assist_color = '';
                    $other_color = 'background-color: #fff261;';*/
                }else{
                    /*$who = '&who=5';
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
                    $other_color = '';*/

                    $who = '&who=4';
                    $whose = 'Администраторы ';
                    /*$selected_stom = ' selected';
                    $selected_cosm = ' ';
                    $datatable = 'scheduler_stom';
                    $kabsForDoctor = 'stom';*/
                    $type = 4;

                    $stom_color = '';
                    $cosm_color = '';
                    $somat_color = '';
                    $admin_color = 'background-color: #fff261;';
                    $assist_color = '';
                    $other_color = '';
                }
            }else{
                /*$who = '&who=5';
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
                $other_color = '';*/

                $who = '&who=4';
                $whose = 'Администраторы ';
                /*$selected_stom = ' selected';
                $selected_cosm = ' ';
                $datatable = 'scheduler_stom';
                $kabsForDoctor = 'stom';*/
                $type = 4;

                $stom_color = '';
                $cosm_color = '';
                $somat_color = '';
                $admin_color = 'background-color: #fff261;';
                $assist_color = '';
                $other_color = '';
            }

            include_once 'ffun.php';

            $filials_j = getAllFilials(true, false);

            $msql_cnnct = ConnectToDB2 ();

            //Процент с выручки для этого типа
            $revenue_percent_j = array();

            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `fl_spr_revenue_percent` WHERE `permission` = '{$type}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    if (!isset($revenue_percent_j[$arr['filial_id']])){
                        $revenue_percent_j[$arr['filial_id']] = array();
                    }
                    if (!isset($revenue_percent_j[$arr['filial_id']][$arr['category']])){
                        $revenue_percent_j[$arr['filial_id']][$arr['category']] = array();
                    }
                    $revenue_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                }
            }
            //var_dump($revenue_percent_j);


            //Категории для этого типа
            $categories_j = array();

            $arr = array();
            $rez = array();

            $query = "SELECT * FROM `spr_categories` WHERE `permission` = '{$type}'";
            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($rez, $arr);
                }
                $categories_j = $rez;
            }

            //переменная, чтоб вкл/откл редактирование
            echo '
                    <script>
                        var iCanManage = true;
                    </script>';

            echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <!--<a href="fl_percent_cats.php" class="b">Категории процентов</a>
                                <a href="fl_percent_cats.php" class="b">Персональные</a>-->
                            </div>
                            <h1>Проценты от выручки</h1>
                        </header>';


            echo '
                        <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
    
                        </div>
                        <div id="data" style="margin: 8px 0 0;">';

            echo '		
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <!--<a href="?who=5" class="b" style="'.$stom_color.'">Стоматологи</a>
                                <a href="?who=6" class="b" style="'.$cosm_color.'">Косметологи</a>
                                <a href="?who=10" class="b" style="'.$somat_color.'">Специалисты</a>-->
                                <a href="?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                <!--<a href="?who=11" class="b" style="'.$other_color.'">Прочие</a>-->
                            </li>';

            echo '
                            <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                                <tr>
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>Филиал</b></td>';

            if (!empty($categories_j)) {

                foreach ($categories_j as $category_item) {
                    echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b><i>'.$category_item['name'].'</i></b></td>';
                }
            }

            echo '
                                </tr>';

            foreach ($filials_j as $filial_item){

                echo '
                                <tr>';
                echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">'.$filial_item['name'].'</td>';

                foreach ($categories_j as $category_item) {

                    echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';

                    if (isset($revenue_percent_j[$filial_item['id']][$category_item['id']])) {
                        echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(true, '.$type.', \''.$whose.'\','.$filial_item['id'].', \''.$filial_item['name'].'\', '.$category_item['id'].', \''.$category_item['name'].'\', '.$revenue_percent_j[$filial_item['id']][$category_item['id']]['value'].');">
                                '.$revenue_percent_j[$filial_item['id']][$category_item['id']]['value'].'%
                            </div>';
                    }else{
                        echo'
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(false, '.$type.', \''.$whose.'\', '.$filial_item['id'].', \''.$filial_item['name'].'\', '.$category_item['id'].', \''.$category_item['name'].'\', \'\');">
                                <span style="color: red;">Не указано</span>
                            </div>';
                    }

                    echo '
                                        </div>
                                    </td>';
                }


                echo '
                                </tr>';

            }

            echo '
                            </table>';

  /*                              <li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF;">
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

                foreach ($revenue_percent_j as $filial_item){
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

                echo '</ul>';*/
            /*}else{
                echo 'Ничего нет...';
            }*/


            echo '
                        </div>
                    </div>';

            echo '
                    <!--<div id="revenue_percent_change" style="display: none;">
                        <input type="text" size="30" id="revenue_percent_change_val" placeholder="" value="0" class="who"  autocomplete="off">
                    </div>-->

                    <!-- Подложка только одна -->
                    <div id="overlay"></div>';


        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
    }else{
        header("location: enter.php");
    }

    require_once 'footer.php';

?>