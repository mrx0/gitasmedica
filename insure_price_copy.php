<?php

//insure_price_copy.php
//Копировать прайс из одной страховой в другую

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($items['add_new'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			    $insure_j = SelDataFromDB('spr_insure', $_GET['id'], 'id');
                //var_dump($insure_j);
			
                if ($insure_j !=0){

                    require 'config.php';
                    mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
                    mysql_select_db($dbName) or die(mysql_error());
                    mysql_query("SET NAMES 'utf8'");

                    $arr = array();
                    $rez = array();

                    echo '
                        <div id="status">
                            <header>
                                <h2>Копирование прайса из <a href="insure.php?id='.$_GET['id'].'" class="ahref" style="color: green; font-size: 90%; font-weight: bold;">'.$insure_j[0]['name'].'</a> в другую</h2>
                            </header>
                            <a href="insure_price.php?id='.$_GET['id'].'" class="b">В прайс компании</a><br>';

                    echo '
                            <div id="data">';
                    echo '
                            <div id="errrror"></div>';

                    echo '
                                <div style="font-size: 85%; color: #FF0202; margin: 15px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i>
                                    При копировании, существующий прайс второй компании, <br>
                                    если он есть, будет стёрт.<br>
                                    Будьте внимательны.<br>
                                    Позиции и цены полностью копируются из исходного.<br><br>
                                    <span style="font-size: 120%; color: #7D7D7D; margin-bottom: 5px;">
                                        Выберите страховую,<br>
                                        куда хотите скопировать прайс.
                                    </span>
                                </div>
                    
                                <form action="insure_price_copy_f.php">';
                                echo '
                                            <select name="insurecompany" id="insurecompany">
                                                <option value="0">Выберите страховую</option>';
                    $insures_j = SelDataFromDB('spr_insure', '', '');

                    if ($insures_j != 0){
                        for ($i=0;$i<count($insures_j);$i++){

                            echo "<option value='".$insures_j[$i]['id']."'>".$insures_j[$i]['name']."</option>";
                        }
                    }
                    echo '
                                             </select>';

                    echo '				
                                    <input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
                                    <div id="errror"></div>
                                    <input type="button" class="b" value="Скопировать" onclick="Ajax_insure_price_copy('.$_GET['id'].')">';
                }


			    echo '				
							</form>';	
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