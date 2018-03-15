<?php
	
//testreg.php
//	
	
    require_once 'header.php';
	require_once 'header_tags.php';
	
	unset($_SESSION['journal_tooth_status_temp']);
	unset($_SESSION['permissions']);
	unset($_SESSION['filial']);
	
	//вся процедура работает на сессиях. Именно в ней хранятся данные  пользователя, пока он находится на сайте. Очень важно запустить их в  самом начале странички!!!
	if (isset($_POST['login'])){
		$login = $_POST['login']; 
		if ($login == ''){
			unset($login);
		}
	} 
	//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
    if (isset($_POST['password'])){
		$password=$_POST['password']; 
		if ($password ==''){
			unset($password);
		}
	}
    //заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
	if (empty($login) or empty($password)){ 
	//если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
		exit ('<h1>Не ввели логин и пароль?</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
    }
    //если логин и пароль введены,то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
    $login = stripslashes($login);
    $login = htmlspecialchars($login);
	$password = stripslashes($password);
    $password = htmlspecialchars($password);
	//удаляем лишние пробелы
    $login = trim($login);
    $password = trim($password);

	include_once 'DBWork.php';
	$rezult = SelDataFromDB('spr_workers', $login, 'login');
	//извлекаем из базы все данные о пользователе с введенным логином
	//var_dump ($rezult);
    if ($rezult !=0){
		if (empty($rezult[0]['password'])){
			//если пользователя с введенным логином не существует
			exit ('<h1>Что-то пошло не так</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
		}else{
			//Если уволен - не пускать
			if ($rezult[0]['fired'] != '1'){
				//если существует, то сверяем пароли
				if ($rezult[0]['password'] == $password){
					//если пароли совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
					$_SESSION['login']=$rezult[0]['login']; 
					$_SESSION['id']=$rezult[0]['id'];
					$_SESSION['name']=$rezult[0]['name'];
					$_SESSION['permissions']=$rezult[0]['permissions'];
					//эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь
					
					//логирование
					AddLog (GetRealIp(), $_SESSION['id'], '', 'Пользователь вошёл в систему');
					
					if (($_SESSION['permissions'] != 4) && ($_SESSION['permissions'] != 7)){
						
						echo '<h1>Вы успешно вошли в систему!</h1><a href="index.php">Главная страница</a>
						<script type="text/javascript">
							setTimeout(function () {
							   window.location.href = "index.php";
							}, 1000);
						</script>';
					
					}else{
						$offices = SelDataFromDB('spr_filials', '', '');
						
						echo '<h1>Вы успешно вошли в систему!</h1>
						Выберите филиал, на котором вы сегодня работаете.
						<br>
						<br>
						
						<form action="testreg2.php" method="post">
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">
									<select name="filial" id="filial">
										<option value="0" selected>Выберите филиал</option>';
									if ($offices !=0){
										for ($i=1;$i<count($offices);$i++){
											echo "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
										}
									}
									echo '
									</select>
									<label id="filial_error" class="error"></label>
								</div>
							</div>
							<input type="submit" name="submit" value="Войти">
						</form>

						<script type="text/javascript">
							//setTimeout(function () {
							//   window.location.href = "index.php";
							//}, 1000);
						</script>';
					}
				}else{
					//если пароли не сошлись
					exit ('<h1>Что-то пошло не так</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
				}
			}else{
				//если пароли не сошлись
				exit ('<h1>Нельзя пользоваться программой, если вас уволили</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
			}
		}
	}else{
		exit ('<h1>Что-то пошло не так</h1><a href="enter.php">Вернуться и попытаться ещё</a>');
	}
	
	require_once 'footer.php';
	
?>