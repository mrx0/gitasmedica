<?php

//solar_add.php
//Солярий добавить

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        include_once 'ffun.php';

        require 'variables.php';

        $filial_id = 15;

        if (isset($_POST['filial_id'])){
            $filial_id = $_POST['filial_id'];
        }else{
            if (isset($_SESSION['filial_id'])){
                $filial_id = $_SESSION['filial_id'];
            }
        }

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        //Виды соляриев
        $solar_devices_j = array(
            array(
                    'id' => 1,
                    'name' => 'Вертикальный Luxura V7',
                    'min_price' => 35
            ),
            array(
                'id' => 1,
                'name' => 'Вертикальный Luxura V5',
                'min_price' => 30
            ),
            array(
                'id' => 1,
                'name' => 'Горизонтальный X7',
                'min_price' => 30
            )
        );
        //var_dump($solar_devices_j);

        $day = date('d');
        $month = date('m');
        $year = date('Y');

        $msql_cnnct = ConnectToDB ();

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <!--<a href="fl_tabels.php" class="b">Важный отчёт</a>-->
                            <a href="zapis_solar.php" class="b">Обратно</a>
                        </div>
                        <h2>Добавить посещение солярия ';

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

        if (($finances['see_all'] == 1) || $god_mode){

            echo '
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:85%;  color: #555;">Дата<br><span style="font-size: 80%">если админы забыли отметить вовремя</span></span><br><br>';

            echo '
                                    <input type="text" id="iWantThisDate2" name="iWantThisDate2" class="dateс" style="border:none; color: rgb(30, 30, 30); font-weight: bold;" value="'.date($day.'.'.$month.'.'.$year).'" onfocus="this.select();_Calendar.lcs(this)" 
                                        onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)"> 
                                </div>
                            </div>';

        }else{
            echo '
                            <input type="hidden" id="iWantThisDate2" value="'.date($day.'.'.$month.'.'.$year).'">';
        }



        echo '
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Тип солярия</span><br>
                                    <select name="selectDeviceType" id="selectDeviceType">';

        foreach ($solar_devices_j as $device_item){
            echo '
                                    
                                        <option value="'.$device_item['id'].'" min_price="'.$device_item['min_price'].'">'.$device_item['name'].'</option>';

        }
        echo '
                                    </select>';

        echo '
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                <span style="font-size:80%;  color: #555;">Кол-во минут</span><br>
                                    <input type="text" id="min_count" value="0" class="paidout_summ2" autocomplete="off" autofocus>
                                    <label id="min_count_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Оплата</span><br>
                                    <input id="summ_type" name="summ_type" value="1" type="radio" checked> Наличный<br>
                                    <input id="summ_type" name="summ_type" value="2" type="radio"> Безналичный<br>
                                    <input id="summ_type" name="summ_type" value="3" type="radio"> Абонемент
                                </div>
                            </div>';

        echo '
                            <div id="selectAbonementBlock" class="cellsBlock2" style="display: none;">
                                <div class="cellRight">
                                    <ul style="">
                                        <li style="color: #555; font-size:80%;">
                                            По абонементу
                                        </li>
                                        <li style="margin-bottom: 5px;" id="showAbonPayAdd_button">
                                            <input type="button" class="b" value="Добавить абонемент" onclick="showAbonPayAdd()">
                                        </li>
                                        <li style="margin-bottom: 5px;">
                                            <table id="abons_result" width="100%" border="0" class="tableInsStat" style="background-color: rgba(255,255,250, .7); color: #333; display: none;">
                                                <tr>
                                                    <td><span class="lit_grey_text">Номер</span></td>
                                                    <td><span class="lit_grey_text">Всего минут</span></td>
                                                    <td><span class="lit_grey_text">Осталось</span></td>
                                                    <td style="text-align: center;"><i class="fa fa-times" aria-hidden="true" title="Удалить"></i></td>
                                                </tr>
                                            </table>
                                            
                                        </li>
                                    </ul>
                                </div>
                            </div>';

        echo '            
                            <div id="oneMinPriceBlock" class="cellsBlock2">
                                <div class="cellLeft">
                                <span style="font-size:80%;  color: #555;">Цена 1 мин. (руб.)</span><br>
                                    <div id="oneMinPrice"></div>
                                </div>
                            </div>
                            
                            <div id="finPriceBlock" class="cellsBlock2">
                                <div class="cellLeft">
                                <span style="font-size:80%;  color: #555;">Стоимость (руб.)</span><br>
                                    <div id="finPrice" class="calculateOrder"></div>
                                </div>
                            </div>
                            
                            <!--<div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Филиал</span><br>
                                    <select name="SelectFilial" id="SelectFilial">';

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
                            </div>-->
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Комментарий</span><br>
                                    <textarea name="descr" id="descr" cols="60" rows="5"></textarea>
                                </div>
                            </div>';

        echo '                    
                            <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showSolarAdd()">
                                <!--fl_showPaidoutAnotherAdd-->
                            </div>
                        </form>';

        echo '
                    </div>
                </div>';

        echo '
                <div id="search_abon_input" style="display: none;">
                    <input type="text" size="30" name="searchdata" id="search_abon" placeholder="Наберите номер абонемента для поиска" value="" class="who"  autocomplete="off" style="width: 90%;">
                    <br><span class="lit_grey_text" style="font-size: 75%">Нажмите на галочку, чтобы добавить</span>
                    <div id="search_result_abon" class="search_result_abon" style="text-align: left;"></div>
                </div>
                
                <!-- Подложка только одна -->
                <div id="overlay"></div>
                
                <script>
                
                    $(document).ready(function(){
                        //console.log($("#selectDeviceType").val());
                        
                        //Получаем значение аттрибута в select
                        var min_price = $("option:selected", $("#selectDeviceType")).attr("min_price");
                        //console.log(min_price);
                        
                        //Цена за 1 минуту
                        $("#oneMinPrice").html(min_price);
                        
                        var min_count = $("#min_count").val();
                        //console.log(min_count);
                        
                        $("#finPrice").html(min_price * min_count);
                        

                    });
                    

                    //Изменение типа солярия
                    $("#selectDeviceType").change(function(){
                        
                        blockWhileWaiting (true);
                        
                        //Получаем значение аттрибута в select
                        var min_price = $("option:selected", this).attr("min_price");
                        //console.log( min_price);
                        //console.log($(this).val());
                        
                        $("#oneMinPrice").html(min_price);
                        
                        var min_count = $("#min_count").val();
                        //console.log(min_count);
                        
                        $("#finPrice").html(min_price * min_count);
                        
                        blockWhileWaiting (false);
                        
                    });
                    //Потеря фокуса на кол-ве минут
                    $("#min_count").blur(function() {
                        //console.log($(this).val());
                        
                        var value = $(this).val();
                        //Если не число
                        if (isNaN(value)){
                            $(this).val(0);
                        }else{
                            if (value < 0){
                                $(this).val(value * -1);
                            }else{
                                if (value == ""){
                                    $(this).val(0);
                                }else{
                                    if (value === undefined){
                                        $(this).val(0);
                                    }else{
                                        //Всё норм с типами данных
                                        //console.log("Всё норм с типами данных")
                                    }
                                }
                            }
                        }
                        
                        //Получаем значение аттрибута в select
                        var min_price = $("option:selected", $("#selectDeviceType")).attr("min_price");
                        
                        $("#oneMinPrice").html(min_price);
                        
                        var min_count = $("#min_count").val();
                        //console.log(min_count);
                        
                        $("#finPrice").html(min_price * min_count);
                        
                    });
                    //Если действия над кол-вом минут
                    $("#min_count").bind("change keyup input click", function() {
                        if($(this).val().length > 0){
                            //console.log($(this).val().length);
                            
                            //меняем запятую на точку (разделитель)
                            $(this).val($(this).val().replace(\',\', \'.\'));
                            
                            if ($(this).val() == 0){
                                $(this).val("")
                            }
                            $("#finPrice").html(0);
                        }
                        if (!isNaN($(this).val())){
                            //console.log($(this).val());
                            //console.log($(this).val().length);
                            
                            //Получаем значение аттрибута в select
                            var min_price = $("option:selected", $("#selectDeviceType")).attr("min_price");
                            //console.log(min_price);
                            
                            if ($(this).val().length > 0){ 
                                
                                //console.log($(this).val().length);
                                
                                $("#oneMinPrice").html(min_price);
                                
                                var min_count = $("#min_count").val();
                                //console.log(min_count);
                                
                                $("#finPrice").html(min_price * min_count);
                            }else{
                                $("#finPrice").html(0);
                            }
                        }
                    });
                    //Изменение типа оплаты
                    $("input[id=summ_type]").change(function() {
                        //console.log($(this).val());
                        
                        if ($(this).val() == 3){
                            //console.log("Абонемент");
                            $("#selectAbonementBlock").show();
                            
                            $("#oneMinPriceBlock").hide();
                            $("#finPriceBlock").hide();
                        }else{
                            $("#selectAbonementBlock").hide();
                            
                            $("#oneMinPriceBlock").show();
                            $("#finPriceBlock").show();
                        }
                    });
                    
                    
            
                </script>';

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }

}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>