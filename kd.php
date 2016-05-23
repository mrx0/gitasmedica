<?php

//
//

	require_once 'header.php';
	
	if ($enter_ok){
		if (($cosm['see_all'] == 1) || ($cosm['see_own'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$rezult = array();
				
				$task = SelDataFromDB('spr_kd_img', $_GET['client'], 'img');
				//var_dump($task);

				if ($task !=0){
					foreach($task as $value){
						if ($value['face_graf'] == 1){
							$rezult[$value['uptime']]['face'] = $value['id'];
						}
						if ($value['face_graf'] == 2){
							$rezult[$value['uptime']]['graf'] = $value['id'];
						}
					}
					
					//var_dump($rezult);
					
					
					echo '
						<div id="status">
							<header>
								<h2>КД '.WriteSearchUser('spr_clients', $_GET['client'], 'user').'</h2>
							</header>';

					echo '
							<div id="data">
						';
							
					foreach($rezult as $value){
						echo '
								<div class="cellsBlock2">
									<div class="cellRight">';
						echo '			
									<img src="kd/'.$value['face'].'.jpg" width="512" class="jLoupe" />';
						echo '
									<img src="kd/'.$value['graf'].'.jpg" width="256"/>';
						echo '	
									</div>
								</div>';
					}


					echo '
								</form>';	
						
					echo '
							</div>
						</div>
						
<script type="text/javascript" src="js/jquery.jloupe.js"></script>
<script type="text/javascript">
	$(function(){ 
		// Image 1 and 2 use built-in jLoupe selector

		// Image 3
		$(\'#custom\').jloupe({
		    radiusLT: 100,
		    margin: 12,
		    borderColor: false,
		    image: \'img\loupe-trans.png\'
		});

		// Image 4
		$(\'#shape\').jloupe({
		    radiusLT: 0,
		    radiusRT: 10,
		    radiusRB: 0,
		    radiusLB: 10,
		    width: 300,
		    height: 150,
		    borderColor: \'#f2730b\',
		    backgroundColor: \'#000\',
		    fade: false
		});
	});
</script>
						
						';
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