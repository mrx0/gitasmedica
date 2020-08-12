<?php
header('Content-disposition: inline');
header('Content-type: application/msword'); // not sure if this is the correct MIME type
readfile('download/insure_xls/1016.xls');
exit;
?>