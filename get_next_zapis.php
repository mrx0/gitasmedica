<?php 

//get_next_zapis.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		include_once 'functions.php';
		//var_dump ($_POST);

		$data = array();
		$req = 0;
		$next_time_start = 0;
		$next_time_end = 0;

		if ($_POST){
			/*if ($_POST['datatable'] == 'scheduler_stom'){
				$datatable = 'zapis_stom';
			}elseif ($_POST['datatable'] == 'scheduler_cosm'){
				$datatable = 'zapis_cosm';
			}else{
				$datatable = 'zapis_stom';
			}*/

			$type = $_POST['type'];
			$day = $_POST['day'];
			$month = $_POST['month'];
			$year = $_POST['year'];
			$kab = $_POST['kab'];
			$start_time = $_POST['start_time'];
			$end_time = $_POST['start_time'] + $_POST['wt'];
			$filial = $_POST['filial'];

			if (!isset($_POST['zapis_id'])){
                $zapis_id = 0;
            }else {
                $zapis_id = $_POST['zapis_id'];
            }

            $msql_cnnct = ConnectToDB();

			//$query = "SELECT * FROM `zapis` WHERE `type` = '$type' AND `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `kab` = '$kab' AND `office` = '$filial' AND (`start_time` >= '$start_time' OR `start_time` < '$end_time') AND `enter` <> 8 AND `enter` <> 9 AND `id` <> '$zapis_id' ORDER BY `start_time` ASC LIMIT 1";

            if ($_POST['direction'] == "prev") {
                $query = "SELECT `id`, `start_time`, `wt` FROM `zapis` WHERE `type` = '$type' AND `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `kab` = '$kab' AND `office` = '$filial' AND `start_time` < '$start_time' AND `enter` <> 8 AND `enter` <> 9 AND `id` <> '$zapis_id' ORDER BY `start_time` ASC";
            }else {
                $query = "SELECT `id`, `start_time`, `wt` FROM `zapis` WHERE `type` = '$type' AND `day` = '$day' AND `month` = '$month' AND `year` = '$year' AND `kab` = '$kab' AND `office` = '$filial' AND `start_time` >= '$start_time' AND `enter` <> 8 AND `enter` <> 9 AND `id` <> '$zapis_id' ORDER BY `start_time` ASC";
            }

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

			$number = mysqli_num_rows($res);
			if ($number != 0){
				while ($arr = mysqli_fetch_assoc($res)){
					array_push($data, $arr);
				}
				$req = 1;
			}else{
				$req = 0;
			}
			//mysql_close();
			if ($req != 0){
                if ($_POST['direction'] == "prev") {

                    $next_time_start = 0;
                    $next_time_end = 0;

                    $idz = 0;

                    foreach ($data as $Dannie){
                        if ($Dannie['start_time'] + $Dannie['wt'] > $next_time_start){
                            $next_time_start = $Dannie['start_time'];
                            $next_time_end = $Dannie['start_time'] + $Dannie['wt'];

                            $idz = $Dannie['id'];
                        }
                    }

                }else{

                    $next_time_start = 10000;
                    $next_time_end = 10000;

                    $idz = 0;

                    foreach ($data as $Dannie){
                        if ($Dannie['start_time'] + $Dannie['wt'] < $next_time_start){
                            $next_time_start = $Dannie['start_time'];
                            $next_time_end = $Dannie['start_time'] + $Dannie['wt'];

                            $idz = $Dannie['id'];
                        }
                    }
                }
			}
			echo '{"req":"'.$req.'", "next_time_start":"'.$next_time_start.'", "next_time_end":"'.$next_time_end.'"}';
			//var_dump($data);
			/*
			if ($_POST['worker'] !=0){
				if ($_POST['patient'] != ''){
					if ($_POST['contacts'] != ''){
						if ($_POST['contacts'] != ''){
							//запись в базу
							WriteToDB_EditZapis ($datatable, $_POST['year'], $_POST['month'], $_POST['day'], $_POST['filial'], $_POST['kab'], $_POST['worker'], $_POST['author'], $_POST['patient'], $_POST['contacts'], $_POST['description'], $_POST['start_time'], $_POST['wt']);
							
							echo '
								Изменения в расписание внесены<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
							//header ('Location: scheduler.php?filial='.$_POST['filial'].$who.'&m='.$_POST['month'].'&y='.$_POST['year'].'');
						}else{
							echo '
								Не указано описание<br /><br />
								<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
						}
					}else{
						echo '
							Не указали контакты<br /><br />
							<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
					}
				}else{
					echo '
						Не указали пациента<br /><br />
						<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
				}
			}else{
				echo '
					Не выбрали врача<br /><br />
					<a href="scheduler_day.php?filial='.$_POST['filial'].$who.'&d='.$_POST['day'].'&m='.$_POST['month'].'&y='.$_POST['year'].'" class="b">К расписанию</a>';
			}*/
		}
	}
?>