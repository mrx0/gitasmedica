<?php

// массив для хранения ошибок
$errorContainer = array();
// полученные данные
$arrayFields = array();

if (isset($_POST['client']))
    $arrayFields['client'] = $_POST['client'];
if (isset($_POST['worker']))
    $arrayFields['worker'] = $_POST['worker'];
if (isset($_POST['filial']))
    $arrayFields['filial'] = $_POST['filial'];
if (isset($_POST['name']))
    $arrayFields['name'] = $_POST['name'];
if (isset($_POST['age']))
    $arrayFields['age'] = $_POST['age'];
if (isset($_POST['summ']))
    $arrayFields['summ'] = $_POST['summ'];
if (isset($_POST['admSettings'])){
	//var_dump($_POST);
	foreach($_POST['admSettings'] as $key => $value){
		$arrayFields[$key] = $value;
	}
}

//Проверка при регистрации нового клиента или его редактировании
if (isset($_POST['fname']))
    $arrayFields['fname'] = $_POST['fname'];
if (isset($_POST['iname']))
    $arrayFields['iname'] = $_POST['iname'];
if (isset($_POST['oname']))
    $arrayFields['oname'] = $_POST['oname'];
if (isset($_POST['sel_date']))
    $arrayFields['sel_date'] = $_POST['sel_date'];
if (isset($_POST['sel_month']))
    $arrayFields['sel_month'] = $_POST['sel_month'];
if (isset($_POST['sel_year']))
    $arrayFields['sel_year'] = $_POST['sel_year'];
if (isset($_POST['sex']))
    $arrayFields['sex'] = $_POST['sex'];

// проверка всех полей на пустоту
foreach($arrayFields as $fieldName => $oneField){
	
    if($oneField == '' || !isset($oneField) || ($oneField == '0')){
        $errorContainer[$fieldName] = 'В этом поле ошибка';
    }
	
	if (isset($_POST['summ'])){
		if (!is_numeric($oneField)) {
			$errorContainer[$fieldName] = 'В этом поле ошибка';
		}
		if ($oneField <= 0){
			$errorContainer[$fieldName] = 'В этом поле ошибка';
		}
	}
	
	if (isset($_POST['admSettings'])){
		if (!is_numeric($oneField)) {
			$errorContainer[$fieldName] = 'В этом поле ошибка';
		}
		if ($oneField <= 0){
			$errorContainer[$fieldName] = 'В этом поле ошибка';
		}
	}
}
 /*
// сравнение введенных паролей
if($arrayFields['password_user'] != $arrayFields['password_2_user'])
    $errorContainer['password_2_user'] = 'Пароли не совпадают';
 */
// делаем ответ для клиента
if(empty($errorContainer)){
    // если нет ошибок сообщаем об успехе
    echo json_encode(array('result' => 'success'));
}else{
    // если есть ошибки то отправляем
    echo json_encode(array('result' => 'error', 'text_error' => $errorContainer));
}

?>