<?php

//abonement_edit.php
//Редактируем абонемент

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($spravka['edit'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';

            $abonement_j = SelDataFromDB('journal_abonement_solar', $_GET['id'], 'id');

            if ($abonement_j != 0){

                //Соберем данные по типам абонементов
                $abon_types_j = array();

                $msql_cnnct = ConnectToDB ();

                $query = "SELECT * FROM `spr_solar_abonements` WHERE `status` <> '9'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

                if ($number != 0) {
                    while ($arr = mysqli_fetch_assoc($res)) {
                        array_push($abon_types_j, $arr);
                    }
                }

                echo '
						<div id="status">
							<header>
							    <div class="nav">
                                    <a href="certificates.php" class="b">Абонементы</a>
                                </div>
								<h2>Редактировать абонемент <a href="abonement.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
							</header>';

                echo '
							<div id="data">';
                echo '
								<div id="errrror"></div>';
                echo '
								<form action="cert_edit_f.php">
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Номер</div>
                                        <div class="cellRight">
                                            <input type="text" name="num" id="num" value="'.$abonement_j[0]['num'].'">
                                            <label id="num_error" class="error"></label>
                                        </div>
                                    </div>

                                    <div class="cellsBlock2">
                                                <div class="cellLeft">Тип абонемента</div>
                                                <div class="cellRight">';
                if (!empty($abon_types_j)){
                    echo '
                                                    <table style="font-size: 80%;">
                                                        <tr>
                                                            <td style="text-align: center; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            Название
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            минут
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            Срок действ. (дней)
                                                        </td>
                                                            <td style="text-align: center; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            Стоим. (руб.)
                                                            </td>
                                        <!--                    <td>
                                                                Стоим. 1 мин.
                                                            </td>-->
                                                        </tr>';
                    foreach ($abon_types_j as $ab_type){

                        $checked = '';

                        if ($ab_type['id'] == $abonement_j[0]['abon_type']){
                            $checked = ' checked';
                        }

                        echo '
                                                        <tr class="cellsBlockHover">
                                                            <td style="border: 1px solid rgba(102, 102, 102, 0.38);">
                                                                <input id="abon_type" name="abon_type" value="'.$ab_type['id'].'" type="radio" '.$checked.'>'.$ab_type['name'].'
                                                        </td>
                                                            <td style="text-align: right; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            '.$ab_type['min_count'].'
                                                        </td>
                                                            <td style="text-align: right; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            '.$ab_type['exp_days'].'
                                                        </td>
                                                            <td style="text-align: right; border: 1px solid rgba(102, 102, 102, 0.38);">
                                                            '.$ab_type['summ'].'
                                                        </td>
                                                            <!--<td>
                                                                -
                                                            </td>-->
                                                        </tr>';
                    }
                    echo '
                                                    </table>';
                }


                echo '</div></div>';
                echo '
                                    <div id="errror"></div> ';
                if ($abonement_j[0]['cell_time'] == '0000-00-00 00:00:00'){
                    echo '<input type="button" class="b" value="Применить" onclick="showAbonAdd('.$_GET['id'].', \'edit\')">';
                }else {
                    echo '<i style="color:red;">Абонемент уже продан. Редактировать нельзя.</i><br>';
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