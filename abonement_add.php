<?php

//abonement_add.php
//Добавить абонемент

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    echo '
			<div id="status">
				<header>
					<div class="nav">
						<a href="abonements.php" class="b">Абонементы</a>
					</div>
					<h2>Добавить Абонемент</h2>
					<!--Заполните поля-->
				</header>';

    //Соберем данные по типам абонементов
    $abon_types_j = array();

    $msql_cnnct = ConnectToDB ();

    $query = "SELECT * FROM `spr_solar_abonements` WHERE `status` <> '9' ORDER BY `name`";

    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    $number = mysqli_num_rows($res);

    if ($number != 0) {
        while ($arr = mysqli_fetch_assoc($res)) {
            array_push($abon_types_j, $arr);
        }
    }
    //var_dump($abon_types_j);

    echo '
				<div id="data">';
    echo '
					<div id="errrror"></div>';
    echo '
					<form action="abonement_add_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Номер</div>
							<div class="cellRight">
								<input type="text" name="num" id="num" value="">
								<label id="num_error" class="error"></label>
							</div>
						</div>
						
						<div class="cellsBlock2" style="width: auto;">
							<div class="cellLeft">
							    Тип абонемента';
    if (($finances['see_all'] == 1) || $god_mode){
        echo '
							    <a href="abonement_type_add.php" class="b4">Добавить тип</a>';
    }

    echo '
							</div>
							<div class="cellRight">';
    if (!empty($abon_types_j)){
        echo '<table style="font-size: 80%;">
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
            echo '
                <tr class="cellsBlockHover">
                    <td style="border: 1px solid rgba(102, 102, 102, 0.38);">
                        <input id="abon_type" name="abon_type" value="'.$ab_type['id'].'" type="radio">'.$ab_type['name'].'
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
        echo '</table>';
    }
    echo '
							</div>
						</div>
						
<!--
						<div class="cellsBlock2">
							<div class="cellLeft">Срок годности (дней)</div>
							<div class="cellRight">
								<select name="expirationDate" id="expirationDate">
										<option value="3">30</option>
										<option value="6">45</option>
										<option value="12">60</option>
							    </select>
							</div>
						</div>
-->
						
						<div id="errror"></div>                        
						<input type="button" class="b" value="Добавить" onclick="showAbonAdd(0, \'add\')">
					</form>';

    echo '
				</div>
			</div>';
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>