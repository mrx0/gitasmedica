<?php

//percent_cat_add.php
//Добавить категорию процентовок

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    echo '
			<div id="status">
				<header>
					<div class="nav">
						<a href="percent_cats.php" class="b">Категории процентовок</a>
					</div>
					<h2>Добавить категорию для процент</h2>
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
							<div class="cellLeft">Номинал</div>
							<div class="cellRight">
								<input type="text" name="nominal" id="nominal" value="">
								<label id="nominal_error" class="error"></label>
							</div>
						</div>
						
						<div id="errror"></div>                        
						<input type="button" class="b" value="Добавить" onclick="showCertAdd(0, \'add\')">
					</form>';

    echo '
				</div>
			</div>';
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>