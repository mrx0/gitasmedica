<?php
	
//auth.php
//	
	
    session_start();

	/*unset($_SESSION['journal_tooth_status_temp']);
	unset($_SESSION['permissions']);
	unset($_SESSION['filial']);
    unset($_SESSION['calculate_data']);*/

    unset($_SESSION['login']);
    unset($_SESSION['name']);
    unset($_SESSION['id']);
    unset($_SESSION['journal_tooth_status_temp']);
    unset($_SESSION['permissions']);
    unset($_SESSION['filial']);
    unset($_SESSION['invoice_data']);
    unset($_SESSION['calculate_data']);
    unset($_SESSION['fl_calcs_tabels']);
	
	//вся процедура работает на сессиях. Именно в ней хранятся данные  пользователя, пока он находится на сайте. Очень важно запустить их в  самом начале странички!!!
	if (isset($_POST['login'])){
		$login = $_POST['login']; 
		if ($login == ''){
			unset($login);
		}
	} 
	
	//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
    if (isset($_POST['password'])){
		$password = $_POST['password']; 
		if ($password == ''){
			unset($password);
		}
	}
	
    //заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
	if (empty($login) or empty($password)){ 
		//если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
		exit (json_encode(array('result' => 'error', 'data' => 'Не ввели логин и пароль')));
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
			exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
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
					$_SESSION['options'] = array();
					//эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь
					
					//!!!если пользователь определенной группы, костыль!!!
					if (($_SESSION['permissions'] != 4) && ($_SESSION['permissions'] != 7)){
						//логирование
						AddLog (GetRealIp(), $_SESSION['id'], '', 'Пользователь вошёл в систему');
						
						exit (json_encode(array('result' => 'success', 'data' => 'Вы успешно вошли в систему<br>и будете перенаправлены на <a href="index.php">главную</a>')));
					
					}else{
						
						$rez_data = '';
						$office_ch = false;
						
						if (isset($_POST['office'])){
							if ($_POST['office'] > 0){
								$office_ch = true;
							}
						}
						
						if ($office_ch){
							$_SESSION['filial'] = $_POST['office'];
							exit (json_encode(array('result' => 'success', 'data' => 'Вы успешно вошли в систему<br>и будете перенаправлены на <a href="index.php">главную</a>')));
						}else{
							$offices = SelDataFromDB('spr_filials', '', '');
							
							if ($offices != 0){
							
								$rez_data .= '
								Выберите филиал, на котором вы сегодня работаете.
								<br><br>
									<select name="office" id="office">
										<option value="0" selected>Выберите филиал</option>';
								for ($i=1;$i<count($offices);$i++){
									$rez_data .= "<option value='".$offices[$i]['id']."'>".$offices[$i]['name']."</option>";
								}
								$rez_data .= '
									</select>';
							
								exit (json_encode(array('result' => 'office', 'data' => $rez_data)));
								
							}else{
								exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
							}
						}
					}
				}else{
					//если пароли не сошлись
					exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
				}
			}else{
				//если звёзды не сошлись
				exit (json_encode(array('result' => 'error', 'data' => 'Нельзя пользоваться программой, если вас уволили')));
			}
		}
	}else{
			exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
	}
	
?>