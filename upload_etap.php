<?php

//upload_etap.php
//!!! Хз, зачем это тут, хотя и упоминается где-то

//var_dump($_FILES);

// A list of permitted file extensions
$allowed = array('png', 'jpg');

if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

	$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

	if(!in_array(strtolower($extension), $allowed)){
		echo '{"status":"error"}';
		exit;
	}

	if(move_uploaded_file($_FILES['upl']['tmp_name'], 'uploads_etap/'.$_FILES['upl']['name'])){
		echo '{"status":"error"}';
		exit;
	}
}

echo '{"status":"error"}';
exit;