<?php

//fl_createDailyReport.php
//Добавить ежедневный отчёт администратор

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';

        if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){

            include_once 'DBWork.php';

            echo '
                <div id="status">
                    <header>
                        <h2>Добавить ежедневный отчёт</h2>
                    </header>';

            echo '
                    <div id="data">';
            echo '				
                        <div id="errrror"></div>';
            echo '
                    
                            <div class="cellsBlock2">
                                <div class="cellLeft">Дата отчёта</div>
                                <div class="cellRight">
                                    <input type="text" id="datastart" name="datastart" class="dateс" value="'.date("d.m.Y").'" onfocus="this.select();_Calendar.lcs(this)"
												onclick="event.cancelBubble=true;this.select();_Calendar.lcs(this)">
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Z-отчёт</div>
                                <div class="cellRight">
                                    <input type="text" name="zreport" id="zreport" value="">
                                </div>
                            </div>
                            
                            <div class="cellsBlock2">
                                <div class="cellLeft">Контакты</div>
                                <div class="cellRight">
                                    <textarea name="contacts" id="contacts" cols="35" rows="5"></textarea>
                                </div>
                            </div>
    
                            <input type="button" class="b" value="Добавить" onclick="">';

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