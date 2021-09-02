<?php

//test_video2.php
//Тест с видео

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

        echo '
            <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet" >';

        echo '
            <video
                id="my-video"
                class="video-js"
                controls
                preload="auto"
                width="640"
                data-setup="{}"
            >
                 <source src="video2/01.mp4" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>
            </video>
        ';

        echo '
            <script src="https://vjs.zencdn.net/7.14.3/video.min.js"></script>
        ';

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>