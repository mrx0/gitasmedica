<?php

//edit_zapis_change_client.php
//Сменить пациента в записи и всё, что ему создали

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($scheduler['see_all'] == 1) || $god_mode){
			if ($_GET){
                if ((isset($_GET['zapis_id'])) && (isset($_GET['client_id']))){
                    include_once 'DBWork.php';
                    include_once 'functions.php';

                    $edit_options = false;
                    $upr_edit = false;
                    $admin_edit = false;
                    $stom_edit = false;
                    $cosm_edit = false;
                    $finance_edit = false;

                    $sheduler_zapis = array();

                    $msql_cnnct = ConnectToDB ();

                    $query = "SELECT * FROM `zapis` WHERE `id`='".$_GET['zapis_id']."'";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                    $number = mysqli_num_rows($res);
                    if ($number != 0){
                        while ($arr = mysqli_fetch_assoc($res)){
                            array_push($sheduler_zapis, $arr);
                        }
                    }else
                        $sheduler_zapis = 0;

                    //var_dump ($sheduler_zapis);

                    if ($sheduler_zapis != 0){

                        echo '
                            <div id="status">
                                <header>
                                    <h2>Перенос записи и сопутствующих данных другому пациенту</h2>
                                </header>';
                        echo '
                                <span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> Данное действие невозможно будет отменить. Будут перенесены ВСЕ данные:<br>
                                    <b>Все оплаты нарядов будут сброшены</b><br>
                                    <b>Ордеры не переносятся!</b>
                                </span><br>';

                        echo '
                                <div id="data">';
                        echo '
                                <div id="errrror"></div>';


                        // !!! **** тест с записью
                        include_once 'showZapisRezult.php';

                        echo showZapisRezult($sheduler_zapis, $edit_options, $upr_edit, $admin_edit, $stom_edit, $cosm_edit, $finance_edit, 0, true, false);

                        echo '				
                                        <div class="cellsBlock2">
                                            <div class="cellLeft">
                                                Кому перенести<br />
                                            </div>
                                            <div class="cellRight">
                                                <input type="text" size="30" name="searchdata" id="search_client" placeholder="Введите ФИО пациента" value="" class="who"  autocomplete="off" style="width: 90%;"> 
                                                <ul id="search_result" class="search_result"></ul><br />
                                            </div>
                                        </div>';

                        echo '				
                                        <input type="hidden" id="zapis_id" name="zapis_id" value="'.$_GET['zapis_id'].'">
                                        <input type="hidden" id="client_id" name="client_id" value="'.$_GET['client_id'].'">
                                        <div id="errror"></div>
                                        <input type="button" class="b" value="Применить" onclick="Ajax_edit_zapis_change_client('.$_GET['zapis_id'].', '.$_GET['client_id'].')">
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