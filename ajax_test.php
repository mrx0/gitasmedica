<?php

// массив для хранения ошибок
$errorContainer = array();
// полученные данные
$arrayFields = array(
    'client' => $_POST['client'],
    'filial' => $_POST['filial'],
);
 
// проверка всех полей на пустоту
foreach($arrayFields as $fieldName => $oneField){
    if($oneField == '' || !isset($oneField) || ($oneField == '0')){
        $errorContainer[$fieldName] = '<span style="color: red">Поле обязательно для заполнения</span>';
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