<?php
	
//FastSearchNameFCertName.php
//Поиск сертификата

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 2)){
			//--
		}else{
			include_once 'DBWork.php';
			include_once 'functions.php';

			$fast_search = SelForFastSearchCert ('journal_cert_name', $_POST['searchdata']);

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

//                    if ($fast_search[$i]['expires_time'] != '0000-00-00') {
//                        //время истечения срока годности
//                        $sd = $fast_search[$i]['expires_time'];
//                        //текущее
//                        $cd = date('Y-m-d', time());
//                        //сравнение не прошла ли гарантия
//                        if (strtotime($sd) > strtotime($cd)) {
//                            $expired_txt .= '';
//                        } else {
//                            $expired_txt .= 'истёк срок';
//                            $expired = true;
//                            $expired_color = 'background-color: rgba(239,47,55, .7)';
//                        }
//                    }

                    //использовано
                    if ($fast_search[$i]['invoice_id'] != 0) {
                        $debited_txt .= 'использовано';
                        $debited = true;
                        $debited_color = 'background-color: rgba(239,47,55, .7)';
                    }

                    //if (($fast_search[$i]['cell_time'] != '0000-00-00 00:00:00') && ($fast_search[$i]['status'] == 7)) {
                        $rez .= "<tr>
                                    <td><span class='lit_grey_text'>номер</span><br><a href='certificate_name.php?id=".$fast_search[$i]['id']."' class='ahref' target='_blank' rel='nofollow noopener'>" . $fast_search[$i]['num'] . "</a></td>
                                    <!--<td><span class='lit_grey_text'>номинал</span><br>" . $fast_search[$i]["nominal"] . "</td>-->
                                    <td><span class='lit_grey_text'>был выдан</span><br>";

                        if (($fast_search[$i]['cell_time'] == '0000-00-00 00:00:00') && ($fast_search[$i]['status'] != 7)) {
                            $rez .= '
                                        <span style="/*background-color: rgba(20 255 45 / 70%);*/ padding: 0 7px;">нет</span>';
                        } else {
                            $rez .= date('d.m.y H:i', strtotime($fast_search[$i]['cell_time']))."<br>";
                        }

                        //$rez .= '<span style="'.$expired_color.'">'.$expired_txt.'</span>';

                        $rez .= "    </td>
                                    <td><span class='lit_grey_text'>кому</span><br>";

                        if ($fast_search[$i]['client_id'] != 0){
                            $rez .= WriteSearchUser('spr_clients', $fast_search[$i]['client_id'], 'user', true);
                        }else{
                            $rez .= '-';
                        }

                        $rez .= "<br>";

                        $rez .= '<span style="'.$debited_color.'">'.$debited_txt.'</span>';

                        $rez .= "    </td>";

                        if (!$expired && !$debited) {
                            if ($fast_search[$i]['client_id'] != 0) {
                                $rez .= "<td style = 'background-color:rgb(204 204 204); text-align: center;'></td >";
                            }else{
                                $rez .= "<td style = 'background-color: rgb(207 255 207); text-align: center; cursor: pointer;' onclick = 'changeCertificateNameId(" . $fast_search[$i]['id'] . ")' ><i class='fa fa-check' aria - hidden = 'true' ></i ></td >";
                            }
                        }else{
                            $rez .= "<td style = 'background-color:rgb(204 204 204); text-align: center;'></td >";
                        }
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