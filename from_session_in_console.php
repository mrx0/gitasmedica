<?php

//from_session_in_console.php
//

    session_start();

    //var_dump($_POST);
    //var_dump($_SESSION);
    echo json_encode(array('data' => $_SESSION));

?>