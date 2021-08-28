<?php

//test_video.php
//Тест с загрузкой видео

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
        echo '
        <script type="text/javascript" src="js/webtricks_demo_js_jquery.form.js"></script>';

        echo '
        <script type="text/javascript">
            function upload_video(){
                let bar = $("#bar");
                let percent = $("#percent");
                $("#myForm").ajaxForm({
                    beforeSubmit: function() {
                        document.getElementById("progress_div").style.display="block";
                        let percentVal = "0%";
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
            
                    uploadProgress: function(event, position, total, percentComplete) {
                        let percentVal = percentComplete + "%";
                        console.log(percentVal);
                      
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                
                    success: function() {
                        let percentVal = "100%";
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                
                    complete: function(xhr) {
                        if(xhr.responseText)
                        {
                            document.getElementById("output_video").innerHTML=xhr.responseText;
                            console.log(xhr);
                        }
                    }
                }); 
            }
        </script>
        ';


        echo '

        
<style type="text/css">

form 
{ 
  display: block; 
  margin: 20px auto; 
  background: #eee; 
  border-radius: 10px; 
  padding: 15px 
}
.progress 
{
  display:none; 
  position:relative; 
  width:400px; 
  border: 1px solid #ddd; 
  padding: 1px; 
  border-radius: 3px; 
}
.bar 
{ 
  background-color: #B4F5B4; 
  width:0%; 
  height:20px; 
  border-radius: 3px; 
}
.percent 
{ 
  position:absolute; 
  display:inline-block; 
  top:3px; 
  left:48%; 
}
</style>
        
        
<form action="upload_video.php" id="myForm" name="frmupload" method="post" enctype="multipart/form-data">
    <input type="file" id="upload_file" name="upload_file">
    <input type="submit" name="submit_video" value="Submit Comment" onclick="upload_video();">
</form>

<div class="progress" id="progress_div">
    <div class="bar" id="bar"></div>
    <div class="percent" id="percent">0%</div>
</div>

<div id="output_video"></div>
        ';




	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>