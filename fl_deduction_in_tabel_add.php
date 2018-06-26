<?php

//fl_deduction_in_tabel_add.php
//Добавить вычет к табелю

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

                if ($tabel_j != 0){

                    echo '
                            <div id="status">
                                <header>
                                    <div class="nav">
                                        <!--<a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="b">Вернуться в табель #'.$_GET['tabel_id'].'</a>-->
                                        <a href="fl_tabels.php" class="b">Важный отчёт</a>
                                    </div>
                                    <h2>Добавить удержание [';

                    if ($_GET['type'] == 2){
                        echo ' налог ';
                    }elseif ($_GET['type'] == 3){
                        echo ' штраф ';
                    }elseif ($_GET['type'] == 4){
                        echo ' ссуда ';
                    }elseif ($_GET['type'] == 5){
                        echo ' за обучение ';
                    }else {
                        echo ' за материалы ';
                    }

                    echo '
                                    ] из <a href="fl_tabel.php?id='.$_GET['tabel_id'].'" class="ahref">табеля #'.$_GET['tabel_id'].'</a></h2>
                                    Заполните поля
                                </header>';

                    echo '
                                <div id="data">';
                    echo '
                                    <div id="errrror"></div>';
                    echo '
                                    <form action="cert_add_f.php">
                                
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                            <span style="font-size:80%;  color: #555;">Сумма (руб.)</span><br>
                                                <input type="text" name="deduction_summ" id="deduction_summ" value="">
                                                <label id="deduction_summ_error" class="error"></label>
                                            </div>
                                        </div>
                                        
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                <span style="font-size:80%;  color: #555;">Комментарий</span><br>
                                                <textarea name="descr" id="descr" cols="60" rows="8"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div id="errror"></div>                        
                                        <input type="button" class="b" value="Добавить" onclick="fl_showDeductionAdd(0, '.$_GET['tabel_id'].', '.$_GET['type'].', \'add\')">
                                    </form>';

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