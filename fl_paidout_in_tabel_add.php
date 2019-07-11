<?php

//fl_paidout_in_tabel_add.php
//Добавить выплату к табелю

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        include_once 'ffun.php';

        require 'variables.php';

        if ($_GET){
            if (isset($_GET['tabel_id']) && isset($_GET['type']) && isset($_GET['filial_id'])){

                $link = 'fl_tabel.php';
//                if (isset($_GET['w_type'])){
//                    $link = 'fl_tabel2.php';
//                }

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                //var_dump($tabel_j);

                if ($tabel_j != 0){

                    $filials_j = getAllFilials(false, false, false);
                    //var_dump($filials_j);

                    echo '
                            <div id="status">
                                <header>
                                    <div class="nav">
                                        <!--<a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="b">Вернуться в табель #'.$_GET['tabel_id'].'</a>-->
                                        <a href="fl_tabels.php" class="b">Важный отчёт</a>
                                    </div>
                                    <h2>Добавить выплату [';

                    if ($_GET['type'] == 1){
                        echo ' аванс ';
                    }elseif ($_GET['type'] == 2){
                        echo ' отпускной ';
                    }elseif ($_GET['type'] == 3){
                        echo ' больничный ';
                    }elseif ($_GET['type'] == 4){
                        echo ' на карту ';
                    }elseif ($_GET['type'] == 7){
                        echo ' зп ';
                    }elseif ($_GET['type'] == 5){
                        echo ' ночь ';

                        $link = 'fl_tabel_noch.php';
                    }

                    $noch = 0;

                    //Если за ночь
                    if (isset($_GET['noch'])){
                        if ($_GET['noch'] == 1){
                            $noch = 1;
                        }
                    }

                    //Сумма, которую предлагаем выплатить
                    $paidout_summ_value = 0;

                    if (($_GET['type'] == 1) || ($_GET['type'] == 7)){
                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
                        $paidout_summ_value = $tabel_j[0]['summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
                        //Если ассистент, то плюсуем сумму за РЛ
                        if ($tabel_j[0]['type'] == 7){
                            $paidout_summ_value += $tabel_j[0]['summ_calc'];
                        }
                    }

                    echo '
                                   ] в <a href="'.$link.'?id='.$_GET['tabel_id'].'" class="ahref">табель #'.$_GET['tabel_id'].'</a></h2>
                                   <div style="font-size: 87%; padding-bottom: 10px; font-weight: bold;"><i>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user', false).'  ['.$filials_j[$tabel_j[0]['office_id']]['name2'].']  '.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</i></div>
                                    Заполните поля
                                </header>';

                    echo '
                                <div id="data">';
                    echo '
                                    <div id="errrror"></div>';
                    echo '
                                    <form>
                                
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                            <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                                <input type="text" name="paidout_summ" id="paidout_summ" value="'.intval($paidout_summ_value).'" class="paidout_summ2" tabel_id="'.$_GET['tabel_id'].'" paidout_summ_tabel="'.intval($paidout_summ_value).'" autocomplete="off" autofocus><!--<span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick=""><i class="fa fa-check-square" style=" color: green;"></i> Применить</span>-->
                                                <label id="paidout_summ_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                <select name="SelectFilial" id="SelectFilial" disabled>';

                        if (!empty($filials_j)) {
                        foreach ($filials_j as $f_id => $filials_j_data) {
                            $selected = '';
                            //if (isset($_GET['filial'])){
                                if ($f_id == $_GET['filial_id']){
                                    $selected = 'selected';
                                }
                            //}
                            echo "<option value='".$f_id."' $selected>".$filials_j_data['name']."</option>";
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
                                        <div id="tabelFilialSubtraction">
                                        </div>';

                    //Сколько денег с какого филиала надо будет снять при выплате ЗП
                    //returnTabelFilialPaidouts ($_GET['tabel_id']);



                    echo '                    
                                        <input type="hidden" name="noch" id="noch" value="'.$noch.'">
                                        
                                        <div id="errror"></div>
                                        <div id="showPaidoutAddbutton" style="display: none;">
                                            <input type="button" class="b" value="Добавить" onclick="fl_showPaidoutAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', '.$tabel_j[0]['worker_id'].', '.$tabel_j[0]['month'].', '.$tabel_j[0]['year'].', \''.$link.'\', \'add\', false)">
                                            <!--<input type="button" class="b" value="Добавить и провести" onclick="fl_showPaidoutAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', \''.$link.'\', \'add\', true)">-->
                                        </div>
                                    </form>';

                    echo '
                                </div>
                            </div>
                            
                            <script>
                                $(document).ready(function(){
                                    var
                                        summ = $("#paidout_summ").val(),
                                        tabel_id = $("#paidout_summ").attr("tabel_id"),
                                        paidout_summ_tabel = $("#paidout_summ").attr("paidout_summ_tabel");

                                    if (summ.length > 2) {
                                        tabelSubtractionPercent(tabel_id, summ, paidout_summ_tabel);
                                    }
                                });
                            </script>
                            ';

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