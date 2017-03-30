<?php 

//show_tree.php
//

	include_once 'DBWork.php';
	include_once 'functions.php';

	echo '
		<select name="group" id="group" size="6" style="width: 250px;">
			<option value="0">*</option>';

    showTree(0, '', 'select', 0, TRUE, 0, FALSE, 'spr_pricelist_template', 0);

    echo '
		</select>';

	
?>