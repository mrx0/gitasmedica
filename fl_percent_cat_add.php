<?php

//fl_percent_cat_add.php
//Добавить категорию процентов

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['add_new'] == 1) || $god_mode){

        include_once 'DBWork.php';

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="fl_percent_cats.php" class="b">Категории процентов</a>
                        </div>
                        <h2>Добавить категорию процентов</h2>
                        Заполните поля
                    </header>';

        echo '
                    <div id="data">';
        echo '
                        <div id="errrror"></div>';
        echo '
                        <form action="cert_add_f.php">
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Название</div>
                                <div class="cellRight">
                                    <input type="text" name="cat_name" id="cat_name" value="">
                                    <label id="cat_name_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Процент за работу (общий)</div>
                                <div class="cellRight">
                                    <input type="text" name="work_percent" id="work_percent" value="">
                                    <label id="work_percent_error" class="error"></label>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Процент за материал (общий)</div>
                                <div class="cellRight">
                                    <input type="text" name="material_percent" id="material_percent" value="">
                                    <label id="material_percent_error" class="error"></label>
                                </div>
                            </div>';

        echo '					
                            <div class="cellsBlock2">
                                <div class="cellLeft">Персонал</div>
                                <div class="cellRight">';
        echo '
                                <select name="personal_id" id="personal_id">
                                    <option value="0">Нажмите, чтобы выбрать</option>';

        $permissions_j = SelDataFromDB('spr_permissions', '', '');
        //var_dump($permissions_j);

        if ($permissions_j != 0){
            for ($i=0;$i<count($permissions_j);$i++){
                //не админ и не директор
                if ($permissions_j[$i]['id'] > 2) {
                    echo "<option value='" . $permissions_j[$i]['id'] . "'>" . $permissions_j[$i]['name'] . "</option>";
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
                            <input type="button" class="b" value="Добавить" onclick="Ajax_cat_add(\'add\')">
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