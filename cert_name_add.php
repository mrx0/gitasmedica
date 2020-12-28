<?php

//cert_name_add.php
//Добавить сертификат именной

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    echo '
			<div id="status">
				<header>
					<div class="nav">
						<a href="certificates_name.php" class="b">Сертификаты именные</a>
					</div>
					<h2>Добавить Сертификат именной</h2>
					Заполните поля
				</header>';

    echo '
				<div id="data">';
    echo '
					<div id="errrror"></div>';
    echo '
					<form action="cert_add_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Номер</div>
							<div class="cellRight">
								<input type="text" name="num" id="num" value="">
								<label id="num_error" class="error"></label>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Сумма на счёт</div>
							<div class="cellRight">
								<input type="text" name="nominal" id="nominal" value="500">
								<label id="nominal_error" class="error"></label>
							</div>
						</div>
						
						<!--<div class="cellsBlock2">
							<div class="cellLeft">Срок годности (месяцев)</div>
							<div class="cellRight">
								<select name="expirationDate" id="expirationDate">
										&lt;!&ndash;<option value="3">3</option>&ndash;&gt;
										<option value="6">6</option>
										<option value="12">12</option>
							    </select>
							</div>
						</div>-->
						
						<div id="errror"></div>                        
						<input type="button" class="b" value="Добавить" onclick="showCertNameAdd(0, \'add\')">
					</form>';

    echo '
				</div>
			</div>';
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>