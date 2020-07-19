<?php

//fl_percent_cat_edit.php
//Редактировать категорию процентов

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['add_new'] == 1) || $god_mode){

        include_once 'DBWork.php';

        $percent_j = SelDataFromDB('fl_spr_percents', $_GET['id'], 'id');
        //var_dump($percent_j);

        if ($percent_j != 0){

            $permissions_j = SelDataFromDB('spr_permissions', '', '');
            //var_dump($permissions_j);

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="fl_percent_cats.php" class="b">Категории процентов</a>
                        </div>
                        <h2>Редактировать Категорию процентов <a href="fl_percent_cat.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '
                        <div id="errrror"></div>';
            echo '
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Название</div>
                                <div class="cellRight">
                                    <input type="text" name="cat_name" id="cat_name" value="'.$percent_j[0]['name'].'"> <div style="float: right; width: 20px; height: 20px; background-color: rgb('.$percent_j[0]['color'].'); border: 1px solid grey;"></div>
                                    <label id="cat_name_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Процент за работу (общий)</div>
                                <div class="cellRight">
                                    <input type="text" name="work_percent" id="work_percent" value="'.$percent_j[0]['work_percent'].'">
                                    <label id="work_percent_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Процент за материал (общий)</div>
                                <div class="cellRight">
                                    <input type="text" name="material_percent" id="material_percent" value="'.$percent_j[0]['material_percent'].'">
                                    <label id="material_percent_error" class="error"></label>
                                </div>
                            </div>

                            <div class="cellsBlock2">
                                <div class="cellLeft">Спец. цена фиксированная, руб.</div>
                                <div class="cellRight">
                                    <input type="text" name="summ_special" id="summ_special" value="'.$percent_j[0]['summ_special'].'">
                                    <label id="summ_special_error" class="error"></label>
                                </div>
                            </div>';

            echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Персонал (тип)</div>
                                <div class="cellRight">';
            echo '
                                <select name="personal_id" id="personal_id">
                                    <option value="0">Нажмите, чтобы выбрать</option>';

            if ($permissions_j != 0){
                for ($i=0; $i < count($permissions_j); $i++){
                    if ($permissions_j[$i]['id'] == $percent_j[0]['type']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }

                    //стом, косм, спец, ассист
                    if (($permissions_j[$i]['id'] == 5) || ($permissions_j[$i]['id'] == 6) || ($permissions_j[$i]['id'] == 7) || ($permissions_j[$i]['id'] == 10)) {
                        echo "<option value='" . $permissions_j[$i]['id'] . "' $selected>" . $permissions_j[$i]['name'] . "</option>";
                    }
                }
            }

            echo '
                                </select>
                                    <label id="personal_id_error" class="error"></label>
                                </div>
                            </div>';

            echo '					
                            <div id="errror"></div>                        
                            <input type="button" class="b" value="Применить" onclick="Ajax_cat_add('.$_GET['id'].', \'edit\')">';

            echo '
                    </div>
                </div>';
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