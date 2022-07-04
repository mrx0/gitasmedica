<?php

//individuals2_add.php
//Добавить работу с админами

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['see_all'] == 1) || $god_mode){

        include_once 'DBWork.php';
        include_once 'functions.php';

        include_once 'ffun.php';

        require 'variables.php';

        $day = date('d');
        $month = date('m');
        $year = date('Y');

        $filial_id = 15;

        if (isset($_SESSION['filial_id'])){
            $filial_id = $_SESSION['filial_id'];
        }

        //$filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        //$db = new DB();

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="individuals2.php" class="b">Работа с админами</a>
                        </div>
                        <h2>Добавить работу с админами';

        echo '
                      </h2>
                        <!--Заполните поля-->
                        <input type="hidden" id="task_id" value="0">
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
                                    <span style="font-size:80%;  color: #555;">Дата</span><br>
                                    <input type="text" id="iWantThisDate" name="iWantThisDate" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">';
        echo '
			                        </select>
			                    </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">ФИО сотрудника</span><br>
                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                    <ul id="search_result2" class="search_result2"></ul><br />
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">План работы</span><br>
                                    <textarea name="plan_text" id="plan_text" cols="60" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Количество принятых звонков</span><br>
                                    <input type="number" size="2" name="rings_count" id="rings_count" min="0" max="99" value="0">
                                 </div>
                            </div>                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Замечание по звонку</span><br>
                                    <textarea name="rings_review_text" id="rings_review_text" cols="60" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Работа с  пациентами в холле</span><br>
                                    <textarea name="work_w_patients_text" id="work_w_patients_text" cols="60" rows="3"></textarea>
                                </div>
                            </div>
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Коррекция ошибок</span><br>
                                    <textarea name="error_correction_text" id="error_correction_text" cols="60" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Статистика звонков (результат)</span><br>
                                    <textarea name="ring_stat_text" id="ring_stat_text" cols="60" rows="3"></textarea>
                                </div>
                            </div>';


        echo '                    
                            <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showAddFunction(\'individuals2\',\'add\')">
                            </div>
                        </form>';

        echo '
                    </div>
                </div>';

        echo '

				<script type="text/javascript">
				    //Проверка и установка checkbox
//					let status = 0;
//					
//					$("input[name=status]").change(function() {
//						status = $("input[name=status]:checked").val();
//						
//						if (status === undefined){
//							status = 0;
//						}
//					});
				</script>';

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }

}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>