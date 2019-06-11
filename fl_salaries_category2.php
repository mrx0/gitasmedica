<?php

//fl_salaries_category2.php
//Оклады по должностям (без категорий)

require_once 'header.php';
require_once 'blocks_dom.php';

if ($enter_ok){
    require_once 'header_tags.php';
    //var_dump($stom);

    if (($finances['see_all'] == 1) || $god_mode){
        include_once 'DBWork.php';
        include_once 'functions.php';

        require 'variables.php';

        $filials_j = getAllFilials(true, false, false);
        //var_dump ($filials_j);

        //тип (космет/стомат/...)
        if (isset($_GET['who'])) {
            $getWho = returnGetWho($_GET['who'], 4, array(4,7,13,14,15,11));
        }else{
            $getWho = returnGetWho(4, 4, array(4,7,13,14,15,11));
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

        //$categories_j = array();
        //$spr_salaries_j = array();

        //Сотрудники этого типа
//        $arr = array();
//        $rez = array();
//
//        $query = "SELECT * FROM `spr_categories` WHERE `permission` = '{$type}'";
//        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
//
//        $number = mysqli_num_rows($res);
//        if ($number != 0){
//            while ($arr = mysqli_fetch_assoc($res)){
//                array_push($rez, $arr);
//            }
//            $categories_j = $rez;
//        }
        //var_dump($categories_j);

        //переменная, чтоб вкл/откл редактирование
        echo '
                    <script>
                        var iCanManage = true;
                    </script>';

        echo '
                    <div id="status">
                        <header>
                            <div class="nav">
                                <a href="fl_salaries.php" class="b">Оклады сотрудников</a>
                            </div>
                            <h1>Оклады по должностям</h1>
                        </header>';


        echo '
                        <div id="infoDiv" style="display: none; position: absolute; background-color: rgba(249, 255, 171, 0.89);" class="query_neok">
    
                        </div>
                        <div id="data" style="margin: 8px 0 0;">';

        echo '		
                            <span style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">Выберите раздел</span><br>
                            <li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; margin-bottom: 10px;">
                                <a href="fl_salaries_category.php?who=4" class="b" style="'.$admin_color.'">Администраторы</a>
                                <a href="fl_salaries_category.php?who=7" class="b" style="'.$assist_color.'">Ассистенты</a>
                                <a href="?who=13" class="b" style="'.$sanit_color.'">Санитарки</a>
                                <a href="?who=14" class="b" style="'.$ubor_color.'">Уборщицы</a>
                                <a href="?who=15" class="b" style="'.$dvornik_color.'">Дворники</a>
                                <!--<a href="?who=11" class="b" style="'.$other_color.'">Прочие</a>-->
                            </li>';

        //if (!empty($categories_j)){
            //var_dump($rezult2);
            echo '
                            <ul style="margin-left:6px;">
                                <li class="cellsBlock2" style="font-weight: bold; font-size: 11px; background: #FFF; margin-bottom: -1px;">	
                                    <div class="cellName" style="text-align: center">
                                        Филиал                                        
                                    </div>';

//            foreach ($categories_j as $category) {
                echo '

                                    <div class="cellName" style="text-align: center;">
                                        -
                                    </div>';
//            }

            echo '
                                </li>';

            foreach ($filials_j as $filial_item) {

                echo '
                                <li class="cellsBlock2 cellsBlockHover" style="font-weight: normal; font-size: 11px; margin-bottom: -1px;">
                                    <div class="cellName" style="text-align: left;">
                                        '.$filial_item['name'].'                                      
                                    </div>';

//                foreach ($categories_j as $category) {

                    //Оклад этого сотрудника, который действует сейчас
                    $arr = array();
                    $rez = array();

                    $salary = 0;

                    $query = "SELECT * FROM `fl_spr_salaries_category` WHERE `permission` = '{$type}' AND `filial_id` = '{$filial_item['id']}' ORDER BY `date_from` DESC LIMIT 1";
                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        while ($arr = mysqli_fetch_assoc($res)) {
                            $salary = $arr['summ'].' руб.';
                        }
                    } else {
                        $salary = '<span style="color: red;">Не указано</span>';
                    }

                    echo '
                                        <div class="cellName" style="text-align: center;">
                                            <a href="fl_edit_salary_category.php?type_id='.$type.'&category_id=' . $type . '&filial_id='.$filial_item['id'].'" class="ahref">' . $salary . '</a>
                                        </div>
                                        <!--<div class="cellName" style="text-align: center; padding: 4px 0 0;">
                                            С какого числа
                                        </div>-->
                                        ';



//                }
                echo '
                                </li>';
            }

            echo '</ul>';
//        }else{
//            echo 'Ничего нет...';
//        }


        echo '
                        </div>
                    </div>';

        echo '	
                <!-- Подложка только одна -->
                <div id="overlay"></div>';

        echo '
                 <div id="doc_title">Оклады по должностям</div>';
    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>