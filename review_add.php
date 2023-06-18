<?php

//review_add.php
//Добавить отзыв

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

        $filial_id = 16;

        if (isset($_SESSION['filial_id'])){
            $filial_id = $_SESSION['filial_id'];
        }

        $filials_j = getAllFilials(false, false, false);
        //var_dump($filials_j);

        //$db = new DB();

        echo '
                <div id="status">
                    <header>
                        <div class="nav">
                            <a href="reviews.php" class="b">Отзывы</a>
                        </div>
                        <h2>Добавить отзыв';

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
                                    <span style="font-size:80%;  color: #555;">Дата</span><br>
                                    <input type="text" id="iWantThisDate" name="iWantThisDate" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">';

        echo '
			                        </select>
			                    </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">ФИО врача</span><br>
                                    <input type="text" size="30" name="searchdata2" id="search_client2" placeholder="Введите ФИО" value="" class="who2"  autocomplete="off" style="width: 90%;">
                                    <ul id="search_result2" class="search_result2"></ul><br />
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
                                    <span style="font-size:80%;  color: #555;">Отзыв</span><br>
                                    <textarea name="review_text" id="review_text" cols="60" rows="5"></textarea>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Добавил</span><br>
                                    <input type="text" size="30" name="searchdata3" id="search_client3" placeholder="Введите ФИО" value="" class="who3"  autocomplete="off" style="width: 90%;">
                                    <ul id="search_result3" class="search_result3"></ul><br />
                                </div>
                            </div>
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Сайты</span><br>
                                    <textarea name="sites" id="sites" cols="60" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">
                                    <span style="font-size:80%;  color: #555;">Добавлен</span><br>
                                    <input type="checkbox" name="status" value="1"> <span style="font-size:80%;"></span>
                                </div>
                            </div>';


        echo '                    
                            <div id="errror"></div>
                                <input type="button" class="b" value="Добавить" onclick="fl_showReviewAdd(\'add\')">
                            </div>
                        </form>';

        echo '
                    </div>
                </div>';

        echo '

				<script type="text/javascript">
				    //Проверка и установка checkbox
					let status = 0;
					
					$("input[name=status]").change(function() {
						status = $("input[name=status]:checked").val();
						
						if (status === undefined){
							status = 0;
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