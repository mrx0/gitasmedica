<?php

//material_cost_add_test.php
//Тестовая временная фигня. Добавить расходы по материалам

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        /*!!!Тест PDO*/
        include_once('DBWorkPDO.php');


        include_once('DBWork.php');

        include_once 'functions.php';
        include_once 'ffun.php';

        require 'variables.php';

        $filial_id = 15;

        if (isset($_GET['filial_id'])){
            $filial_id = $_GET['filial_id'];
        }else{
            if (isset($_SESSION['filial_id'])){
                $filial_id = $_SESSION['filial_id'];
            }
        }

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        //Категории процентов
        //$percent_cats_j = SelDataFromDB('fl_spr_percents', '', '');
        //var_dump($percent_cats_j);


        $percent_cats_arr = array();
        $percent_cats_j = array();

        //$msql_cnnct = ConnectToDB ();
        $db = new DB();

        //Выбрать все категории
        $percent_cats_arr = $db::getRows("SELECT sperc.id, sperc.name, sperc.type, sperm.name AS type_name FROM `fl_spr_percents` sperc RIGHT JOIN `spr_permissions` sperm ON sperm.id = sperc.type ORDER BY sperc.type, sperc.name");
        //var_dump($percent_cats_arr);

        //Соберём удобный массив по типам
        if (!empty($percent_cats_arr)){
            foreach ($percent_cats_arr as $pc_data){
                if ($pc_data['id'] != NULL){
                    //var_dump($pc_data);

                    if (!isset($percent_cats_j[$pc_data['type']])){

                        $percent_cats_j[$pc_data['type']] = array();
                        $percent_cats_j[$pc_data['type']]['type_name'] = $pc_data['type_name'];
                        $percent_cats_j[$pc_data['type']]['data'] = array();
                    }
                    array_push($percent_cats_j[$pc_data['type']]['data'], $pc_data);
                }
            }
        }
        //var_dump($percent_cats_j);

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="material_costs_test.php?filial_id='.$filial_id.'" class="b">Расходы на материалы</a>
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                            <a href="fl_main_report2.php?filial_id='.$filial_id.'" class="b">Финальный отчёт</a>
                        </div>
                        <h2>Добавить расходы на метериалы';

        echo '
                      </h2>
                        <!--Заполните поля-->
                    </header>';


        echo '
                    <div id="data">';
        echo '
                        <div id="errrror"></div>';




        echo '
                        <form>';

        echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Период</span><br>
                                    <select name="iWantThisMonth" id="iWantThisMonth" style="margin-right: 5px;">';
        foreach ($monthsName as $mNumber => $mName){
            $selected = '';
            if ((int)$mNumber == date('m')){
                $selected = 'selected';
            }
            echo '
				                        <option value="'.$mNumber.'" '.$selected.'>'.$mName.'</option>';
        }
        echo '
			                        </select>
			                        <select name="iWantThisYear" id="iWantThisYear">';
        for ($i = 2017; $i <= (int)date('Y')+2; $i++){
            $selected = '';
            if ($i == (int)date('Y')){
                $selected = 'selected';
            }
            echo '
				                        <option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        }
        echo '
			                        </select>
			                    </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                    <input type="text" name="paidout_summ" id="paidout_summ" value="" class="paidout_summ2" autocomplete="off" autofocus>
                                    <label id="paidout_summ_error" class="error"></label>
                                </div>
                            </div>';

        echo '                  
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Филиал</span><br>
                                    <select name="SelectFilial" id="SelectFilial">
                                    <option value="0" selected>Выберите филиал</option>';

        if (!empty($filials_j)) {
            foreach ($filials_j as $f_id => $filials_j_data) {
                $selected = '';
                if ($filial_id == $f_id){
                    $selected = ' selected';
                }
                echo "<option value='".$f_id."' $selected>".$filials_j_data['name']."</option>";
            }
        }

        echo '
                                    </select>
                                </div>
                            </div>';


        echo '                  
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Категория</span><br>
                                    <select name="SelectCategory" id="SelectCategory">
                                    <option value="0" selected>Выберите категорию</option>';

        if (!empty($percent_cats_j)) {
            foreach ($percent_cats_j as $type_id => $percent_cat_arr) {
//                $selected = '';
//                if ($filial_id == $f_id){
//                    //$selected = ' selected';
//                }

                echo "<option disabled style='font-size: 120%; color: red;'>".$percent_cat_arr['type_name']."</option>";

                foreach ($percent_cat_arr['data'] as $percent_cat_data) {

                    echo "<option value='" . $percent_cat_data['id'] . "' >" . $percent_cat_data['name'] . "</option>";
                }
            }
        }

        echo '
                                    </select>
                                </div>
                            </div>';


        echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Комментарий</span><br>
                                    <textarea name="descr" id="descr" cols="60" rows="5"></textarea>
                                </div>
                            </div>';


        echo '                    
                            <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showMaterialCostAdd_test()">
                            </div>
                        </form>';

        echo '
                    </div>
                </div>';
    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }

}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>