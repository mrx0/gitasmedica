<?php

//upload_video.php
//

//var_dump($_FILES);


if(isset($_POST['submit_video']))
{
    $uploadfile=$_FILES["upload_file"]["tmp_name"];

    $folder="video2/";

    move_uploaded_file($_FILES["upload_file"]["tmp_name"], $folder.$_FILES["upload_file"]["name"]);

    //echo '<img src="'.$folder."".$_FILES["upload_file"]["name"].'">';

    echo 'Ok';
    exit();
}

