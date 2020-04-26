<?php

//Для создания кириллического шрифта для fpdf.php
//http://www.php.su/articles/?cat=others&page=004

require('fpdf/makefont/makefont.php');
MakeFont('arial.ttf','cp1251');
?>