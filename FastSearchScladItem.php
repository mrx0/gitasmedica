<?php
	
//FastSearchScladItem.php
//Поиск позиции склада

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 1)){
			//--
		}else{
			include_once 'DBWork.php';

			//$fast_search = SelForFastSearchAbon ('journal_abonement_solar', $_POST['searchdata']);
            $fast_search = array();

            $msql_cnnct = ConnectToDB ();

            $rez = array();

            //!Использовать надо везде. Очищение данных от мусора
            $search_data = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['searchdata']))));
            $datatable = 'spr_sclad_items';

            $query = "SELECT * FROM `$datatable` WHERE (`name` LIKE '{$search_data}%') AND `status`<> 9 ORDER BY `name` ASC LIMIT 5";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    array_push($fast_search, $arr);
                }
            }

            CloseDB ($msql_cnnct);


            if (!empty($fast_search)){
                //var_dump ($fast_search);

                $rez = '';

                //$rez .= '<table width="100%" border="0" class="tableInsStat">';
                for ($i = 0; $i < count($fast_search); $i++){


                    $rez .= "\n<li id='".$fast_search[$i]["id"]."' onmouseover='$(\".sclad_item_search\").blur();'>".$fast_search[$i]["name"]."</li>";

//                        $rez .= "<tr>
//                        <td><span class='lit_grey_text'>номер</span><br><a href='abonement.php?id=".$fast_search[$i]['id']."' class='ahref'>" . $fast_search[$i]["name"] . "</a></td>
//                            <td><span class='lit_grey_text'>номинал</span><br>-</td>
//                            <td><span class='lit_grey_text'>был продан</span><br>";
////                        if (($fast_search[$i]['cell_time'] == '0000-00-00 00:00:00') && ($fast_search[$i]['status'] != 7)) {
////                            $rez .= '
////                                        нет';
////                        } else {
////                            $rez .= date('d.m.y H:i', strtotime($fast_search[$i]['cell_time']))."<br>";
////                        }
//
//                        $rez .= '<span style="">-</span>';
//
////                        $rez .= "</td>
////                            <td><span class='lit_grey_text'>остаток</span><br>" . ($fast_search[$i]["nominal"] - $fast_search[$i]["debited"])."<br>";
//                        $rez .= "</td>
//                            <td><span class='lit_grey_text'>остаток</span><br>-<br>";
//
//                        $rez .= '<span style="">-</span>';
//
//                        $rez .= "</td>";
//
//                        $rez .= "
//                        </tr>";
                    //}
                }
                //$rez .= '</table>';

                echo $rez;
            }
			
		}
	}

?>