<?php
	function WriteToFile ($filepath, $data){
		$file = fopen($filepath, "ab");
		//$file = fopen($filepath, "ab");
		$str = $data;
		if (!$file){
			echo "Ошибка открытия файла";
		}else{
			if (is_array($data)){
				for ($i=1;$i<count($data);$i++)
					fputs ($file, $data[$i].PHP_EOL);
			}else{
				fputs ($file, $data.PHP_EOL);
			}
		}
		fclose ($file);
	}
?>