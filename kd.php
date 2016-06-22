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
							
					foreach($rezult as $uptime => $value){
						echo '
							<div class="cellsBlock2">
								<div class="cellLeft">'.date('d.m.y H:i', $uptime).'</div>
							</div>';
							
						echo '
								<div class="cellsBlock2">';
						echo '									
									<div class="cellLeft">		
										<img src="kd/'.$value['face'].'.jpg" width="512" class="jLoupe" />
									</div>';
						echo '
									<div class="cellRight">';
						echo '
										<div>
											<a href="#open1" onclick="show(\'hidden_'.$uptime.'\',600,100)">Диаграмма</a>
										</div>';
						echo '
										<div id=hidden_'.$uptime.' style="display:none;">';
						echo '
											<img src="kd/'.$value['graf'].'.jpg" width="768"/>';
						echo '
										</div>';
						echo '
									</div>';
						echo '	
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
					echo '
			<script language="JavaScript" type="text/javascript">
				 /*<![CDATA[*/
				 var s=[],s_timer=[];
				 function show(id,h,spd)
				 { 
					s[id]= s[id]==spd? -spd : spd;
					s_timer[id]=setTimeout(function() 
					{
						var obj=document.getElementById(id);
						if(obj.offsetHeight+s[id]>=h)
						{
							obj.style.height=h+"px";obj.style.overflow="auto";
						}
						else 
							if(obj.offsetHeight+s[id]<=0)
							{
								obj.style.height=0+"px";
								obj.style.display="none";
							}
							else 
							{
								obj.style.height=(obj.offsetHeight+s[id])+"px";
								obj.style.overflow="hidden";
								obj.style.display="block";
								setTimeout(arguments.callee, 10);
							}
					}, 5);
				 }
				 /*]]>*/
			 </script>
					';
				}else{
					echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
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