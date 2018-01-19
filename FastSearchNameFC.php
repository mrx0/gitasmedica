<?php
	
//FastSearchNameFC.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 3)){
			//--
		}else{
			include_once 'DBWork.php';

			$fast_search = SelForFastSearchFullName ('spr_clients', $_POST['searchdata']);
			if ($fast_search != 0){
				//var_dump ($fast_search);
				for ($i = 0; $i < count($fast_search); $i++){
					echo '
                    <div style="border-bottom: 1px #ccc solid; width: 350px;">
                        <a href="client.php?id='.$fast_search[$i]["id"].'" class="ahref" style="display: block; height: 100%;">
                            <span style="font-size: 80%; font-weight: bold;">'.$fast_search[$i]["full_name"].'</span>
                            <br>
                            <span style="font-size: 70%">Дата рождения: ';

                    if ($fast_search[$i]['birthday2'] == '0000-00-00'){
                        echo 'не указана';
                    }else{
                        echo
                                date('d.m.Y', strtotime($fast_search[$i]['birthday2']));
                    }
                    echo '
                            </span>
                            <br>
                            <span style="font-size: 70%">
                                тел.: '.$fast_search[$i]['telephone'];
                    if ($fast_search[$i]['htelephone'] != NULL) {
                        echo '
                                 /д.тел: ' . $fast_search[$i]['htelephone'];
                    }
                    if ($fast_search[$i]['telephoneo'] != NULL) {
                        echo '
                                 /тел оп.: ' . $fast_search[$i]['telephoneo'];
                    }
                    if ($fast_search[$i]['htelephoneo'] != NULL) {
                        echo '
                                 /д.тел оп.: ' . $fast_search[$i]['htelephoneo'];
                    }
                    echo '
                            </span>';
                    if ($fast_search[$i]['passport'] != NULL) {
                        echo '
                            <br>
                            <span style="font-size: 70%">паспорт: ' . $fast_search[$i]['passport'] . '</span>';
                    }
                    echo '
                            <br>
                            <span style="font-size: 70%">№ карты: '.$fast_search[$i]['card'].'</span>
                        </a>
					</div>';
				}
			}
			
		}
	}

?>