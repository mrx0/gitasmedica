<?php

//
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		include_once 'DBWork.php';
		include_once 'functions.php';
	
		require 'config.php';

		echo '
			<div id="data">
			
			
			<p><span class="a-action lasttreedrophide">скрыть всё</span>, <span class="a-action lasttreedropshow">раскрыть всё</span>.</p>';
			
		echo '
			<ul class="ul-tree ul-drop" id="lasttree">';

		showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0);		
			
		echo '
			</ul>';	
		
		echo '
			</div>';
			
		echo '
		<script>
$(document).ready(function(){




	$(".ul-dropfree").find("li:has(ul)").prepend(\'<div class="drop"></div>\');
	$(".ul-dropfree div.drop").click(function() {
		if ($(this).nextAll("ul").css(\'display\')==\'none\') {
			$(this).nextAll("ul").slideDown(400);
			$(this).css({\'background-position\':"-11px 0"});
		} else {
			$(this).nextAll("ul").slideUp(400);
			$(this).css({\'background-position\':"0 0"});
		}
	});
	$(".ul-dropfree").find("ul").slideUp(400).parents("li").children("div.drop").css({\'background-position\':"0 0"});


	$(".ul-drop").find("li:has(ul)").prepend(\'<div class="drop"></div>\');
	$(".ul-drop div.drop").click(function() {
		if ($(this).nextAll("ul").css(\'display\')==\'none\') {
			$(this).nextAll("ul").slideDown(400);
			$(this).css({\'background-position\':"-11px 0"});
		} else {
			$(this).nextAll("ul").slideUp(400);
			$(this).css({\'background-position\':"0 0"});
		}
	});
	$(".ul-drop").find("ul").slideUp(400).parents("li").children("div.drop").css({\'background-position\':"0 0"});



    $(".lasttreedrophide").click(function(){
		$("#lasttree").find("ul").slideUp(400).parents("li").children("div.drop").css({\'background-position\':"0 0"});
	});
    $(".lasttreedropshow").click(function(){
		$("#lasttree").find("ul").slideDown(400).parents("li").children("div.drop").css({\'background-position\':"-11px 0"});
	});




});
		</script>';
		

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>