<?php

//переход на новую систему 2018.10.17
//автоматический перенос категорий из РЛ в наряды

	include_once '../DBWork.php';

    $rez = array();
    $rez2 = array();

	$msql_cnnct = ConnectToDB();
	
	//$query = "SELECT * FROM `spr_clients` WHERE `birthday2`='0000-00-00'";
	/*$query = "SELECT fl_jcalc.id, fl_jcalc.invoice_id, fl_jcalcex.* FROM `fl_journal_calculate` fl_jcalc
              LEFT JOIN `fl_journal_calculate_ex` fl_jcalcex ON fl_jcalcex.calculate_id = fl_jcalc.id";*/

    /*$query = "SELECT fl_jcalc.id, fl_jcalc.invoice_id FROM `fl_journal_calculate` fl_jcalc";

	$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
	
	$number = mysqli_num_rows($res);
	
	if ($number != 0){
		while ($arr = mysqli_fetch_assoc($res)){
			$rez[$arr['id']] = $arr;
		}
	}*/
	
	//if (!empty($rez)){
		//var_dump($rez);
		
		//foreach ($rez as $data){
			//var_dump($data);

            $query = "SELECT * FROM `fl_journal_calculate_ex`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);

            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $rez2[$arr['id']] = $arr;
                }
            }

            if (!empty($rez2)) {
                //var_dump($rez2);

                foreach ($rez2 as $data2) {

                    $query = "UPDATE `journal_invoice_ex` SET `percent_cats`='" . $data2['percent_cats'] . "' WHERE `id`='" . $data2['inv_pos_id'] . "' AND `percent_cats` = '0'";


                    mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);


                    if (mysqli_affected_rows ($msql_cnnct) > 0) {
                        var_dump('id => '.$data2['inv_pos_id']. '|| calculate_id => ' . $data2['calculate_id'].'['.mysqli_affected_rows ($msql_cnnct).']');
                    }
                }
            }

                            /*$query = "UPDATE `spr_clients` SET `birthday2`='".$newDate."' WHERE `id`='".$client['id']."' AND `birthday2`='0000-00-00' LIMIT 1";

                            mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);*/
			
			/*if (date('Y-m-d', $client['birthday']) != $client['birthday2']){
				echo $client['id'].' => '.$client['birthday'].' = '.date('Y-m-d', $client['birthday']).' => '.$client['birthday2'].'<br>';
			}*/

			echo 'Ok!!! ThE eNd';

		//}

	//}
	
?>