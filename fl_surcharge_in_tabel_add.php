<?php

//fl_surcharge_in_tabel_add.php
//Добавить надбавку к табелю

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        include_once 'ffun.php';

        require 'variables.php';

        if ($_GET){
            if (isset($_GET['tabel_id']) && isset($_GET['type'])){

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                //var_dump($tabel_j);

                if ($tabel_j != 0){

                    echo '
                            <div id="status">
                                <header>
                                    <div class="nav">
                                        <!--<a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="b">Вернуться в табель #'.$_GET['tabel_id'].'</a>-->
                                        <a href="fl_tabels.php" class="b">Важный отчёт</a>
                                    </div>
                                    <h2>Добавить надбавку [';

                    if ($_GET['type'] == 2){
                        echo ' отпускной ';
                    }elseif ($_GET['type'] == 3){
                        echo ' больничный ';
                    }else {
                        echo ' премия ';
                    }

                    echo '
                                   ] сотруднику '.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user', true).'</h2>
                                    Заполните поля
                                </header>';

                    echo '
                                <div id="data">';
                    echo '
                                    <div id="errrror"></div>';


                    echo '
                                    <div class="cellsBlock2" style="margin-top: 10px;">
                                        Месяц: 
                                        <select id="tabelMonth">';

                    foreach ($monthsName as $val => $name){

                        if ($val == $tabel_j[0]['month']){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }

                        echo '<option value="'.$val.'" '.$selected.'>'.$name.'</option>';

                    }

                    echo '
			                            </select>
                                        Год: 
                                        <input id="tabelYear" type="number" value="'.$tabel_j[0]['year'].'" min="2000" max="2030" size="4" style="width: 60px;">
                                    </div>';
                    echo '
                                    <div class="cellsBlock2" style="margin-top: 10px;">
                                        <div class="cellLeft">
                                            <span style="font-size:80%;  color: #555;">Выберите филиал</span><br>';

                    echo '
									        <select name="SelectFilial" id="SelectFilial" style="margin-right: 5px;">
									            <option value="0" selected>Все</option>';

                    $offices_j = getAllFilials(true, false);

                    foreach ($offices_j as $offices_val){

                        echo '
										        <option value="'.$offices_val['id'].'">'.$offices_val['name'].'</option>';
                    }

                    echo '
									        </select>

									    </div>
            
                                    </div>

                                
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">
                                        <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                            <input type="text" name="surcharge_summ" id="surcharge_summ" value="">
                                            <label id="surcharge_summ_error" class="error"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">
                                            <span style="font-size:80%;  color: #555;">Комментарий</span><br>
                                            <textarea name="descr" id="descr" cols="60" rows="8"></textarea>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" id="worker_id" value="'.$tabel_j[0]['worker_id'].'">
                                    
                                    <div id="errror"></div>                        
                                    <input type="button" class="b" value="Добавить" onclick="fl_showSurchargeAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', \'add\')">';

                    echo '
                                </div>
                            </div>';

                }else{
                    echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
                }
            }else{
                echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
            }
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