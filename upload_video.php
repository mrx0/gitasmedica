<?php

//upload_video.php
//

//var_dump($_FILES);


if(isset($_POST['submit_video']))
{
    $uploadfile = $_FILES["upload_file"]["tmp_name"];

    $folder = "video2/";

    move_uploaded_file($_FILES["upload_file"]["tmp_name"], $folder.$_FILES["upload_file"]["name"]);

    //echo '<img src="'.$folder."".$_FILES["upload_file"]["name"].'">';

    //echo 'Ok';

    echo '
    <video width="640" height="480" controls poster="">
        <!--<source src="video/nubex.ogv" type=\'video/ogg; codecs="theora, vorbis"\'>
        <source src="video/nubex.mp4" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>-->
        <source src="'.$folder.$_FILES["upload_file"]["name"].'" <!--type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'-->>
        <!--<source src="video/Сергей Мельников Кураторы лечения в стоматологии-master.m3u8" type="application/x-mpegURL">-->
        <!--<source src="video/nubex.webm" type=\'video/webm; codecs="vp8, vorbis"\'>-->
        Ваш браузер не поддерживает тег video.
    </video>';

    exit();
}

