<?php

//cert_name_edit.php
//Редактируем именной сертификат

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($spravka['edit'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';

            $cert_j = SelDataFromDB('journal_cert_name', $_GET['id'], 'id');

            if ($cert_j != 0){
                echo '
						<div id="status">
							<header>
							    <div class="nav">
                                    <a href="certificates_name.php" class="b">Сертификаты именные</a>
                                </div>
								<h2>Редактировать сертификат <a href="certificate_name.php?id='.$_GET['id'].'" class="ahref">#'.$_GET['id'].'</a></h2>
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
                                            <input type="text" name="num" id="num" value="'.$cert_j[0]['num'].'">
                                            <label id="num_error" class="error"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Сумма на счёт</div>
                                        <div class="cellRight">
                                            <input type="text" name="nominal" id="nominal" value="'.$cert_j[0]['nominal'].'">
                                            <label id="nominal_error" class="error"></label>
                                        </div>
                                    </div>
                                    <div id="errror"></div> ';
                if ($cert_j[0]['cell_time'] == '0000-00-00 00:00:00'){
                    echo '<input type="button" class="b" value="Применить" onclick="showCertNameAdd('.$_GET['id'].', \'edit\')">';
                }else {
                    echo '<i style="color:red;">Сертификат уже выдан. Редактировать нельзя.</i><br>';
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