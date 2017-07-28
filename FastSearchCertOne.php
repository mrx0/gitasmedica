<?php
	
//FastSearchCertOne.php
//Поиск сертификата одного

	//var_dump ($_POST);
	if ($_POST){

	    $rez = '';

		if (!isset($_POST['id'])){
		    //
		}else{
			include_once 'DBWork.php';	
			$fast_search = SelDataFromDB ('journal_cert', $_POST['id'], 'id');
			if ($fast_search != 0){

				    $expired = false;
                    $debited = false;

				    if ($fast_search[0]['expires_time'] != '0000-00-00') {
                        //время истечения срока годности
                        $sd = $fast_search[0]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        //сравнение не прошла ли гарантия
                        if (strtotime($sd) > strtotime($cd)) {

                        } else {
                            $expired = true;
                        }
                    }

                    //потрачено
 				    if ($fast_search[0]["nominal"] - $fast_search[0]["debited"] <= 0) {
                        $debited = true;
                     }

                    if (($fast_search[0]['cell_time'] != '0000-00-00 00:00:00') && ($fast_search[0]['status'] == 7)) {
                        if (!$expired && !$debited) {
                        $rez .= "<tr>
                        <td>" . $fast_search[0]["num"] . "</td>
                        <td>" . $fast_search[0]["nominal"] . "</td>
                        <td><div class='cert_pay' cert_id='".$fast_search[0]["id"]."'>" . ($fast_search[0]["nominal"] - $fast_search[0]["debited"])."</div></td>
                        <td style='text-align: center; cursor: pointer;' onclick='certsResultDel();'><i class='fa fa-times' aria-hidden='true' style='color: red;' title='Удалить'></i></td>
                        </tr>";

                    }
				}
                echo json_encode(array('result' => 'success', 'data' => $rez));
			}else{
                echo json_encode(array('result' => 'error', 'data' => $fast_search));
            }
		}
	}

?>