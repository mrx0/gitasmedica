<?php

//notes_removes.php
//Напоминания и направления

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        if (($_SESSION['permissions'] == 1) || ($_SESSION['permissions'] == 2) || ($_SESSION['permissions'] == 3) || ($_SESSION['permissions'] == 9) || ($_SESSION['permissions'] == 12) || ($_SESSION['permissions'] == 11) || $god_mode){

            require 'variables.php';


			echo '
                        <div id="status_notes">';

            echo '
                            <div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">
                                <ul>
                                    <li><a href="#tabs-1">Напоминания</a></li>
                                    <li><a href="#tabs-2">Направления</a></li>
                                </ul>';
            echo '
                                <div id="tabs-1">';
            if (true){
                echo '
                                    <a href="temp_notes_cert.php" class="b">Выданные сертификаты</a>';
            }
            echo '
                                    <div id="notes"></div>
                                </div>';

            echo '
                                <div id="tabs-2">
                                    <div id="removes"></div>
                                </div>';

			echo '
				            </div>';
				

			echo '
                        </div>';

				
			echo '
				<script>

                    $(document).ready(function() {
                        getNotesfunc (0);
                        //setTimeout(function () {
                            getRemovesfunc (0);					        
        				//}, 10000);
				    });

 				</script> 
			';

        }else{
            echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
        }
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>