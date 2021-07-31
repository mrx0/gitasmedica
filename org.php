<?php

//org.php
//Карточка организации

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($spravka['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				//include_once 'functions.php';
				
				$org_j = SelDataFromDB('spr_org', $_GET['id'], 'id');
				//var_dump($org_j);
				
				if ($org_j != 0){
					echo '
						<div id="status">
							<header>
                                <div class="nav">
                                    <a href="orgs.php" class="b">Организации</a>
                                </div>
								<h2>
									Карточка организации';
					
					if (($spravka['edit'] == 1) || $god_mode){
						if ($org_j[0]['status'] != 9){
							echo '
										<a href="org_edit.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Редактировать"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
						}
						if (($org_j[0]['status'] == 9) && (($spravka['close'] == 1) || $god_mode)){
							echo '
								<a href="#" onclick="Ajax_reopen_org('.$_GET['id'].')" title="Разблокировать" class="info" style="font-size: 80%;"><i class="fa fa-reply" aria-hidden="true"></i></a><br>';
						}
					}
					if (($spravka['close'] == 1) || $god_mode){
						if ($org_j[0]['status'] != 9){
							echo '
										<a href="org_del.php?id='.$_GET['id'].'" class="info" style="font-size: 80%;" title="Удалить"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
						}
					}

					echo '
								</h2>';
								
					if ($org_j[0]['status'] == 9){
						echo '<i style="color:red;">Организация удалена (заблокирована).</i><br>';
					}
					
					echo '
							</header>';

					echo '
							<div id="data">';
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">'.$org_j[0]['name'].' ['.$org_j[0]['full_name'].']</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">Юр. адрес</div>
									<div class="cellRight">'.$org_j[0]['ur_address'].'</div>
								</div>
									
								<div class="cellsBlock2">
									<div class="cellLeft">ИНН</div>
									<div class="cellRight">'.$org_j[0]['inn'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">КПП</div>
									<div class="cellRight">'.$org_j[0]['kpp'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">ОГРН</div>
									<div class="cellRight">'.$org_j[0]['ogrn'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Р.счет</div>
									<div class="cellRight">'.$org_j[0]['rs'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">К.счет</div>
									<div class="cellRight">'.$org_j[0]['ks'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">БИК</div>
									<div class="cellRight">'.$org_j[0]['bik'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Банк</div>
									<div class="cellRight">'.$org_j[0]['bank_name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">ОКПО</div>
									<div class="cellRight">'.$org_j[0]['okpo'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">ОКТМО</div>
									<div class="cellRight">'.$org_j[0]['oktmo'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">ОКВЕД</div>
									<div class="cellRight">'.$org_j[0]['okved'].'</div>
								</div>
								
							</div>';

                    echo '
                    <div id="doc_title">Карточка филиала '.$org_j[0]['name'].'</div>';
						
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>