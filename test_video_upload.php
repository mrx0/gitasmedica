<?php

//test_video.php
//Тест с загрузкой видео

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
        echo '
        <script type="text/javascript" src="js/webtricks_demo_js_jquery.form.js"></script>';


        echo '
        
            <form action="upload_video.php" id="form_video_upload" name="frmupload" method="post" enctype="multipart/form-data">
                <input type="file" id="upload_file" name="upload_file">
                <input type="submit" name="submit_video" value="Submit Comment" onclick="upload_video();">
            </form>
            
            <div class="progress_video_upload" id="progress_div">
                <div class="bar_video_upload" id="bar_video_upload"></div>
                <div class="percent_video_upload" id="percent_video_upload">0%</div>
            </div>
            
            <div id="output_video"></div>
';




	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>