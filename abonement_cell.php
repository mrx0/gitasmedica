<?php

//abonement_cell.php
//Продать абонемент

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($finances['add_new'] == 1) || ($finances['add_own'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $filials_j = getAllFilials(true, false, false);
            //var_dump($filials_j);

            $abonement_j = SelDataFromDB('journal_abonement_solar', $_GET['id'], 'id');
            //var_dump($abonement_j);

            if ($abonement_j != 0){

                $abon_type_name = '';

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT `name` FROM `spr_solar_abonements` WHERE `id` = '{$abonement_j[0]['abon_type']}' LIMIT 1";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    $arr = mysqli_fetch_assoc($res);

                    $abon_type_name = $arr['name'];
                }
                //var_dump($abon_types_j);

                echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="abonements.php" class="b">Абонементы</a>
                                </div>
								<h2>
									Продать абонемент <a href="abonement.php?id='.$abonement_j[0]['id'].'" class="ahref">#'.$abonement_j[0]['id'].'</a>';


                echo '
								</h2>';

                 echo '
							</header>';

                echo '
							<div id="data">';

                if (isset($_SESSION['filial'])){
                    echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Номер</div>
									<div class="cellRight">'.$abonement_j[0]['num'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$abon_type_name.'</div>
								</div>';

                    echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Срок годности (дней)</div>
                                    <div class="cellRight">
                                        '.$abonement_j[0]['exp_days'].'
                                    </div>
                                </div>';

                    echo '
                                <div class="cellsBlock2">
                                    <div class="cellLeft">Всего минут</div>
                                    <div class="cellRight">
                                        '.$abonement_j[0]['min_count'].'
                                    </div>
                                </div>';

                    echo '								
								<div class="cellsBlock2">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight">';

                    //$offices_j = SelDataFromDB('spr_filials', $_SESSION['filial'], 'offices');

                    if (!empty($filials_j)) {
                        echo $filials_j[$_SESSION['filial']]['name'].'
                                <input type="hidden" id="filial_id" name="filial_id" value="'.$_SESSION['filial'].'">';
                    }

                    echo '</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">
									    Способ оплаты<br>
									</div>
									<div class="cellRight">
									    <input id="summ_type" name="summ_type" value="1" type="radio" checked> Наличный<br>
                                        <input id="summ_type" name="summ_type" value="2" type="radio"> Безналичный
									</div>
								</div>';

                    $day = date('d');
                    $month = date('m');
                    $year = date('Y');

                    if (($finances['see_all'] == 1) || $god_mode){

                        echo '
                                <div class="cellsBlock2">
									<div class="cellLeft">
                                        Дата продажи<br><span style="font-size: 70%;">если админы забыли продать вовремя</span>
									</div>
									<div class="cellRight">
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
									    Цена продажи(руб.)<br>
									    <span style="font-size: 70%">если не соответствует номиналу</span>
									</div>
									<div class="cellRight">
									    <input type="text" name="cell_price" id="cell_price" value="'.$abonement_j[0]['summ'].'">
                                        <label id="cell_price_error" class="error"></label>
									</div>
								</div>
                                <div id="errror"></div>   
                                <input type="button" class="b" value="Продать" onclick="showAbonCell('.$abonement_j[0]['id'].')">';

                }else{
                    echo '
								<span style="font-size: 85%; color: #FF0202; margin-bottom: 5px;"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="font-size: 120%;"></i> У вас не определён филиал <i class="ahref change_filial">определить</i></span><br>';
                }
            echo '				
							</div>
                            <!-- Подложка только одна -->
                            <div id="overlay"></div>';

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