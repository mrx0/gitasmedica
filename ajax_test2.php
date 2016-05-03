<?php

//
//

//var_dump($_POST);

$error_text = '';


// массив для хранения ошибок
$errorContainer = array();
// полученные данные
if (isset($_POST['smena1']) && ($_POST['smena1'] == 1)){
	if (isset($_POST['smena2']) && ($_POST['smena2'] == 1)){
		$smena = 9;
	}else{
		$smena = 1;
	}
}elseif(isset($_POST['smena2']) && ($_POST['smena2'] == 1)){
	$smena = 2;
}else{
	$smena = 0;
}

$arrayFields = array(
    'worker' => $_POST['worker'],
    'smena' => $smena,
);
 
// проверка всех полей на пустоту
foreach($arrayFields as $fieldName => $oneField){
    if($oneField == '' || !isset($oneField) || ($oneField == '0')){
		if ($fieldName == 'smena'){
			$error_text = '<span style="color: red">Не выбрали смену</span>';
		}
		if ($fieldName == 'worker'){
			$error_text = '<span style="color: red">Не выбрали врача</span>';
		}
		$errorContainer[$fieldName] = $error_text;
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