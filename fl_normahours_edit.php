<?php

//fl_normahours_edit.php
//Редактировать норму

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['add_new'] == 1) || $god_mode){

        include_once 'DBWork.php';

        $normahours_j = SelDataFromDB('fl_spr_normahours', $_GET['id'], 'id');
        //var_dump($normahours_j);

        if ($normahours_j != 0){

            $permission_j = SelDataFromDB('spr_permissions', $normahours_j[0]['type'], 'id');
            //var_dump($permission_j);

            echo '
                <div id="status">
                    <header>
                        <div class="nav">
                             <a href="fl_normahours.php" class="b">Нормы часов</a>
                        </div>
                        <h2>Редактировать Норму часов <a href="normahours_item.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '
                        <div id="errrror"></div>';
            echo '
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Должность</div>
                                <div class="cellRight">
                                   '.$permission_j[0]['name'].'
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Норма</div>
                                <div class="cellRight">
                                    <input type="text" name="norma_hours" id="norma_hours" value="'.$normahours_j[0]['count'].'">
                                    <label id="norma_hours_error" class="error"></label>
                                </div>
                            </div>';

            echo '					
                            <div id="errror"></div>                        
                            <input type="button" class="b" value="Применить" onclick="Ajax_normahours_add('.$_GET['id'].', \'edit\')">';

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