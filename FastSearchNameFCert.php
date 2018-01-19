<?php
	
//FastSearchNameFCert.php
//Поиск сертификата

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 2)){
			//--
		}else{
			include_once 'DBWork.php';

			$fast_search = SelForFastSearchCert ('journal_cert', $_POST['searchdata']);
            if (!empty($fast_search)){
                //var_dump ($fast_search);

                $rez = '';

                $rez .= '<table width="100%" border="0" class="tableInsStat">';
                for ($i = 0; $i < count($fast_search); $i++){

                    $expired_txt = '';
                    $expired = false;
                    $expired_color = '';
                    $debited_txt = '';
                    $debited = false;
                    $debited_color = '';

                    if ($fast_search[$i]['expires_time'] != '0000-00-00') {
                        //время истечения срока годности
                        $sd = $fast_search[$i]['expires_time'];
                        //текущее
                        $cd = date('Y-m-d', time());
                        //сравнение не прошла ли гарантия
                        if (strtotime($sd) > strtotime($cd)) {
                            $expired_txt .= '';
                        } else {
                            $expired_txt .= 'истёк срок';
                            $expired = true;
                            $expired_color = 'background-color: rgba(239,47,55, .7)';
                        }
                    }

                    //потрачено
                    if ($fast_search[$i]["nominal"] - $fast_search[$i]["debited"] <= 0) {
                        $debited_txt .= 'потрачено';
                        $debited = true;
                        $debited_color = 'background-color: rgba(239,47,55, .7)';
                    }

                    //if (($fast_search[$i]['cell_time'] != '0000-00-00 00:00:00') && ($fast_search[$i]['status'] == 7)) {
                        $rez .= "<tr>
                        <td><span class='lit_grey_text'>номер</span><br><a href='certificate.php?id=".$fast_search[$i]['id']."' class='ahref'>" . $fast_search[$i]["num"] . "</a></td>
                            <td><span class='lit_grey_text'>номинал</span><br>" . $fast_search[$i]["nominal"] . "</td>
                            <td><span class='lit_grey_text'>был продан</span><br>";
                        if (($fast_search[$i]['cell_time'] == '0000-00-00 00:00:00') && ($fast_search[$i]['status'] != 7)) {
                            $rez .= '
                                        нет';
                        } else {
                            $rez .= date('d.m.y H:i', strtotime($fast_search[$i]['cell_time']))."<br>";
                        }

                        $rez .= '<span style="'.$expired_color.'">'.$expired_txt.'</span>';

                        $rez .= "</td>
                            <td><span class='lit_grey_text'>остаток</span><br>" . ($fast_search[$i]["nominal"] - $fast_search[$i]["debited"])."<br>";

                        $rez .= '<span style="'.$debited_color.'">'.$debited_txt.'</span>';

                        $rez .= "</td>";

                        $rez .= "    
                        </tr>";
                    //}
                }
                $rez .= '</table>';

                echo $rez;
            }
			
		}
	}

?>