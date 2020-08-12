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

                $month_names = array(
                    "01" => "Январь",
                    "02" => "Февраль",
                    "03" => "Март",
                    "04" => "Апрель",
                    "05" => "Май",
                    "06" => "Июнь",
                    "07" => "Июль",
                    "08" => "Август",
                    "09" => "Сентябрь",
                    "10" => "Октябрь",
                    "11" => "Ноябрь",
                    "12" => "Декабрь"
                );

                $csv_data = csv_to_array('uploads/'.$_POST['name']);

                $msql_cnnct = ConnectToDB ();

                foreach ($csv_data as $data){
                    //var_dump($data);

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

                        //Удалим сначала всё с этого года и дальше
                        $query = "DELETE FROM `spr_proizvcalendar_holidays` WHERE `year` >= '$year'";
                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                        foreach ($month_names as $mon_num => $month){
//                            var_dump($mon_num);
//                            var_dump($month);
                            foreach(explode(',', $data[$month]) as $holiday_data){
                                //var_dump($holiday_data);

                                if (mb_strstr($holiday_data, '*') == FALSE){
                                    $query = "INSERT INTO `spr_proizvcalendar_holidays` (`year`, `month`, `day`) 
											VALUES (
											'{$year}', '".dateTransformation((int)$mon_num)."', '".dateTransformation((int)$holiday_data)."')";

                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                }
                            }
                        }



                    }
                }
            }

		}
	}
?>