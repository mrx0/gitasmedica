<?php

//fl_paidout_another_test_in_tabel_add.php
//Тестовая временная фигня. Добавить таке выплаты, которые еще не учитываются в общей программе

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        include_once 'ffun.php';

        require 'variables.php';

        $filial_id = 16;

        if (isset($_POST['filial_id'])){
            $filial_id = $_POST['filial_id'];
        }else{
            if (isset($_SESSION['filial_id'])){
                $filial_id = $_SESSION['filial_id'];
            }
        }

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="fl_consolidated_report_admin.php?filial_id='.$filial_id.'" class="b">Сводный отчёт по филиалу</a>
                            <a href="fl_main_report2.php?filial_id='.$filial_id.'" class="b">Финальный отчёт</a>
                        </div>
                        <h2>Добавить выплату / расход';

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
                                    <span style="font-size:80%;  color: #555;">Сотрудник</span><br>
                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                    <ul id="search_result2" class="search_result2"></ul><br />
                                </div>
                            </div>
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                    <input type="text" name="paidout_summ" id="paidout_summ" value="" class="paidout_summ2" autocomplete="off" autofocus>
                                    <label id="paidout_summ_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Тип</span><br>
                                    <select name="SelectType" id="SelectType">
                                        <option value="1">аванс</option>
                                        <option value="7">зп</option>
                                        <option value="2">отпускной</option>
                                        <option value="3">больничный</option>
                                        
                                    </select>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Филиал</span><br>
                                    <select name="SelectFilial" id="SelectFilial">
                                    <option value="0" selected>Выберите филиал</option>';

        if (!empty($filials_j)) {
            foreach ($filials_j as $f_id => $filials_j_data) {
                $selected = '';
                if ($filial_id == $f_id){
                    //$selected = ' selected';
                }
                echo "<option value='".$f_id."' >".$filials_j_data['name']."</option>";
            }
        }

        echo '
                                    </select>
                                </div>
                            </div>
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Комментарий</span><br>
                                    <textarea name="descr" id="descr" cols="60" rows="5"></textarea>
                                </div>
                            </div>';


        echo '                    
                            <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showPaidoutAnotherAdd()">
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