<?php
	
//enter.php
//Форма входа на сайт
	
	require_once 'header.php';
		
	echo '
		<h1>Вы вошли как гость. <br />Войдите в систему, используя логин и пароль.</h1>
		<form action="testreg.php" method="post">
		<!--****  testreg.php - это адрес обработчика. То есть, после нажатия на кнопку  "Войти", данные из полей отправятся на страничку testreg.php методом  "post" ***** -->
			<div class="cellsBlock2">
				<div class="cellLeft">Логин:</div>
				<div class="cellRight"><input name="login" type="text" size="15" maxlength="15"></div>
				<!--**** В текстовое поле (name="login" type="text") пользователь вводит свой логин ***** -->
			</div>
			<div class="cellsBlock2">
				<div class="cellLeft">Пароль:</div>
				<div class="cellRight"><input name="password" type="password" size="15" maxlength="15"></div>
				<!--**** В поле для паролей (name="password" type="password") пользователь вводит свой пароль ***** --> 
			</div>
			<input type="submit" name="submit" value="Войти">
			<!--**** Кнопочка (type="submit") отправляет данные на страничку testreg.php ***** --> 
		</form>';
	
	require_once 'footer.php';
	
?>