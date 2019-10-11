<?php 

//update_proizvcalendar_f.php
//Обновление производственного календаря

// Файл качаем отсюда и подкидыываем в программу
//https://data.gov.ru/opendata/7708660670-proizvcalendar

	session_start();

	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
            include_once 'DBWork.php';
            include_once 'functions.php';

            if (!isset($_POST['name']) || ($_POST['name'] == '')){
                echo 'Что-то пошло не так.<br><br>';
            }else {
                //var_dump($_POST);
                //var_dump(csv_to_array('uploads/'.$_POST['name']));

                $csv_data = csv_to_array('uploads/'.$_POST['name']);

                foreach ($csv_data as $data){
                    var_dump($data);

                    //Если год текущий или будущий
                    if ($data['Год/Месяц'] >= date('Y', time())){
                        $year = $data['Год/Месяц'];
                        //var_dump($year);

//                        $january_holidays_str =
//                        $february_holidays_str =
//                        $march_holidays_str =
//                        $april_holidays_str =
//                        $may_holidays_str =
//                        $june_holidays_str =
//                        $july_holidays_str =
//                        $august_holidays_str =
//                        $september_holidays_str =
//                        $october_holidays_str =
//                        $november_holidays_str =
//                        $december_holidays_str =


                    }
                }
            }

		}
	}
?>