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
				<div style="width: 350px; height: 500px; overflow: scroll; border: 1px solid #CCC;">
					<ul class="ul-tree ul-drop" id="lasttree">';

		showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0);		
			
		echo '
					</ul>
				</div>
				
				<div id="rez">
				12
				</div>
				';	
		
		echo '
			</div>';
			
		

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>