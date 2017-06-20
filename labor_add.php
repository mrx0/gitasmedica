<?php

//labor_add.php
//Добавить лабораторию

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    include_once 'DBWork.php';

    echo '
			<div id="status">
				<header>
					<h2>Добавить Лабораторию</h2>
					Заполните поля
				</header>';

    echo '
				<div id="data">';
    echo '				
					<div id="errrror"></div>';
    echo '
					<form action="labor_add_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">Название</div>
							<div class="cellRight">
								<input type="text" name="name" id="name" value="">
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">-</div>
							<div class="cellRight">
								<textarea name="contract" id="contract" cols="35" rows="5"></textarea>
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">Контакты</div>
							<div class="cellRight">
								<textarea name="contacts" id="contacts" cols="35" rows="5"></textarea>
							</div>
						</div>

						<input type="button" class="b" value="Добавить" onclick="Ajax_add_labor('.$_SESSION['id'].')">
					</form>';

    echo '
				</div>
			</div>';
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>