<?php

//certificate.php
//карточка сертификата

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($spravka['see_all'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $cert_j = SelDataFromDB('journal_cert', $_GET['id'], 'id');
            //var_dump($cert_j);

            if ($cert_j != 0){
                echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="certificates.php" class="b">Сертификаты</a>
                                </div>
								<h2>
									Продать сертификат <a href="certificate.php?id='.$cert_j[0]['id'].'" class="ahref">#'.$cert_j[0]['id'].'</a>';


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
									<div class="cellRight">'.$cert_j[0]['num'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Номинал</div>
									<div class="cellRight">'.$cert_j[0]['nominal'].' руб.</div>
								</div>';
                    echo '								
								<div class="cellsBlock2">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight">';

                    $offices_j = SelDataFromDB('spr_office', $_SESSION['filial'], 'offices');
                    if ($offices_j != 0) {
                        echo $offices_j[0]['name'].'
                                <input type="hidden" id="office_id" name="office_id" value="'.$_SESSION['filial'].'">';
                    }

                    echo '</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">
									    Цена продажи(руб.)<br>
									    <span style="font-size: 70%">если не соответствует номиналу</span>
									</div>
									<div class="cellRight">
									    <input type="text" name="cell_price" id="cell_price" value="'.$cert_j[0]['nominal'].'">
                                        <label id="cell_price_error" class="error"></label>
									</div>
								</div>
                                <div id="errror"></div>   
                                <input type="button" class="b" value="Продать" onclick="showCertCell('.$cert_j[0]['id'].')">';

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