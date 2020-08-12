<?php

//fl_spr_revenue_percent.php
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

            //тип (космет/стомат/...)
            if (isset($_GET['who'])) {
                $getWho = returnGetWho($_GET['who'], 4, array(4,7));
            }else{
                $getWho = returnGetWho(4, 4, array(4,7));
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

            $filials_j = getAllFilials(true, false, false);

            $msql_cnnct = ConnectToDB2 ();

            //Процент с выручки для этого типа
            $revenue_percent_j = array();
            $revenue_solar_percent_j = array();
            $revenue_realiz_percent_j = array();

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

            //Доберём солярий, абонементы и крема, если это админы
            if ($type == 4){
                //Солярий
                $query = "SELECT * FROM `fl_spr_revenue_solar_percent` WHERE `permission` = '{$type}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($revenue_solar_percent_j[$arr['filial_id']])){
                            $revenue_solar_percent_j[$arr['filial_id']] = array();
                        }
                        if (!isset($revenue_solar_percent_j[$arr['filial_id']][$arr['category']])){
                            $revenue_solar_percent_j[$arr['filial_id']][$arr['category']] = array();
                        }
                        $revenue_solar_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                    }
                }
                //var_dump($revenue_solar_percent_j);
                //Крема
                $query = "SELECT * FROM `fl_spr_revenue_realiz_percent` WHERE `permission` = '{$type}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($revenue_realiz_percent_j[$arr['filial_id']])){
                            $revenue_realiz_percent_j[$arr['filial_id']] = array();
                        }
                        if (!isset($revenue_realiz_percent_j[$arr['filial_id']][$arr['category']])){
                            $revenue_realiz_percent_j[$arr['filial_id']][$arr['category']] = array();
                        }
                        $revenue_realiz_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                    }
                }
                //var_dump($revenue_realiz_percent_j);
                //Абонементы
                $query = "SELECT * FROM `fl_spr_revenue_abon_percent` WHERE `permission` = '{$type}'";
                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        if (!isset($revenue_abon_percent_j[$arr['filial_id']])){
                            $revenue_abon_percent_j[$arr['filial_id']] = array();
                        }
                        if (!isset($revenue_abon_percent_j[$arr['filial_id']][$arr['category']])){
                            $revenue_abon_percent_j[$arr['filial_id']][$arr['category']] = array();
                        }
                        $revenue_abon_percent_j[$arr['filial_id']][$arr['category']] = $arr;
                    }
                }
                //var_dump($revenue_abon_percent_j);
            }


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

            echo '% с закрытых работ';

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
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'main\', true, '.$type.', \''.$whose.'\','.$filial_item['id'].', \''.$filial_item['name'].'\', '.$category_item['id'].', \''.$category_item['name'].'\', '.$revenue_percent_j[$filial_item['id']][$category_item['id']]['value'].');">
                                '.$revenue_percent_j[$filial_item['id']][$category_item['id']]['value'].'%
                            </div>';
                    }else{
                        echo'
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'main\', false, '.$type.', \''.$whose.'\', '.$filial_item['id'].', \''.$filial_item['name'].'\', '.$category_item['id'].', \''.$category_item['name'].'\', \'\');">
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

            if ($type == 4) {

                echo '% с солярия';

                echo '
                            <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                                <tr>
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>Филиал</b></td>';

                if (!empty($categories_j)) {

                    foreach ($categories_j as $category_item) {
                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b><i>' . $category_item['name'] . '</i></b></td>';
                    }
                }

                echo '
                                </tr>';

                foreach ($filials_j as $filial_item) {

                    echo '
                                <tr>';
                    echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">' . $filial_item['name'] . '</td>';

                    foreach ($categories_j as $category_item) {

                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';

                        if (isset($revenue_solar_percent_j[$filial_item['id']][$category_item['id']])) {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'solar\', true, ' . $type . ', \'' . $whose . '\',' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', ' . $revenue_solar_percent_j[$filial_item['id']][$category_item['id']]['value'] . ');">
                                ' . $revenue_solar_percent_j[$filial_item['id']][$category_item['id']]['value'] . '%
                            </div>';
                        } else {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'solar\', false, ' . $type . ', \'' . $whose . '\', ' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', \'\');">
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

                echo '% с реализации';

                echo '
                            <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                                <tr>
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>Филиал</b></td>';

                if (!empty($categories_j)) {

                    foreach ($categories_j as $category_item) {
                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b><i>' . $category_item['name'] . '</i></b></td>';
                    }
                }

                echo '
                                </tr>';

                foreach ($filials_j as $filial_item) {

                    echo '
                                <tr>';
                    echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">' . $filial_item['name'] . '</td>';

                    foreach ($categories_j as $category_item) {

                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';

                        if (isset($revenue_realiz_percent_j[$filial_item['id']][$category_item['id']])) {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'realiz\', true, ' . $type . ', \'' . $whose . '\',' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', ' . $revenue_realiz_percent_j[$filial_item['id']][$category_item['id']]['value'] . ');">
                                ' . $revenue_realiz_percent_j[$filial_item['id']][$category_item['id']]['value'] . '%
                            </div>';
                        } else {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'realiz\', false, ' . $type . ', \'' . $whose . '\', ' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', \'\');">
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

                echo '% с проданных абонементов';

                echo '
                            <table style="border-bottom: 1px solid #BFBCB5; border-right: 1px solid #BFBCB5; margin:5px; font-size: 80%;">
                                <tr>
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b>Филиал</b></td>';

                if (!empty($categories_j)) {

                    foreach ($categories_j as $category_item) {
                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;"><b><i>' . $category_item['name'] . '</i></b></td>';
                    }
                }

                echo '
                                </tr>';

                foreach ($filials_j as $filial_item) {

                    echo '
                                <tr>';
                    echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px;">' . $filial_item['name'] . '</td>';

                    foreach ($categories_j as $category_item) {

                        echo '
                                    <td style="border-top: 1px solid #BFBCB5; border-left: 1px solid #BFBCB5; padding: 5px; text-align: right;">';

                        if (isset($revenue_abon_percent_j[$filial_item['id']][$category_item['id']])) {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'abon\', true, ' . $type . ', \'' . $whose . '\',' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', ' . $revenue_abon_percent_j[$filial_item['id']][$category_item['id']]['value'] . ');">
                                ' . $revenue_abon_percent_j[$filial_item['id']][$category_item['id']]['value'] . '%
                            </div>';
                        } else {
                            echo '
                            <div style="cursor: pointer;" onclick="revenuePercentChangeShow(\'abon\', false, ' . $type . ', \'' . $whose . '\', ' . $filial_item['id'] . ', \'' . $filial_item['name'] . '\', ' . $category_item['id'] . ', \'' . $category_item['name'] . '\', \'\');">
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
            }

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