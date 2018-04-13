<?php
  $a = date("H", time()-60*60);
  if ($a > 16) 
	  echo '17';
  else
	  echo '00';
?>