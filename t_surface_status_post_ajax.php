<?php

//t_surface_status_post_ajax.php
//

	if($_POST){
		include_once 't_surface_status.php';
		//var_dump($_POST);
		
		echo t_surface_status($_POST['stat_id'], $_POST['stat_id']);
	}

?>