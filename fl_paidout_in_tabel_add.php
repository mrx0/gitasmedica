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

                if (isset($_GET['ref'])){
                    //var_dump($_GET['ref']);
                }

                $tabel_j = SelDataFromDB('fl_journal_tabels', $_GET['tabel_id'], 'id');
                //var_dump($tabel_j);

                if ($tabel_j != 0){

                    $filials_j = getAllFilials(false, false, false);
                    //var_dump($filials_j);

                    $msql_cnnct = ConnectToDB ();

                    //Сумма премии
                    $surchargesSumm = 0;

                    //Получим премии отдельно, !!! потом надо будет переделать, а то лишний запрос какой-то
                    $query = "SELECT SUM(`summ`) AS `surchargesSumm`
                    FROM  `fl_journal_surcharges` 
                    WHERE `tabel_id` = '{$_GET['tabel_id']}' AND `type`='1';";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);
                            $surchargesSumm = $arr['surchargesSumm'];
                    }
                    //var_dump($surchargesSumm);

                    //Сумма выплат
                    $paidoutssSumm = 0;

                    //Получим премии отдельно, !!! потом надо будет переделать, а то лишний запрос какой-то
                    $query = "SELECT SUM(`summ`) AS `paidoutssSumm`
                    FROM  `fl_journal_paidouts` 
                    WHERE `tabel_id` = '{$_GET['tabel_id']}' AND (`type`='1' OR `type`='4' OR `type`='7');";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0) {
                        $arr = mysqli_fetch_assoc($res);
                        $paidoutssSumm = $arr['paidoutssSumm'];
                    }
                    //var_dump($paidoutssSumm);

                    echo '
                            <div id="status">
                                <header>
                                    <div class="nav">
                                        <!--<a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="b">Вернуться в табель #'.$_GET['tabel_id'].'</a>-->
                                        <a href="fl_tabels.php" class="b">Важный отчёт</a>';
                    if (isset($_GET['ref'])){
                        echo '
                                        <a href="fl_tabels_simple_pay.php?&filial='.$tabel_j[0]['office_id'].'&m='.$tabel_j[0]['month'].'&y='.$tabel_j[0]['year'].'&who='.$tabel_j[0]['type'].'" class="b">Проверка табелей 2</a>';
                    }
                    echo '
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

                    echo '
                                   ] в <a href="'.$link.'?id='.$_GET['tabel_id'].'" class="ahref">табель #'.$_GET['tabel_id'].'</a></h2>
                                   <div style="font-size: 87%; padding-bottom: 10px; font-weight: bold;"><i>'.WriteSearchUser('spr_workers', $tabel_j[0]['worker_id'], 'user', false).'  ['.$filials_j[$tabel_j[0]['office_id']]['name2'].']  '.$monthsName[$tabel_j[0]['month']].' '.$tabel_j[0]['year'].'</i></div>
                                    <!--Заполните поля-->
                                </header>';

                    $noch = 0;

                    //Если за ночь
                    if (isset($_GET['noch'])){
                        if ($_GET['noch'] == 1){
                            $noch = 1;
                        }
                    }


                    //Сумма, которую предлагаем выплатить
                    $paidout_summ_value = 0;

                    //Если 1 - аванс, 7 - ЗП, 4 - на карту
                    if (($_GET['type'] == 1) || ($_GET['type'] == 7) || ($_GET['type'] == 4)){
                        //Общая сумма, которую осталось выплатить = сумма (РЛ) + надбавки + за ночь + пустые смены - вычеты - оплачено - выплачено
                        //$paidout_summ_value = $tabel_j[0]['summ'] + $tabel_j[0]['surcharge'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'] - $tabel_j[0]['deduction'] - $tabel_j[0]['paid'] - $tabel_j[0]['paidout'];
                        $paidout_summ_value = $tabel_j[0]['summ'] + $tabel_j[0]['night_smena'] + $tabel_j[0]['empty_smena'] - $tabel_j[0]['deduction'] - $paidoutssSumm + $surchargesSumm;
                        //Если ассистент, то плюсуем сумму за РЛ
                        if ($tabel_j[0]['type'] == 7){
                            $paidout_summ_value += $tabel_j[0]['summ_calc'];
                        }
                    }

                    //Тип начисления
                    $surcharge_type = 0;
                    //Для отображения начислений (а надо ли?)
                    $rezultS = '';
                    //Массив для начислений
                    $tabel_surcharges_j = array();

//                    //Если отпускной
//                    if ($_GET['type'] == 2){
//                        $surcharge_type = 2;
//                    }
//
//                    //Если больничный
//                    if ($_GET['type'] == 3){
//                        $surcharge_type = 3;
//                    }
//
//                    //Если на карту
//                    if ($_GET['type'] == 4){
//                        $surcharge_type = 4;
//                    }

                    //Если выплачиваем 2 - отпускной, 3 - больничный, 4 - на карту
                    if (($_GET['type'] == 2) || ($_GET['type'] == 3)) {

                        //Надбавки
                        //$query = "SELECT * FROM `fl_journal_surcharges` WHERE `tabel_id`='{$tabel_j[0]['id']}' AND `type` = '{$_GET['type']}';";
                        $query = "
                              SELECT fl_js.* FROM 
                              `fl_journal_tabels` fl_jt
                              RIGHT JOIN `fl_journal_surcharges` fl_js ON fl_jt.id = fl_js.tabel_id  AND fl_js.type = '{$_GET['type']}' 
                              WHERE fl_jt.worker_id = '{$tabel_j[0]['worker_id']}' AND fl_jt.month = '{$tabel_j[0]['month']}' AND fl_jt.year = '{$tabel_j[0]['year']}';";

                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct) . ' -> ' . $query);

                        $number = mysqli_num_rows($res);
                        if ($number != 0) {
                            while ($arr = mysqli_fetch_assoc($res)) {
                                array_push($tabel_surcharges_j, $arr);
                            }
                        }
//                        var_dump($query);
//                        var_dump($tabel_surcharges_j);

                        if (!empty($tabel_surcharges_j)) {

                            foreach ($tabel_surcharges_j as $rezData) {

                                $rezultS .=
                                    '
                                        <div class="cellsBlockHover" style="background-color: #ffffff; border: 1px solid #BFBCB5; margin: 1px 7px 7px;; position: relative; display: inline-block; vertical-align: top;">
                                            <div style="display: inline-block; width: 200px;">
                                                <div>
                                                <div>
                                                    <div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 120%; margin: 1px; padding: 2px; font-weight: bold; font-style: italic;">
                                                            <i class="fa fa-file-o" aria-hidden="true" style="background-color: #FFF; text-shadow: none;"></i>
                                                        </div>
                                                        <div style="display: inline-block; vertical-align: middle; font-size: 90%;">
                                                            <b>';
                                if ($rezData['type'] == 2) {
                                    $rezultS .= ' отпускной ';
                                } elseif ($rezData['type'] == 3) {
                                    $rezultS .= ' больничный ';
                                } else {
                                    $rezultS .= ' прочее ';
                                }
                                $rezultS .=
                                    '#' . $rezData['id'] . '</b> <span style="    color: rgb(115, 112, 112);"><br>создано: ' . date('d.m.y H:i', strtotime($rezData['create_time'])) . '</span>
                                                        </div>
                                                        <div style="font-size: 80%; text-align: right;">
                                                            В <a href="fl_tabel.php?id='.$rezData['tabel_id'].'" class="ahref">табеле '.$rezData['tabel_id'].'</a>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px; font-size: 10px">
                                                            Сумма: <span class="calculateInvoice calculateCalculateN" style="font-size: 11px">' . $rezData['summ'] . '</span> руб.
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                </div>';
                                if (mb_strlen($rezData['descr']) > 0) {
                                    $rezultS .= '
                                                <div style="margin: 5px 0 0 3px; font-size: 80%;">
                                                    <b>Комментарий:</b> ' . $rezData['descr'] . '                                                
                                                </div>';
                                }
                                $rezultS .= '
                                            </div>';
                                if ($tabel_j[0]['status'] != 7) {
//                                    $rezultS .= '
//                                            <div style="display: inline-block; vertical-align: top;">
//                                                <div class="settings_text" style="border: 1px solid #CCC; padding: 3px; margin: 1px; width: 12px; text-align: center;"  onclick="contextMenuShow(' . $tabel_j[0]['id'] . ', ' . $rezData['id'] . ', event, \'tabel_surcharge_options\');">
//                                                    <i class="fa fa-caret-down"></i>
//                                                </div>
//                                            </div>';
                                }
                                $rezultS .= '
                                            <!--<span style="position: absolute; top: 2px; right: 3px;"><i class="fa fa-check" aria-hidden="true" style="color: darkgreen; font-size: 110%;"></i></span>-->
                                        </div>';

                                if ($rezData['tabel_id'] == $_GET['tabel_id']){
                                    $paidout_summ_value += $rezData['summ'];
                                }

                            }
                        }
                    }
                    //var_dump($paidout_summ_value);
                    //var_dump($rezultS);


                    echo '
                                <div id="data">';
                    echo '
                                    <div id="errrror"></div>';


                    if (!empty($tabel_surcharges_j)){
                        echo '
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">
                                        <span style="font-size:80%;  color: #555;">Документы, выписанные данному сотруднику в этом месяце:</span><br>';
                        echo $rezultS;
                        echo '
                                        </div>
                                    </div>';
                    }

                    echo '
                                    <form>
                                
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                            <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                                <input type="text" name="paidout_summ" id="paidout_summ" value="', intval($paidout_summ_value) == 0 ? '' : intval($paidout_summ_value) ,'" class="paidout_summ2" tabel_id="'.$_GET['tabel_id'].'" paidout_summ_tabel="'.intval($paidout_summ_value).'" autocomplete="off" autofocus><!--<span class="button_tiny" style="font-size: 90%; cursor: pointer" onclick=""><i class="fa fa-check-square" style=" color: green;"></i> Применить</span>--><br>
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
                                        <input type="hidden" name="tabel_type" id="tabel_type" value="'.$tabel_j[0]['type'].'">
                                        <input type="hidden" name="paidout_type" id="paidout_type" value="'.$_GET['type'].'">';

                    if (isset($_GET['ref'])){
                        echo '
                                        <input type="hidden" name="ref" id="ref" value="fl_tabels_simple_pay.php?&filial='.$tabel_j[0]['office_id'].'&m='.$tabel_j[0]['month'].'&y='.$tabel_j[0]['year'].'&who='.$tabel_j[0]['type'].'">';
                    }
                    echo '
                                        <div id="errror"></div>
                                        <div id="showPaidoutAddbutton" style="display: none;">
                                            <input type="button" class="b" value="Добавить" onclick="fl_showPaidoutAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', '.$tabel_j[0]['worker_id'].', '.$tabel_j[0]['month'].', '.$tabel_j[0]['year'].', \''.$link.'\', \'add\', false, 2)">
                                            <input id="addDeployButton" type="button" class="b" value="Добавить и провести" onclick="fl_showPaidoutAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', '.$tabel_j[0]['worker_id'].', '.$tabel_j[0]['month'].', '.$tabel_j[0]['year'].', \''.$link.'\', \'add\', true, 2)">
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
                                        paidout_summ_tabel = $("#paidout_summ").attr("paidout_summ_tabel"),
                                        tabel_type = $("#tabel_type").val();
                                        paidout_type = $("#paidout_type").val();
                                    //console.log(tabel_type);
                                    
                                    //if (summ.length > 2) {
                                        tabelSubtractionPercent(tabel_id, tabel_type, paidout_type, summ, paidout_summ_tabel);
                                    //}
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